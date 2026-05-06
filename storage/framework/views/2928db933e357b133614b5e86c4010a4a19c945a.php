

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('public/design')); ?>/images/erp-logo-icon.png" type="image/png"/>
    <title>Venus ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/_main.css" />
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/_icomoon.css" />
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/_header.css" />
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/_navbar.css" />
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/purchase-order.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Toastr -->

</head>
<body>


<style>
.toast-center-center { top: 45%; left: 55%; transform: translate(-50%, -50%); position: fixed; z-index: 9999; }
.toast-top-right { top: 70px; right: 20px; position: fixed; z-index: 9999; }
.toast-top-right > .toast { opacity: 1 !important; }
.toast-title { font-size: 17px; }
.toast-message { font-size: 13px; }
</style>
<script>
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "timeOut": "10000",
    "extendedTimeOut": "10000",
    "showDuration": "300",
    "hideDuration": "1000",
    "positionClass": "toast-top-right"
}
</script>
<?php echo Toastr::message(); ?>


    
 

    <?php /*
    if(session('logged_session_data.auth_status')==0){
        header("Location: ".url('/')."/crm-auth");
        exit();
    } */ ?>
    <div id="loading_bg"
        style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index:9999; text-align: center; display:none;">
        <img src="<?php echo asset('public/design/assets/images/loader.gif'); ?>" style="margin: 20%;">
    </div>

    <div class="venus-app page-purchase-order">

        
            
            
            

        <?php echo $__env->make('backEnd.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>


        <main>
            <section class="main-content">

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Your Password Expired
                </h4>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">

					<div class="row">
							<div class="col-4"></div>
							<div class="col-4"><br /><br /><br />
								
								<div class="white-box">
									<div class="col-12"> 

										<h5>Update Your Password</h5>
										<hr />
				
				
						<?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'change-password2', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student'])); ?>

				
				
										<?php if(session()->has('message-success') != ""): ?>
											<?php if(session()->has('message-success')): ?>
											<div class="alert alert-success">
												<?php echo e(session()->get('message-success')); ?>

											</div>
											<?php endif; ?>
										<?php endif; ?>
										 <?php if(session()->has('message-danger') != ""): ?>
											<?php if(session()->has('message-danger')): ?>
											<div class="alert alert-danger">
												<?php echo e(session()->get('message-danger')); ?>

											</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
				
										
											<input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
				
											<div class="row mb-4">
												<div class="col-12">
													<div class="input-effect">
														<label><?php echo app('translator')->getFromJson('lang.current'); ?> <?php echo app('translator')->getFromJson('lang.password'); ?></label>
														<input class="primary-input dynamicstxt_s form-control<?php echo e($errors->has('current_password') || session()->has('password-error') ? ' is-invalid' : ''); ?>" type="password" name="current_password">
														<span class="focus-border"></span>
														<?php if($errors->has('current_password')): ?>
														<span class="invalid-feedback" role="alert">
															<strong><?php echo e($errors->first('current_password')); ?></strong>
														</span>
														<?php endif; ?>
														<?php if(session()->has('password-error')): ?>
														<span class="invalid-feedback" role="alert">
															<strong><?php echo e(session()->get('password-error')); ?></strong>
														</span>
														<?php endif; ?>
													</div>
												</div>
											</div>
				
											<div class="row mb-4">
												<div class="col-12">
													<div class="input-effect">
														<label><?php echo app('translator')->getFromJson('New'); ?> <?php echo app('translator')->getFromJson('lang.password'); ?></label>
														<input class="primary-input dynamicstxt_s form-control<?php echo e($errors->has('new_password') ? ' is-invalid' : ''); ?>" type="password" name="new_password">
														<span class="focus-border"></span>
														<?php if($errors->has('new_password')): ?>
														<span class="invalid-feedback" role="alert">
															<strong><?php echo e($errors->first('new_password')); ?></strong>
														</span>
														<?php endif; ?>
													</div>
												</div>
											</div>
				
											<div class="row mb-4">
												<div class="col-lg-12">
													<div class="input-effect">
														<label><?php echo app('translator')->getFromJson('lang.confirm'); ?> <?php echo app('translator')->getFromJson('lang.password'); ?></label>
														<input class="primary-input dynamicstxt_s form-control<?php echo e($errors->has('confirm_password') ? ' is-invalid' : ''); ?>" type="password" name="confirm_password">
														<span class="focus-border"></span>
														<?php if($errors->has('confirm_password')): ?>
														<span class="invalid-feedback" role="alert">
															<strong><?php echo e($errors->first('confirm_password')); ?></strong>
														</span>
														<?php endif; ?>
													</div>
												</div>
											</div>
				
				
											
				
											<div class="row">
												<div class="col-lg-12 text-end">
				
				
													<?php if(Illuminate\Support\Facades\Config::get('app.app_sync')): ?>
													<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo "> <button class="primary-btn small fix-gr-bg  demo_view" style="pointer-events: none;" type="button" disabled> <?php echo app('translator')->getFromJson('lang.change'); ?> <?php echo app('translator')->getFromJson('lang.password'); ?></button></span>
												<?php else: ?>
												<button type="submit" class="btn btn-light">
													<i class="ico icon-outline-bookmark-square text-success"></i>
												   <?php echo app('translator')->getFromJson('lang.change'); ?> <?php echo app('translator')->getFromJson('lang.password'); ?>
												</button>
												<?php endif; ?> 
												   
												</div>
											</div>
									   
									<?php echo e(Form::close()); ?>

								</div>
								<br /><br /><br /><br />
							</div>
							<div class="col-4"></div>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>


            </section>
        </main>
        


    </div>
    

    <!-- Flatpickr CSS DATE -->
    <link rel="stylesheet" href="<?php echo e(asset('public/design')); ?>/assets/css/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Flatpickr CSS DATE -->
 
    <script src="<?php echo e(asset('public/design')); ?>/assets/js/aditional.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="<?php echo e(asset('public/design')); ?>/assets/js/_main.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('public/design')); ?>/assets/js/purchase-order.js" type="text/javascript"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo e(asset('public/backEnd/')); ?>/js/erpjs.js"></script>

    



    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>
 
</body>
</html>
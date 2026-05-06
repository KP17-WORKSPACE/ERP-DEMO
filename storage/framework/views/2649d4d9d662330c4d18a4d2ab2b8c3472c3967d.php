
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Toastr -->

<script src="<?php echo e(asset('public/backEnd/')); ?>/js/erpjs.js"></script>

</head>
<body>


<style>
/* Toastr Notification Styles */
.toast-container {
    pointer-events: none;
}

.toast-container .toast {
    pointer-events: auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    border-radius: 8px !important;
    border: none !important;
    overflow: visible !important;
    padding: 15px 20px 15px 15px !important;
    opacity: 1 !important;
}

.toast-center-center {
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    position: fixed;
    z-index: 9999;
}

.toast-top-right {
    top: 85px;
    right: 20px;
    position: fixed;
    z-index: 9999;
}

.toast-top-right > .toast {
    margin-bottom: 12px;
    min-width: 320px;
    max-width: 400px;
}

.toast-title {
    font-size: 15px !important;
    font-weight: 600 !important;
    margin-bottom: 4px !important;
    color: #ffffff !important;
}

.toast-message {
    font-size: 13px !important;
    line-height: 1.5 !important;
    color: rgba(255, 255, 255, 0.95) !important;
    font-weight: 400 !important;
}

/* Success Notification */
.toast-success {
    background-color: #22c55e !important;
    background-image: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
}

/* Error Notification */
.toast-error {
    background-color: #ef4444 !important;
    background-image: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
}

/* Warning Notification */
.toast-warning {
    background-color: #f59e0b !important;
    background-image: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
}

/* Info Notification */
.toast-info {
    background-color: #3b82f6 !important;
    background-image: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
}

/* Progress Bar */
.toast-progress {
    height: 4px !important;
    opacity: 0.6 !important;
    background-color: rgba(255, 255, 255, 0.4) !important;
}

/* Close Button */
.toast-close-button {
    color: rgba(255, 255, 255, 0.95) !important;
    opacity: 0.8 !important;
    font-weight: 300 !important;
    text-shadow: none !important;
    font-size: 18px !important;
}

.toast-close-button:hover {
    opacity: 1 !important;
    color: #ffffff !important;
}

/* Animation */
@keyframes  slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.toast {
    animation: slideInRight 0.4s ease-out !important;
}

/* Responsive */
@media (max-width: 768px) {
    .toast-top-right > .toast {
        min-width: 280px;
        max-width: calc(100vw - 40px);
    }
}
</style>

<script>
// Toastr Configuration
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "300",
    "timeOut": "1000",
    "extendedTimeOut": "2000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
</script>
<?php echo Toastr::message(); ?>


    
    <?php
    if(App\SysHelper::password_update()==1){
        header("Location: ".url('/')."/password-exp");
        exit();
    } ?>

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


        <main class="show-nav">
            
        
            <?php if($currentSubdomain === 'demo'): ?>
                <?php echo $__env->make('backEnd.partials.sidenavnew', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php elseif($currentSubdomain === 'marketing'): ?>
                <?php echo $__env->make('backEnd.partials.sidenavmarketing', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php elseif($currentSubdomain === 'hrms1'): ?>
                <?php echo $__env->make('backEnd.partials.sidenavhrms', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php else: ?>
                <?php echo $__env->make('backEnd.partials.sidenavnew', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>

            <section class="main-content">

        <?php echo $__env->make('components.center-popup', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                
                <?php echo $__env->yieldContent('mainContent'); ?>
                

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

    


<script>
    // Global variable
    window.SELECTED_STATE_ID = null;
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
document.querySelectorAll('input').forEach(i => {
    i.setAttribute('autocomplete', 'off'); // or 'disable-autocomplete'
});

});
</script>

<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
document.querySelectorAll('input').forEach(i => {
    i.setAttribute('autocomplete', 'nope'); // or 'disable-autocomplete'
});

});
</script> -->


    <script>

        

        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // When a modal is opened, reattach Select2 dropdown inside that modal
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.js-example-basic-single').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                });
            });
        });
        
    </script>

    <script>
function pollNotifications() {
    $.get("<?php echo e(url('/notifications/pending')); ?>", function(data) {
        if (data.length > 0) {
            location.reload();
        }
    });
}

// 900000 ms = 15 minutes
setInterval(pollNotifications, 900000);
</script>

  <?php echo $__env->yieldPushContent('modals'); ?>
  <?php echo $__env->yieldPushContent('scripts'); ?>




  <script>

    

        $(document).ready(function() {

   // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-popover="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });
        });

</script>
<script src="<?php echo e(asset('public/backEnd/')); ?>/js/draggable-modal.js"></script>

</body>
</html>


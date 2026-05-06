<?php
    $notification=\App\SysNotifications::where('received_id',Auth::user()->id)->where('is_read',0)->latest()->get();
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top">
    <!-- Sidebar Toggle (Topbar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>    
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3  disbbtn">
        <i class="fa fa-bars"></i>
    </button>

    <script>
        navigator.serviceWorker.register("sw.js");
        function requestPermission() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    
                    // get service worker
                    navigator.serviceWorker.ready.then((sw) =>{
                        
                        // subscribe
                        sw.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey:"BOPWfY51U_FzhkN3YGiLoRpNwHEN7Q_R_2YSRgqijTn4VVb8aBy5YoEEoAbevT0hL74L91qig0-hTAW3xo1Eg6M"
                        }).then((subscription) => {

                            // subscription successful
                            fetch("https://erp.venushrms.com/api/push-subscribe/"+<?php echo e(Auth::user()->id); ?>, {
                                method: "post",
                                body:JSON.stringify(subscription)
                            }).then( alert("Notification Enabled Successfully") );
                        });
                    });
                }
            });
        }
    </script>

    <div>
        <h2 class="page-heading m-0" style="font-size: 17px; font-weight: normal;"><span class=""><i class="fa fa-star text-warning" aria-hidden="true"></i> 
            <?php echo e(App\SysHelper::user_wish_text()); ?> <span style="font-size: 20px; font-weight: bold;"><?php echo e(Auth::user()->full_name); ?>!</span> Your dashboard says it&apos;s time to shine! <i class="fa fa-star text-warning" aria-hidden="true"></i></h2>
        <input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>" />
</div>

    
    <script>
        $(document).ready(function() {
            if(window.innerWidth<480){
                $('#sidebarToggleTop').click();                
            }
        });
    </script>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow mx-1 mt-3">
        <?php $com_list = App\SysHelper::get_company_names(); ?>
        <select class="form-control ml-2 mr-2" style="width: 300px; background: #0b2262; color: #ffffff;" id="main_company_id">
            <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e(@$list->id); ?>" <?php if(session('logged_session_data.company_id') == @$list->id): ?> selected <?php endif; ?>><?php echo e(@$list->company_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <script>
            $(document).on("change", "#main_company_id", function () {
                var companyid = $("#main_company_id").val();
                var action = "<?php echo e(URL::to('set-company-id')); ?>";
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            companyid: companyid,
                        },
                        cache: false,
                        success: function(dataResult) {
							
                            var companyid = $("#main_company_id").val();
                            localStorage.setItem("active_company_id", companyid);
							
                            var returl = "<?php echo e(URL::to('crm-dashboard')); ?>";
                            window.location.href = returl;
                            //location.reload();
                        },
                    });
                $("#loading_bg").css("display", "block");
            });
        </script>
        </li>
        <?php if(Auth::user()->role_id == 1): ?>
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" >
                <i class="fas fa-list-alt fa-fw text-primary" aria-hidden="true" style="font-size: 31px; cursor: pointer;" onclick="fn_d_dropdown()"></i>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" id="d_dropdown">
                <h6 class="dropdown-header">
                    Dashboard Views
                </h6>
                <div style="max-height: 80vh;">
                    <div class="p-3">
                        <?php $d_userlist = @App\SmStaff::select('user_id','full_name')->wherein('role_id',[3,5,27,4,6,29,8,9,10,26,28,31,32])->orderby('full_name','asc')->get(); ?>
                        <select id="userSelect" class="form-control js-example-basic-single" onchange="get_d_company_list()">
                            <option value="">Select User</option>
                            <?php $__currentLoopData = $d_userlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($list->user_id); ?>"><?php echo e($list->full_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <br /><br />
                        <select id="companySelect" class="form-control">
                            <option value="">Select Company</option>
                        </select>
                        <br />
                        <button type="button" class="btn-sm btn-danger float-right" onclick="view_dashboard()">View Dashboard</button>
                        <br />
                        <br />
                    </div>
                </div>
            </div>
            <script>
                function fn_d_dropdown() {
                    var dropdown = $('#d_dropdown');
                    if (dropdown.css('display') === 'none') {
                        dropdown.css('display', 'block');
                    } else {
                        dropdown.css('display', 'none');
                    }
                }

                function get_d_company_list()
                {
                    //var url = window.location.origin;
                    var url = window.location.origin+'/syscom-erp';
                $.ajax({
                    type: "POST",
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        user_id: $('#userSelect').val(),
                    },
                    url: url + '/' + 'get-user-company',
                    cache: false,
                    success: function(response) {
                        var response = JSON.parse(response);
                        var len = 0;
                        if (response['data'] == "ERROR") {
                            alert("Error found in something!!");
                        } else {
                            if (response['data'] != null) {
                                len = response['data'].length;
                            }
                            if (len > 0) {

                                $('#companySelect').find('option').not(':first').remove();

                                for (var i = 0; i < len; i++) {
                                    var id = response['data'][i].id;
                                    var name = response['data'][i].company_name;
                                    var option = "<option value='" + id + "'>" + name + "</option>";
                                    //$("#shipping_name").append($(option));
                                    //$('#shipping_name').append(new Option(name, id));
                                    $("#companySelect").append(option);
                                }
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {}
                });
                }
                function view_dashboard()
                {
                    var uid = $('#userSelect').val();
                    var cid = $('#companySelect').val();
                    if(uid==""){$('#userSelect').focus(); return false;}
                    if(cid==""){$('#companySelect').focus(); return false;}
                    //var url = window.location.origin;
                    var url = window.location.origin+'/syscom-erp';
                    var url = url + '/' + 'crm-dashboard-views/' + cid + '/' + uid;
                    window.open(url, '_blank');
                }
            </script>
        </li>
        <?php endif; ?>

        <li class="nav-item dropdown no-arrow mx-1"><a class="nav-link" href="<?php echo e(url('stock-search')); ?>" target="_blank" title="Stock Search" ><i class="fas fa-search fa-fw"></i></a></li>
        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter"><?php echo e(count(@$notification)); ?></span>
            </a>
            <!-- Dropdown - Alerts -->

            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Notification Center
                </h6>
                <div style="max-height: 80vh; overflow: hidden; overflow-y: scroll;">
                    <button class="btn btn-warning pt-0 pb-0 pl-2 pr-2 mt-2 ml-2" onclick="requestPermission()">Enable Notification</button>
                    <hr />
                <?php $__currentLoopData = @$notification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item d-flex align-items-center" href="#" onclick="notification_read(<?php echo e($data->id); ?>)">
                    <div class="mr-3">
                        <?php if(str_contains($data->message, 'eject')): ?>
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="small text-gray-500">
                            <?php echo e(date('F d, Y', strtotime(@$data->created_at))); ?>

                            
                        </div>
                        <span class="font-weight-bold"><?php echo e(@$data->message); ?></span>
                    </div>
                </a>
                <input type="hidden" id="url_link_<?php echo e($data->id); ?>" value="<?php echo e($data->link); ?>" />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a class="dropdown-item text-center small text-gray-500" href="#" onclick="notification_read_all(<?php echo e(Auth::user()->id); ?>)" >Mark As Read</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small text-capitalize"><?php echo e(Auth::user()->full_name); ?></span>
                <?php if(file_exists(@$profile_image)): ?>
                    <img class="img-profile rounded-circle" src="<?php echo e(file_exists(@$profile_image) ? asset($profile_image) : asset('public/uploads/staff/demo/staff.jpg')); ?>" alt="">
                <?php else: ?>
                    <img class="img-profile rounded-circle" src="<?php echo e(asset('/')); ?>public/uploads/staff/demo/staff.jpg" alt="">
                <?php endif; ?>
                
            </a>
            <!-- Dropdown - User Information -->

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="<?php echo e(route('viewStaff', Auth::user()->staff->id)); ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="<?php echo e(url('change-password')); ?>">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Change Password
                </a>
                <a class="dropdown-item" href="<?php echo e(url('/crm-dashboard')); ?>">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Dashboard
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>



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
                    <a class="btn btn-primary" href="<?php echo e(route('logout')); ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>
<!-- Logout Modal-->



<script>
    function notification_read_all(id){
        var action = "<?php echo e(URL::to('notification-read')); ?>";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                location.reload();
            }
        });
    }

    function notification_read(id){
        var url = $("#url_link_"+id).val();
        var action = "<?php echo e(URL::to('notification-read-one')); ?>";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                window.location.href = url;
            }
        });
    }
</script>
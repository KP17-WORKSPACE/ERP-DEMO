<header class="main-header">
            <div class="logo"><img src="<?php echo e(asset('public/design')); ?>/assets/images/logo-white.png" alt="VENUS LOGO" /></div>
            <div class="right-section">
                <div class="dropdown">
                    <?php $com_list = App\SysHelper::get_company_names(); ?>
                    <select class="btn btn-light dropdown-toggle syscom-dropdown-toggle text-start w-100"  id="main_company_id">
                        <?php if($com_list): ?>
                              <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(@$list->id); ?>" <?php if(session('logged_session_data.company_id') == @$list->id): ?> selected <?php endif; ?>><?php echo e(@$list->company_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                                  
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
                                        var returl = "<?php echo e(URL::to('crm-dashboard')); ?>";
                                        window.location.href = returl;
                                        //location.reload();
                                    },
                                });
                            $("#loading_bg").css("display", "block");
                        });
                    </script>
                </div>
                                
                <?php if(Auth::user()->role_id == 1): ?>
                <style> #d_dropdown{top:36px; right:100px; width: 250px; height: 180px;}</style>
                <a class="btn btn-light add-btn" title="Stock Ledger" onclick="fn_d_dropdown()"><i class="ico icon-outline-display"></i></a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" id="d_dropdown">
                <h4 class="dropdown-header">
                    <b>Dashboard Views</b>
                </h4>
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
                        <button type="submit" class="btn btn-light text-success" style="font-size: 12px;" onclick="view_dashboard()"><i class="ico icon-outline-pie-chart-2 text-success"></i> View Dashboard</button>
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
                    //$("#loading_bg").css("display", "block");
                    var url = window.location.origin;
                    var url = window.location.origin+'/syscom-erp-design';
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
                    $("#loading_bg").css("display", "none");
                }
                function view_dashboard()
                {
                    var uid = $('#userSelect').val();
                    var cid = $('#companySelect').val();
                    if(uid==""){$('#userSelect').focus(); return false;}
                    if(cid==""){$('#companySelect').focus(); return false;}
                    var url = window.location.origin;
                    //var url = window.location.origin+'/syscom-erp-design';
                    var url = url + '/' + 'crm-dashboard-views/' + cid + '/' + uid;
                    window.open(url, '_blank');
                }
            </script>
                <?php endif; ?>

                <a class="btn btn-light add-btn" title="Stock Ledger" href="<?php echo e(url('stock-search')); ?>" target="_blank"><i class="ico icon-outline-notebook"></i></a>
                <a class="btn btn-light add-btn" title="Customer Search" href="<?php echo e(url('customer-search')); ?>" target="_blank"><i class="ico icon-bold-users-group-two-rounded"></i></a>

                <button class="btn btn-light notification-unread"><i class="ico icon-outline-bell"></i></button>
                <div class="profile-dropdown">
                    <div class="profile-img">
                         <?php if(file_exists(@$profile_image)): ?>
                            <img src="<?php echo e(file_exists(@$profile_image) ? asset($profile_image) : asset('public/uploads/staff/demo/staff.jpg')); ?>" alt="" width="28px" height="28px">
                        <?php else: ?>
                            <img  src="<?php echo e(asset('public/design')); ?>/assets/images/profile.png" alt="" width="28px" height="28px">
                        <?php endif; ?>
                        
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <div class="dropdown-toggle-text">
                                <div class="title"><?php echo e(Auth::user()->full_name); ?></div>
                                <div class="role"><?php echo e(session('logged_session_data.role_name')); ?></div>
                            </div>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico text-white"></i>
                        </button>
                        <div class="dropdown-menu profile-dropdown-body py-4 px-3">
                            <div class="profile-content">
                                <img src="<?php echo e(asset('public/design')); ?>/assets/images/profile_img.png" alt="profile_img" class="profile-image">
                                <h4 class="profile_name"><?php echo e(Auth::user()->full_name); ?></h4>
                               <div class="d-flex justify-content-center mb-3">
                                <div class="company-name"><?php echo e(session('logged_session_data.company_name')); ?></div></div>
                                <div class="profile-second-section py-3">
                                    <h4 class="company-details-text mb-3">Company Details</h4>
                                    <div class="row">
                                        <div class="col-7">Designation:
                                            <h6 class=""><?php echo e(session('logged_session_data.designation_name')); ?></h6>
                                            staff ID:
                                            <h6><?php echo e(session('logged_session_data.staffid')); ?></h6>
                                        </div>
                                        <div class="col-5">Departments:
                                            <h6><?php echo e(session('logged_session_data.department_name')); ?></h6>
                                            Date Of Joining:
                                            <h6><?php echo e(session('logged_session_data.joining_date')); ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-third-section py-3">
                                    <h4 class="personal-details-text">Personal Details</h4>
                                    <div class="contact-email">
                                        <img src="<?php echo e(asset('public/design')); ?>/assets/images/telephone_Vector.png" class="telephone" alt="telephone_icon">
                                        <h6><?php echo e(session('logged_session_data.mobile')); ?></h6>
                                    </div>
                                    <div class="contact-email">
                                        <img src="<?php echo e(asset('public/design')); ?>/assets/images/envelop_Vector.png" class="email" alt="email_icon">
                                        <h6><?php echo e(Auth::user()->email); ?></h6>
                                    </div>
                                </div>
                                <div class="profile-last-section">
                                    <div>
                                        <a class="btn" href="<?php echo e(route('viewStaff', Auth::user()->staff->id)); ?>">
                                            <i class="ico icon-outline-pen-2"></i>
                                        </a>
                                        <p class="button-text">Edit Profile</p>
                                    </div>
                                    <div>
                                        <a class="btn" href="<?php echo e(url('change-password')); ?>">
                                            <img src="<?php echo e(asset('public/design')); ?>/assets/images/Lock Keyhole Minimalistic.png" alt="lock_img">
                                        </a>
                                        <p class="button-text">Change Password</p>
                                    </div>
                                    <div>
                                        <button class="btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                            <img src="<?php echo e(asset('public/design')); ?>/assets/images/Logout 2.png" alt="logout">
                                        </button>
                                        <p class="button-text-logout" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
<!-- Logout Modal-->
<div class="modal side-panel fade" id="logoutModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" style="height: 149px !important; width:250px !important;"> 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Ready to Leave?</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2 text-center">
                                        Select "Logout" below if you are ready to end your current session.
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a type="button" class="btn btn-light add-btn ms-2" href="<?php echo e(route('logout')); ?>">
							<i class="ico icon-outline-logout-2 text-success"></i> Logout
                        </a>
					</div>
              	</div>
            </div>
        </div>
<!-- Logout Modal-->
<?php $__env->startSection('mainContent'); ?>
    <?php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
  

    <div class="content-container col-9 page-chart-of-accounts">

         <div class="smart_search_wrapper">
                <div id="smart_search_list"></div>
            </div>
           
        <h4 style="position: fixed; margin-top: 7px;">Chart of Accounts</h4>
      
        <div class="purchase-order-content-header-right" style="margin-top:-14px">

           
            <input type="text" class="form-control w-25 rounded" id="smart_search" name="smart_search"
                placeholder="Search..." autocomplete="off" />
             
            <script>
                $(document).ready(function() {
                    let searchTimeout;

                    $("#smart_search").on("keyup", function() {
                        let query = $(this).val().trim();

                        // Clear previous timeout for better performance
                        clearTimeout(searchTimeout);

                        if (query.length > 2) { // Reduced from 3 to 2 for better UX
                            // Add delay to avoid too many requests
                            searchTimeout = setTimeout(function() {
                                $.ajax({
                                    url: "<?php echo e(route('chartofaccounts.search')); ?>",
                                    method: "GET",
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        console.log(data)
                                        $("#smart_search_list").html(data).show();
                                    },
                                    error: function() {
                                        $("#smart_search_list").html(
                                            '<div class="alert alert-danger">Search failed</div>'
                                        ).show();
                                    }
                                });
                            }, 300); // 300ms delay
                        } else {
                            $("#smart_search_list").hide();
                        }
                    });

                    $(document).on("click", function(e) {
                        if (!$(e.target).closest("#smart_search, #smart_search_list").length) {
                            $("#smart_search_list").hide();
                        }
                    });

                });
            </script>
            <style>
                .smart_search_wrapper {
                    position: relative;
                    display: block;
                    width: 100%;
                }
                #smart_search_list {
                    display: none;
                    position: absolute;
                    top: 100%;
                    left: 40px;
                    right: 0;
                    width: 95%;
                    max-height: 350px;
                    overflow-y: auto;
                    background: #fff;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    z-index: 9999;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    margin-top: 51px;
                }
            </style>


            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-add-square text-success"></i> Add
                </button>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#groupModal"><i
                                class="ico title-15 icon-outline-add-square me-2 text-success"></i> Group</a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#subgroupModal"><i
                                class="ico title-15 icon-outline-add-square me-2 text-success"></i> Sub
                            Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#accountModal"><i
                                class="ico title-15 icon-outline-add-square me-2 text-success"></i> Account</a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#accountSubModal"><i
                                class="ico title-15 icon-outline-add-square me-2 text-success"></i> Sub
                            Account</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#accountSubEmployeeModal"><i
                                class="ico title-15 icon-outline-add-square me-2 text-success"></i> Employee
                            Account</a>
                    </li>
                </ul>
            </div>

 



            <?php echo $__env->make('backEnd.accounts.accountgroupsubadd_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('backEnd.accounts.accountgroupsub2add_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('backEnd.chart-of-accounts.accountadd_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('backEnd.chart-of-accounts.accountsubadd_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('backEnd.chart-of-accounts.accountsubemployeeadd_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>




            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-document-text text-success"></i> List
                </button>
                <ul class="dropdown-menu">


                    <li><a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('accountgroupsub-add')); ?>"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('accountgroupsub2-add')); ?>"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('chartofaccounts-add')); ?>"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Account</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('chartofaccounts-add-sub')); ?>"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Account</a></li>
                    <li><a class="dropdown-item d-flex align-items-center"
                            href="<?php echo e(url('chartofaccounts-opening-balance')); ?>"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Opening Balance</a>
                    </li>



                </ul>
            </div>
            <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27): ?>
                <?php echo $__env->make('backEnd.chart-of-accounts.accountmerge_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('backEnd.chart-of-accounts.accountsubmerge_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>

            <div class="dropdown" id="custom-dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">


                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#collapseMerge" data-bs-toggle="collapse"
                            aria-expanded="false" onclick="event.stopPropagation();">
                            <span class="text-muted"><i class="ico icon-outline-link-square title-15 me-2"></i> Merge</span>

                        </a>
                    </li>
                    <li>
                        <div class="collapse" id="collapseMerge">
                            <ul class="list-unstyled  mb-0">
                                <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27): ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                            data-bs-target="#ModalMergeAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-link-square title-15 me-2"></i> Account Merge</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                            data-bs-target="#ModalMergeSubAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-link-square title-15 me-2"></i> Sub Account
                                            Merge</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#collapseMove" data-bs-toggle="collapse"
                            aria-expanded="false" onclick="event.stopPropagation();">
                            <span class="text-muted"><i class="ico icon-outline-move-to-folder title-15 me-2"></i>
                                Move</span>

                        </a>
                    </li>
                    <li>
                        <div class="collapse" id="collapseMove">
                            <ul class="list-unstyled  mb-0">
                                <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27): ?>
                                    <li><a class="dropdown-item d-flex align-items-center"
                                            href="#" data-bs-toggle="modal"
                                            data-bs-target="#ModalMoveAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-move-to-folder title-15 me-2"></i> Account Move</a>
                                    </li>
                                    <li><a class="dropdown-item d-flex align-items-center"
                                            href="#"  data-bs-toggle="modal"
                                            data-bs-target="#ModalMoveSubAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-move-to-folder title-15 me-2"></i> Sub Account
                                            Move</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>





                </ul>
            </div>
        </div>

        <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27): ?>
                <?php echo $__env->make('backEnd.chart-of-accounts.account_move_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('backEnd.chart-of-accounts.subaccount_move_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php endif; ?>


        <style>
            /* Increase width of all dropdown menus */
            #custom-dropdown .dropdown-menu {
                min-width: 180px;
                /* default minimum width */
                width: auto;
                /* adjust width automatically based on content */
                max-width: 400px;
                /* optional maximum width */
            }

            /* Optional: prevent text from wrapping */
            #custom-dropdown .dropdown-item {
                white-space: nowrap;
            }
        </style>

        <style>
            .list-group-item {
                cursor: pointer;
                padding: 6px 10px;
                transition: background-color 0.15s ease-in-out;
                /* Smooth transitions */
            }

            .list-group-item:hover {
                background: #f8f9fa;
            }

            h6 {
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .inactive {
                background: #d9d9d9;
            }

            .active-li {
                background-color: #49925826 !important;
            }

            .list-group-item:focus,
            .list-group-item:active {
                background-color: inherit !important;
                box-shadow: none !important;
            }

            /* Performance optimizations */
            .tab-content {
                will-change: transform;
                /* Optimize for animations */
            }

            .tab-pane {
                will-change: opacity;
                /* Optimize fade transitions */
            }

            /* Optimize text truncation */
            .truncate-text-custom {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
            }
        </style>


        <script>
            function selectItem(element, cssSelector) {
                document.querySelectorAll(cssSelector).forEach(li => {
                    li.classList.remove("active-li");
                });
                void element.offsetWidth;
                element.classList.add("active-li");
            }
        </script>

        <div class="row">

            
            <div class="col-2 border-end">
                <h6 class="px-2 py-1 border-bottom text-center">Heads
                </h6>
                <ul class="list-group">
                    <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item heads d-flex justify-content-between align-items-center"
                            onclick="selectItem(this,'.heads');  showLayer(2, 'group<?php echo e($g->id); ?>')">
                            <span><?php echo e($g->title); ?></span>
                            <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            
            <div class="col-2 border-end tab-content">
                <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $subs = App\SysAccountGroupSub::where('group_id', $g->id)->where('status', 1)->get();
                    ?>
                    <div class="tab-pane fade" id="group<?php echo e($g->id); ?>">



                        <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                            <h6 class="mb-0 text-center flex-grow-1">
                                Groups
                                

        
                    <i class="ico icon-outline-add-square text-success"     data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Create Group"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#groupModal"></i> 
               
                            </h6>
                            <!-- Compact button to open modal -->
                            <button type="button" class="btn btn-sm brn-light" data-bs-target="#GroupTableModal"
                            data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View Groups"
                            data-bs-placement="top"
                                data-bs-toggle="modal">
                                <i class="ico icon-outline-document-text title-15"></i>
                            </button>
                        </div>

                        <ul class="list-group">
                            <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item groups d-flex justify-content-between align-items-center"
                                    onclick="selectItem(this,'.groups'); showLayer(3, 'sub<?php echo e($s->id); ?>')">
                                    <span class="truncate-text-custom"><?php echo e($s->title); ?></span>
                                    <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="col-2 border-end tab-content">
                <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $subs = App\SysAccountGroupSub::where('group_id', $g->id)->where('status',1)->get(); ?>
                    <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); ?>
                        <div class="tab-pane fade" id="sub<?php echo e($s->id); ?>">
                            <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                <h6 class="mb-0 text-center flex-grow-1">
                                    Sub Groups 
                    <i class="ico icon-outline-add-square text-success"     data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Create Sub Group"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#subgroupModal"></i> 

                                </h6>
                                <!-- Compact button to open modal -->
                                <button type="button" class="btn btn-sm brn-light" data-bs-target="#SubGroupTableModal"
                                  data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View Sub Groups"
                            data-bs-placement="top"
                                    data-bs-toggle="modal">
                                    <i class="ico icon-outline-document-text title-15"></i>
                                </button>
                            </div>
                            <ul class="list-group">
                                <?php $__currentLoopData = $subs2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item subgroups d-flex justify-content-between align-items-center"
                                        onclick="selectItem(this,'.subgroups'); showLayer(4, 'sub2<?php echo e($s2->id); ?>')">
                                        <span class="truncate-text-custom"><?php echo e($s2->title); ?></span>
                                        <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="col-3 border-end tab-content">
                <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $subs = App\SysAccountGroupSub::where('group_id',$g->id)->where('status',1)->get(); ?>
                    <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); ?>
                        <?php $__currentLoopData = $subs2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $account = App\SysChartofAccounts::where([
                                    'subgroup2' => $s2->id,
                                    'main_account_id' => 0,
                                ])
                                    ->whereRaw("find_in_set($com_id,company_access)")
                                    ->orderby('account_name', 'asc')
                                    ->get();
                            ?>
                            <div class="tab-pane fade" id="sub2<?php echo e($s2->id); ?>">
                                <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                    <h6 class="mb-0 text-center flex-grow-1">
                                        Accounts 
                    <i class="ico icon-outline-add-square text-success"     data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Create Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountModal"></i> 

                                    </h6>
                                    <!-- Compact button to open modal -->
                                    <button type="button" class="btn btn-sm accountsmodalbtn"
                                      data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View Accounts"
                            data-bs-placement="top">
                                        <i class="ico icon-outline-document-text title-15"></i>
                                    </button>
                                </div>
                                <ul class="list-group">
                                    <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item accounts d-flex justify-content-between align-items-center <?php if($a->status != 1): ?> inactive <?php endif; ?>"
                                            onclick="selectItem(this,'.accounts'); showLayer(5, 'acc<?php echo e($a->id); ?>')">
                                            <span class="truncate-text-custom"> 
                                               <?php if(\Illuminate\Support\Str::startsWith($a->account_code, 'CUS')): ?>
                                                    <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                                        <?php echo e($a->account_code); ?> 
                                                    <?php endif; ?>
                                                   <?php echo e($a->account_name); ?>

                                                <?php elseif(\Illuminate\Support\Str::startsWith($a->account_code, 'SUP')): ?>
                                                        <?php if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                                        <?php echo e($a->account_code); ?> 
                                                        <?php endif; ?>    
                                                        <?php echo e($a->account_name); ?>

                                                <?php elseif(\Illuminate\Support\Str::startsWith($a->account_code, 'ACC')): ?>
                                                        <?php if(@App\SysHelper::getCompanyCodeSettings()['is_account_code']): ?>
                                                        <?php echo e($a->account_code); ?> 
                                                        <?php endif; ?>    
                                                        <?php echo e($a->account_name); ?>

                                                <?php elseif(\Illuminate\Support\Str::startsWith($a->account_code, 'ACC')): ?>
                                                        <?php if(@App\SysHelper::getCompanyCodeSettings()['is_subaccount_code']): ?>
                                                        <?php echo e($a->account_code); ?> 
                                                        <?php endif; ?>    
                                                        <?php echo e($a->account_name); ?>

                                                <?php else: ?>
                                                <?php echo e($a->account_code); ?>     <?php echo e($a->account_name); ?>

                                               <?php endif; ?>
                                                </span>
                                            <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($account->isEmpty()): ?>
                                        <li class="list-group-item text-muted">No Accounts</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="col-3 tab-content">
                <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $subs = App\SysAccountGroupSub::where('group_id',$g->id)->where('status',1)->get(); ?>
                    <?php $__currentLoopData = $subs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); ?>
                        <?php $__currentLoopData = $subs2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $accounts = App\SysChartofAccounts::where([
                                    'subgroup2' => $s2->id,
                                    'main_account_id' => 0,
                                ])
                                    ->whereRaw("find_in_set($com_id,company_access)")
                                    ->orderby('account_name', 'asc')
                                    ->get();
                            ?>
                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $subacc = $account_sub->where('main_account_id',$a->id); ?>
                                <div class="tab-pane fade" id="acc<?php echo e($a->id); ?>">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                        <h6 class="mb-0 text-center flex-grow-1">
                                            Sub Accounts 
                    <i class="ico icon-outline-add-square text-success"     data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Create Sub Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountSubModal"></i> 

                                        </h6>
                                        <!-- Compact button to open modal -->
                                        <button type="button" class="btn btn-sm brn-light"
                                         data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View Sub Accounts"
                            data-bs-placement="top"
                                            data-bs-target="#SubAccountTableModal" data-bs-toggle="modal">
                                            <i class="ico icon-outline-document-text title-15"></i>
                                        </button>
                                    </div>
                                    <ul class="list-group">
                                        <?php $__currentLoopData = $subacc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center <?php if($sa->status != 1): ?> inactive <?php endif; ?>">
                                                <span class="truncate-text-custom"><?php echo e($sa->account_code); ?> -
                                                    <?php echo e($sa->account_name); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($subacc->isEmpty()): ?>
                                            <li class="list-group-item text-muted">No Sub Accounts</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>









        <div id="AccountTableContent" class=" p-4">

        </div>


        



    </div>

    <script>
        function showLayer(level, id) {
            // Hide all tab-panes in this level
            document.querySelectorAll(`.tab-content:nth-of-type(${level}) .tab-pane`)
                .forEach(el => el.classList.remove('show', 'active'));

            // Also hide all deeper levels
            for (let i = level + 1; i <= 5; i++) {
                document.querySelectorAll(`.tab-content:nth-of-type(${i}) .tab-pane`)
                    .forEach(el => el.classList.remove('show', 'active'));
            }

            // Show selected tab-pane
            let target = document.getElementById(id);
            if (target) {
                target.classList.add('show', 'active');
            }
        }
    </script>




    </div>







    



    <section class="admin-visitor-area mr-2 ml-2">
        <div class="container-fluid p-0">

            <div class="row">

                <div class="col-lg-12">


                    <div class="row">

                        <div class="col-lg-12">
                            <?php if(session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != ''): ?>
                                <tr>
                                    <td colspan="6">
                                        <?php if(session()->has('message-success-delete')): ?>
                                            <div class="alert alert-success">
                                                <?php echo e(session()->get('message-success-delete')); ?>

                                            </div>
                                        <?php elseif(session()->has('message-danger-delete')): ?>
                                            <div class="alert alert-danger">
                                                <?php echo e(session()->get('message-danger-delete')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            

                            <?php if(isset($accountgroup) && 1 == 2): ?>
                                <?php $a = 1; ?>
                                <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="background-color: #000000 !important;">
                                        <td class=" text-white"><span class="ti-arrow-right"></span>&nbsp;&nbsp;&nbsp;<b>
                                                <a class="text-white" data-toggle="collapse"
                                                    href="#collapseExample1-<?php echo e($a); ?>"><?php echo e(@$value->title); ?></a></b>
                                        </td>
                                    </tr>
                                    <?php
                                        $accountgroupsub = @App\SysAccountGroupSub::where('group_id', @$value->id)
                                            ->where('status', 1)
                                            ->get();
                                    ?>
                                    <?php if(isset($accountgroupsub)): ?>
                                        <?php $b = 1; ?>
                                        <?php $__currentLoopData = $accountgroupsub; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="collapse show" id="collapseExample1-<?php echo e($a); ?>">
                                                <td>&nbsp;&nbsp;&nbsp;<span
                                                        class="ti-arrow-right"></span>&nbsp;&nbsp;&nbsp;<b>
                                                        <?php echo e(@$value2->title); ?></b></td>
                                            </tr>

                                            <?php
                                                $accountgroupsub2 = @App\SysAccountGroupSub2::where(
                                                    'sub_id',
                                                    @$value2->id,
                                                )
                                                    ->where('status', 1)
                                                    ->get();
                                            ?>
                                            <?php if(isset($accountgroupsub2)): ?>
                                                <?php $i = 1; ?>
                                                <?php $__currentLoopData = $accountgroupsub2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value4): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><a data-toggle="collapse"
                                                                href="#collapseExample2-<?php echo e($a); ?>-<?php echo e($b); ?>">
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo e(@$value4->title); ?></b></a>
                                                        </td>
                                                    </tr>
                                                    <?php $account = @App\SysChartofAccounts::where(['subgroup2' => @$value4->id,'status' => 1])->get() ?>
                                                    <?php if(isset($account)): ?>
                                                        <?php $i = 1; ?>
                                                        <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr
                                                                id="collapseExample2-<?php echo e($a); ?>-<?php echo e($b); ?>">
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <?php echo e(@$value3->account_code); ?>&nbsp;-&nbsp;<?php echo e(@$value3->account_name); ?>

                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <tr class="collapse"
                                                            id="collapseExample2-<?php echo e($a); ?>-<?php echo e($b); ?>">
                                                            <td></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <?php $account = @App\SysChartofAccounts::where(['subgroup2' => @$value4->id,'status' => 1])->get() ?>
                                                <?php if(isset($account)): ?>
                                                    <?php $i = 1; ?>
                                                    <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr class="collapse"
                                                            id="collapseExample2-<?php echo e($a); ?>-<?php echo e($b); ?>">
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <?php echo e(@$value3->account_code); ?>&nbsp;-&nbsp;<?php echo e(@$value3->account_name); ?>

                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <tr class="collapse"
                                                        id="collapseExample2-<?php echo e($a); ?>-<?php echo e($b); ?>">
                                                        <td></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php $b++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php $a++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>



                            <style>
                                .mb-0>a {
                                    display: block;
                                    color: #000000;
                                    position: relative;
                                }

                                .mb-0>a:after {
                                    content: "\f078";
                                    /* fa-chevron-down */
                                    font-family: 'FontAwesome';
                                    position: absolute;
                                    right: 0;
                                }

                                .mb-0>a[aria-expanded="true"]:after {
                                    content: "\f077";
                                    /* fa-chevron-up */
                                }

                                .card-header {
                                    padding: 7px 10px 5px 10px;
                                }

                                .card-header h5 {
                                    font-size: 12px;
                                }

                                .card-body {
                                    padding: 5px 10px;
                                }

                                .card {
                                    background-color: #e5e5de;
                                    box-shadow: none;
                                    border-radius: 5px;
                                    margin-bottom: 5px;
                                }

                                .level4 {
                                    padding-left: 10px;
                                    font-weight: normal;
                                    color: #000000
                                }
                            </style>


                            
                            
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>









    <script>
        $(document).ready(function() {

            $('.accountsmodalbtn').on('click', function() {
                console.log("clicked")


                // Load content via AJAX
                $.ajax({
                    url: '/load-account-modal-data',
                    method: 'GET',
                    beforeSend: function() {
                        $("#loading_bg").show();

                    },
                    success: function(response) {
                        $('#AccountTableContent').html(response);
                        // Show the modal first
                        $('#AccountTableModal').modal('show');
                        $("#loading_bg").hide();

                    },
                    error: function(xhr, status, error) {
                        $('#AccountTableContent').html(
                            '<div class="alert alert-danger">Failed to load data. Please try again later.</div>'
                        );
                        $("#loading_bg").hide();

                    }
                });
            });

        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
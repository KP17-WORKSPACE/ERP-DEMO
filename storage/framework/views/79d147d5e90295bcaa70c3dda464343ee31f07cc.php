
<?php try { ?>
   
    <style>
        .head {font-size: 14px;}
        .card h2{font-size: 14px;}
        .card h4{font-size: 14px;}
        .card h5{font-size: 14px;}
        .card h6{font-size: 12px;}
        .card p{font-size: 11px;}
        .card span{font-size: 11px;}
        .card b{font-size: 11px;}
        .modal-body h4{font-size: 17px;}
        .table th, .table td { padding: 1px; font-size: 12px; }
    </style>

    <style>
            #data-details label {
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #data-details .green-heading{
              
                text-align: center !important;
               
            }
             #data-details .green-heading p{
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #data-details .form-control-plaintext {
                text-align: center !important;
            }
        </style>


                            
                            <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
                                <h4 class="purchase-order-content-header-left">
                                   <a href="<?php echo e(url('crm-deals/show/'.$del->id)); ?>" class="text-dark font-weight-600">  <?php echo e($del->deal_code->code); ?> </a>
                                    <span class="badge bg-info"><?php echo App\SysHelper::deal_type_new($del->isproject); ?></span>
                                </h4>
                                <div class="purchase-order-content-header-right">
                                    
                                    <a href="<?php echo e(url('crm-deals/'.$del->id)); ?>"  class="btn btn-light text-dark">
                                       <i class="ico icon-outline-document-text text-success"></i>  View
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ico icon-outline-hamburger-menu"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                         
                                        </ul>
                                    </div> 
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                                        <div class="row">

                                            <div class="col-3 mb-2">
                                                <label class="form-label">Customer Name </label>

                                                <?php

                                                $deliveryCompany = strtolower(str_replace(' ', '', $del->delivery_company));
                                                $customerName = strtolower(str_replace(' ', '', $del->customername->name));

                                                ?>

                                          
                                                <?php if($deliveryCompany == $customerName): ?>
                                              <div class="form-control-plaintext truncate-text-custom"><a href="<?php echo e(url('customers')); ?>/<?php echo e(@$del->customername->id); ?>" target="_blank"><?php echo e($del->customername->name); ?></a> </div>

                                                <?php else: ?>
                                              <div class="form-control-plaintext truncate-text-custom"><a class=" text-warning" href="<?php echo e(url('customers')); ?>/<?php echo e(@$del->customername->id); ?>" target="_blank"><?php echo e($del->customername->name); ?></a> </div>

                                                <?php endif; ?>

                                            </div>

                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Deal Name </label>
                                              <div class="form-control-plaintext truncate-text-custom"><?php echo e($del->deal_name); ?> </div>
                                            </div>
                                   
                                           
                                            <div class="col-1 mb-2" style="width: 15%">
                                                <label class="form-label">Brand:<br /></label>
                                                <div class="form-control-plaintext truncate-text-custom">

                                                <?php if($del->tags != ""): ?>
                                                <?php $myArray = explode(',', $del->tags); ?>
                                                <?php $__currentLoopData = $myArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo e($item); ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php else: ?>
                                                --
                                                <?php endif; ?>
                                                </div> 
                                            </div>
                                        
                                            
                                          
                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Deal Value</label> 
                                                     <div class="form-control-plaintext truncate-text-custom">
                                                    <?php echo e(App\SysHelper::currancy_format_deal($del->deal_value,$del->company_id)); ?> <?php echo e($del->dealcurrency->code); ?>

                                                </div>
                                            </div>

                                            <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 12 || Auth::user()->role_id == 8): ?>

                                              <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Profit</label>  
                                                     <div class="form-control-plaintext truncate-text-custom">

                                                    <?php echo e(App\SysHelper::currancy_format_deal(($del->deal_profit),$del->company_id)); ?> <?php echo e($del->dealcurrency->code); ?> 
                                                    <?php
                    $dealvalue = $del->deal_value;
                    $dealprofit = $del->deal_profit;
                    if($dealprofit!=0 && $dealvalue != 0){ $dealpercentage = $dealprofit / $dealvalue * 100; }
                    else{ $dealpercentage=0; }
                    ?>

                                                    <span class="text-success"><?php echo e(@App\SysHelper::com_curr_format($dealpercentage,2,'.',',')); ?>%</span></div>
                                            </div>
                                                
                                            <?php endif; ?>
                                          

                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Sales Person</label>
                                                     <div class="form-control-plaintext truncate-text-custom">

                                                    <?php echo e(@$del->ownername->first_name); ?> <?php echo e(@$del->ownername->middle_name); ?> <?php echo e(@$del->ownername->last_name); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#customer-info" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Customer Info</button>
                                    </li>
                                     <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#delivery-location" type="button" role="tab" aria-controls="vat-details" aria-selected="false">Delivery Location /Address</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#sales-person-info" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Sales Person Info</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#submited-details" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Submited Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#internal-note" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Internal Note</button>
                                    </li>
                                    
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade " id="customer-info" role="tabpanel" aria-labelledby="extra-fields-tab">

                                        

                                        
                                           <div class="row text-start">

                                                <?php if(App\SysHelper::get_company_status($del->customername) == 0): ?>
                                                    <a class="btn-sm btn-light" style="float: right" target="_blank" href="<?php echo e(url('customers/' . $del->customername->id) . '?customer_action=edit'); ?>">Update Info</a>
                                                    <?php else: ?>
                                                
                                                <?php endif; ?>


                                                <!-- Sales Person -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Customer Name	</p>
                                                    <?php echo e($del->customername->name); ?>

                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Contact Person	</p>
                                               <?php echo e($del->cust_name); ?>

                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Designation</p>
                                                    <?php echo e($del->designation); ?>

                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Mobile</p>
                                                    <?php echo e($del->cust_no); ?>

                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                <?php echo e($del->cust_email); ?>

                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 1	</p>
                                                    <?php echo e(@$del->customername->addresses->first()->address); ?>

                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 2	</p>
                                                    <?php echo e(@$del->customername->addresses->first()->address2); ?>, <?php echo e(@$del->customername->addresses->first()->city); ?>

                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">State & Country	</p>
                                                   <?php echo e(@$del->customername->addresses->first()->statename->name); ?>, <?php echo e(@$del->customername->addresses->first()->countryname->name); ?>

                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">PO Box	</p>
                                                   <?php echo e(@$del->customername->addresses->first()->zip_code); ?>

                                                </div>

                                            </div>

                                        
                                    </div>

                                     <div class="tab-pane fade" id="delivery-location" role="tabpanel" aria-labelledby="vat-details-tab">


                                         <div class="row text-start">

                                                <!-- Company -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Company</p>
                                                   <?php echo e(@$del->delivery_company); ?>

                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Contact Person</p>
                                                <?php echo e(@$del->delivery_name); ?>

                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Telephone</p>
                                                    <?php echo e(@$del->delivery_number); ?>

                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                    <?php echo e(@$del->delivery_email); ?>

                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-3 col-lg-6 col-md-6 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Address</p>
                                               <?php echo e(@$del->address); ?>

                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 2</p>
                                                   <?php echo e(@$del->delivery_address2); ?>, <?php echo e(@$del->delivery_city); ?>

                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">State & Country</p>
                                                    <?php echo e(@$del->state->name); ?>, <?php echo e(@$del->country->name); ?>

                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">PO Box</p>
                                                    <?php echo e(@$del->delivery_zip_code); ?>

                                                </div>

                                              

                                            </div>

                                        
                                    </div>

                                    <div class="tab-pane fade" id="sales-person-info" role="tabpanel" aria-labelledby="shipping-details-info-tab">


                                           <div class="row text-start">

                                                <!-- Sales Person -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Sales Person</p>
                                                    <?php echo e(@$del->ownername->first_name); ?> <?php echo e(@$del->ownername->middle_name); ?> <?php echo e(@$del->ownername->last_name); ?>

                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Mobile</p>
                                                <?php echo e(@$del->ownername->mobile); ?>

                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                    <?php echo e(@$del->ownername->email); ?>

                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Ext No</p>
                                                    <?php echo e(@$del->ownername->ext_no ?? '--'); ?>

                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Source</p>
                                                <?php if(@$del->source != ''): ?><?php echo e(@$del->source); ?><?php if(@$del->source_o != ''): ?> - <?php echo e(@$del->source_o); ?> <?php endif; ?> <?php endif; ?>
                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Close Date</p>
                                                    <?php echo e(date('m/d/Y', strtotime(@$del->estimated_close_date))); ?>

                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Added By</p>
                                                    <?php echo e(@$deal->createdby->full_name); ?>

                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Added On</p>
                                                    <?php echo e(date('d/m/Y h:i A', strtotime(@$del->created_at))); ?>

                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Updated On</p>
                                                    <?php echo e(date('d/m/Y h:i A', strtotime(@$del->updated_at))); ?>

                                                </div>

                                            </div>

                                        
                                    </div>
                                   
                                    <div class="tab-pane fade show active" id="submited-details" role="tabpanel" aria-labelledby="vat-details-tab">


                                            <div class="row text-start">

                                                <!-- Sales Person -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Expected Delivery</p>
                                                   <?php echo e(date('d/m/Y', strtotime(@$deal->delivery_date))); ?>

                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Payment Terms</p>
                                           <span class="truncate-text-custom"><?php echo e(@$deal->paymentterms->title); ?> <?php if(@$deal->payment_terms == 22): ?> - <?php echo e(@$deal->payment_terms_txt); ?> <?php endif; ?></span>    
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Payment mode</p>
                                                   <?php if($deal->payment_mode==1): ?> Cash <?php endif; ?>
                        <?php if($deal->payment_mode==2): ?> Cheque <?php endif; ?>
                        <?php if($deal->payment_mode==3): ?> Bank Transfer <?php endif; ?>
                        <?php if($deal->payment_mode==4): ?> Open Credit <?php endif; ?>
                        <?php if($deal->payment_mode==5): ?> Credit Card <?php endif; ?>
                        <?php if($deal->payment_mode==6): ?> Bank TT <?php endif; ?>
                        <?php if($deal->payment_mode==7): ?> Letter of Credit <?php endif; ?>

                        <?php if($deal->payment_mode_sec==1): ?> , Cash <?php endif; ?>
                        <?php if($deal->payment_mode_sec==2): ?> , Cheque <?php endif; ?>
                        <?php if($deal->payment_mode_sec==3): ?> , Bank Transfer <?php endif; ?>
                        <?php if($deal->payment_mode_sec==4): ?> , Open Credit <?php endif; ?>
                        <?php if($deal->payment_mode_sec==5): ?> , Credit Card <?php endif; ?>
                        <?php if($deal->payment_mode_sec==6): ?> , Bank TT <?php endif; ?>
                        <?php if($deal->payment_mode_sec==7): ?> , Letter of Credit <?php endif; ?>
                                                </div>

                                           

                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Product Purchase</p>
     <!-- Ext No -->
                                             <?php if($deal->purchease_required==1): ?>
                                                <span class="">
                                                    Purchase Required
                                                    <?php try { ?>
                                                        <?php if($purchease[0]->validation == 3): ?> <span class="text-muted"><span
                                                                class="text-success text-bold text-xs">(Under Purchase)</span></span> <?php endif; ?>
                                                    <?php } catch (\Throwable $th) {
                                                    } ?>
                                                    <?php if(session('logged_session_data.designation_id')==20): ?>
                                                    <script type="text/javascript">
                                                        var blink = document.getElementById('blink');
                                                        setInterval(function () {
                                                            blink.style.opacity = (blink.style.opacity == 0 ? 1 : 0);
                                                        }, 500);
                                                    </script>
                                                    <?php endif; ?>
                                                </span>
                                                <?php endif; ?>
                                                </div>
                                                

                                                
                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Partial Delivery</p>
                                                    <?php if($deal->partial_delivery==1): ?>
                                                Partial Delivery
                                                <?php endif; ?>
                                                </div>
                                                

                                                <!-- Close Date -->
                                               
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Professional Service</p>
                                                     <?php if($deal->technical==1 || $deal->technical==0): ?>

                                                      <?php if($deal->technical==0): ?> NO <?php endif; ?>
                                                     <?php if($deal->technical==1): ?> YES <?php endif; ?>
                                                        <?php endif; ?>
                                                </div>
                                             

                                        
                                                
                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Approval Not Required</p>
                                                            <?php if($deal->purchease_approval==0 || $deal->invoice_approval==0 || $deal->delivery_approval==0 ||
    $deal->receivables_approval==0): ?>
    <span class="truncate-text-custom">
 <?php if($deal->purchease_approval==0): ?> Purchase <?php endif; ?>
                                                    <?php if($deal->invoice_approval==0): ?> , Invoice <?php endif; ?>
                                                    <?php if($deal->delivery_approval==0): ?> , Delivery, <?php endif; ?>
                                                    <?php if($deal->receivables_approval==0): ?> Receivables <?php endif; ?>
    </span>
                                                   
                                                    <?php endif; ?>
                                                </div>
                                               

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">LPO</p>
                                                     <?php $file = explode("|", $deal->lpo); ?>
                        <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($f)): ?>
                        <a class="btn-sm btn-light text-dark"
                            href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($f); ?>" target="_blank"><i
                                class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i>
                            Download</a>
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Cheque//TT Copy</p>
                                                    <?php $file = explode("|", $deal->cheque_copy); ?>
                        <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($f)): ?>
                        <a class="btn-sm btn-light text-dark"
                            href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($f); ?>" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            Download</a>
                             <?php else: ?>
                        N/A
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>


                                                  <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Puchase Quote</p>
                                                    <?php $file = explode("|", $deal->purchease_quote); ?>
                        <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($f)): ?>
                        <a class="btn-sm btn-light text-dark"
                            href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($f); ?>" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            Download</a>
                             <?php else: ?>
                        N/A
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>

                                                    <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Quotation</p>
                                                  <a class="btn-sm btn-light text-dark"
            href="<?php echo e(url('crm-quote/'.$del->id.'/download/'.$del->quote_id)); ?>" target="_blank"><i
                class="ico text-success icon-bold-download-minimalistic fw-bold title-15 text-success"></i> Download</a>
                                                </div>

                                            </div>

                                     
                                        </div>
                                    
                                    <style>
                                        .comments-card span{
                                            font-size: 13px;
                                        }
                                        #scrollBox::-webkit-scrollbar {
                                            width: 3px;
                                        }

                                        #scrollBox::-webkit-scrollbar-thumb {
                                            border-radius: 10px;
                                        }
                                    </style>
                                    <div class="tab-pane fade" id="internal-note" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row">

                                             <div class="col-7">
                                           
                                                 <div id="scrollBox"  style="max-height: 400px; overflow-y: auto;">
                    

                                                        <?php if(isset($comments)): ?>
                                                        <div class="mt-3">
                                                            <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                                                                               <div class="card border-0 rounded-3 mb-3 comments-card">
                            <div class="card-body p-2">

                            

                                <!-- Top Row: Right-Aligned Icons -->
                                <div class="d-flex justify-content-between mb-1">


                        <!-- Comment -->
                                <p class="mb-2 fw-semibold <?php if($cmts->deleted_at): ?> text-decoration-line-through text-muted <?php endif; ?>" style="font-size:13px">
                                     <?php echo nl2br($cmts->comments); ?>

                                </p>


                                <div class="d-flex align-items-baseline gap-2">
                                        <?php if($cmts->commentsdoc): ?>
                                                        <a href="<?php echo e(asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc)); ?>"
                                                        target="_blank" class="btn btn-sm btn-light me-1">
                                                            <i class="ico icon-bold-paperclip" style="font-size:13px"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if($cmts->created_by == Auth::user()->id): ?>
                                                        <?php if($cmts->deleted_at): ?>
                                                            <a href="<?php echo e(url('crm-deals-comments-restore/' . $cmts->id)); ?>"
                                                            onclick="return confirm('Are you sure you want to restore this comment?')"
                                                            class="btn btn-sm btn-light">
                                                                <i class="ico icon-bold-restart" style="font-size:13px"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo e(url('crm-deals-comments-delete/' . $cmts->id)); ?>"
                                                            onclick="return confirm('Are you sure you want to delete this comment?')"
                                                            class="btn btn-sm btn-light">
                                                                <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:13px"></i>
                                                            </a>
                                                        <?php endif; ?>
                                            <?php endif; ?>
                                </div>


                                

                                </div>

                                <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                <div class="text-end small text-muted">

                                    <span>
                                        <i class="ico icon-bold-user me-1"></i>
                                        <?php echo e($cmts->createdby->first_name); ?> <?php echo e($cmts->createdby->last_name); ?>

                                    </span>

                                    <span>•</span>

                                    <span>
                                        <i class="ico icon-bold-clock me-1"></i>
                                        <?php echo e(date('d/m/Y h:i A', strtotime($cmts->created_at))); ?>

                                    </span>

                                    <?php if($cmts->deleted_at): ?>
                                    <span>•</span>
                                        
                                        <span class="text-danger">
                                            Deleted: <?php echo e(date('d/m/Y h:i A', strtotime($cmts->deleted_at))); ?>

                                        </span>
                                    <?php endif; ?>

                                </div>

                            </div>
</div>

                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>

                                                        <?php endif; ?>



                                                 </div>
                                                   
                                                </div>
                                             <div class="col-5">

                                             


                                               
                                                <label class="font-weight-bold form-label">Internal Note</label>
                                                <input type="hidden" value="dealtrack" name="page">
                                                 <textarea name="comments" class="form-control" id="comments" cols="10" rows="3" required></textarea>
                                               
                                                <input type="hidden" id="commentsid" name="commentsid" value="<?php echo e($deal->deal_id); ?>" />
                                                    <div class="row mt-2">
                                                        <div class="col-md-4 d-flex justify-content-start align-items-center">
                                                        <button type="button" id="submitComment"
                                                            class="btn btn-light d-inline-flex align-items-center gap-2">
                                                            <i class="ico icon-outline-add-square fs-5 text-success"></i>
                                                            <span>Add Note</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                                                    </div>

                                                    
                                                </div>
                                                

                                                </div>

                                                    <script>
                $(document).on('click', '#submitComment', function (e) {
                    e.preventDefault();

                    let formData = new FormData();
                    formData.append('comments', $('textarea[name="comments"]').val());
                    formData.append('commentsid', $('input[name="commentsid"]').val());

                    let fileInput = $('#commentsdoc')[0];
                    if (fileInput.files.length > 0) {
                        formData.append('commentsdoc', fileInput.files[0]);
                    }


                    $.ajax({
                        url: '<?php echo e(url('crm-deals-comments-add')); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        },
                        beforeSend: function () {
                           $("#loading_bg").css("display", "block");
                        },
                        success: function (response) {
                            console.log("response", response); // Debugging line to check response
                           
                           
                            $('textarea[name="comments"]').val('');
                            $('#commentsdoc').val('');
                            $("#loading_bg").css("display", "none");
                            location.reload();
                            // Optionally append new comment to comment list
                        },
                        error: function (xhr) {
                            $("#loading_bg").css("display", "none");
                            alert('Something went wrong: ' + xhr.responseText);
                        }
                    });
                });
            </script>

                                               

                                             <style>
                                                   .btn-fixed {
                                                        display: inline-block !important; /* optional if you still want inline behavior */
                                                        width: 116px;                     
                                                                     /* keep text centered */
                                                        white-space: nowrap;        
                                                        padding:0px 5px;      /* prevent wrapping */
                                                        }

                                                </style> 
                                            
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-update-quote-sort-order', 'method' => 'POST', 'id' => 'crm-update-quote-sort-order'])); ?>

                            
                            
                            <div class="table-container">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead>
                                        <tr>
                                            <th width="50px"  class="text-center">No</th>
                                            <th  width="160px" class="text-center ">Part No</th>
                                            <th width="170px"   class="text-center">Description</th>
                                            
                                            <th width="80px"   class=" text-nowrap text-center">Cost</th>
                                            <th width="50px"   class=" text-nowrap text-center">Qty</th>
                                            <th width="80px"  class=" text-nowrap text-center">Unit Price</th>
                                            <th width="80px"  class=" text-nowrap text-center">Value</th>
                                            <th width="80px"  class=" text-nowrap text-center">Discount</th>
                                            <th width="80px"   class=" text-nowrap text-center">Taxable</th>
                                            <th width="80px"   class=" text-nowrap text-center">VAT</th>
                                            <th width="120px"   class=" text-nowrap text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $t_qty = 0; $t_value=0; $t_deli=0; $t_discount=0; $t_taxableamount=0; $t_vatamount=0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; $t_cost=0;
                                $vat =$quoteitems->max('vat'); $deal_discount_sum_amount=0;?>
                                
                                
                                <tbody>
                                    <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $value = $Item->price * $Item->qty;
                                        $taxableamount = $value - $Item->discount;
                                        $vatamount = $taxableamount * $Item->vat / 100;
                                        $deli = App\SysHelper::get_deal_delivery_qty($Item->id);

                                        $t_cost += $Item->cost * $Item->qty;
                                        $t_deli += $deli;
                                        $t_qty += $Item->qty;
                                        $t_value += $value;
                                        $t_discount += $Item->discount;
                                        $t_taxableamount += $taxableamount;
                                        $t_vatamount += $vatamount;
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="text" name="sort_id[]" value="<?php echo e($Item->sort_id); ?>" class="text-center" style="width: 35px; border: none;">
                                            <input type="hidden" class="form-control" name="item_id[]" value="<?php echo e($Item->id); ?>">
                                        </td>
                                        <td><?php try{ ?> <?php echo e($Item->productname->part_number); ?> <?php }catch (\Exception $e){} ?></td>
                                        <td><?php echo $Item->description; ?></td>
                                        
                                        <td class="text-end"><?php echo e($Item->cost); ?></td>
                                        <td class="text-center"><?php echo e($Item->qty); ?></td>
                                        <td class="text-end"><?php echo e(App\SysHelper::currancy_format($Item->price,$Item->currency_id)); ?></td>
                                        <td class="text-end"><?php echo e(App\SysHelper::currancy_format($value,$Item->currency_id)); ?></td>
                                        <td class="text-end"><?php echo e(App\SysHelper::currancy_format($Item->discount,$Item->currency_id)); ?></td>
                                        <td class="text-end"><?php echo e(App\SysHelper::currancy_format($taxableamount,$Item->currency_id)); ?></td>
                                        <td class="text-end"><?php echo e(App\SysHelper::currancy_format($vatamount,$Item->currency_id)); ?></td>
                                        <td class="text-end text-nowrap"><?php echo e(App\SysHelper::currancy_format(($taxableamount + $vatamount),$Item->currency_id)); ?></td>
                                    </tr>
                                    <?php $currency_id = $Item->currency_id; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    
                                        <tr>
                                            <td colspan="11">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                     <thead>
                                    <tr>
                                        <th> &nbsp;&nbsp;&nbsp; </th>
                                        <th></th>
                                        <th></th>
                                        
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_cost,$currency_id)); ?></th>
                                        <th class="text-center"><?php echo e($t_qty); ?></th>
                                        <th></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_value,$currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_discount,$currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_taxableamount,$currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_vatamount,$currency_id)); ?></th>
                                        <th class="text-end text-nowrap"><?php echo e($Item->currency->code); ?> <?php echo e(App\SysHelper::currancy_format($t_taxableamount+$t_vatamount,$currency_id)); ?></th>
                                    </tr>
                                    <?php if($del->deal_discount > 0): ?>
                                    <tr>
                                        <?php
                                        $deal_discount_taxable_amount = $del->deal_discount;
                                        $deal_discount_vat_amount = $del->deal_discount*($vat)/100;
                                        $deal_discount_sum_amount = $deal_discount_taxable_amount+$deal_discount_vat_amount;
                                        ?>
                                        <td colspan="7" class="text-end font-weight-600">Additional Discount</td>
                                        <td class="text-end font-weight-600"><?php echo e(App\SysHelper::currancy_format(($del->deal_discount), $currency_id)); ?></td>
                                        <td class="text-end font-weight-600"><?php echo e(App\SysHelper::currancy_format(($deal_discount_taxable_amount), $currency_id)); ?></td>
                                        <td class="text-end font-weight-600"><?php echo e(App\SysHelper::currancy_format(($deal_discount_vat_amount), $currency_id)); ?></td>
                                        <td class="text-end font-weight-600"><?php echo e($Item->currency->code); ?> <?php echo e(App\SysHelper::currancy_format(($deal_discount_sum_amount), $currency_id)); ?></td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                 
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_cost,$currency_id)); ?></th>
                                        <th class="text-center"><?php echo e($t_qty); ?></th>
                                        <th></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_value,$currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_discount+$del->deal_discount,$currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_taxableamount-$deal_discount_taxable_amount, $currency_id)); ?></th>
                                        <th class="text-end"><?php echo e(App\SysHelper::currancy_format($t_vatamount-$deal_discount_vat_amount, $currency_id)); ?></th>                              
                                        <th class="text-end"><?php echo e($Item->currency->code); ?> <?php echo e(App\SysHelper::currancy_format(($t_taxableamount+$t_vatamount-$deal_discount_sum_amount), $currency_id)); ?></th>
                                    </tr>
                                    <?php endif; ?> 
                                </thead>
                                <?php echo e(Form::close()); ?>



                                <tbody >
                                    <?php if(count($poitems)>0): ?>
                                    <?php $po_sum = 0; ?>
                                  <tr>
                                    <td style="height:20px"></td>
                                  </tr>
                                    <tr> 
                                        <td colspan="11"><b>Aditional Items (Purchase Order)</b></td>
                                    </tr>
                                    <?php $__currentLoopData = $poitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo e($Item->partno); ?></td>
                                        <td><?php echo e($Item->description); ?></td>
                                      
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($Item->unitprice,2,'.',',')); ?></td>
                                        <td class="text-center"><?php echo e($Item->qty); ?></td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <?php $po_sum += $Item->unitprice*$Item->qty; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        
                                        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($po_sum,2,'.',',')); ?></th>
                                        <th class="text-center"><?php echo e($poitems->sum('qty')); ?></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>

                                <tbody>
                                    <?php if(count($dnitems)>0): ?>
                                     <tr>
                                    <td style="height:20px"></td>
                                  </tr>
                                    <tr>
                                        <th colspan="12"><b>Aditional Items (Delivery Note)</b></th>
                                    </tr>
                                    <?php $__currentLoopData = $dnitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo e($Item->partno); ?></td>
                                        <td><?php echo e($Item->description); ?></td>
                                        
                                        <td class="text-end"><?php echo e($Item->taxableamount); ?></td>
                                        <td class="text-center"><?php echo e($Item->qty); ?></td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        
                                        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($dnitems->sum('taxableamount'),2,'.',',')); ?></th>
                                        <th class="text-center"><?php echo e($dnitems->sum('qty')); ?></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                                </table>
                            </div>
                            <div class="status-timeline mb-3" style="display: none;">
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Account Status</div>
                                        <div class="status-circle bg-success"></div>
                                    </div>
                                    <div class="status"><a class="badge bg-success" href="#" data-bs-toggle="modal" data-bs-target="#accountStatusModal">Approved</a>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>                                    
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Sales Status</div>
                                        <div class="status-circle bg-success"></div>
                                    </div>
                                    <div class="status"><a class="badge bg-success" href="#" data-bs-toggle="modal" data-bs-target="#salesStatusModal">Approved</a>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Purchase Status</div>
                                        <div class="status-circle bg-info"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-info">Not Applicable</div>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Invoice Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Delivery Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Recievable Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                            </div>

<?php if($del->stage!=6): ?>

                                    <div class="" id="allstatus" >
  <?php echo $__env->make('backEnd.crm.DealTrackApprovalStatus-Sales', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    </div>
<?php endif; ?>

                            



  <script>
    function toggle_tool_tip(id) {
        var element = $('#desc_' + id);
        var currentWhiteSpace = element.css('white-space');

        if (currentWhiteSpace === 'nowrap') {
            element.css('white-space', '');
        } else {
            element.css('white-space', 'nowrap');
        }
    }
  </script>

<?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>



    

    <style>
      .files input {
          outline: 2px dashed #92b0b3;
          outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear;
          padding: 20px 0px 60px 35%;
          text-align: center !important;
          margin: 0;
          width: 100% !important;
      }
      .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
       }
      .files{ position:relative}
      .files:after {  pointer-events: none;
          position: absolute;
          top: 60px;
          left: 0;
          width: 100%;
          right: 0;
          height: 30px;
          content: "";
          /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
          display: block;
          margin: 0 auto;
          background-size: 100%;
          background-repeat: no-repeat;
      }
      .color input{ background-color:#f1f1f1;}
      .files:before {
          position: absolute;
          bottom: 10px;
          left: 0;  pointer-events: none;
          width: 100%;
          right: 0;
          height: 30px;
          content: " or drag it here. ";
          display: block;
          margin: 0 auto;
          color: #2ea591;
          font-weight: 600;
          text-transform: capitalize;
          text-align: center;
      }
  </style>
  
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


$(document).on("change", "#deliver_by", function () {
  var deliver_by = $("#deliver_by").val();
  var driver = $("#driver").val();
  var action = "<?php echo e(URL::to('getdriverbyshipping')); ?>";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            deliver_by: deliver_by,
        },
        cache: false,
        success: function(dataResult) {
            //alert(dataResult);
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if(dataResult['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    $('#driver').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].driver_name;
                        var name = dataResult['data'][i].driver_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#driver").append(option);
                    }
                }
            }
          }
    });
});



    </script>

                                <script>
document.addEventListener("DOMContentLoaded", function () {

    // --- Restore last active tab ---
    let lastTab = localStorage.getItem("active-deliveryapproval-tab");
    if (lastTab) {
        let tabTrigger = document.querySelector('[data-bs-target="' + lastTab + '"]');
        if (tabTrigger) {
            let tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // --- Save tab when user changes it ---
    let tabButtons = document.querySelectorAll('#purchaseDetailsTabs button[data-bs-toggle="tab"]');

    tabButtons.forEach(btn => {
        btn.addEventListener("shown.bs.tab", function (e) {
            localStorage.setItem("active-deliveryapproval-tab", e.target.getAttribute("data-bs-target"));
        });
    });

});
</script>


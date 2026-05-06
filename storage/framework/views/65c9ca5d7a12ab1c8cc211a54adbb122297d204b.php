<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<style>
    .fixed-info-table {
        table-layout: fixed;
        /* Fix column widths */
        width: 100%;
    }

    .fixed-info-table th {
        width: 35%;
        /* Always 30% for label */
        white-space: nowrap;
        /* Prevent wrapping */
        text-align: left;
        font-weight: 500;
    }

    .fixed-info-table td {
        width: 65%;
        /* Always 70% for value */
        word-break: break-word;
        /* Wrap long text if needed */
    }
</style>

<?php try { ?>

<?php
    function showPicName($data)
    {
        $name = explode('/', $data);
        return $name[4];
    }
    function showJoiningLetter($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
    function showResume($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
    function showOtherDocument($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }

?>





<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        <?php if(isset($custDetails)): ?>
            <?php echo e(@$custDetails->code); ?>

        <?php endif; ?>
    </h4>
    <div class="purchase-order-content-header-right">


        
        
          
        <form method="GET" action="<?php echo e(url('customers/'.@$custDetails->id.'?customer_action=edit')); ?>">
            <button type="submit" name="customer_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>
        <form method="GET" action="<?php echo e(url('customers?customer_action=add')); ?>">
            <button type="submit" name="customer_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>
 <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




            <?php if($custDetails->supplier_id != null && $custDetails->supplier_id != ''): ?>
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('suppliers/' . $custDetails->supplier_id)); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        View Supplier
                    </a>
                </li>
                
            <?php else: ?>
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('suppliers?supplier_action=createsupplier&customer_id=' . $custDetails->id)); ?>">
                        <i class="ico icon-outline-add-square text-success  title-15 me-2"></i>
                        Create Supplier
                    </a>
                </li>
            <?php endif; ?>



            </ul>
        </div>

    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div
                class="font-weight-600 title-15 me-3 
            
             <?php if(@$custDetails->type == 1): ?> text-success <?php endif; ?>
             <?php if(@$custDetails->type == 2): ?> text-warning <?php endif; ?>
             <?php if(@$custDetails->type == 3): ?> text-danger <?php endif; ?>
             <?php if(@$custDetails->type == 4): ?> text-dark <?php endif; ?>
            ">
                <?php echo e(@$custDetails->customer_name_display); ?>

            </div>

            


            <?php if(@$custDetails->status == 2): ?>
                <span class="badge bg-danger">Inactive</span>
            <?php else: ?>
                <span class="badge bg-success">Active</span>
            <?php endif; ?>

        </div>
        <div class="row">
            <div class="col-2 mb-3">
                <label class="form-label">Customer Type:</label>
                <div class="form-control-plaintext">
                    <?php if(@$custDetails->account_type == 1): ?>
                        Reseller
                    <?php endif; ?>
                    <?php if(@$custDetails->account_type == 2): ?>
                        Enduser
                    <?php endif; ?>
                    <?php if(@$custDetails->account_type == 3): ?>
                        Ecommerce
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Contact Name:</label>
                <div class="form-control-plaintext truncate-text-custom "> <?php echo e(@$custDetails->customer_salutation); ?>

                    <?php echo e(@$custDetails->first_name); ?> <?php echo e(@$custDetails->last_name); ?>

                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Designation:</label>
                <div class="form-control-plaintext truncate-text-custom "><?php echo e(@$custDetails->designation); ?></div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Contact Number:</label>
                <div class="form-control-plaintext"> <?php echo e(str_replace(' ', '', @$custDetails->contcat_number)); ?></div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Mobile:</label>
                <div class="form-control-plaintext"><?php echo e(@$custDetails->mobile); ?></div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Mail: </label>
                <div class="form-control-plaintext truncate-text-custom "> <?php echo e(@$custDetails->email); ?></div>
            </div>

            <?php if(@$custDetails->website): ?>
                 <div class="col-2 mb-3">
                <label class="form-label">Website: </label>
                <div class="form-control-plaintext truncate-text-custom "> <a href="<?php echo e(@$custDetails->website); ?>" target="_blank"><?php echo e(@$custDetails->website); ?></a> </div>
            </div>
            <?php endif; ?>

            <?php if(@$custDetails->maps_location): ?>
                  <div class="col-2 mb-3">
                <label class="form-label">Location: </label>
                <div class="form-control-plaintext truncate-text-custom "> <a href="<?php echo e(@$custDetails->maps_location); ?>" target="_blank">View on Map</a> </div>
            </div>
            <?php endif; ?>



           

            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="" class="text-dark fw-normal">
                        <?php if(count($editAssign) > 0): ?>
                            <?php $__currentLoopData = $editAssign; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($e->full_name); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </a>
                </div>
            </div>

             <div class="col-2 mb-3">
                <label class="form-label">Created By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="#" class="text-dark fw-normal">
                        <?php echo e(@$custDetails->createdby->full_name); ?>  <?php echo e(optional($custDetails->created_at)->format('d/m/Y h:i A')); ?>

                    </a>
                </div>
            </div>

             <div class="col-2 mb-3">
                <label class="form-label">Updated By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="#" class="text-dark fw-normal">
                        <?php echo e(@$custDetails->updatedby->full_name); ?>  <?php echo e(optional($custDetails->updated_at)->format('d/m/Y h:i A')); ?>

                       
                    </a>
                </div>
            </div>

              


        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="deal-info-tab" data-bs-toggle="tab" data-bs-target="#deal-info"
                type="button" role="tab" aria-controls="deal-info" aria-selected="true">Address</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-person-info-tab" data-bs-toggle="tab" data-bs-target="#sales-person-info"
                type="button" role="tab" aria-controls="sales-person-info" aria-selected="false">Contact
                Person</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vat-info-tab" data-bs-toggle="tab" data-bs-target="#vat-info" type="button"
                role="tab" aria-controls="vat-info" aria-selected="false">VAT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-info-tab" data-bs-toggle="tab" data-bs-target="#payment-info"
                type="button" role="tab" aria-controls="payment-info" aria-selected="false">Payment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-info-tab" data-bs-toggle="tab" data-bs-target="#customer-info"
                type="button" role="tab" aria-controls="customer-info" aria-selected="false">Documents</button>
        </li>
        


    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active pt-0" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
            <div class="row">
                <?php
                    //sort billing first in customer
                    $custAddress = $custAddress->sortBy('is_shipping');
                ?>

                <?php if(count($custAddress) > 0): ?>
                    <?php $__currentLoopData = $custAddress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="col-3 mt-4">
                             <h4 class="mb-1 color-sub-head font-size-13 mb-2">

                              

                                <?php if($data->is_shipping == 0): ?>
                                    Billing Address
                                <?php elseif($data->is_shipping == 1): ?>
                                    Shipping Address
                                <?php else: ?>
                                    <div class="fw-bold" style="visibility: hidden;">Placeholder</div>
                                <?php endif; ?>
                            </h4>
                            <table class="detail-item-table-noborder table table-hover">
                                <thead>

                                     <tr>
                                        <td class="text-start" width="100px">Country</td>
                                        <td>:&nbsp;&nbsp;<?php echo e($data->countryname['name']); ?></td>
                                    </tr>

                                     <tr>
                                        <td class="text-start" width="100px">State</td>
                                        <td class="truncate-text-custom">:&nbsp;&nbsp;<?php echo e($data->statename['name']); ?></td>
                                    </tr>

                                      <tr>
                                        <td class="text-start" width="100px">City</td>
                                        <td>:&nbsp;&nbsp;<?php echo e($data->city); ?></td>
                                    </tr>
                                    
                                  
                                   
                                    
                                   
                                   

                                </thead>

                            </table>
                        </div>

                         <div class="col-3 mt-4 border-end">
                             
                            <table class="detail-item-table-noborder table table-hover">
                                <thead>

                                 
                                    <tr>
                                        <td class="text-start" width="100px">Area</td>
                                        <?php if($data->area == null || $data->area == ''): ?>
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;<?php echo e($custDetails->address2); ?></td>
                                        <?php else: ?>
                                           
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;<?php echo e($data->area); ?></td>

                                        <?php endif; ?>
                                        
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Building Name</td>
                                        <?php if($data->building_name == null || $data->building_name == ''): ?>
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;<?php echo e($custDetails->address); ?></td>
                                        <?php else: ?>
                                        <td class="truncate-text-custom">:&nbsp;&nbsp;<?php echo e($data->building_name); ?></td>

                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Flat/Office No</td>
                                        <td>:&nbsp;&nbsp;<?php echo e($data->flat_office_no); ?></td>
                                    </tr>
                                  
                                   
                                    
                                    <tr>
                                        <td class="text-start" width="100px">Post Box</td>
                                        <td>:&nbsp;&nbsp;<?php echo e($data->zip_code); ?></td>
                                    </tr>
                                   

                                </thead>

                            </table>
                        </div>
                       
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>


            </div>


        </div>
        <div class="tab-pane fade" id="sales-person-info" role="tabpanel" aria-labelledby="sales-person-info-tab">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>Salutation</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email Address</th>
                                <!-- <th>Work Phone</th> -->
                                <th>Mobile</th>
                                <th>Designation</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if(count($custContact) > 0): ?>
                                <?php $__currentLoopData = $custContact; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($data->salutation); ?></td>
                                        <td><?php echo e($data->first_name); ?></td>
                                        <td><?php echo e($data->last_name); ?></td>
                                        <td><?php echo e($data->email_address); ?></td>
                                        <!-- <td><?php echo e(str_replace(' ', '', $data->work_phone)); ?></td> -->
                                        <td><?php echo e(str_replace(' ', '', $data->mobile)); ?></td>
                                        <td><?php echo e($data->designation); ?></td>
                                        <td><?php echo e($data->department); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>



                        </tbody>

                    </table>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="vat-info" role="tabpanel" aria-labelledby="vat-info-tab">
            <div class="row">


                <div class="col-2 mb-3">
                    <label class="form-label">Vat Country:</label>
                    <div class="form-control-plaintext"> <?php echo e(@$custDetails->vatcountry->name); ?></div>
                </div>

                <?php if(isset($custDetails) && !empty(@$custDetails->vat_state)): ?>
                    <div class="col-2 mb-3">
                        <label class="form-label">VAT State</label>
                        <div class="form-control-plaintext">
                            <?php if(isset($custDetails)): ?>
                                <?php echo e(@$custDetails->vatstate->name); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-2 mb-3">
                    <label class="form-label">VAT Percentage: </label>
                    <div class="form-control-plaintext d-flex align-items-center gap-2">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->vat_percentage); ?>% <?php if($custDetails->vat_is_fixed == 1): ?>
                                <button class="btn btn-warning m-0 p-0">&nbsp;Fixed&nbsp;</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">VAT Number:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->vat_number); ?>

                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-2 mb-3">
                    <label class="form-label">Customer Type:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->customertype->title); ?>

                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-2 mb-3">
                    <label class="form-label">Sale Type:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->saletype->title); ?>

                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="payment-info" role="tabpanel" aria-labelledby="payment-info-tab">
            <div class="row">

                <div class="col-2 mb-3">
                    <label class="form-label">Transaction Type:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->transaction_type); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Credit Limit:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@App\SysHelper::com_curr_format($custDetails->credit_limit,'','',',')); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Credit Days:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->credit_days); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Payment Terms:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(@$custDetails->paymentterms->title); ?> <?php echo e(@$custDetails->payment_terms_txt); ?>

                        <?php endif; ?>
                    </div>
                </div>

                <?php if($custDetails->grn_select): ?>
                <div class="col-2 mb-3">
                    <label class="form-label">GRN:</label>
                    <div class="form-control-plaintext">
                        <?php if(isset($custDetails)): ?>
                            <?php echo e(ucfirst(@$custDetails->grn_select)); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                 

            </div>
        </div>

        <div class="tab-pane fade" id="customer-info" role="tabpanel" aria-labelledby="customer-info-tab">

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <tbody>

                            <?php if(count($custDoc) > 0): ?>
                                <?php $__currentLoopData = $custDoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($doc->doc_name); ?></td>
                                        <td><?php echo e(date('d/m/Y', strtotime(@$doc->doc_exp_date))); ?></td>
                                        <td>
                                            <a class="btn-sm btn-light text-dark"
                                                href="<?php echo e(asset('public/uploads/cust-suppl/')); ?>/<?php echo e($doc->doc_file); ?>"
                                                target="_blank">

                                                <i
                                                    class="ico icon-bold-download-minimalistic text-success fw-bold title-15"></i>

                                                Download</a>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>


        </div>

    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1-info"
                type="button" role="tab" aria-controls="tab1-info" aria-selected="true">Deals In
                Progress</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2-info" type="button"
                role="tab" aria-controls="tab2" aria-selected="false">Invoice Completed</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3-info" type="button"
                role="tab" aria-controls="tab3" aria-selected="false">Payment Pending</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4-info" type="button"
                role="tab" aria-controls="tab4" aria-selected="false">Completed Orders</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5-info" type="button"
                role="tab" aria-controls="tab5" aria-selected="false">AMC</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6-info" type="button"
                role="tab" aria-controls="tab6" aria-selected="false">Project Service</button>
        </li>

          <li class="nav-item" role="presentation">
            <button class="nav-link" id="outstanding-tab" data-bs-toggle="tab" data-bs-target="#outstanding-info" type="button"
                role="tab" aria-controls="outstanding" aria-selected="false">Outstanding</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6-info" type="button"
                role="tab" aria-controls="tab6" aria-selected="false">History</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="tab1-info" role="tabpanel" aria-labelledby="tab1-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th><?php echo app('translator')->getFromJson('Deal'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Deal Name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Stage'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Ownership'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Updated On'); ?></th>
                        <th class="text-end"><?php echo app('translator')->getFromJson('Deal Value'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Date'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Closing Date'); ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    ?>
                    <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $total_deal += 1; ?>
                        <?php if(
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3)): ?>
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            <?php else: ?>
                            <tr>
                        <?php endif; ?>
                        <td><a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>"
                                target="_blank"><?php echo e(@$value->code); ?></a></td>
                        <td class="text-start">

                            <?php echo e(@$value->deal_name); ?>

                        </td>
                        <td>
                            <?php if($value->stage == 1): ?>
                                <span class="badge bg-warning">Prospecting</span>
                            <?php endif; ?>
                            <?php if($value->stage == 2): ?>
                                <span class="badge bg-success">Quote</span>
                            <?php endif; ?>
                            <?php if($value->stage == 3): ?>
                                <span class="badge bg-info">Closure</span>
                            <?php endif; ?>
                            <?php if($value->stage == 4): ?>
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                <?php echo $data; ?>

                            <?php endif; ?>
                            <?php if($value->stage == 5): ?>
                                <span class="badge bg-danger">Lost</span>
                            <?php endif; ?>
                            <?php if($value->stage == 6): ?>
                                <span class="badge bg-dark">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-start"><?php echo e(@$value->ownername->full_name); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->updated_at))); ?></td>
                        <td class="text-end">
                            <?php $aed = @App\SysHelper::get_aed_amount($value->deal_currency, $value->deal_value); ?>
                            <?php echo e(@App\SysHelper::currancy_format_deal($aed, $value->company_id)); ?>

                            <?php $total_amount += $aed; ?> AED
                        </td>
                        <td><?php echo e(date('d/m/Y', strtotime(@$value->created_at))); ?></td>
                        <td><?php echo e(date('d/m/Y', strtotime(@$value->estimated_close_date))); ?></td>
                        <td>
                            <a class="badge text-center" href="<?php echo e(url('crm-deals/show/' . $value->id)); ?>">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"><?php echo e($total_deal); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            <?php echo e(@App\SysHelper::currancy_format_deal($total_amount, $value->company_id)); ?> AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="tab-pane fade" id="tab2-info" role="tabpanel" aria-labelledby="tab2-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead class="text-center">
                    <tr>
                        <th><?php echo app('translator')->getFromJson('Deal'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Deal Name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Stage'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Ownership'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Updated On'); ?></th>
                        <th class="text-end"><?php echo app('translator')->getFromJson('Deal Value'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Date'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Clossing Date'); ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    ?>
                    <?php $__currentLoopData = $invoiced; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $total_deal += 1; ?>
                        <?php if(
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3)): ?>
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            <?php else: ?>
                            <tr>
                        <?php endif; ?>
                        <td><a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>"
                                target="_blank"><?php echo e(@$value->code); ?></a></td>
                        <td class="text-start"><?php echo e(@$value->deal_name); ?></td>
                        <td>
                            <?php if($value->stage == 1): ?>
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            <?php endif; ?>
                            <?php if($value->stage == 2): ?>
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            <?php endif; ?>
                            <?php if($value->stage == 3): ?>
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            <?php endif; ?>
                            <?php if($value->stage == 4): ?>
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                <?php echo $data; ?>

                            <?php endif; ?>
                            <?php if($value->stage == 5): ?>
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            <?php endif; ?>
                            <?php if($value->stage == 6): ?>
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-start"><?php echo e(@$value->ownername->full_name); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->updated_at))); ?></td>
                        <td class="text-end">
                            <?php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); ?>
                            <?php echo e(@App\SysHelper::currancy_format_deal($aed, $value->company_id)); ?>

                            <?php $total_amount += $aed; ?> AED
                        </td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->created_at))); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->estimated_close_date))); ?></td>
                        <td>
                            <a class="badge text-center" href="<?php echo e(url('crm-deals/' . $value->id . '/view')); ?>">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"><?php echo e($total_deal); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            <?php echo e(@App\SysHelper::currancy_format_deal($total_amount, $value->company_id)); ?> AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="tab-pane fade" id="tab3-info" role="tabpanel" aria-labelledby="tab3-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th><?php echo app('translator')->getFromJson('Deal'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Deal Name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Stage'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Ownership'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Updated On'); ?></th>
                        <th class="text-end"><?php echo app('translator')->getFromJson('Deal Value'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Date'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Clossing Date'); ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    ?>
                    <?php $__currentLoopData = $delivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $total_deal += 1; ?>
                        <?php if(
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3)): ?>
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            <?php else: ?>
                            <tr>
                        <?php endif; ?>
                        <td><a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>"
                                target="_blank"><?php echo e(@$value->code); ?></a></td>
                        <td class="text-start">

                            <?php echo e(@$value->deal_name); ?>

                        </td>
                        <td>
                            <?php if($value->stage == 1): ?>
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            <?php endif; ?>
                            <?php if($value->stage == 2): ?>
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            <?php endif; ?>
                            <?php if($value->stage == 3): ?>
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            <?php endif; ?>
                            <?php if($value->stage == 4): ?>
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                <?php echo $data; ?>

                            <?php endif; ?>
                            <?php if($value->stage == 5): ?>
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            <?php endif; ?>
                            <?php if($value->stage == 6): ?>
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(@$value->ownername->full_name); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->updated_at))); ?></td>
                        <td class="text-end">

                            <?php $vat = @App\SysHelper::get_deal_vat_amount($value->id, $value->quote_id); ?>

                            <?php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value+$vat); ?>
                            <?php echo e(@App\SysHelper::currancy_format_deal($aed, $value->company_id)); ?>

                            <?php $total_amount += $aed; ?> AED
                        </td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->created_at))); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->estimated_close_date))); ?></td>
                        <td>


                            <a class="badge text-center" href="<?php echo e(url('crm-deals/' . $value->id . '/view')); ?>">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"><?php echo e($total_deal); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            <?php echo e(@App\SysHelper::currancy_format_deal($total_amount, $value->company_id)); ?> AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tab-pane fade" id="tab4-info" role="tabpanel" aria-labelledby="tab4-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th><?php echo app('translator')->getFromJson('Deal'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Deal Name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Stage'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Ownership'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Updated On'); ?></th>
                        <th class="text-end"><?php echo app('translator')->getFromJson('Deal Value'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Date'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Clossing Date'); ?></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    ?>
                    <?php $__currentLoopData = $receivables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $total_deal += 1; ?>
                        <?php if(
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3)): ?>
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            <?php else: ?>
                            <tr>
                        <?php endif; ?>
                        <td><a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>"
                                target="_blank"><?php echo e(@$value->code); ?></a></td>
                        <td class="text-start">

                            <?php echo e(@$value->deal_name); ?>

                        </td>
                        <td>
                            <?php if($value->stage == 1): ?>
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            <?php endif; ?>
                            <?php if($value->stage == 2): ?>
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            <?php endif; ?>
                            <?php if($value->stage == 3): ?>
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            <?php endif; ?>
                            <?php if($value->stage == 4): ?>
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                <?php echo $data; ?>

                            <?php endif; ?>
                            <?php if($value->stage == 5): ?>
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            <?php endif; ?>
                            <?php if($value->stage == 6): ?>
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-start"><?php echo e(@$value->ownername->full_name); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->updated_at))); ?></td>
                        <td class="text-end">
                            <?php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); ?>
                            <?php echo e(@App\SysHelper::currancy_format_deal($aed, $value->company_id)); ?>

                            <?php $total_amount += $aed; ?> AED
                        </td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->created_at))); ?></td>
                        <td><?php echo e(date('d-M-Y', strtotime(@$value->estimated_close_date))); ?></td>
                        <td>
                            <a class="badge  text-center" href="<?php echo e(url('crm-deals/' . $value->id . '/view')); ?>">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"><?php echo e($total_deal); ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            <?php echo e(@App\SysHelper::currancy_format_deal($total_amount, $value->company_id)); ?> AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tab-pane fade" id="tab5-info" role="tabpanel" aria-labelledby="tab5-info-tab">
            <div class="table-responsive">


                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                    <thead>

                        <tr class="text-center">
                            <th><?php echo app('translator')->getFromJson('Sr No'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Deal ID'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Date'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Customer Name'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Contact Person'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Mobile No'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Start Date'); ?></th>
                            <th><?php echo app('translator')->getFromJson('End Date'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Invoicing'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Sales Person'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Description'); ?></th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        <?php if(count($amcdata) > 0): ?>
                            <?php $__currentLoopData = $amcdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr <?php if(@$value->is_delete == 1): ?> class="bg-dark" <?php endif; ?>>
                                    <td><?php echo e(@$value->id); ?></td>
                                    <td><a href="<?php echo e(url('get-url-deal-track/' . $value->deal_code->code)); ?>"
                                            target="_blank"><?php echo e(@$value->deal_code->code); ?></a></td>
                                    <td><?php echo e(date('d/m/Y', strtotime(@$value->date))); ?></td>
                                    <td><?php echo e(@$value->custname->name); ?></td>
                                    <td><?php echo e(@$value->contact_person); ?></td>
                                    <td><?php echo e(@$value->mobile_no); ?></td>
                                    <td><?php echo e(date('d/m/Y', strtotime(@$value->start_date))); ?></td>
                                    <td><?php echo e(date('d/m/Y', strtotime(@$value->end_date))); ?></td>
                                    <td><?php echo e(@$value->invoice); ?></td>
                                    <td><?php echo e(@$value->amount); ?></td>
                                    <td><?php echo e(@$value->salesperson->full_name); ?></td>
                                    <td><?php echo e(@$value->description); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>



        <div class="tab-pane fade" id="tab6-info" role="tabpanel" aria-labelledby="tab6-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <?php if(session()->has('message-success') != '' || session()->get('message-danger') != ''): ?>
                        <tr>
                            <td colspan="6">
                                <?php if(session()->has('message-success')): ?>
                                    <div class="alert alert-success">
                                        <?php echo e(session()->get('message-success')); ?>

                                    </div>
                                <?php elseif(session()->has('message-danger')): ?>
                                    <div class="alert alert-danger">
                                        <?php echo e(session()->get('message-danger')); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr class="text-center">
                        <th><?php echo app('translator')->getFromJson('PS ID'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Deal No'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Date '); ?></th>
                        <th><?php echo app('translator')->getFromJson('Customer Name'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Contact Person'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Mobile No'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Location of Work'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Amount'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Sales Person'); ?></th>
                        <th><?php echo app('translator')->getFromJson('Description'); ?></th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    <?php if(count($support) > 0): ?>
                        <?php $__currentLoopData = $support; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(@$value->id); ?></td>

                                <td><a href="<?php echo e(url('get-url-deal-track/' . $value->deal_code->code)); ?>"
                                        target="_blank"><?php echo e(@$value->deal_code->code); ?></a></td>
                                <td><?php echo e(date('d-M-Y', strtotime(@$value->date))); ?></td>
                                <td><?php echo e(@$value->custname->name); ?> <input type="hidden"
                                        id="list_custname_<?php echo e($value->id); ?>"
                                        value="<?php echo e(@$value->custname->name); ?>" /></td>
                                <td><?php echo e(@$value->contact_person); ?> <input type="hidden"
                                        id="list_contact_person_<?php echo e($value->id); ?>"
                                        value="<?php echo e(@$value->contact_person); ?>" /></td>
                                <td><?php echo e(@$value->mobile); ?> <input type="hidden"
                                        id="list_mobile_<?php echo e($value->id); ?>" value="<?php echo e(@$value->mobile); ?>" /></td>
                                <td><?php echo e(@$value->location_of_work); ?> <input type="hidden"
                                        id="list_location_of_work_<?php echo e($value->id); ?>"
                                        value="<?php echo e(@$value->location_of_work); ?>" /></td>
                                <td><?php echo e(@$value->amount); ?></td>
                                <td><?php echo e(@$value->ownername->full_name); ?></td>
                                <td><?php echo e(@$value->deal_description); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


          <div class="tab-pane fade " id="outstanding-info" role="tabpanel" aria-labelledby="outstanding-info-tab">
              <script>
                function set_total(id, at) {
                    $('#sum_' + id).text(at.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
                    $('#collapse' + id).css('display', '');
                    $('#account_table' + id).css('display', '');
                }
            </script>

            
            <div class="accordion gap-0" id="accordionExample">
                <?php if(count($data_all) > 0): ?>
                    <?php $no = 1;
                    $all_total = 0;
                    $k = 0; ?>
                    <?php $__currentLoopData = $data_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        if (count($data) > 0) {
                            $data_adjestment = @App\SysSalesReturnAdjestment::select('siv_no', DB::raw('sum(paid_amount) as paid_amount'))
                                ->wherein('siv_no', $data->pluck('transaction_no'))
                                ->groupby('siv_no')
                                ->get();
        
                            $data_receipt = DB::table('sys_receipt as r')
                                ->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name')
                                ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')
                                ->where('ra.account_id', $data[0]->account_id)
                                ->wherein('bi_doc_no', $data->pluck('transaction_no'))
                                ->where('r.status', 1)
                                ->get();
        
                            $data_receipt2 = DB::table('sys_journalvoucher as j')
                                ->select('ra.bi_doc_no', 'j.doc_number', 'ra.bi_amount', 'j.doc_date')
                                ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'j.doc_number')
                                ->where('ra.account_id', $data[0]->account_id)
                                ->wherein('bi_doc_no', $data->pluck('transaction_no'))
                                ->where('j.status', 1)
                                ->get();
                        }
                        ?>

                        <?php
                        $aname = $accounts->where('id', $data[0]->account_id)->first();
                        $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code);
                        ?>

                        <table id="account_table<?php echo e($aname->id); ?>" class="table"
                            style="border: solid 1px #e3e6f0; margin-bottom: -1px !important;">
                            <thead>
                                <tr>
                                    <th class="border text-center" width="100px">
                                        <a href="<?php echo e(url('get-url-customer/' . $aname->account_code)); ?>" target="_blank">
                                            <?php echo e($aname->account_code); ?>

                                        </a>
                                    </th>
                                    <th class="border text-left">
                                        <a class="text-left" type="button" data-toggle="collapse"
                                            data-target="#collapse<?php echo e($aname->id); ?>" aria-expanded="true"
                                            aria-controls="collapse<?php echo e($aname->id); ?>">
                                            <?php echo e($aname->account_name); ?>

                                            <span style="font-weight: normal; color: #3d3d3d;"><?php echo $cust_det; ?></span>
                                        </a>
                                    </th>
                                    <th class="border text-end" width="100px">
                                        <label id="sum_<?php echo e($aname->id); ?>"></label>
                                    </th>
                                </tr>
                            </thead>
                        </table>

                        <div id="collapse<?php echo e($aname->id); ?>" class="" data-parent="#accordionExample">
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">Doc No</th>
                                        <th class="border text-center">LPO No</th>
                                        <th class="border text-center">Deal ID</th>
                                        <th class="border text-center">Amount</th>
                                        <th class="border text-center">Adjustments</th>
                                        <th class="border text-center">Balance</th>
                                        <th class="border text-center">Total Balance</th>
                                        <th class="border text-center hidecol_<?php echo e($aname->id); ?>">Receipt Date</th>
                                        <th class="border text-center hidecol_<?php echo e($aname->id); ?>">Doc Number</th>
                                        <th class="border text-center">Sales Person</th>
                                        <th class="border text-center">Payment Terms</th>
                                        <th class="border text-center">Due Date</th>
                                        <th class="border text-center">Over Due</th>
                                        <th class="border text-center">0-30</th>
                                        <th class="border text-center">31-60</th>
                                        <th class="border text-center">61-90</th>
                                        <th class="border text-center">>90</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $adjustments = 0;
                                        $b = 0;
                                        $grand_debit_amount = 0;
                                        $grand_paid = 0;
                                        $grand_balance = 0;
                                        $grand_total_balance = 0;
                                        $gtot1 = 0;
                                        $gtot2 = 0;
                                        $gtot3 = 0;
                                        $gtot4 = 0;
                                    ?>
                                    <?php if(count($data) > 0): ?>
                                        <?php $sum_b = 0; ?>
                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $adjustments = 0;
                                                $receipt_date = '';
                                                $doc_number = '';
                                                $cheque_number = '';
                                                $bank_name = '';
                                                $bi_amount = 0;
                                                $bi_amount2 = 0;
                                                $paid = 0;
                                            ?>
                                            <?php
                                                $adjustments = $data_adjestment->where('siv_no', $dt->transaction_no)->max('paid_amount');
                                                $receipt = $data_receipt->where('bi_doc_no', $dt->transaction_no);
                                                if (count($receipt) > 0) {
                                                    foreach ($receipt as $r) {
                                                        $receipt_date .= date('d/m/Y', strtotime($r->receipt_date)) . ',';
                                                        $doc_number .= $r->doc_number . ',';
                                                        if ($r->cheque_number != '') {
                                                            $cheque_number .= $r->cheque_number . ',';
                                                        }
                                                        if ($r->cheque_bank_name != '') {
                                                            $bank_name .= $r->cheque_bank_name . ',';
                                                        }
                                                        $bi_amount += $r->bi_amount;
                                                    }
                                                }
                            
                                                $receipt2 = $data_receipt2->where('bi_doc_no', $dt->transaction_no);
                                                if (count($receipt2) > 0) {
                                                    foreach ($receipt2 as $r) {
                                                        $receipt_date .= date('d/m/Y', strtotime($r->doc_date)) . ',';
                                                        $doc_number .= $r->doc_number . ',';
                                                        $bi_amount2 += $r->bi_amount;
                                                    }
                                                }
                            
                                                $paid += $adjustments + $bi_amount + $bi_amount2;
                            
                                                $deal_id = '';
                                                $deal_code = '';
                                                $lpo_no = '';
                                                $sales_person = '';
                                                $deal = @App\SysHelper::get_deal_track_detail_for_receivable_outstanding($dt->transaction_no);
                                                $lpono = @App\SysHelper::get_sales_invoice_details($dt->transaction_no);
                                                if (isset($deal) && $deal != '') {
                                                    $deal_id = $deal->id;
                                                    $deal_code = $deal->code;
                                                    $sales_person = $deal->full_name;
                                                }
                                                if (isset($lpono) && $lpono != '') {
                                                    $lpo_no = $lpono->lpo_number;
                                                }
                                            ?>
                                            <?php
                                            if ($dt->debit_amount != $paid) {
                                                $grand_debit_amount += $dt->debit_amount;
                                                $grand_paid += $paid;
                                                $grand_balance += $dt->debit_amount - abs($paid);
                                            }
                                            ?>
                                            <?php
                                                $DueData = @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no, $dt->transaction_date);
                                            ?>

                                            <?php if($dt->debit_amount != $paid): ?>
                                                <tr>
                                                    <td class="border text-center">
                                                        <?php echo e(date('d/m/Y', strtotime($dt->transaction_date))); ?>

                                                    </td>
                                                    <td class="border text-center">
                                                        <a href="<?php echo e(url('get-url-sales-invoice/' . $dt->transaction_no)); ?>" target="_blank">
                                                            <?php echo e($dt->transaction_no); ?>

                                                        </a>
                                                    </td>
                                                    <td class="border text-center"><?php echo e($lpo_no); ?></td>
                                                    <td class="border text-center">
                                                        <a href="<?php echo e(url('crm-deals/' . $deal_id . '/view')); ?>" target="_blank">
                                                            <?php echo e($deal_code); ?>

                                                        </a>
                                                    </td>
                                                    <td class="border text-center"><?php echo e($dt->debit_amount); ?></td>
                                                    <td class="border text-center">
                                                        <?php echo e(@App\SysHelper::com_curr_format($paid, 2, '.', ',')); ?>

                                                    </td>
                                                    <td class="border text-center">
                                                        <?php echo e(@App\SysHelper::com_curr_format($dt->debit_amount - abs($paid), 2, '.', ',')); ?>

                                                        <?php $b += $dt->debit_amount - abs($paid); ?>
                                                    </td>
                                                    <td class="border text-center">
                                                        <?php echo e(@App\SysHelper::com_curr_format($b, 2, '.', ',')); ?>

                                                    </td>

                                                    <?php
                                                        $sum_b += $dt->debit_amount - abs($paid);
                                                        $all_total += $dt->debit_amount - abs($paid);
                                                    ?>
                                                    <script>
                                                        set_total(<?php echo e($aname->id); ?>, <?php echo e($sum_b); ?>);
                                                    </script>

                                                    <td class="border text-center hidecol_<?php echo e($aname->id); ?>">
                                                        <?php echo e(rtrim($receipt_date, ',')); ?>

                                                    </td>
                                                    <td class="border text-center hidecol_<?php echo e($aname->id); ?>">
                                                        <?php echo e(rtrim($doc_number, ',')); ?>

                                                    </td>

                                                    <td class="border text-center"><?php echo e($sales_person); ?></td>
                                                    <td class="border text-center"><?php echo e($DueData[2]); ?></td>
                                                    <td class="border text-center"><?php echo e($DueData[0]); ?></td>
                                                    <?php if ($DueData[1] > 0) { ?>
                                                        <td class="border text-center" style="color:red">
                                                            <?php echo e($DueData[1]); ?>

                                                        </td>
                                                    <?php } else { ?>
                                                        <td class="border text-center"><?php echo e($DueData[1]); ?></td>
                                                    <?php } ?>

                                                    <?php
                                                    if ($DueData[3] == 1) {
                                                        $gtot1 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 2) {
                                                        $gtot2 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 3) {
                                                        $gtot3 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 4) {
                                                        $gtot4 += $dt->debit_amount - abs($paid);
                                                    }
                                                    ?>

                                                    <?php if($DueData[3] == 1): ?>
                                                        <td class="border text-center">
                                                            <?php echo e(@App\SysHelper::com_curr_format($dt->debit_amount - abs($paid), 2, '.', ',')); ?>

                                                        </td>
                                                    <?php else: ?>
                                                        <td class="border text-center">&nbsp;</td>
                                                    <?php endif; ?>
                                                    <?php if($DueData[3] == 2): ?>
                                                        <td class="border text-center">
                                                            <?php echo e(@App\SysHelper::com_curr_format($dt->debit_amount - abs($paid), 2, '.', ',')); ?>

                                                        </td>
                                                    <?php else: ?>
                                                        <td class="border text-center">&nbsp;</td>
                                                    <?php endif; ?>
                                                    <?php if($DueData[3] == 3): ?>
                                                        <td class="border text-center">
                                                            <?php echo e(@App\SysHelper::com_curr_format($dt->debit_amount - abs($paid), 2, '.', ',')); ?>

                                                        </td>
                                                    <?php else: ?>
                                                        <td class="border text-center">&nbsp;</td>
                                                    <?php endif; ?>
                                                    <?php if($DueData[3] == 4): ?>
                                                        <td class="border text-center">
                                                            <?php echo e(@App\SysHelper::com_curr_format($dt->debit_amount - abs($paid), 2, '.', ',')); ?>

                                                        </td>
                                                    <?php else: ?>
                                                        <td class="border text-center">&nbsp;</td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if(count($receipt) == 0): ?>
                                                <script>
                                                    $('.hidecol_' + <?php echo e($aname->id); ?>).css('display', 'none');
                                                </script>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_debit_amount, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_paid, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_balance, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($b, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center" colspan="4">&nbsp </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot1, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot2, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot3, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot4, 2, '.', ','); ?> </b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <table class="table" style="border: solid 1px #e3e6f0;">
                        <thead>
                            <tr>
                                <th class="border fw-bold text-end" colspan="1">Total</th>
                                <th class="border fw-bold text-end" colspan="1" width="200px">
                                    <?php echo e($all_total); ?>

                                </th>
                            </tr>
                        </thead>
                    </table>
                <?php endif; ?>
            </div>
           
        </div>


    </div>
</div>


<?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

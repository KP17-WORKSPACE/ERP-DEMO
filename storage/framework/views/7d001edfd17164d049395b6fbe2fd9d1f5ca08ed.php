<?php $__env->startSection('mainContent'); ?>
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    
    <?php try { ?>
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Sales Invoice View</h2>
                    <span class="page-label">Home - Sales Invoice</span>
                </div>
                <div>
                    <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                    <a href="<?php echo e(url('sales-invoice/create')); ?>" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="<?php echo e(url('sales-invoice/'.$edit_si->id.'/edit')); ?>" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                    <!-- Input with Search -->
                    <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                        <input type="text" id="quick_search_doc_number" placeholder="SI Number" class="form-control pr-4" /> 
                        <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                        <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <script>
                        const baseUrl = "<?php echo e(url('get-edit-url-sales-invoice')); ?>";                
                        document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                var val = this.value.trim();
                                if (val !== '') {                                
                                    window.location.href = baseUrl + '/' + val;
                                }
                            }
                        });
                    </script>
                    <!-- Input with Search -->
                    <a href="<?php echo e(url('sales-invoice')); ?>" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
                <input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
                <input type="hidden" id="si_id" name="id" value="<?php echo e(isset($edit_si) ? $edit_si->id : ''); ?>">
                <input type="hidden" id="net_vat" name="net_vat" value="<?php echo e($edit_si->net_vat); ?>">
                
                <div class="white-box">

                    <div class="col-lg-12 text-right">
                        <?php if(session()->has('message-success') != '' || session()->get('message-danger') != ''): ?>
                            <?php if(session()->has('message-success')): ?>
                                <p class="text-success">
                                    <?php echo e(session()->get('message-success')); ?>

                                </p>
                            <?php elseif(session()->has('message-danger')): ?>
                                <p class="text-danger">
                                    <?php echo e(session()->get('message-danger')); ?>

                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="add-visitor">
                              
                                <div class="row">
                                    <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Customer <span>*</span></label>
                                                <select class="form-control js-example-basic-single" name="customer" id="customer" required disabled>
                                                    <option value=""></option>
                                                    <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->id); ?>"
                                                            <?php echo e(isset($edit_si) ? (!empty($edit_si->customer) ? (@$edit_si->customer == @$value->id ? 'selected' : '') : '') : ''); ?>><?php echo e(@$value->account_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Doc'); ?> <?php echo app('translator')->getFromJson('Number'); ?><span>*</span></label>

                                                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="<?php echo e($edit_si->doc_number); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Invoice Date</label>
                                                    <?php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit_si) && !empty($edit_si->doc_date) ){
                                                        @$value = date('Y-m-d', strtotime(@$edit_si->doc_date)); }
                                                    ?>
                                                    <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                                        name="doc_date" value="<?php echo e(@$value); ?>" required>
                                                </div>
                                            </div>
        
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Currency</label>
                                                <?php
                                                    $currency1=1;
                                                    if(session('logged_session_data.company_id')==8){
                                                        $currency1=2;
                                                    }
                                                ?>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    

                                                    <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->id); ?>"
                                                            <?php if(isset($edit_si)): ?>
                                                                <?php if($edit_si->currency == @$value->id): ?> selected <?php endif; ?>
                                                            <?php else: ?>
                                                                <?php if($value->id == $currency1): ?> selected <?php endif; ?>
                                                            <?php endif; ?> 
                                                            >
                                                            <?php echo e(@$value->code); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        

                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-2">
                                        <div class="input-effect">
                                            <label class="txtlbl">Pending list</label>
                                            <div id="plist"
                                                style="width: 100%; height: 250px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                                            </div>
                                            <a data-modal-size="modal-md" data-target="#profo_pending_popup_win" id="addProfoPending"
                                                data-toggle="modal"></a>
                                            <input type="hidden" id="grn_id" name="profo_id">
                                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="<?php echo e($edit_si->net_vat); ?>">
                                        </div>
                    
                                    </div>
                                    <div class="col-lg-8 mb-2">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Delivery Terms'); ?><span>*</span></label>
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->delivery_terms) ? @$edit_si->delivery_terms : old('delivery_terms')) : 'Ex-Dubai'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Printed Invoice Number'); ?><span></span></label>
                                                    <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off" id="printed_invoice_number" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->printed_invoice_number) ? @$edit_si->printed_invoice_number : old('printed_invoice_number')) : ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Salesman'); ?><span>*</span></label>
                                                    <select class="form-control" name="sales_man" id="sales_man" required>
                                                        <option value="">-Select-</option>
                                                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->user_id); ?>"
                                                            <?php if(isset($edit_si)): ?> <?php if($edit_si->sales_man == $value->user_id): ?> selected <?php endif; ?> <?php else: ?> <?php if($value->user_id == Auth::user()->id): ?> selected  <?php endif; ?> <?php endif; ?>
                                                            ><?php echo e(@$value->full_name); ?>

                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Payment Terms'); ?><span>*</span></label>
                                                    <select class="form-control" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
                                                        <option value="" ></option>
                                                        <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                             <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_si)? !empty(@$edit_si->payment_terms)? @$edit_si->payment_terms==@$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->title); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="<?php echo e($edit_si->lpo_number); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control" type="date" name="reference_date" autocomplete="off" id="reference_date" value="<?php echo e($edit_si->lpo_date); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="<?php echo e(@App\SysHelper::get_code_from_dealid($edit_si->deal_id)); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Supplier Name<span>*</span></label>
                                                    <input class="form-control" type="text" name="supplier_name" autocomplete="off" id="supplier_name" value="<?php echo e($edit_si->supplier_name); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Created'); ?> <?php echo app('translator')->getFromJson('By'); ?><span>*</span></label>
                                                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->created_by) ? @$edit_si->createdby->full_name : old('createdby')) : Auth::user()->full_name); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Narration<span></span></label>
                                                    <input class="form-control" type="text" name="narration" autocomplete="off" id="narration" value="<?php echo e($edit_si->narration); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                
                                <div class="col-lg-3 mb-2" style="display: none;">
                                    
                                    <div class="input-effect">
                                        <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Other Payment Terms'); ?><span>*</span></label>
                                        <input class="form-control"
                                            type="text" name="payment_terms2" autocomplete="off"
                                            id="payment_terms2"
                                            value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->payment_terms2) ? @$edit_si->payment_terms2 : old('payment_terms2')) : ''); ?>">
                                        <span class="focus-border"></span>
                                        <?php if($errors->has('payment_terms2')): ?>
                                            <span class="invalid-feedback" role="alert">
                                                <strong><?php echo e($errors->first('payment_terms2')); ?></strong>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                <div class="col-lg-12 mb-0">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="enduser-tab" data-toggle="tab" href="#enduser" role="tab" aria-controls="enduser" aria-selected="false">End User Details</a>
                        </li>
                        <?php if(session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10): ?>
                        <li class="nav-item">
                          <a class="nav-link" id="arabic-tab" data-toggle="tab" href="#arabic" role="tab" aria-controls="arabic" aria-selected="false">Arabic Address</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Name'); ?> <span></span></label>
                                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="<?php echo e($edit_si->shipping_name); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Address'); ?> <span></span></label>
                                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="<?php echo e($edit_si->shipping_address); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="vat" role="tabpanel" aria-labelledby="vat-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Customer Type'); ?></label>
                                            <select class="form-control" name="customer_type" id="customer_type">
                                                <option value="0" ></option>
                                                <?php $__currentLoopData = $customertype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_si)? !empty(@$edit_si->customer_type)? @$edit_si->customer_type==@$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->title); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Sale Type'); ?></label>
                                            <select class="form-control" name="sale_type" id="sale_type">
                                                <option value="0" ></option>
                                                <?php $__currentLoopData = $saletype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_si)? !empty(@$edit_si->sale_type)? @$edit_si->sale_type==@$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->title); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Customer Country'); ?> <span></span></label>
                                            <select class="form-control" name="customer_country" id="country">
                                                <option data-display="" value="0"></option>
                                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>"
                                                        <?php try{?>                                                        
                                                        <?php if(isset($edit_si)): ?> <?php if(@$edit_si->customer_country == $value->id): ?> selected <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        ><?php echo e(@$value->name); ?> </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Customer State'); ?> <span></span></label>
    
                                            <div id="sectionStateDiv">
                                                <select class="form-control" name="customer_state" id="state">
                                                    <option data-display="" value="0"></option>
                                                    <?php try{?>
                                                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($value->id); ?>" <?php if(isset($edit_si)): ?> <?php if(@$edit_si->customer_state == $value->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e($value->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php } catch (\Throwable $th) {} ?>
                                                </select>
                                            </div>
    
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="tab-pane" id="enduser" role="tabpanel" aria-labelledby="enduser-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('End User Name'); ?> <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->end_user_name) ? @$edit_si->end_user_name : '') : old('end_user_name')); ?>" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Contact Person Name'); ?> <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->contact_person_name) ? @$edit_si->contact_person_name : '') : old('contact_person_name')); ?>">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Contact Person Email'); ?> <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->contact_person_email) ? @$edit_si->contact_person_email : '') : old('contact_person_email')); ?>">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Contact Person No'); ?> <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="<?php echo e(isset($edit_si) ? (!empty(@$edit_si->contact_person_no) ? @$edit_si->contact_person_no : '') : old('contact_person_no')); ?>">
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <?php if(session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10): ?>
                        <div class="tab-pane" id="arabic" role="tabpanel" aria-labelledby="arabic-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Company Name in Arabic'); ?> <span></span></label>
                                            <input type="text" class="form-control text-right" name="company_name_ar" id="company_name_ar" value="<?php if(isset($edit_ar)): ?><?php echo e($edit_ar->company_name_ar); ?><?php endif; ?>" autocomplete="off" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Contact Person in Arabic'); ?> <span></span></label>
                                            <input type="text" class="form-control text-right" name="contact_person_ar" id="contact_person_ar" value="<?php if(isset($edit_ar)): ?><?php echo e($edit_ar->contact_person_ar); ?><?php endif; ?>" autocomplete="off" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl"><?php echo app('translator')->getFromJson('Address in Arabic'); ?> <span></span></label>
                                            <textarea class="form-control text-right" name="address_ar" id="address_ar" rows="4"><?php if(isset($edit_ar)): ?><?php echo e($edit_ar->address_ar); ?><?php endif; ?></textarea>
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>





                        <div class="equipment comon-status row d-block">
                            <hr />
                            <h6 class="primary-color"><?php echo app('translator')->getFromJson('Item Details'); ?>:</h6> 
                            

                            <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                        <th style="width:350px;"><?php echo app('translator')->getFromJson('Description'); ?></th>
                                        <th style="width:70px;"><?php echo app('translator')->getFromJson('Tax'); ?></th>
                                        <th style="width:70px;"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                        <th class="text-right"style="width:80px;"><?php echo app('translator')->getFromJson('Unit Price'); ?></th>
                                        <th class="text-right"style="width:70px;"><?php echo app('translator')->getFromJson('Value'); ?></th>
                                        <th class="text-right"style="width:70px;"><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                        <th class="text-right"style="width:120px;"><?php echo app('translator')->getFromJson('Taxable Amount'); ?></th>
                                        <th class="text-right"style="width:100px;"><?php echo app('translator')->getFromJson('VAT Amount'); ?></th>
                                        <th class="text-right"style="width:100px;"><?php echo app('translator')->getFromJson('Total Amount'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $qty = 0; $unitprice = 0; $value = 0; $discount = 0; $taxableamount = 0; $vatamount = 0; $totalamount = 0; $i=1; ?>
                                    <?php if(count($edit_si_items)>0): ?>
                                    <?php $__currentLoopData = $edit_si_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($dt->productname->part_number); ?> <input type="hidden" name="partno[]" id="partno_<?php echo e($dt->id); ?>" value="<?php echo e($dt->productname->part_number); ?>" />
                                            <input type="hidden" id="pid_<?php echo e($dt->id); ?>" value="<?php echo e($dt->part_number); ?>" /></td>
                                        <td><?php echo e($dt->description); ?> <input type="hidden" id="description_<?php echo e($dt->id); ?>" value="<?php echo e($dt->description); ?>" /></td>
                                        <td><?php echo e($dt->tax); ?> <input type="hidden" name="tax[]" id="tax_<?php echo e($dt->id); ?>" value="<?php echo e(intval($dt->tax)); ?>" /></td>
                                        <td><?php echo e($dt->qty); ?> <input type="hidden" name="qty[]" id="qty_<?php echo e($dt->id); ?>" value="<?php echo e($dt->qty); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->unitprice,2,'.',',')); ?> <input type="hidden" name="unitprice[]" id="unitprice_<?php echo e($dt->id); ?>" value="<?php echo e($dt->unitprice); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->value,2,'.',',')); ?> <input type="hidden" name="value[]" id="value_<?php echo e($dt->id); ?>" value="<?php echo e($dt->value); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->discount,2,'.',',')); ?> <input type="hidden" name="discount[]" id="discount_<?php echo e($dt->id); ?>" value="<?php echo e($dt->discount); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',')); ?> <input type="hidden" name="taxableamount[]" id="taxableamount_<?php echo e($dt->id); ?>" value="<?php echo e($dt->taxableamount); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->vatamount,2,'.',',')); ?> <input type="hidden" name="vatamount[]" id="vatamount_<?php echo e($dt->id); ?>" value="<?php echo e($dt->vatamount); ?>" /></td>
                                        <td align="right"><?php echo e(@App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',')); ?> <input type="hidden" id="totalamount_<?php echo e($dt->id); ?>" value="<?php echo e($dt->taxableamount+$dt->vatamount); ?>" /></td>
                                        </tr>
                                    <?php $i++; $qty += $dt->qty; $unitprice += $dt->unitprice; $value += $dt->value; $discount += $dt->discount; $taxableamount += $dt->taxableamount; $vatamount += $dt->vatamount; $totalamount += ($dt->taxableamount+$dt->vatamount); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                            
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td></td>
                                        <td class="font-weight-bold"></td>
                                        <td class="font-weight-bold"><label id="qty_total"><?php echo e($edit_si_items->sum('qty')); ?></label></td>
                                        <td class="text-right font-weight-bold"><label id="unitprice_total"></label></td>
                                        <td class="text-right font-weight-bold"><label id="value_total"><?php echo e(@App\SysHelper::com_curr_format($edit_si_items->sum('value'),2,'.',',')); ?></label></td>
                                        <td class="text-right font-weight-bold"><label id="discount_total"><?php echo e(@App\SysHelper::com_curr_format($edit_si_items->sum('discount'),2,'.',',')); ?></label></td>
                                        <td class="text-right font-weight-bold"><label id="taxableamount_total"><?php echo e(@App\SysHelper::com_curr_format($edit_si_items->sum('taxableamount'),2,'.',',')); ?></label></td>
                                        <td class="text-right font-weight-bold"><label id="vatamount_total"><?php echo e(@App\SysHelper::com_curr_format($edit_si_items->sum('vatamount'),2,'.',',')); ?></label></td>
                                        <td class="text-right font-weight-bold"><label id="amount_total"><?php echo e(@App\SysHelper::com_curr_format($edit_si_items->sum('taxableamount') + $edit_si_items->sum('vatamount'),2,'.',',')); ?></label></td>
                                    </tr>

                                    <?php if(isset($edit_si)): ?>
                                    <?php if($edit_si->deal_discount>0): ?>
                                    <?php
                                                $vat = $edit_si_items->max('tax');
                                                $deal_discount_taxable_amount = $edit_si->deal_discount;
                                                $deal_discount_vat_amount = $edit_si->deal_discount*($vat)/100;
                                                $deal_discount_sum_amount = $deal_discount_taxable_amount+$deal_discount_vat_amount;

                                                $t_discount = $edit_si_items->sum('discount');
                                                $t_taxableamount = $edit_si_items->sum('taxableamount');
                                                $t_vatamount = $edit_si_items->sum('vatamount');
                                                $t_total = $edit_si_items->sum('taxableamount') + $edit_si_items->sum('vatamount');
                                                ?>
                                    <tr>
                                        <td colspan="6" class="text-right">Aditional Discount</td>
                                        <td class="text-right"><?php echo e($edit_si->deal_discount); ?></td>
                                        <td class="text-right"><?php echo e($deal_discount_taxable_amount); ?></td>
                                        <td class="text-right"><?php echo e($deal_discount_vat_amount); ?></td>
                                        <td class="text-right"><?php echo e($deal_discount_sum_amount); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-right"></td>
                                        <td class="text-right font-weight-bold"><?php echo e(@App\SysHelper::com_curr_format($t_discount + $edit_si->deal_discount,2,'.',',')); ?></td>
                                        <td class="text-right font-weight-bold"><?php echo e(@App\SysHelper::com_curr_format($t_taxableamount - $deal_discount_taxable_amount,2,'.',',')); ?></td>
                                        <td class="text-right font-weight-bold"><?php echo e(@App\SysHelper::com_curr_format($t_vatamount - $deal_discount_vat_amount,2,'.',',')); ?></td>
                                        <td class="text-right font-weight-bold"><?php echo e(@App\SysHelper::com_curr_format($t_total - $deal_discount_sum_amount,2,'.',',')); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    
                                </tfoot>
                            </table>

<script>
function ddl_part_change(id)
{
var selOpt = $('#part_number_'+id+' :selected').val();
$('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');
var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
$('#description_'+id+'').val(selOpt2);
$('#description_'+id+'').focus();
}

function calc_change(id) {
    var net_vat = $('#net_vat').val();
    //var net_vat = $('#vat_percentage').val();

    var qty = $('#qty_' + id + '').val();
    var unitprice = $('#unitprice_' + id + '').val();
    var value = $('#value_' + id + '').val();
    var discount = $('#discount_' + id + '').val();
    var taxamount = $('#taxamount_' + id + '').val();
    var vatamount = $('#vatamount_' + id + '').val();
    var totalamount = $('#totalamount_' + id + '').val();


    qty = (qty === '') ? '0' : qty;
    unitprice = (unitprice === '') ? '0' : unitprice;
    var fin_value = (unitprice * qty);
    $('#value_' + id + '').val(fin_value.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));


    value = (value === '') ? '0' : value;
    discount = (discount === '') ? '0' : discount;
    var fin_taxableamount = ((unitprice * qty) - Number(discount));
    $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));

    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
    $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));

    var fin_totalamount = (fin_taxableamount + fin_vatableamount);
    $('#totalamount_' + id + '').val(fin_totalamount.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));

    calc_total();
}

$(document).on("change", ".unitprice", function () {
    var tot = 0;
    $(".unitprice").each(function() {
        var vale = $(this).val();
        if(!isNaN(parseFloat(vale))){
            tot = parseInt(tot) + parseInt(vale);
        }
    });
    alert(tot);
});


function calc_total()
{
var countrow = document.getElementById('si-row-count').value;

//var countrow = $('#si-table >tbody >tr').length;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
for(var i=1; i<=countrow; i++)
{
t1 += Number($('#qty_'+i).val());
t2 += Number($('#unitprice_'+i).val());
t3 += Number($('#value_'+i).val());
t4 += Number($('#discount_'+i).val());
t5 += Number($('#customcharges_'+i).val());
t6 += Number($('#taxableamount_'+i).val());
t7 += Number($('#vatamount_'+i).val());
}
$('#qty_total').text(t1);
$('#unitprice_total').text(t2.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#value_total').text(t3.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#discount_total').text(t4.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#customcharges_total').text(t5.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#taxableamount_total').text(t6.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#vatamount_total').text(t7.toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
$('#net_total').text((t6+t7).toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>));
}

function fn_payment_terms()
{
var val_payment_terms = $('#payment_terms').val();
if(val_payment_terms==22)
{
$('#div_payment_terms').css('display','block');
}
else
{
$('#div_payment_terms').css('display','none');
}
}
function fn_shipping_name()
{
var shipping_id = $('#shipping_name').val();
var shipping_data = $('#ship_'+shipping_id).val();        
var ret = shipping_data.split("#");
$('#shipping_address_1').val(ret[0]);
$('#shipping_address_1').focus();
$('#shipping_address_2').val(ret[1]);
$('#shipping_address_2').focus();
$('#shipping_contact_no').val(ret[2]);
$('#shipping_contact_no').focus();
}

</script>



                            </div>

                            <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
                                <div class="col-lg-12 text-right">
                                    <button type="button" class="primary-btn small fix-gr-bg"
                                        id="addRowEquipment">
                                        <span class="ti-plus pr-2"></span><?php echo app('translator')->getFromJson('lang.item'); ?></button>
                                </div>
                            </div>
                            
                       

                        <div class="row mt-40" style="display: none;">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                    <label class="dynamicslbl"><?php echo app('translator')->getFromJson('lang.note'); ?> <span></span></label>
                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="note"><?php echo e(isset($edit) ? (!empty(@$edit->note) ? @$edit->note : '') : old('description')); ?></textarea>
                                    
                                </div>
                            </div>
                        </div>


                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="pi-table2" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('Name'); ?></th>
                                        <th style="width:350px;"><?php echo app('translator')->getFromJson('Credit Account'); ?></th>
                                        <th style="width:70px;"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                                        <th style="width:80px;"><?php echo app('translator')->getFromJson('Remarks'); ?></th>
                                    </tr>
                                    <script>
                                        function add_fright()
                                        {
                                            var id = $('#fright_row').val();
                                            id=Number(id)+1;
                                            $('#fright_row').val(id);
                                            $('#fright_row_'+id).css("display", "");
                                        }
                                        function add_fright_edit(id)
                                        {
                                            $('#fright_row_'+id).css("display", "");
                                        }
                                        function cfc_row_delete(id)
                                        {
                                            $('#fright_row_'+id).remove();
                                            //$(this).closest("tr").remove();
                                        } 
                                    </script>
                                </thead>
                                <tbody>
                                    <tr id="fright_row_1">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                                                <option value=""></option>
                                                <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name==$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                                readonly="true">
                                                <option value="none"></option>
                                                <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_credit_account)? @$edit_cfc[0]->cfc_credit_account==@$value->id ? 'selected':'':'':''); ?>><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]"
                                                autocomplete="off" min="0" onchange="cfc_amount_change(1)" value="<?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_amount) ? @$edit_cfc[0]->cfc_amount : old('')) : old('')); ?>" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                                autocomplete="off" value="<?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_remarks) ? @$edit_cfc[0]->cfc_remarks : old('')) : old('')); ?>">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_2">
                                        <?php if(isset($edit_cfc[1])): ?>
                                        <?php if(@$edit_cfc[1]->cfc_amount != ""): ?>
                                        <script>
                                            add_fright_edit(2);
                                        </script>                            
                                        <?php endif; ?>                            
                                        <?php endif; ?>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                                <option value=""></option>
                                                <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name==$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                                readonly="true">
                                                <option value="none"></option>
                                                <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_credit_account)? @$edit_cfc[1]->cfc_credit_account==@$value->id ? 'selected':'':'':''); ?>><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]"
                                                autocomplete="off" min="0" onchange="cfc_amount_change(2)" value="<?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_amount) ? @$edit_cfc[1]->cfc_amount : old('')) : old('')); ?>" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                                autocomplete="off" value="<?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_remarks) ? @$edit_cfc[1]->cfc_remarks : old('')) : old('')); ?>">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_3">                        
                                    <?php if(isset($edit_cfc[2])): ?>
                                    <?php if(@$edit_cfc[2]->cfc_amount != ""): ?>
                                    <script>
                                        add_fright_edit(3);
                                    </script>                            
                                    <?php endif; ?>                            
                                    <?php endif; ?>
                                    <td>
                                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name==$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->account_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                                readonly="true">
                                                <option value="none"></option>
                                                <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_credit_account)? @$edit_cfc[2]->cfc_credit_account==@$value->id ? 'selected':'':'':''); ?>><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]"
                                                autocomplete="off" min="0" onchange="cfc_amount_change(3)" value="<?php echo e(isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_amount) ? @$edit_cfc[2]->cfc_amount : old('')) : old('')); ?>" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]"
                                                autocomplete="off" value="<?php echo e(isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_remarks) ? @$edit_cfc[2]->cfc_remarks : old('')) : old('')); ?>">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_4">
                                    <?php if(isset($edit_cfc[3])): ?>
                                    <?php if(@$edit_cfc[3]->cfc_amount != ""): ?>
                                    <script>
                                        add_fright_edit(4);
                                    </script>                            
                                    <?php endif; ?>                            
                                    <?php endif; ?>
                                    <td>
                                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name==$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->account_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                                readonly="true">
                                                <option value="none"></option>
                                                <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_credit_account)? @$edit_cfc[3]->cfc_credit_account==@$value->id ? 'selected':'':'':''); ?>><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]"
                                                autocomplete="off" min="0" onchange="cfc_amount_change(4)" value="<?php echo e(isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('')); ?>" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]"
                                                autocomplete="off" value="<?php echo e(isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_remarks) ? @$edit_cfc[3]->cfc_remarks : old('')) : old('')); ?>">
                                        </td>
                                    </tr>                    
                                    <tr style="display: none;" id="fright_row_5">                        
                                    <?php if(isset($edit_cfc[4])): ?>
                                    <?php if(@$edit_cfc[4]->cfc_amount != ""): ?>
                                    <script>
                                        add_fright_edit(5);
                                    </script>                            
                                    <?php endif; ?>                            
                                    <?php endif; ?>
                                    <td>
                                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                                            <option value=""></option>
                                            <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[4])? !empty(@$edit_cfc[4]->cfc_name)? @$edit_cfc[4]->cfc_name==$value->id ? 'selected':'':'':''); ?> ><?php echo e(@$value->account_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                                readonly="true">
                                                <option value="none"></option>
                                                <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[4])? !empty(@$edit_cfc[4]->cfc_credit_account)? @$edit_cfc[4]->cfc_credit_account==@$value->id ? 'selected':'':'':''); ?>><?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]"
                                                autocomplete="off" min="0" onchange="cfc_amount_change(5)" value="<?php echo e(isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('')); ?>" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]"
                                                autocomplete="off" value="<?php echo e(isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->cfc_remarks) ? @$edit_cfc[4]->cfc_remarks : old('')) : old('')); ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        
                        <div class="row mt-40">
                            <div class="col-lg-12 text-left mb-2">
                                <?php if(count($receiptAdjustments)>0): ?>
                                <b>Adjusted Items</b>
                                    <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;"><?php echo app('translator')->getFromJson('#'); ?></th>
                                                <th style="width:100px;"><?php echo app('translator')->getFromJson('Receipt Number'); ?></th>
                                                <th style="width:100px;"><?php echo app('translator')->getFromJson('Receipt Date'); ?></th>
                                                <th style="width:100px;" class="text-right">Total</th>
                                                <th style="width:100px;" class="text-right">Paid</th>
                                                <th style="width:100px;" class="text-right">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $receiptAdjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(@$loop->iteration); ?></td>
                                                <td><?php echo e(@$item->bi_doc_number); ?></td>
                                                <td><?php echo e(@$item->bi_doc_date); ?></td>
                                                <td class="text-right"><?php echo e(@$item->bi_total); ?></td>
                                                <td class="text-right"><?php echo e(@$item->bi_paid); ?></td>
                                                <td class="text-right"><?php echo e(@$item->bi_balance); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <a type="submit" href="<?php echo e(url('sales-invoice/' . $edit_si->id . '/download')); ?>" class="btn btn-warning"><span class="ti-check"></span><?php echo app('translator')->getFromJson('Print Invoive'); ?></a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        
        
    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>



    
    <form id="po">
        <div class="modal fade admin-query" id="profo_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Invoice Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_profo_id" />
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th><?php echo app('translator')->getFromJson('#'); ?> </th>
                                                    <th><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Unit Price'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Value'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Taxable Amount'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('VAT Amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            <?php echo app('translator')->getFromJson('Close'); ?>
                                        </button>

                                        <button class="btn btn-primary bg-success" type="button" id="addProfoPendingItems">
                                            Add Selected
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    


    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Add Shipping</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control <?php echo e($errors->has('shipping_name') ? 'is-invalid' : ' '); ?>" type="text" id="shipping_name_add" name="shipping_name" value="<?php echo e(isset($editData)?@$editData->shipping_name:old('shipping_name')); ?>" >
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Shipping Name'); ?> <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control <?php echo e($errors->has('contact_name') ? 'is-invalid' : ' '); ?>" type="text" id="contact_name_add" name="contact_name" value="<?php echo e(isset($editData)?@$editData->contact_name:old('contact_name')); ?>" >
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Contact Name'); ?> <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control<?php echo e($errors->has('contact_no') ? ' is-invalid' : ''); ?>" type="number" id="contact_no_add" name="contact_no" value="<?php echo e(isset($editData)?@$editData->contact_no:old('contact_no')); ?>">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Contact No'); ?> <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control<?php echo e($errors->has('address1') ? ' is-invalid' : ''); ?>" type="text" id="address1_add" name="address1" value="<?php echo e(isset($editData)?@$editData->address1:old('address1')); ?>">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Address 1'); ?> <span>*</span> </label>  
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>                                  
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control<?php echo e($errors->has('address2') ? ' is-invalid' : ''); ?>" type="text" id="address2_add" name="address2" value="<?php echo e(isset($editData)?@$editData->address2:old('address2')); ?>">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Address 2'); ?> <span>*</span> </label>    
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>                              
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            <?php echo app('translator')->getFromJson('lang.cancel'); ?>
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save" onclick="return validateAttachForm()">
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="attachment_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Attach File'); ?> <span>*</span> </label>
                                    <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Date'); ?> <span>*</span> </label>
                                    <input class="form-control" type="date" id="att_date" name="att_date" value="<?php echo e(date('Y-m-d')); ?>"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('File Name'); ?> <span>*</span> </label>
                                    <input class="form-control" type="text" id="doc_name" name="doc_name" value=""/>
                                </div>
                            </div>
                            <script>
                                function updateDocName() {
                                    var fileInput = document.getElementById('att_file');
                                    var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                    var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                    document.getElementById('doc_name').value = fileNameWithoutExtension;
                                }
                            </script>
                        </div>
                        
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="att-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 30%;">Date</th>
                                        <th style="width: 50%;">Attachment</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                        <br />

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                            <?php echo app('translator')->getFromJson('Close'); ?>
                                        </button>
                                        <input type="hidden" id="srl_id" />
                                        <button class="btn btn-success" type="button" onclick="add_attachment()">
                                            Add Attachment
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>

    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "<?php echo e(URL::to('add-sales-invoice-attachment')); ?>";
        
        var formData = new FormData();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');  // Append CSRF token
        formData.append('siv_id', $('#si_id').val());
        formData.append('att_date', $('#att_date').val()); // Append other form data
        formData.append('att_file', $('#att_file')[0].files[0]); 
        formData.append('doc_name', $('#doc_name').val());


        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#customer :selected').text() + " " + $('#doc_number').val());
        var action = "<?php echo e(URL::to('view-sales-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                siv_id : $('#si_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function delete_attachment(id){
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('delete-sales-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id : id,
                siv_id : $('#si_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    function popup_profo_pending(id) {
        $("#loading_bg").css("display", "block");
        $("#hd_pending_profo_id").val(id);
        $("#profo_id").val(id);
        document.getElementById('addProfoPending').click();
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer", function () {
        var id = $("#customer").val();
        get_cust_details(id);
    });

    function get_cust_details(id) {
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('get-customer-details')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            $('#payment_terms').val(dataResult['data'][i].payment_terms);
                            $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type);
                            $('#sale_type').val(dataResult['data'][i].sale_type);
                            $('#country').val(dataResult['data'][i].vat_country);
                            $('#state').val(dataResult['data'][i].vat_state);
                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                            $('.vat').val(dataResult['data'][i].vat_percentage);
                        }                        
                    }
                    else{
                        $('#payment_terms').val();
                        $('#shipping_name').val();
                        $('#shipping_address').val();
                        $('#customer_type').val();
                        $('#sale_type').val();
                        $('#country').val();
                        $('#state').val();
                        $('#net_vat').val();
                        $('.vat').val();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    
    function get_profo_list(id) {
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('get-proforma-invoice-for-si')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var doc_number = dataResult['data'][i].doc_number;
                                var option = "<option value='" + id + "'>" + doc_number +
                                    "</option>";
                                var innerHtml =
                                    "<input type='radio' onclick='popup_profo_pending(" + id +
                                    ")' id='pending_grn_" + i +
                                    "' name='pending_grn' value='" + doc_number +
                                    "'> <label for='pending_grn_" + i + "'> " + doc_number +
                                    "</label><br />";

                                $("#plist").append(innerHtml);
                                
                  
                        }                        
                    }
                    else{
                        $("#plist").empty();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }


    function validateAttachForm() {
    var val1 = $("#shipping_name_add").val();
    var val2 = $("#contact_name_add").val();
    var val3 = $("#contact_no_add").val();
    var val4 = $("#address1_add").val();
    var val5 = $("#address2_add").val();

    if (val1 === "") {
        $('.modal_input_validation_1').show();
        $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_1").addClass("red_alert");
        return false;
    }
    if (val2 === "") {
        $('.modal_input_validation_2').show();
        $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_2").addClass("red_alert");
        return false;
    }
    if (val3 === "") {
        $('.modal_input_validation_3').show();
        $(".modal_input_validation_3").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_3").addClass("red_alert");
        return false;
    }
    if (val4 === "") {
        $('.modal_input_validation_4').show();
        $(".modal_input_validation_4").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_4").addClass("red_alert");
        return false;
    }
    if (val5 === "") {
        $('.modal_input_validation_5').show();
        $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_5").addClass("red_alert");
        return false;
    }
    //return true;

    var url = $('#url').val();
    $.ajax({
        type: "POST",
        data: {
            shipping_name: val1,
            contact_name: val2,
            contact_no: val3,
            address1: val4,
            address2: val5
        },

        //url: 'http://syscom.company/venus-erp/shipping-store2',
        //url: 'http://localhost:81/venus-erp/shipping-store2',
        url: url + '/' + 'shipping-store2',
              cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if (response['data'] != null) {
                len = response['data'].length;
                }
                if(len > 0){
                    
                    //$('#shipping_name').find('option').not(':first').remove();

                    for(var i=0; i<len; i++){
                        var id = response['data'][i].id;
                        var name = response['data'][i].shipping_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        //$("#shipping_name").append($(option));
                        //$('#shipping_name').append(new Option(name, id));
                        $("#shipping_name").append(option);
                        $("#vendor").append(option);
                    }
                    
                    alert('Shipping Added Successfully!!');
                        $('#btn_close2').click();
                }
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });


    //preventDefault();
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.masterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
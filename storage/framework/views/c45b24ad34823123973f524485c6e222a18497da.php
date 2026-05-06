<?php $__env->startSection('mainContent'); ?>
    <?php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Delivery Note</h4>

                    <div class="search-filter-container mb-4" id="short-list">
                        
                        <div class="input-group flex-nowrap">
                            <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>                        
                        <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()" style="height: 32px;">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                        
                    </div>

                    <div class="left-nav-list" id="invoice_list">
                        <ul id="short-list-items" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                         <?php if(count($deliverynote)>0): ?>
                         <?php $__currentLoopData = $deliverynote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item <?php echo e($loop->first ? 'active' : ''); ?>" data-id="<?php echo e($value->id); ?>">
                                
                                    <div class="row w-100">
                                        <div class="col-4">
                                            <div class="form-control-plaintext"><?php echo e($value->doc_number); ?></div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text"><?php echo e(@App\SysHelper::com_curr_format($value->amount,2,'.',',')); ?> <?php echo e($value->currency_name->code); ?></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text"><?php echo e($value->accountname->account_code); ?> - <?php echo e($value->accountname->account_name); ?></label>
                                        </div>
                                    </div>
                                
                                </button>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </ul>
                        <div id="long-list" style="display: none;">
                               
                                    <button type="button" class="btn btn-light list_style_search_btn"  onclick="search_box_show_hide()">
                                        <i class="ico icon-outline-magnifer"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_search()">
                                        <i class="ico icon-outline-list-down"></i>
                                    </button>

                            <div class="card mt-3" id="search_box" style="display: none;">
                                <div class="card-body">
                            <div class="row">
                                <div class="col-12">
<?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note', 'method' => 'get', 'id' => 'delivery-note-search'])); ?>

                <div class="row">
    
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Documents Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Customer</label>
                            <select class="form-control js-account-select" name="customer" id="customer">
                                <option value=""></option>
                                
                            </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Supplier</label>
                        <input class="form-control" type="text" autocomplete="off" name="supplier" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Sales Invoice Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="sales_invoice_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">SRT Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="srt" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Date</label>
                        <input class="form-control" type="date" autocomplete="off" name="date" value="">
                    </div>
    
                    <div class="col-1"><br />
                        <button type="submit" class="btn btn-light">
                        <i class="ico icon-outline-magnifer"></i> Filter
                    </button>
                    </div>
                </div>
                <?php echo e(Form::close()); ?>

                                </div>
                            </div>
                                </div>
                            </div>
                        

                            <div class="row">
                                <div class="col-12">
                        <table class="table table-hover mt-2">
                            <thead>
                                <tr>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('DN Date'); ?></th>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('DN No'); ?></th>
                                    <th><?php echo app('translator')->getFromJson('Customer'); ?></th>
                                    <th><?php echo app('translator')->getFromJson('Supplier'); ?></th>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('SIV No'); ?></th>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('SRT No'); ?></th>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('Deal ID'); ?></th>
                                    <th class="text-center"><?php echo app('translator')->getFromJson('Currency'); ?></th>
                                    <th class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                                    <th><i class="ico icon-bold-paperclip"></i></th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php $count =1; ?>
                             <?php $__currentLoopData = $deliverynote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             
                        <?php if($pending_si==1): ?>
                        <?php if(empty($value->invoice_no)): ?>
                        
                        <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                            <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                             <td class="text-center"><a href="<?php echo e(url('delivery-note/'.$value->id.'/view')); ?>" target="_blank"><?php echo e(@$value->doc_number); ?></a></td>
                             <td><?php echo e(@$value->accountname->account_name); ?></td>
                             <td><?php echo e(@$value->supplier_name); ?></td>
                             
                             <!-- Sales Invoice Numbers -->
                            <td class="text-center">
                                <span class="text-dark">Pending</span>
                            </td>

                            <!-- Sales Return Numbers -->
                            <td class="text-center">
                                <?php if(empty($value->srtno)): ?>
                                <span class="text-dark">Pending</span>
                                <?php else: ?>
                                    <?php $__currentLoopData = explode(',', $value->srtno); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(url('get-url-sales-return/' . trim($srt))); ?>" target="_blank"><?php echo e(trim($srt)); ?></a><?php if(!$loop->last): ?>, <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>

                            <!-- Deal Codes -->
                            <td class="text-center">
                                <?php if(empty($value->code)): ?>
                                <span class="text-dark">Pending</span>
                                <?php else: ?>
                                    <?php $__currentLoopData = explode(',', $value->code); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(url('get-url-deal-track/' . trim($code))); ?>" target="_blank"><?php echo e(trim($code)); ?></a><?php if(!$loop->last): ?>, <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                             <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->amount,2,'.',',')); ?></td>
                             <td>
                                <?php if(empty(@$value->attach)): ?>
                                    
                                <?php else: ?>
                                    <?php $__currentLoopData = explode(',', @$value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>
                             <td class="text-center">
                                <?php if(in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6])): ?>
                                    <a class="btn-sm btn-light" href="<?php echo e(url('delivery-note/'.$value->id.'/download/t')); ?>" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                <?php else: ?>
                                    <a class="btn-sm btn-light" href="<?php echo e(url('delivery-note/'.$value->id.'/download')); ?>" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                <?php endif; ?>
                                <a class="btn-sm btn-light" href="<?php echo e(url('delivery-note/'.$value->id.'/edit')); ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <?php if(@$value->status == 2): ?>
                                <a class="btn-sm btn-light" href="<?php echo e(url('delivery-note/'.$value->id.'/restore')); ?>" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                <?php else: ?>
                                <a class="btn-sm btn-light" href="<?php echo e(url('delivery-note/'.$value->id.'/delete')); ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                <?php endif; ?>
                             </td>
                         </tr>
                         <?php endif; ?>

                        <?php else: ?>
                             <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                 <td class="text-center"><a href="<?php echo e(url('delivery-note/'.$value->id.'/view')); ?>" target="_blank"><?php echo e(@$value->doc_number); ?></a></td>
                                 <td><?php echo e(@$value->accountname->account_name); ?></td>
                                 <td><?php echo e(@$value->supplier_name); ?></td>
                                 
                                 <!-- Sales Invoice Numbers -->
                                <td class="text-center">
                                    <?php if(empty($value->invoice_no)): ?>
                                    <span class="text-dark">Pending</span>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', $value->invoice_no); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url('get-url-sales-invoice/' . trim($inv))); ?>" target="_blank"><?php echo e(trim($inv)); ?></a><?php if(!$loop->last): ?>, <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Sales Return Numbers -->
                                <td class="text-center">
                                    <?php if(empty($value->srtno)): ?>
                                    <span class="text-dark">Pending</span>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', $value->srtno); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url('get-url-sales-return/' . trim($srt))); ?>" target="_blank"><?php echo e(trim($srt)); ?></a><?php if(!$loop->last): ?>, <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Deal Codes -->
                                <td class="text-center">
                                    <?php if(empty($value->code)): ?>
                                    <span class="text-dark">Pending</span>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', $value->code); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url('get-url-deal-track/' . trim($code))); ?>" target="_blank"><?php echo e(trim($code)); ?></a><?php if(!$loop->last): ?>, <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                                 <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->amount,2,'.',',')); ?></td>
                                 <td>
                                    <?php if(empty(@$value->attach)): ?>
                                        
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', @$value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>
                                 <td class="text-center">
                                    <?php if(in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6])): ?>
                                    <a class="btn btn-light d-block" href="<?php echo e(url('delivery-note/'.$value->id.'/download/t')); ?>"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                    <?php else: ?>
                                    <a class="btn btn-light d-block" href="<?php echo e(url('delivery-note/'.$value->id.'/download')); ?>"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                    <?php endif; ?>
                                 </td>
                             </tr>
                            <?php endif; ?>

                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                
                <div class="content-container col-9">
                    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                        <script>
                        $(document).ready(function () {
    // Delegated click works for both static + dynamic .data-item
    $(document).on('click', '.data-item', function () {
        
        $("#loading_bg").css("display", "block");

        var id = $(this).data('id');

        // highlight active
        $('.data-item').removeClass('active');
        $(this).addClass('active');

        var action = "<?php echo e(URL::to('delivery-note-details')); ?>/" + id;

        $.ajax({            
            url: action,
            method: 'GET',
            success: function (response) {
                $('#data-details').html(response);
            },
            error: function () {
                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
            },
            complete: function () {
                // always hide loading, success or error
                $("#loading_bg").css("display", "none");
            }
        });
    });
});
                        </script>
                        <script>
$(document).ready(function(){

    $('#search_invoice').on('keyup', function(){
        var query = $(this).val();

        $.ajax({
            url: "<?php echo e(route('delivery-note.search')); ?>",
            type: "GET",
            data: { query: query },
            success: function(data){
                $('#short-list-items').html('');

                if(data.length > 0){
                    $.each(data, function(index, invoice){

                    let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
            <div class="col-4">
                <div class="form-control-plaintext">${invoice.doc_number}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text">
                    ${get_format_date(invoice.doc_date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text">
                    ${Number(invoice.amount).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
            <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.account_code} - ${invoice.account_name}
                </label>
            </div>
        </div>
    </button>
</li>`;
$('#short-list-items').append(ims);
                    });
                } else {
                    $('#short-list-items').html('<div class="p-2">No results found</div>');
                }
            }
        });
    });

});
</script>
                        <?php if(count($deliverynote)>0): ?>
                        
                        


                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                            <?php if(count($deliverynote) > 0): ?>
                                <?php echo $__env->make('backEnd.deliverynote.dn_add_deal_track', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <?php echo $__env->make('backEnd.deliverynote.dn_add_deal_track', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

<script>
  const leftNav = document.querySelector('.left-nav');
  const content = document.querySelector('.content-container');
  const state = localStorage.getItem("leftNavState");
  if (state === "expanded") {
    leftNav.classList.remove('col-3');
    leftNav.classList.add('col-12');
    if (content) {
      content.classList.remove('col-9');
      content.classList.add('col-0');
    }
    $('#short-list').hide();
    $('#short-list-items').hide();
    $('#long-list').show();
  } 
  else if (state === "collapsed") {
    leftNav.classList.remove('col-12');
    leftNav.classList.add('col-3');
    if (content) {
      content.classList.remove('col-0');
      content.classList.add('col-9');
    }
    $('#short-list').show();
    $('#short-list-items').show();
    $('#long-list').hide();
  }
</script>

<script>
$(document).ready(function () {
    $(".list_style_search_btn").on("click", function () {
        $("#search_box").slideToggle(200); // expands/collapses smoothly
    });
});
</script>

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
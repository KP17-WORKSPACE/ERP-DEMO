<?php try { 
    
    ?>
    <style>
        .header-height{
            height: 1rem
        }
        .track-action-btn {
      
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.2);
            color: white;
         
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
           
           
        }
        
        .track-action-btn:hover {
            background: rgba(255,255,255,0.35);
            color: white;
            border-color: rgba(255,255,255,0.6);
        }
         .track-stage-actions {
            display: flex;
            gap: 0.3rem;
            align-items: center;
        }
        .green-track-action-btn {
  
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.15);
    /* background: rgba(0, 128, 0, 0.1); */
    color: #065f46; /* dark green text */
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}



.green-track-stage-actions {
    display: flex;
    gap: 0.3rem;
    align-items: center;
}

    </style>
    <div class="row">

            {{-- Modal Purchase Auto Generate --}}
                            <div class="modal side-panel fade" id="ledgerModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ledgerModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:1100px;width:1100px;left:45rem">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Ledger</h4>
                                            <button type="button" class="btn-close" id="ledger-modal-close"  aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0" id="ledgermodalbody">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                                    {{-- Modal Purchase Auto Generate --}}
                            <div class="modal side-panel fade" id="osModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="osModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:1100px;width:1100px;left:45rem">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Receivable Outstanding</h4>
                                            <button type="button" class="btn-close" id="os-modal-close"  aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0" id="osmodalbody">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

    
            <div class="col p-1" >
                <div class="card mb-3" style="border-radius: 16px" >
                     <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                        @php
                        if ($deal->accounts == 1){
                            $account_status = "bg-success text-white";
                        } else if ($deal->accounts == 2){
                            $account_status = "bg-danger text-white";
                        }
                        else if ($deal->accounts == 3){
                            $account_status = "bg-lightgreen text-dark";
                        } else{
                            $account_status = "bg-lightgreen text-dark";
                            //track-notrequired;
                        }
                    @endphp
                        <tr>
                            <td class="{{ $account_status }} d-flex align-items-center justify-content-between gap-1" style="height:23px">

                              <script>
                                    $(function () {

                                        // Close Ledger Modal
                                        $('#ledger-modal-close').on('click', function () {
                                            const modalEl = document.getElementById('ledgerModal');
                                            const modal = bootstrap.Modal.getInstance(modalEl)
                                                        || new bootstrap.Modal(modalEl);
                                            modal.hide();
                                        });

                                        // Close OS Modal
                                        $('#os-modal-close').on('click', function () {
                                            const modalEl = document.getElementById('osModal');
                                            const modal = bootstrap.Modal.getInstance(modalEl)
                                                        || new bootstrap.Modal(modalEl);
                                            modal.hide();
                                        });

                                        // ESC key close only active modal
                                        $(document).on('keydown', function (e) {
                                            if (e.key === "Escape") {

                                                // Ledger Modal
                                                if ($('#ledgerModal').hasClass('show')) {
                                                    const modal = bootstrap.Modal.getInstance(document.getElementById('ledgerModal'));
                                                    modal.hide();
                                                }

                                                // OS Modal
                                                else if ($('#osModal').hasClass('show')) {
                                                    const modal = bootstrap.Modal.getInstance(document.getElementById('osModal'));
                                                    modal.hide();
                                                }

                                            }
                                        });

                                    });
                              </script>

                                <script>



                                   $(document).ready(function() {
                                        $(document).on('click', '.openLedgerModal', function(e) {
                                            e.preventDefault();
                                            console.log("Ledger modal clicked");

                                            const $form = $(this).closest('form'); 
                                            const $modalBody = $("#ledgermodalbody");
                                            const $loader = $("#loading_bg");

                                            // Show loader
                                            $modalBody.children().not($loader).remove();
                                            $loader.show();

                                            // Build FormData manually to ensure arrays are sent
                                            const formData = new FormData();

                                            // Append all account_id[] values
                                            $form.find('input[name="account_id[]"]').each(function() {
                                                formData.append('account_id[]', $(this).val());
                                            });

                                            // Append other inputs
                                            formData.append('from_date', $form.find('input[name="from_date"]').val());
                                            formData.append('to_date', $form.find('input[name="to_date"]').val());
                                            formData.append('redirect_by_dealtrack', 1);

                                            // Send AJAX
                                            $.ajax({
                                                url: $form.attr('action'),
                                                type: $form.attr('method') || 'POST',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                success: function(html) {
                                                    $loader.hide();
                                                    $modalBody.html(html);
                                                    $("#ledgerModal").modal("show");
                                                },
                                                error: function(xhr) {
                                                    $loader.hide();
                                                    console.error("Failed to load modal:", xhr.responseText);
                                                    $modalBody.html('<div class="alert alert-danger">Failed to load ledger. Try again.</div>');
                                                    $("#ledgerModal").modal("show");
                                                }
                                            });
                                        });
                                    });

                                       $(document).ready(function() {
                                        $(document).on('click', '.openOSModal', function(e) {
                                            e.preventDefault();

                                            const $form = $(this).closest('form'); 
                                            const $modalBody = $("#osmodalbody");
                                            const $loader = $("#loading_bg");

                                            // Show loader
                                            $modalBody.children().not($loader).remove();
                                            $loader.show();

                                            // Build FormData manually to ensure arrays are sent
                                            const formData = new FormData();

                                            // Append all account_id[] values
                                            $form.find('input[name="account_id[]"]').each(function() {
                                                formData.append('account_id[]', $(this).val());
                                            });

                                            // Append other inputs
                                            formData.append('till_date', $form.find('input[name="till_date"]').val());
                                            formData.append('redirect_by_dealtrack', 1);

                                            // Send AJAX
                                            $.ajax({
                                                url: $form.attr('action'),
                                                type: $form.attr('method') || 'POST',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                success: function(html) {
                                                    $loader.hide();
                                                    $modalBody.html(html);
                                                    $("#osModal").modal("show");
                                                },
                                                error: function(xhr) {
                                                    $loader.hide();
                                                    console.error("Failed to load modal:", xhr.responseText);
                                                    $modalBody.html('<div class="alert alert-danger">Failed to load ledger. Try again.</div>');
                                                    $("#osModal").modal("show");
                                                }
                                            });
                                        });
                                    });

                                    

                                </script>

                                <script>
(function () {

    let dragging = false;
    let startX, startY, startLeft, startTop;
    let currentModal = null;

    // Bind drag start
    $(document).on('mousedown', '.modal-dialog.draggable .modal-header', function (e) {
        currentModal = $(this).closest('.modal-dialog');

        dragging = true;

        startX = e.clientX;
        startY = e.clientY;

        const offset = currentModal.offset();
        startLeft = offset.left;
        startTop = offset.top;

        $('body').addClass('unselectable'); // Prevents text selection while dragging
    });

    // Dragging movement
    $(document).on('mousemove', function (e) {
        if (!dragging || !currentModal) return;

        let newLeft = startLeft + (e.clientX - startX);
        let newTop = startTop + (e.clientY - startY);

        currentModal.offset({
            top: newTop,
            left: newLeft
        });
    });

    // Stop drag
    $(document).on('mouseup', function () {
        dragging = false;
        $('body').removeClass('unselectable');
    });

    // Reset modal on open (production behavior)
    $(document).on('show.bs.modal', '.modal', function () {
        let dialog = $(this).find('.modal-dialog.draggable');
        dialog.css({
            top: '10%',
            left: '65%',
            transform: 'translateX(-50%)'
        });
    });

})();
</script> 

                            


                        

                          
                                <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                                    <b>Accounts</b>
                                    @if (App\SysHelper::get_company_status($del->customername) != 0 || 1)
                                        @if(App\SysHelper::account_approval_access() && in_array($deal->accounts,[0,2,3,1]) && ($deal->sales ==0))
                                            <a class="btn-md light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalAccount">
                                                <i class="ico icon-outline-pen-new-square title-15 {{ $account_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Accounts Approval" style="font-size: 12px"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                  <div class="track-stage-actions">
                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'generalledger', 'target' => '_blank', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                        <input type="hidden" id="account_id_ledger" name="account_id[]" value="{{ @$account_id->id }}" />
                                        <input type="hidden" id="from_date_ledger" name="from_date" value="{{ date('Y-01-01') }}" />
                                        <input type="hidden" id="to_date_ledger" name="to_date" value="{{ date('Y-m-d') }}" /> 
                                       
                                         <button class="@if($deal->accounts != 1 && $deal->accounts != 2) green-track-action-btn @else track-action-btn  @endif  openLedgerModal" title="View Ledger">Ledger</button>
                                        {{ Form::close() }}

                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receivable-outstanding', 'target' => '_blank', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                        <input type="hidden" name="account_id[]" value="{{ @$account_id->id }}" />
                                        <input type="hidden" name="till_date" value="{{ date('Y-m-d') }}" />
                                        <button class="@if($deal->accounts != 1 && $deal->accounts != 2) green-track-action-btn @else track-action-btn  @endif openOSModal" title="Receivable Outstanding" style="font-size:11px"> OS</button>                    
                                        {{ Form::close() }}
                                  </div>

                          
                            </td>
                       </tr>
                        
                        @if (count($accounts) > 0)
                            @foreach ($accounts as $val)
                        <tr ><td  class="text-start truncate-text-custom"> <span class="fw-bold">Customer Status</span>  : @if ($val->customer_status == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    @elseif($val->customer_status == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    @else
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            @endif </td></tr>
                        <tr><td class="text-start truncate-text-custom"> <span class="fw-bold">Credit Limit</span> : @if ($val->credit_limit == 1)
                                    Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                @elseif($val->credit_limit == 2)
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                @else
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        @endif</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Terms</span> : @if ($val->payment_terms == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            @elseif($val->payment_terms == 2)
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            @else
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Overdue Payment</span> : @if ($val->pending_payment == 1)
                                No <i class="ico icon-outline-check-read title-15 text-success"></i>
                            @elseif($val->pending_payment == 2)
                                Yes <i class="ico icon-outline-close text-danger"></i>
                            @else
                                Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        @endif</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Other</span> :  @if ($val->other == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            @elseif($val->other == 2)
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            @else
                                Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        @endif 
                    </td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : {!! $val->remarks ?: 'No remarks' !!}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> : {{ $val->createdby->full_name }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }} </td></tr>
                        @endforeach
                        @endif


                    
                       
                    </table>



                    @if(($deal->accounts==0 || $deal->accounts==3) && (Auth::user()->role_id == 27 || Auth::user()->role_id == 28 || Auth::user()->role_id == 1))
                            <a class="text-danger float-center text-center m-2" style="font-size:12px" onclick="acc_updiv()">Set Pending</a>

                            <?php $pendng = App\SysCrmDealTrackApprovalAccountsPending::where('deal_id',$deal->id)->get(); ?>
                            @if(count($pendng)>0)

                                <br style="clear:both;" />
                          @foreach($pendng as $p)
                                <div class="pending-item mb-3 pb-2 border-bottom">
                                    <p class="mb-1 ">{{ $p->remarks }}</p>
                                    <div class="text-muted small">
                                        <span>By: {{ $p->createdby->full_name }}</span> <br>
                                        <span>{{ date('d/m/Y h:i A', strtotime($p->created_at)) }}</span>
                                    </div>
                                </div>
                            @endforeach

                            @endif

                            
                        @endif
                    <div>

                        

                        

                        
{{-- 
                            <div class="border border-primary rounded bg-white text-sm p-3" id="acc_div_update" style="display: none;">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <select class="form-control mb-1" name="acc_status" required>
                                    <option value="" selected>-Select-</option>
                                    <option value="3">Pending</option>
                                    <option value="0">Remove Pending</option>
                                </select>
                                <textarea class="form-control mb-1" name="acc_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                <input type="hidden" name="acc_deal_id" value="{{ $deal->id }}" />
                                <button class="btn btn-xs btn-primary">Update</button>
                                {{ Form::close() }}
                            </div> --}}
                            
                            <div class="modal side-panel fade" id="acc_div_update" data-bs-backdrop="false" tabindex="-1"
                                aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm" style="height: 464px !important;">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                  
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editUpdateInvoice">Update Invoice</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0">
                                            <div class="card mb-0 mt-0">
                                                <div class="card-body">
                                                       <select class="form-control mb-1" name="acc_status" required>
                                    <option value="" selected>-Select-</option>
                                    <option value="3">Pending</option>
                                    <option value="0">Remove Pending</option>
                                </select>
                                <textarea class="form-control mb-1" name="acc_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                <input type="hidden" name="acc_deal_id" value="{{ $deal->id }}" />
                               
                                                        
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                                            </button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                            <script>
                                function acc_updiv() {
                                         $("#acc_div_update").modal("show");

                                    // if($('#acc_div_update').css('display') == 'none'){
                                    //     $("#acc_div_update").css("display", "block");
                                    // }
                                    // else{
                                    //     $("#acc_div_update").css("display", "none");
                                    // }
                                }
                            </script>

                    </div>

                </div>
            </div>
            <div class="col p-1">
                <div class="card mb-3" style="border-radius: 16px">
                     <table class="detail-item-table-sm" width="100%"  style="table-layout: fixed;width:100%">
                        @php
                        if ($deal->sales == 1){
                            $sales_status = "bg-success text-white";
                        } else if ($deal->sales == 2){
                            $sales_status = "bg-danger text-white";
                        }
                        else if ($deal->sales == 3){
                            $sales_status = "bg-lightgreen text-dark";
                        } else{
                            $sales_status = "bg-lightgreen text-dark";
                            //track-notrequired;
                        }
                    @endphp
                     <tr>
                       

                        <td class="{{ $sales_status }} d-flex align-items-center justify-content-start gap-1" style="height:23px">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1">

                            <b>Sales</b>   
                             @if(App\SysHelper::sales_approval_access() && $deal->accounts==1 && in_array($deal->sales,[0,2,3,1]) && (($deal->purchease==0 || in_array($deal->purchease_approval,['0',''])) && ($deal->invoice==0 || in_array($deal->invoice_approval,['0',''])) && ($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) && ($deal->receivables==0 || in_array($deal->receivables_approval,['0','']))
                        ))
                        <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalSales" title="Sales Approval"><i class="ico icon-outline-pen-new-square title-15 {{ $sales_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}"  style="font-size: 12px"></i></a>
                        @endif
                        </div>
                        
                        </td>                
                    </tr>

                        @if (count($sales) > 0)
                            @foreach ($sales as $val)
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Margin</span> : @if ($val->margin == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    @elseif($val->margin == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    @else
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            @endif</td></tr>
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Stock</span> : @if ($val->stock == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    @elseif($val->stock == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    @else
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            @endif</td></tr>

                             <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Purchase Quote</span> : @if ($val->purcease_quote == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            @elseif($val->purcease_quote == 2)
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            @else
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                        @endif

                         <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Other</span> : @if ($val->other == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            @elseif($val->other == 2)
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            @else
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i></td></tr>
                        @endif

                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Approval</span> : @if ($val->purchase_approval == 1) Required
                            @elseif($val->purchase_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>

                                        @php
    $statuses = [];

    if ($val->invoice_approval == 1) $statuses[] = 'R';
    elseif ($val->invoice_approval == 2) $statuses[] = 'NR';

    if ($val->delivery_approval == 1) $statuses[] = 'R';
    elseif ($val->delivery_approval == 2) $statuses[] = 'NR';

    if ($val->receivables_approval == 1) $statuses[] = 'R';
    elseif ($val->receivables_approval == 2) $statuses[] = 'NR';
@endphp
@php
    $popoverContent = '
        <strong>Invoice Approval:</strong> ' . 
            ($val->invoice_approval == 1 ? 'Required' : ($val->invoice_approval == 2 ? 'Not Required' : '')) . '<br>
        <strong>Delivery Approval:</strong> ' . 
            ($val->delivery_approval == 1 ? 'Required' : ($val->delivery_approval == 2 ? 'Not Required' : '')) . '<br>
        <strong>Receivables Approval:</strong> ' . 
            ($val->receivables_approval == 1 ? 'Required' : ($val->receivables_approval == 2 ? 'Not Required' : '')) . '
    ';
@endphp

 <tr data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                                data-bs-html="true"
                                data-bs-content="{!! $popoverContent !!}"
                            data-bs-placement="top"><td class="text-start truncate-text-custom"><span class="fw-bold">SI-DO-REC Approval</span> : 
                                {{ implode('-', $statuses) }}
                        </td></tr>

                         {{-- <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Invoice Approval</span> : @if ($val->invoice_approval == 1) Required
                            @elseif($val->invoice_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Approval</span> : @if ($val->delivery_approval == 1) Required
                            @elseif($val->delivery_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receivables Approval</span> : @if ($val->receivables_approval == 1) Required
                            @elseif($val->receivables_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr> --}}

                        @if ($val->remarks != '')
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : {!! $val->remarks !!}</td></tr>
                        @endif
                          <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                {{ $val->createdby->full_name }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</td></tr>
                      

                            @endforeach
                        @endif
                        
                      

                     </table>
                    <div>

                    </div>

                </div>
            </div>
            <div class="col p-1 ">
                <div class="card mb-3" style="border-radius: 16px">
                <?php $check_po_pending = App\SysDealPurchaseOrderItems::where('deal_id',$deal->deal_id)->where('status',1)->where('cart_id', session('logged_session_data.cart_id'))->count(); ?>
                <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    @php
                    
                    if ($deal->purchease == 1){
                        if ($deal->purchease_approval == 0){
                            $purchease_status = "bg-secondary text-white";
                        }
                        else {
                            $purchease_status = "bg-success text-white";
                        }
                    }
                    elseif ($deal->purchease == 2){
                        $purchease_status = "bg-danger text-white";
                    }
                    elseif ($deal->purchease == 3){
                        $purchease_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->purchease == 4){
                        $purchease_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $purchease_status = "bg-lightgreen text-dark";
                    }

                   
                    @endphp
                    <tr>
                       

                     <td class="{{ $purchease_status }} d-flex align-items-center justify-content-between gap-1" style="height:23px">


                         <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                            <b>Purchase</b>   
                        @if(App\SysHelper::purchase_approval_access() && $deal->accounts==1 && $deal->sales==1 && in_array($deal->purchease,[0,2,3,1]) && (count($invoice)==0 && ($deal->invoice==0 || $deal->invoice==3 || $deal->invoice==1) ) && (($deal->invoice==0 || in_array($deal->invoice_approval,['0',''])) || ($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) || ($deal->receivables==0 || in_array($deal->receivables_approval,['0','']))))
                       
                            @if ($deal->purchease_approval == 1)
                                <a data-bs-toggle="modal" data-bs-target="#modalPurchase" class="btn-md light" title="Purchase Approval"><i class="ico icon-outline-pen-new-square title-15 {{ $purchease_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" style="font-size: 12px" aria-hidden="true"></i></a>                                
                            @endif
                            
                        @elseif(App\SysHelper::purchase_approval_access() && ($deal->purchease == 3 || $deal->purchease == 4))
                    
                        <a class="btn-md light" data-bs-toggle="modal" data-bs-target="#modalPurchase" title="Purchase Approval"><i style="font-size:16px" class="ico icon-outline-pen-new-square  {{ $purchease_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" style="font-size: 12px" aria-hidden="true"></i></a>
                        @endif
                         </div>

                        <div class="track-stage-actions">
                        <button type="button" title="View Purchase" class="@if($deal->purchease != 1 && $deal->purchease != 2) green-track-action-btn @else track-action-btn  @endif  d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#purchase_auto_generate" data-bs-toggle="modal"> <svg @if($deal->purchease != 1 && $deal->purchease != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> PO</button>

                           @if (count($check_po) > 0)

                                <?php $po_item_count =  App\SysPurchaseOrderItems::where('po_id',$check_po->pluck('id'))->sum('qty'); ?>

                                @if($quoteitems->sum('po_qty') < $quoteitems->sum('qty'))
                                    <button type="button" title="Purchase Order Pending List" class="@if($deal->purchease != 1 && $deal->purchease != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#po_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg @if($deal->purchease != 1 && $deal->purchease != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                @endif


                          @elseif ($check_po_pending == 0)
                                    <div>
                                        <button  type="button" title="Purchase Order Pending List" class="@if($deal->purchease != 1 && $deal->purchease != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#po_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg @if($deal->purchease != 1 && $deal->purchease != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                        <?php /*{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-purchase-order-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                            <input type="hidden" name="po_deal_id" value="{{ $deal->deal_id }}" />                                
                                            <button class="btn-sm btn-info p-0" style="width: 100px; float: right;">Generate PO</button>
                                        {{ Form::close() }} */ ?>
                                    </div>
                          @else
                                    <div>
                                        <button title="Purchase Order Pending List"  type="button" class="@if($deal->purchease != 1 && $deal->purchease != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1"  onclick="window.location.href='../purchase-order/create/{{ $del->customername->name }}/{{ $del->ownername->full_name }}/{{ $deal->deal_id }}/{{ $deal->deal_code->code }}'"><svg @if($deal->purchease != 1 && $deal->purchease != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                    </div>
                         @endif

                        </div>


                       

                        </td>
                    </tr>

                        @if (count($purchease) > 0)
                            @foreach ($purchease as $val)
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Quote</span> : @if ($val->purchease_quote == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    @elseif($val->purchease_quote == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    @else
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                                    @endif

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Quote Request</span> : @if ($val->three_quote_request == 1)
                                    Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                @elseif($val->three_quote_request == 3)
                                    Not Required <i class="ico icon-outline-check-read title-15 text-success"></i>
                                @elseif($val->three_quote_request == 2)
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                @else
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i>
                                    </td></tr>
                        @endif
                        
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Status</span> : @if ($val->validation == 1)
                                Purchase Completed <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            @elseif($val->validation == 3)
                                Under Purchase <i class="ico  icon-outline-clock-circle text-warning" aria-hidden="true"></i>
                            @elseif($val->validation == 4)
                                Partial Delivery <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                <p class="my-1 mb-1"><b>Partial Delivery Note</b> : {!! $val->partial_delivery_note !!}</p>
                            @elseif($val->validation == 2)
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            @else
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> </td>
                        @endif
                        </tr>
                        
                        @if($val->lpo_no != "")
                        <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">LPO No</span> : {{ $val->lpo_no }}</td></tr>@endif
                        
                        
                        @if($val->part_no != "")
                        <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Part No</span> : {{ $val->part_no }}</td></tr>
                        @endif
                        @if($val->supplier_name != "")
                        <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Supplier Name</span> : {{ $val->supplier_name }}</td></tr>
                        @endif


                        @php
                            $grnStatus = App\SysHelper::get_deal_track_grn_status($deal->id);
                        @endphp


                       @if ($grnStatus != "")
                            <tr>
                                <td class="text-start truncate-text-custom">
                                    {!! $grnStatus !!}
                                </td>
                            </tr>
                        @endif

                       
                        @if ($val->delivery_date != '' && $val->delivery_date != '1970-01-01')
                            <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Expected Delivery</span> : {{ date('d/m/Y', strtotime($val->delivery_date)) }}
                                </td>
                            </tr>
                        @endif
                        


                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Other</span> : @if ($val->other == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            @elseif($val->other == 2)
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            @else
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i></td> </tr>
                        @endif

                        @if (App\SysHelper::purchase_approval_access())
                            @if ($val->fileone != '')
                                <p class="my-1 mb-1"><a class="btn-sm text-white btn-primary"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->fileone }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Quote 1</a>
                                </p>
                            @endif
                            @if ($val->filetwo != '')
                                <p class="my-1 mb-1"><a class="btn-sm text-white btn-primary"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->filetwo }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Quote 2</a>
                                </p>
                            @endif
                            @if ($val->filethree != '')
                                <p class="my-1 mb-1"><a class="btn-sm text-white btn-primary"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->filethree }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Quote 3</a>
                                </p>
                            @endif
                        @endif
                        

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span>  : {!! $val->remarks !!}</td></tr>
                          <tr>
                             <td class="text-start truncate-text-custom"><span class="fw-bold">Created By </span> :
                                {{ $val->createdby->full_name }}</td>
                            </tr>   
                       

                            <tr>
                                <td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</td>
                            </tr>
                        
                       
                        @endforeach
                        @endif



                       <tr>
                         <td class=" bg-white d-flex align-items-center gap-2 flex-wrap" style="border-radius: 16px">
                      
                             @if ($deal->purchease_approval != 1)
                          
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchase-not-required', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="purchase_not_required_deal_id" value="{{ $deal->deal_id }}" />
                                <button class="btn-danger text-truncate border-0"><i class="ico icon-outline-pen-new-square text-white" style="font-size: 12px"  aria-hidden="true"></i> Not Required</button>
                                {{ Form::close() }}

                            @endif

                            @if (count($check_po) > 0)

        

                            
            <script>
                 $(document).ready(function() {
                    $(document).on('click', '.po-item', function() {
                        var id = $(this).data('id');
                      
                        $('.po-item').removeClass('active');
                        $('.po-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                  

                        var action = "{{ URL::to('purchase-details-pdf') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                    $('#poViewModalbody').html(response);   // load inside modal
                                    $('#poViewModal').modal('show');             
                            },
                            error: function() {
                                $('#po-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




                                <div style="float: right;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                @foreach ($check_po as $po)<a class="btn-sm btn-light po-item" data-id="{{ $po->id }}" style="font-size: 10px;padding: 2px 2px;">&nbsp;{{ $po->doc_number }}&nbsp;</a>
                                @endforeach
                                </div>


                            @endif
            
                        </td>
                       </tr>



                </table>
                

                </div>
            </div>
            <div class="col p-1 ">
                <div class="card mb-3" style="border-radius: 16px">
                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    @php
                    
                    if ($deal->invoice == 1){
                            $invoice_status = "bg-success text-white";
                    }
                    elseif ($deal->invoice == 2){
                        $invoice_status = "bg-danger text-white";
                    }
                    elseif ($deal->invoice == 3){
                        $invoice_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->invoice == 4){
                        $invoice_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $invoice_status = "bg-lightgreen text-dark";
                    }

                    @endphp
                    
                <tr>

                      <td class="{{ $invoice_status }} d-flex align-items-center justify-content-between gap-1" style="height:23px">

                        <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                            <b>Invoice</b>     
                            
                      @if(
    App\SysHelper::invoice_approval_access() && 
    (
        (
            $deal->sales==1 &&
            $deal->accounts==1 &&
            in_array($deal->purchease,[1,4]) &&
            in_array($deal->invoice,[0,2,3,1]) &&
            ($deal->delivery==0 || count($delivery) == 0) &&
            (($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) ||
             ($deal->receivables==0 || in_array($deal->receivables_approval,['0','']))) &&
            $deal->invoice_approval == 1
        )
        ||
        ($del->is_partial_invoice==1)
    )
)
    <a class="btn-md btn-light" title="Invoice Approval" style="display: contents;"
        data-bs-toggle="modal" data-bs-target="#modalInvoice">
        <i class="ico icon-outline-pen-new-square title-15 {{ $invoice_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" 
           style="font-size: 12px"></i>
    </a>
@endif

                        </div>
                        <div class="track-stage-actions">
                        <?php $check_si_pending = App\SysDealSalesInvoiceItems::where('deal_id',$deal->deal_id)->where('status',1)->where('cart_id', session('logged_session_data.cart_id'))->count(); ?>
                        @if (count($check_si) > 0)
                            <?php $si_item_count =  App\SysSalesInvoiceItems::wherein('si_id',$check_si->pluck('id'))->sum('qty'); ?>
                            @if($si_item_count < $quoteitems->sum('qty'))
                            
                            <button type="button" title="Sales Invoice Pending List"  class="@if($deal->invoice != 1 && $deal->invoice != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#si_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"> <svg  @if($deal->invoice != 1 && $deal->invoice != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</button>
                                
                            @endif

                        @elseif ($check_si_pending == 0)
                            @if ($deal->invoice_approval == 1)
                                <div style="float: right;">
                                    <button type="button" title="Sales Invoice Pending List"  class="@if($deal->invoice != 1 && $deal->invoice != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#si_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"> <svg  @if($deal->invoice != 1 && $deal->invoice != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</button>
                                </div>
                            @endif
                        @else
                            @if ($deal->invoice_approval == 1)
                                <div style="float: right;">
                                    <a type="button" title="Sales Invoice Pending List" class="@if($deal->invoice != 1 && $deal->invoice != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" href="../sales-invoice/create/{{ $del->customername->name }}/{{ $del->ownername->full_name }}/{{ $deal->deal_id }}/{{ $deal->deal_code->code }}"><svg @if($deal->invoice != 1 && $deal->invoice != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</a>
                                </div>
                            @endif
                        @endif
                        </div>


                       



                        </td>
                    
                    
                </tr>

                    


                    @if (count($invoice) > 0)
                            @foreach ($invoice as $val)
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Advice</span> : @if ($val->delivery_advice == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    @elseif($val->delivery_advice == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    @else
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            @endif
                        </td></tr>

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Validation</span> : @if ($val->validation == 1)
                                    Approved  <i class="ico icon-outline-check-read title-15 text-success"></i>
                                @elseif($val->validation == 2)
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                @else
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                        </td></tr>

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Hold</span> : @if ($val->hold == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            @elseif($val->hold == 2)
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            @else
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                        </td></tr>

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Print</span> : @if ($val->print == 1)
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            @elseif($val->print == 2)
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            @else
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                        </td></tr>

                        <tr><td  class="text-start">
                        @if ($val->invoice_no != '')
                           <span class="fw-bold">Invoice No</span> : {{ $val->invoice_no }}
                        @endif
                        
                        @if(Auth::user()->role_id == 1)
                        <a class="text-danger float-right" onclick="updiv()"><i class="ico icon-outline-pen-new-square title-15 text-danger"></i> </a>
                        @endif
                        </td></tr>

                            
                        
                        @if ($val->partial_invoice != 0)
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Partial Invoice</span> : Yes</td></tr>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Partial Invoice Amount</span> : {{ $val->partial_invoice_amount }}</td></tr>
                        @endif

                        @if ($val->remarks != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : {!! $val->remarks !!}</td></tr>
                        @endif
                          <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                {{ $val->createdby->full_name }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</td></tr>
                      
                        @endforeach
                        @endif

                        <tr>

                    <td class="bg-white d-flex align-items-center gap-1 flex-wrap" style="border-radius: 16px">
                        {{-- <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square title-15 text-danger" style="font-size: 16px"></i></a> --}}

                        
                      

                        @if ($deal->invoice == 1 && session('logged_session_data.company_id')==2)
                            @if ($deal->invoice_approval == 1)
                       
                                <div>
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-clearance-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    <input type="hidden" name="clearance_deal_id" value="{{ $deal->deal_id }}" />
                                    <button class="btn-primary text-truncate border-0">Clearance</button>
                                {{ Form::close() }}
                                </div>                                    
                            @endif
                        @endif
                        
                        @if (count($check_cl) > 0)
                        @foreach ($check_cl as $cl)<a class="btn-sm btn-light" style="font-size: 10px;padding: 2px 2px;" href="{{url('clearance/'.$cl->id.'/download')}}" target="_blank">&nbsp;{{ $cl->invoice_no }}&nbsp;</a>
                        @endforeach
                        @endif

                      
                        @if (count($check_si) > 0)
                          


                            @if(count($list_sales_invoice)>0)  
                            
                               <script>
                                    $(document).ready(function() {
                                        $(document).on('click', '.si-data-item', function() {
                                            var id = $(this).data('id');



                                        

                                            var action = "{{ URL::to('sales-invoice-pdf') }}/" + id;


                                            $('#loading_bg').show();

                                            $.ajax({
                                                url: action,
                                                method: 'GET',
                                                success: function(response) {
                                                            $('#siViewModalbody').html(response);   // load inside modal
                                                            $('#siViewModal').modal('show');   
                                                },
                                                error: function() {
                                                    $('#data-details').html(
                                                        '<p class="text-danger">No Details Available.</p>');
                                                },
                                                complete: function() {
                                                    $('#loading_bg').hide(); // Always hide loader after request completes
                                                }
                                            });
                                        });
                                    });
                                </script>

                            @foreach($list_sales_invoice as $list)
                                <a class="btn-sm btn-light si-data-item" data-id="{{$list->id}}" style="font-size: 10px;padding: 2px 2px;" >{{ $list->doc_number }}</a>
                            @endforeach
                            @endif
                            

                        
                        @endif
                                                        
                    </td>

                        </tr>


                    </table>
                    {{-- <div class="border border-primary rounded bg-white text-sm p-2" id="div_update" style="display: none;">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="text" class="form-control mb-1" name="inv_no" placeholder="Invoice No" required />
                                <textarea class="form-control mb-1" name="inv_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                <input type="hidden" name="inv_id" value="{{ $val->id }}" />
                                <button id="btn_edit_stage" class="btn btn-xs btn-primary">Update Invoice</button>
                                {{ Form::close() }}
                            </div> --}}

                             <div class="modal side-panel fade" id="div_update" data-bs-backdrop="false" tabindex="-1"
                                aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm" style="height: 464px !important;">
                                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editUpdateInvoice">Update Invoice</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0">
                                            <div class="card mb-0 mt-0">
                                                <div class="card-body">
                                                        <input type="text" class="form-control mb-1" name="inv_no" placeholder="Invoice No" required />
                                                        <textarea class="form-control mb-1" name="inv_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                                        <input type="hidden" name="inv_id" value="@isset($val){{ $val->id }}@endisset" />
                                                       
                                                        
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                                            </button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>

                            <script>
                                function updiv() {
                                     $("#div_update").modal('show');
                                    // if($('#div_update').css('display') == 'none'){
                                    //     $("#div_update").css("display", "block");
                                    // }
                                    // else{
                                    //     $("#div_update").css("display", "none");
                                    // }
                                }
                            </script>



                </div>
            </div>
            <div class="col p-1">
                <div class="card mb-3" style="border-radius: 16px">
                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    @php
                    if ($deal->delivery == 1){
                        if ($deal->delivery_approval == 0){
                            $delivery_status = "track-notrequired";
                        } else {
                            $delivery_status = "bg-success text-white";
                        }
                    }
                    else if ($deal->delivery == 2){
                        $delivery_status = "bg-danger text-white";
                    }
                    elseif ($deal->delivery == 3){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 5){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 4){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 6){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $delivery_status = "bg-lightgreen text-dark";
                    }

                    @endphp
                        <tr>
                            <td class="{{$delivery_status}} d-flex align-items-center justify-content-between gap-1" style="height:23px">
                                
                                <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                                <b>Delivery</b>  
                                
                                @if(App\SysHelper::delivery_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && in_array($deal->delivery,[0,2,3,1,4,5,6]) && ($deal->receivables==0 || count($receivables) == 0))
                        
                                        @if ($deal->delivery_approval == 1)
                                        <a class="btn-md btn-light" title="Delivery Approval" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalDelivery"><i class="ico icon-outline-pen-new-square title-15 {{ $delivery_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" style="font-size: 12px"></i></a>
                                            @endif

                                    @endif
                                </div>

                                  <?php $po_check = 0;
                            if(count($poitems)>0){
                                if ($poitems->sum('dn_qty') < $poitems->sum('qty')){
                                    $po_check = 1;
                                }
                            }
                        ?>


                       

                        <div class="track-stage-actions">
                        <button title="View Delivery Note" class="@if($deal->delivery != 1 && $deal->delivery != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" type="button"  onclick="window.location.href='{{ url('delivery-note-add-deal/'.$deal->deal_id) }}'" > <svg @if($deal->delivery != 1 && $deal->delivery != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> DO</button>
 @if (($quoteitems->sum('dn_qty') < $quoteitems->sum('qty')) || ($po_check == 1 ))
                           @if($quoteitems->sum('dn_qty')==0)    
                     <button title="Delivery Note Pending List" type="button" class="@if($deal->delivery != 1 && $deal->delivery != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#dln_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg @if($deal->delivery != 1 && $deal->delivery != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> DLN </button>
                                      
                             
                            @endif
                        @endif
                        </div>

                        
                        </td>
                    </tr>


                        @if (count($delivery) > 0)
                            @foreach ($delivery as $val)
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">DO Status</span> : @if ($val->do_status == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    @elseif($val->do_status == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    @else
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> 
                            @endif
                            </td></tr>

                            @if ($val->do_no != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Do No</span> : {{ $val->do_no }}</td></tr>
                            @endif

                            @if ($val->print_invoice_no != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Print Invoice No</span> : {{ $val->print_invoice_no }}</td></tr>
                            @endif

                            @if ($val->cheque_collection != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Collection</span> : @if ($val->cheque_collection == 1)
                                        Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    @elseif($val->cheque_collection == 2)
                                        Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    @else
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                    @endif
                                </td></tr>
                            @endif

                            @if ($val->cheque_collection_file != '')
                                <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->cheque_collection_file }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Cheque
                                        Copy</a></td></tr>
                            @endif

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Status</span> : @if ($val->delivery_status == 1)
                                    Delivery Completed <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                @elseif($val->delivery_status == 2)
                                    Pending For Delivery <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                @elseif($val->delivery_status == 4)
                                    Ready For Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                @elseif($val->delivery_status == 3)
                                    Out For Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                @elseif($val->delivery_status == 5)
                                    Partial Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> <a data-bs-toggle="modal" data-bs-target="#modalUpdateItems" class="btn btn-danger p-0">&nbsp;Update Items&nbsp;</a>
                                
                                @endif
                                </td></tr>

                        @if ($val->deliver_by != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivered Through</span> : {{ $val->deliver_by }}
                                @if ($val->driver != '')
                                    , {{ $val->driver }}
                            </td></tr>
                        @endif
                        @endif

                        @if ($val->cash_collected != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cash Collected</span> : {{ $val->cash_collected }}</td></tr>
                        @endif

                        @if ($val->id_no != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">ID No</span> : {{ $val->id_no }}</td></tr>
                        @endif
                        @if ($val->contact_no != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Contact No</span> : {{ $val->contact_no }}</td></tr>
                        @endif
                        @if ($val->awb_no != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">AWB No</span> : {{ $val->awb_no }}</td></tr>
                        @endif
                        @if ($val->attach_file != '')
                           <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                    href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->attach_file }}"
                                    target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Attachment</a>
                            </td></tr>
                        @endif

                        @if ($val->remarks != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : {!! $val->remarks !!}</td></tr>
                        @endif
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                {{ $val->createdby->full_name }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</td></tr>
                       
                        @endforeach
                        @endif



                      

     <?php $delivery_note = App\SysDeliveryNote::where('deal_id',$deal->deal_id)->get(); ?>

     @if ($delivery_note->isNotEmpty())
         
    
                        
                            <tr class="bg-white d-flex align-items-center gap-1 flex-wrap">

                
               
                      
                
               

                   
                         <td>
                        <div>
                                <script>
                $(document).ready(function() {
                    $(document).on('click', '.dln-item', function() {
                        var id = $(this).data('id');

                        

                        var action = "{{ URL::to('delivery-note-pdf') }}/" + id;



                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                 $('#dlnViewModalbody').html(response);   // load inside modal
                                $('#dlnViewModal').modal('show');  
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>
                            @foreach ($delivery_note as $dn)<a class="btn-sm btn-light dln-item" data-id="{{$dn->id}}" style="font-size: 10px;padding: 2px 2px;">&nbsp;{{ $dn->doc_number }}&nbsp;</a>
                            @endforeach
                        </div>
                        </td>

                        

                            </tr>
                        
 @endif

                    </table>

                    <div>

                        
                        

                    </div>

                </div>
            </div>
            <div class="col p-1">

               
            @if ($deal->technical == 1)
                <div class="card mb-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">

                  
            
                    <table class="detail-item-table-sm " width="100%" style="table-layout: fixed;width:100%">
                    <tr style="background:#deebe1 !important" class="text-start"><td  class="mb-2" ><b>Professional Service</b></td></tr></table>
                    <div class="text-center ">
                        @if ($deal->tech == 1)
                            <span class="badge bg-success">Approved</span>
                        @elseif ($deal->tech == 2)
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-warning">Waiting For Approval</span>
                        @endif
                        
                
                        @if(App\SysHelper::professional_service_approval_access() && $deal->tech!=1 && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->technical==1 && $deal->tech!=1)
                            <a data-bs-toggle="modal" data-bs-target="#ModalProfessionalService" class="btn-sm btn-md"><i class="ico icon-outline-pen-new-square title-15 text-white" style="font-size: 12px" aria-hidden="true"></i></a>
                        @endif
                        
                        @if (count($tech) > 0)
                            @foreach ($tech as $val)
                            @if ($val->remarks != '')
                            <p class="my-1 mb-1"><b>Remarks</b> : {!! $val->remarks !!}</p>
                        @endif
                         <p class="my-1 mb-1"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">Created By :
                                {{ $val->createdby->full_name }}</span></p>
                        <p class="my-1 mb-1"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">Created At :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                       
                            @endforeach
                        @endif

                    </div>
                
               
           
                </div>
            @endif
                <div class="card mb-3" style="border-radius: 16px">
                

                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    @php
                    if ($deal->receivables == 1){
                        if ($deal->receivables_approval == 0){
                            $receivables_status = "track-notrequired";
                        } else {
                            $receivables_status = "bg-success text-white";
                        }
                    }
                    else if ($deal->receivables == 2){
                        $receivables_status = "bg-danger text-white";
                    }
                    elseif ($deal->receivables == 3){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 5){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 4){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 6){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $receivables_status = "bg-lightgreen text-dark";
                    }

                    @endphp

                    
                    <tr>
                        <th class="{{ $receivables_status }} d-flex align-items-center justify-content-between gap-1" style="height:23px">

                           
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                                <b>Recievable</b>
                            @if(($deal->technical==1 && $deal->tech==1) || ($deal->technical==0))
                                {{--  @if((App\SysHelper::receivables_approval_access() && $deal->delivery==1) && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->receivables!=1)  --}}

                                @if(App\SysHelper::receivables_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->receivables_approval ==1 && in_array($deal->delivery,[0,2,3,1]))
                                <a class="btn-md btn-light" title="Receivables Approval" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalReceivables"><i class="ico icon-outline-pen-new-square title-15 {{ $receivables_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" style="font-size: 12px"></i></a>
                            @endif
                        @endif
                        </div>

                        <div class="track-stage-actions">
                            @if ($deal->receivables_approval == 1)
                                <div><button type="button" title="Journal Voucher" onclick="window.location.href='{{ url('journalvoucher-add-deal/'.$deal->deal_id.'/'.$del->cust_id) }}'" target="_blank" class="@if($deal->receivables != 1 && $deal->receivables != 2) green-track-action-btn @else track-action-btn  @endif d-inline-flex align-items-center gap-1"> <svg @if($deal->receivables != 1 && $deal->receivables != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> JV</button></div>
                            @endif

                            @if ($deal->receivables_approval == 1)

                                @if(count($check_receipt) == 0)
                                    <div><button type="button" title="Receipts" onclick="window.location.href='{{ url('receipt-add-deal/'.$deal->deal_id.'/'.$deal->payment_mode) }}'" target="_blank" class="@if($deal->receivables != 1 && $deal->receivables != 2) green-track-action-btn @else track-action-btn  @endif border-0 d-inline-flex align-items-center gap-1"><svg  @if($deal->receivables != 1 && $deal->receivables != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> REC</button></div>
                                @endif
                            @endif

                            @if(count($check_receipt) == 0)
                                            
                            @else 

                            @if($check_receipt->sum('amount') < ($t_taxableamount+$t_vatamount-$deal_discount_sum_amount))
                                <div><button type="button" title="Receipts" onclick="window.location.href='{{ url('receipt-add-deal/'.$deal->deal_id.'/'.$deal->payment_mode) }}'" target="_blank" class="@if($deal->receivables != 1 && $deal->receivables != 2) green-track-action-btn @else track-action-btn  @endif border-0 d-inline-flex align-items-center gap-1"><svg  @if($deal->receivables != 1 && $deal->receivables != 2) style="height: 11px;margin-top: -2px;" @else style="height:14px;fill:white"  @endif  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> REC</button></div>
                            @endif
                    
                            @endif

                        </div>


                            

                    </th>
                
                </tr>

                   
                    @if (count($receivables) > 0)
                            @foreach ($receivables as $val)
                                @if ($val->payment_collection == 3)
                                    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Credit Note No</span> : {{ $val->credit_note }}</td></tr>
                                @else
                                    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Collection</span> : @if ($val->payment_collection == 1)
                                            Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                        @elseif($val->payment_collection == 2)
                                            Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                        @elseif($val->payment_collection == 3)
                                            Order Cancelled <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                        @else
                                            Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                @endif
                                </td></tr>

                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Status</span> : @if ($val->payment_status == 1)
                                        Payment Received <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    @elseif($val->payment_status == 2)
                                        Pending <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    @else
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                            @endif
                            </td>
                            
    @if($val->reminder_date != "1970-01-01" && $val->reminder_date != "")
    <tr><td class="text-start truncate-text-custom"><b>Reminder Date</b> : {{ date('d/m/Y h:i:A', strtotime($val->reminder_date)) }}</td></tr>@endif


    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt No</span> : {{ @$val->doc_number }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt Date</span> : {{ date('d/m/Y', strtotime(@$val->receipt_date)) }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt Mode</span> : {{ @$val->receiptmode->account_name }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Invoice No</span> : {{ @$val->invoice_no }}</td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">eceipt Through</span> : @if(@$val->receipt_through==1) Bank Transfer @endif @if(@$val->receipt_through==2) CDC Cheque @endif @if(@$val->receipt_through==3) PDC Cheque @endif</td></tr>
                        
                            @if ($val->amount != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : {{ $val->amount }}</td></tr>
                            @endif
                            @if ($val->amount2 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : {{ $val->amount2 }}</td></tr>
                            @endif
                            @if ($val->amount3 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : {{ $val->amount3 }}</td></tr>
                            @endif
                            @if($val->balance_amount != "")
                            <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Balance</span> : {{ $val->balance_amount }}</td></tr>
                            @endif

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Mode</span> :
                                @if ($val->paymenttype == 1)
                                    Cash
                                @endif
                                @if ($val->paymenttype == 2)
                                    Cheque
                                @endif
                                @if ($val->paymenttype == 3)
                                    Bank Transfer
                                @endif
                                @if ($val->paymenttype == 4)
                                    Open Credit
                                @endif
                                @if ($val->paymenttype == 5)
                                    Credit Card
                                @endif
                                @if ($val->paymenttype == 6)
                                    Bank TT
                                @endif
                            </td></tr>

                            @if ($val->cash_date != '' && $val->cash_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date)) }}</td></tr>
                            @endif
                            @if ($val->cash_date2 != '' && $val->cash_date2 != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Date</span> : {{ date('d/m/Y', strtotime($val->cash_date2)) }}
                                </td></tr>
                            @endif
                            @if ($val->cash_date3 != '' && $val->cash_date3 != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Date</span> : {{ date('d/m/Y', strtotime($val->cash_date3)) }}
                                </td></tr>
                            @endif

                            @if ($val->cheque_no != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : {{ $val->cheque_no }}</td></tr>
                            @endif
                            @if ($val->cheque_date != '1970-01-01' && $val->cheque_date != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    {{ date('d/m/Y', strtotime($val->cheque_date)) }}</td></tr>
                            @endif
                            @if ($val->cheque_no2 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : {{ $val->cheque_no2 }}</td></tr>
                            @endif
                            @if ($val->cheque_date2 != '1970-01-01' && $val->cheque_date2 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    {{ date('d/m/Y', strtotime($val->cheque_date2)) }}</td></tr>
                            @endif
                            @if ($val->cheque_no3 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : {{ $val->cheque_no3 }}</td></tr>
                            @endif
                            @if ($val->cheque_date3 != '1970-01-01' && $val->cheque_date3 != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    {{ date('d/m/Y', strtotime($val->cheque_date3)) }}</td></tr>
                            @endif

                            @if ($val->cheque_copy != '')
                                <tr><td class="text-start truncate-text-custom"><a class="text-info text-xs"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->cheque_copy }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Cheque
                                        Copy</a></td></tr>
                            @endif

                            @if ($val->bank_name != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Bank Name</span> : {{ $val->bank_name }}</td></tr>
                            @endif
                            @if ($val->deposit_date != '' && $val->deposit_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    {{ date('d/m/Y', strtotime($val->deposit_date)) }}</td></tr>
                            @endif
                            @if ($val->deposit_date2 != '' && $val->deposit_date2 != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    {{ date('d/m/Y', strtotime($val->deposit_date2)) }}</td></tr>
                            @endif

                            @if ($val->open_credit_date != '' && $val->open_credit_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Open Credit</span> :
                                    {{ date('d/m/Y', strtotime($val->open_credit_date)) }}</td></tr>
                            @endif

                            @if ($val->credit_card_type != '')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Credit Card</span> : {{ $val->credit_card_type }}</td></tr>
                            @endif
                            @if ($val->payment_date != '' && $val->payment_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Date</span> :
                                    {{ date('d/m/Y', strtotime($val->payment_date)) }}</td></tr>
                            @endif
                            @if ($val->credit_card_deposit_date != '' && $val->credit_card_deposit_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    {{ date('d/m/Y', strtotime($val->credit_card_deposit_date)) }}</td></tr>
                            @endif

                            @if ($val->banktt_date != '' && $val->banktt_date != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    {{ date('d/m/Y', strtotime($val->banktt_date)) }}</td></tr>
                            @endif
                            @if ($val->banktt_date2 != '' && $val->banktt_date2 != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    {{ date('d/m/Y', strtotime($val->banktt_date2)) }}</td></tr>
                            @endif
                            @if ($val->banktt_date3 != '' && $val->banktt_date3 != '1970-01-01')
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    {{ date('d/m/Y', strtotime($val->banktt_date3)) }}</td></tr>
                            @endif
                            @if ($val->banktt_copy != '')
                                <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                        href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $val->banktt_copy }}"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> BankTT
                                        Copy</a></td></tr>
                            @endif
                        @endif



                        @if ($val->remarks != '')
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : {!! $val->remarks !!}</td></tr>
                        @endif
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                {{ $val->createdby->full_name }}</td></tr>
                       <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</td></tr>
                        
                        @endforeach
                        @endif



                    


                            <tr class="bg-white">
                                <td class=" d-flex align-items-center gap-1 flex-wrap">

                                        <script>
                        $(document).ready(function () {
                                // Delegated click works for both static + dynamic .data-item
                                $(document).on('click', '.rec-data-item', function () {
                                    
                                    $("#loading_bg").css("display", "block");

                                    var id = $(this).data('id');

                                    var action = "{{ URL::to('receipt-details-pdf') }}/" + id;

                                    $.ajax({            
                                        url: action,
                                        method: 'GET',
                                        success: function (response) {
                                             $('#recViewModalbody').html(response);   // load inside modal
                                $('#recViewModal').modal('show');  
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
                        $(document).ready(function () {
                                // Delegated click works for both static + dynamic .data-item
                                $(document).on('click', '.jv-data-item', function () {
                                    
                                    $("#loading_bg").css("display", "block");

                                    var id = $(this).data('id');

                                    var action = "{{ URL::to('jv-details-pdf') }}/" + id;

                                    $.ajax({            
                                        url: action,
                                        method: 'GET',
                                        success: function (response) {
                                             $('#jvViewModalbody').html(response);   // load inside modal
                                $('#jvViewModal').modal('show');  
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

                                     @if ($deal->receivables_approval == 1)
                                            
                                            @if(count($check_jv) > 0)
                                                <div>
                                                    @foreach ($check_jv as $cr)
                                                        <a class="btn-sm btn-light jv-data-item" data-id="{{ $cr->id }}" style="font-size: 10px;padding: 2px 2px;" >&nbsp;{{ $cr->doc_number }}&nbsp;</a>
                                                    @endforeach
                                                </div>

                                            @endif


                                            @if(count($check_receipt) == 0)
                                            
                                            @else

                                            @if(count($check_receipt))
                                            <div>
                                                @foreach ($check_receipt as $cr)
                                                    <a class="btn-sm btn-light rec-data-item" data-id="{{ $cr->id }}" style="font-size: 10px;padding: 2px 2px;">&nbsp;{{ $cr->doc_number }}&nbsp;</a>
                                                @endforeach
                                            </div>
                                            @endif

                                          
                                    
                                            @endif
                                        @endif
                                </td>
                            </tr>

                    </table>

                </div>
            </div>

</div>
<!-- Modal -->
<div class="modal side-panel fade" id="modalUpdateItems" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Products</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-delivery-update-items', 'method' => 'POST', 'id' => 'crm-deals-delivery-update-items']) }}
      <div class="modal-body">        
        @if (count($quoteitems) > 0)
        <table class="table table-nowrap table-centered mb-0 table-striped">
            <thead>
                <tr>
                    <th>Part Number</th>
                    <th>Description</th>
                    <th>Quote Qty</th>
                    <th>Delivery Qty</th>
                    <th>check</th>
                </tr>
            </thead>                                
        <?php $t_qty = 0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; ?>
            <tbody>
                <input type="hidden" name="update_item_deal_id" value="{{ $deal->deal_id }}" />
                @foreach ($quoteitems as $Item)
                <tr>
                    <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! nl2br($Item->description) !!}</div></td>
                    <td>{{ $Item->qty }}</td>
                    <td><input type="number" class="form-control" name="qty_{{ $Item->id }}"></td>
                    <td>
                        <input type="checkbox" class="form-control" id="check_bx" name="checkbx[]" value="{{ $Item->id }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
      </div>
      <div class="modal-footer">
      
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>




<!-- Modal GRN-->
<script>
    function set_no($id)
    {
        $("#grn_id").val($id);
    }
</script>
<div class="modal side-panel fade" id="ModalGRN" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add GRN No</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-grn-no-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="grn_id" id="grn_id" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="mb-3">
                            <label for="" class="form-label">GRN NO</label>
                            <input type="text" class="form-control" name="grn_no" id="grn_no" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update GRN No</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal GRN-->

{{-- Modal PO --}}
<div class="modal side-panel  fade" id="po_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Purchase Order Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-selected-deal-items-to-purchase-order-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table  class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr >
                                                    <th style="width:15px"><input type="checkbox" id="po_check_all" onclick="po_check_fun()" checked/>
                                                    <script>
                                                        function po_check_fun(){
                                                            if($("#po_check_all").prop('checked') == true){
                                                                $('.po_check').prop('checked', true);
                                                            } else{
                                                                $('.po_check').prop('checked', false);
                                                            }
                                                        }
                                                    </script>
                                                    </th>
                                                    <th style="width:90px">@lang('Part No')</th>
                                                    <th style="width:100px">@lang('Description')</th>
                                                    <th style="width:30px" class="text-center">@lang('Deal')</th>
                                                    <th style="width:30px" class="text-center">@lang('Exe')</th>
                                                    <th style="width:30px" class="text-center">@lang('Qty')</th>
                                                    <th style="width:60px" class="text-end">@lang('Unit Price')</th>
                                                    <th style="display: none;" class="text-end">@lang('Discount')</th>
                                                    <th style="width:60px" class="text-end">@lang('Value')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($quoteitems)>0)
                                                @foreach ($quoteitems as $Item)
                                                <?php if($Item->cost != '0.00'){
                                                    $up = $Item->cost;
                                                } else {
                                                    $up = $Item->price;
                                                } ?>

                                                @if ((int)$Item->qty > (int)$Item->po_qty)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="po_check" name="selected_item_id[]" checked value="{{ $Item->id }}" />
                                                        <input type="hidden" name="roids[]" value="{{ $Item->id }}" />
                                                    </td>
                                                    <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                                                    <td>{!! $Item->description !!}</td>
                                                    <td class="text-center">{{ $Item->qty }}</td>
                                                    <td class="text-center">0</td>
                                                    <td><input type="number" name="qty[]" class="form-control text-center border-0" value="{{ abs($Item->qty-$Item->po_qty) }}" /></td>
                                                    <td class="text-end pe-0"><input name="unitprice[]" type="text" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($up,2,'.','') }}" /></td>
                                                    <td style="display: none;" class="text-end"><input name="discount[]" type="text" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($Item->discount,2,'.',',') }}"/></td>
                                                    <td class="text-end pe-0"><input name="value[]" type="text" readonly class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format((($up * $Item->qty)),2,'.',',') }}"/></td>
                                                </tr>
                                                <input type="hidden" name="product_id[]" value="{{ $Item->product_id }}" />
                                                <input type="hidden" name="deal_id[]" value="{{ $Item->deal_id }}" />
                                                <input type="hidden" name="deal_code" value="{{ $deal->deal_code->code }}" />
                                                <input type="hidden" name="item_id[]" value="{{ $Item->id }}" />
                                                <input type="hidden" name="deal_qty[]" value="{{ $Item->qty }}" />
                                                <input type="hidden" name="tax[]" value="{{ $Item->vat }}" />
                                                <input type="hidden" name="description[]" value="{{ $Item->description }}" />
                                                @endif
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <div class="modal-footer d-flex justify-content-center p-0">
    <input type="hidden" name="req_deal_id" value="{{ $del->id }}" />
    <button type="submit" class="btn btn-light add-btn ms-2">
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Modal PO --}}

{{-- Modal SI --}}
<div class="modal side-panel fade" id="si_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header m-0 ">
                        <h4 class="modal-title ps-0">Sales Invoice Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="container-fluid">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-selected-deal-items-to-sales-invoice-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px"><input type="checkbox" id="si_check_all" onclick="si_check_fun()" checked/>
                                                        <script>
                                                            function si_check_fun(){
                                                                if($("#si_check_all").prop('checked') == true){
                                                                    $('.si_check').prop('checked', true);
                                                                } else{
                                                                    $('.si_check').prop('checked', false);
                                                                }
                                                            }
                                                        </script>
                                                    </th>
                                                    <th style="width:90px">@lang('Part Number')</th>
                                                    <th style="width:100px">@lang('Description')</th>
                                                    <th class="text-center" style="width:30px">@lang('Deal Qty')</th>
                                                    <th class="text-center" style="width:30px">@lang('Executed Qty')</th>
                                                    <th class="text-center" style="width:30px">@lang('Qty')</th>
                                                    <th class="text-end" style="width:60px">@lang('Unitprice')</th>
                                                    <th class="text-end" style="width:60px">@lang('Discount')</th>
                                                    <th class="text-end" style="width:60px">@lang('Value')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($quoteitems)>0)
                                              
                                                @foreach ($quoteitems as $Item)
                                                
                                                <?php
                                                 /*if($Item->cost != '0.00'){
                                                    $up = $Item->cost;
                                                } else {*/
                                                    $up = $Item->price;
                                                /*}*/ ?>

                                                @if ((int)$Item->qty > (int)$Item->si_qty)
                                                <tr>
                                                    <td class="no-toggle">
                                                        <input class="si_check" type="checkbox" name="selected_item_id[]" checked value="{{ $Item->id }}" />
                                                        <input type="hidden" name="roids[]" value="{{ $Item->id }}" />
                                                    </td>
                                                    <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                                                    <td>{!! $Item->description !!}</td>
                                                    <td class="text-center">{{ $Item->qty }}</td>
                                                    <td class="text-center">0</td>
                                                    <td class="no-toggle text-center"><input type="number" name="qty[]" class="form-control text-center border-0" value="{{ abs($Item->qty-$Item->si_qty) }}" /></td>
                                                    <td class="text-end no-toggle pe-0"><input name="unitprice[]" type="text" step="any" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($up,2,'.','') }}" /></td>
                                                    <td class="text-end no-toggle pe-0"><input name="discount[]" type="text" step="any" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($Item->discount,2,'.','') }}"/></td>
                                                    <td class="text-end no-toggle pe-0"><input name="value[]" type="text" readonly class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format((($up * $Item->qty) - ($Item->discount)),2,'.','') }}"/></td>
                                                </tr>
                                                <input type="hidden" name="product_id[]" value="{{ $Item->product_id }}" />
                                                <input type="hidden" name="part_number[]" value="{{ $Item->productname->part_number }}" />
                                                <input type="hidden" name="deal_id[]" value="{{ $Item->deal_id }}" />
                                                <input type="hidden" name="deal_code" value="{{ $deal->deal_code->code }}" />
                                                <input type="hidden" name="item_id[]" value="{{ $Item->id }}" />
                                                <input type="hidden" name="deal_qty[]" value="{{ $Item->qty }}" />
                                                <input type="hidden" name="tax[]" value="{{ $Item->vat }}" />
                                                <input type="hidden" name="description[]" value="{{ $Item->description }}" />
                                                @endif
                                                @endforeach
                                                @endif
                                                <td colspan="9" style="text-align: center; height: 19px;">
    <!-- You can put placeholder text here if needed -->
  </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                          <div class="modal-footer p-0">
                                <button type="submit" class="btn btn-light add-btn ms-2">
                                    <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                                </button>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
</div>
{{-- Modal SI --}}

{{-- Modal DLN --}}
<div class="modal side-panel fade" id="dln_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Delivery Note Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="container-fluid">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-dln-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table id="long-list" class="table table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px"><input type="checkbox" id="dl_check_all" onclick="dl_check_fun()" checked/>
                                                        <script>
                                                            function dl_check_fun(){
                                                                if($("#dl_check_all").prop('checked') == true){
                                                                    $('.dl_check').prop('checked', true);
                                                                } else{
                                                                    $('.dl_check').prop('checked', false);
                                                                }
                                                            }
                                                        </script>
                                                    </th>
                                                    <th style="width:90px">@lang('Part No')</th>
                                                    {{-- <th>@lang('Description')</th> --}}
                                                    <th style="width:30px" class="text-center">@lang('Deal')</th>
                                                    <th style="width:30px" class="text-center">@lang('Exe')</th>
                                                    <th style="width:30px" class="text-center">@lang('Qty')</th>
                                                    <th class="text-end" style="width:60px">@lang('Unitprice')</th>
                                                    <th class="text-end" style="width:60px">@lang('Discount')</th>
                                                    <th class="text-end" style="width:60px">@lang('Value')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($quoteitems)>0)
                                                @foreach ($quoteitems as $Item)
                                                <?php
                                                    $up = $Item->price;
                                                ?>

                                                @if ((int)$Item->qty > (int)$Item->dn_qty)
                                                <tr>
                                                    <td>
                                                        <input class="dl_check" type="checkbox" name="selected_item_id[]" checked value="{{ $Item->id }}" />
                                                        <input type="hidden" name="roids[]" value="{{ $Item->id }}" />
                                                    </td>
                                                    <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                                                    
                                                    <td class="text-center">{{ $Item->qty }}</td>
                                                    <td class="text-center">0</td>
                                                    <td class="text-center"><input type="number" name="qty[]" class="form-control border-0" value="{{ abs($Item->qty-$Item->dn_qty) }}" /></td>
                                                    <td class="text-end pe-0"><input name="unitprice[]" type="number" step="any" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($up,2,'.','') }}" /></td>
                                                    <td class="text-end pe-0"><input name="discount[]" type="number" step="any" class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format($Item->discount,2,'.','') }}"/></td>
                                                    <td class="text-end pe-0"><input name="value[]" type="number" readonly class="form-control text-end border-0" value="{{ @App\SysHelper::com_curr_format((($up * $Item->qty) - ($Item->discount)),2,'.','') }}"/></td>
                                                </tr>
                                                <input type="hidden" name="product_id[]" value="{{ $Item->product_id }}" />
                                                <input type="hidden" name="part_no_text[]" value="{{ $Item->productname->part_number }}" />
                                                <input type="hidden" name="deal_id" value="{{ $Item->deal_id }}" />
                                                <input type="hidden" name="deal_code" value="{{ $deal->deal_code->code }}" />
                                                <input type="hidden" name="item_id[]" value="{{ $Item->id }}" />
                                                <input type="hidden" name="deal_qty[]" value="{{ $Item->qty }}" />
                                                <input type="hidden" name="tax[]" value="{{ $Item->vat }}" />
                                                <input type="hidden" name="description[]" value="{{ $Item->description }}" />
                                                @endif
                                                @endforeach
                                                @endif

                               <tr><td colspan="9">&nbsp;</td></tr>


                                                @if (count($poitems)>0)
                                                <tr><th colspan="9">Aditional Items</th></tr>
                                                @foreach ($poitems as $Item)
                                                @if ((int)$Item->qty > (int)$Item->dn_qty)
                                                <tr>
                                                    <td>
                                                        <input class="dl_check" type="checkbox" name="selected_item_id[]" checked value="a_{{ $Item->id }}" />
                                                        <input type="hidden" name="roids[]" value="a_{{ $Item->id }}" />
                                                    </td>
                                                    <td>{{ $Item->partno }}</td>
                                                    <td class="text-center">{{ $Item->qty }}</td>
                                                    <td class="text-center">0</td>
                                                    <td><input type="number" name="qty[]" class="form-control border-0" value="{{ abs($Item->qty-$Item->dn_qty) }}" /></td>
                                                    <td class="text-end pe-0"><input name="unitprice[]" type="number" step="any" class="form-control text-end border-0" value="0" /></td>
                                                    <td class="text-end pe-0"><input name="discount[]" type="number" step="any" class="form-control text-end border-0" value="0"/></td>
                                                    <td class="text-end pe-0"><input name="value[]" type="number" readonly class="form-control text-end border-0" value="0"/></td>
                                                </tr>
                                                <input type="hidden" name="product_id[]" value="{{ $Item->part_number }}" />
                                                <input type="hidden" name="part_no_text[]" value="{{ $Item->partno }}" />
                                                <input type="hidden" name="deal_id" value="{{ $quoteitems[0]->deal_id }}" />
                                                <input type="hidden" name="deal_code" value="{{ $deal->deal_code->code }}" />
                                                <input type="hidden" name="item_id[]" value="{{ $Item->id }}" />
                                                <input type="hidden" name="deal_qty[]" value="{{ $Item->qty }}" />
                                                <input type="hidden" name="tax[]" value="{{ $quoteitems[0]->vat }}" />
                                                <input type="hidden" name="description[]" value="{{ $Item->description }}" />
                                                @endif
                                                @endforeach
                                                @endif

                                                <tr>
  <td colspan="8" style="text-align:center; height:19px;">&nbsp;</td>
</tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="moda-footer p-0 mt-1 mb-1">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-end d-flex justify-content-center">
                                        <button type="submit" class="btn btn-light add-btn ms-2">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Modal DLN --}}


{{-- Modal Purchase Auto Generate --}}
<div class="modal side-panel fade" id="purchase_auto_generate" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable" style="width:32rem">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Purchase</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0">
                        <div class="container-fluid">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-create-gen', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="table table-borderless" cellspacing="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 150px; vertical-align: top;">
                                                        Items </th><th>
                                                            @foreach ($quoteitems as $Item)
                                                                <p style="margin-bottom:0px; font-weight: normal; padding-left: 7px;">
                                                                    <input class="dl_check" type="checkbox" name="product_id[]" value="{{ $Item->product_id }}" />
                                                                    {{ $Item->productname->part_number }}</p>
                                                            @endforeach
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px;">
                                                        PO Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_po" checked value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        GRN Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_grn"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        PI Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_pi"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Payment Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_pay"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Payment Options </th><th> :
                                                        <select class="" name="req_mode_acc"required>
                                                            @if(isset($paymentmode_cash))
                                                            @foreach ($paymentmode_cash as $val)
                                                                <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                                            @endforeach
                                                            @endif
                                                            @if(isset($paymentmode_bank))
                                                            @foreach ($paymentmode_bank as $val)
                                                                <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Add Cost </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_cost" checked value="1" />
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                      <div class="modal-footer d-flex justify-content-center p-0">
    <input type="hidden" name="req_deal_id" value="{{ $del->id }}" />
    <button type="submit" class="btn btn-light add-btn">
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


          <div class="modal side-panel fade" id="purchase_auto_generate_MODAL" data-bs-backdrop="false" tabindex="-1" aria-labelledby="purchase_auto_generate_MODAL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable">
                <div class="modal-content" id="purchase_auto_generate_MODALbody">
                    
                    
                </div>
            </div>
        </div>
        
{{-- Modal Purchase Auto Generate --}}



       <div class="modal side-panel fade" id="poViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="poViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="poViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="siViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="siViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="siViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="dlnViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="dlnViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="dlnViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="recViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="recViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="recViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="jvViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="jvViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="jvViewModalbody">
                    
                    
                </div>
            </div>
        </div>

<?php 

}catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
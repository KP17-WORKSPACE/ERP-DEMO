<?php try { 
    
    ?>
    <div class="row shadow-sm" style="background: white;margin-top:1rem">

    
            <div class="col p-1 pt-2" >
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
                            <td class="{{ $account_status }} d-flex align-items-center justify-content-between gap-1">

<style>
    /* Makes modal draggable without breaking Bootstrap layout */
.modal-dialog.draggable {
    position: absolute;
    margin: 0 !important;
}

.modal-header {
    cursor: move;
}

</style>

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
            left: '50%',
            transform: 'translateX(-50%)'
        });
    });

})();
</script>


                           


                    

                          
                                <div class="d-flex align-items-center justify-content-center flex-grow-1 gap-1">
                                    <b>Accounts</b>
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



                  
                    <div>

                        

                        

                    </div>

                </div>
            </div>
            <div class="col p-1 pt-2">
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
                       

                        <td class="{{ $sales_status }} d-flex align-items-center justify-content-center gap-1">
                            <b>Sales</b>   
                            
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
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Invoice Approval</span> : @if ($val->invoice_approval == 1) Required
                            @elseif($val->invoice_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Approval</span> : @if ($val->delivery_approval == 1) Required
                            @elseif($val->delivery_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receivables Approval</span> : @if ($val->receivables_approval == 1) Required
                            @elseif($val->receivables_approval == 2) Not Required
                            @else &nbsp; @endif</td></tr>

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
            <div class="col p-1 pt-2">
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
                       

                     <td class="{{ $purchease_status }} d-flex align-items-center justify-content-between gap-1">

                        

                         <div class="d-flex align-items-center justify-content-center flex-grow-1 gap-1">
                            <b>Purchase</b>   
                        
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
                         <td class=" bg-white d-flex align-items-center gap-2 flex-wrap">
                      
                            

                            @if (count($check_po) > 0)

        


                                <div style="float: right;">
                                @foreach ($check_po as $po)<a class="btn-sm btn-light" href="{{url('purchase-order/'.$po->id)}}" target="_blank">&nbsp;{{ $po->doc_number }}&nbsp;</a>
                                @endforeach
                                </div>


                            @endif
            
                        </td>
                       </tr>



                </table>
                

                </div>
            </div>
            <div class="col p-1 pt-2">
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

                      <td class="{{ $invoice_status }} d-flex align-items-center justify-content-between gap-1">

                        <div class="d-flex align-items-center justify-content-center flex-grow-1 gap-1">
                            <b>Invoice</b>     
                            
                          
                     
                        </div>

                         <?php $check_si_pending = App\SysDealSalesInvoiceItems::where('deal_id',$deal->deal_id)->where('status',1)->where('cart_id', session('logged_session_data.cart_id'))->count(); ?>
                       



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

                    <td class="bg-white d-flex align-items-center gap-1 flex-wrap">
                        {{-- <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square title-15 text-danger" style="font-size: 16px"></i></a> --}}

                        
                      

                        
                        @if (count($check_cl) > 0)
                        @foreach ($check_cl as $cl)<a class="btn-sm btn-light" href="{{url('clearance/'.$cl->id.'/download')}}" target="_blank">&nbsp;{{ $cl->invoice_no }}&nbsp;</a>
                        @endforeach
                        @endif

                      
                        @if (count($check_si) > 0)
                          


                            @if(count($list_sales_invoice)>0)                    
                            @foreach($list_sales_invoice as $list)
                                <a class="btn-sm btn-light" href="{{url('sales-invoice/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                            @endif
                            

                        
                        @endif
                                                        
                    </td>

                        </tr>


                    </table>
                    

                          



                </div>
            </div>
            <div class="col p-1 pt-2">
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
                            <td class="{{$delivery_status}} d-flex align-items-center justify-content-between gap-1" >
                              
                                <div class="d-flex align-items-center justify-content-center flex-grow-1 gap-1">
                                <b>Delivery</b>  
                                
                            
                                </div>

                                  <?php $po_check = 0;
                            if(count($poitems)>0){
                                if ($poitems->sum('dn_qty') < $poitems->sum('qty')){
                                    $po_check = 1;
                                }
                            }
                        ?>


                      
                        
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



                      


                        
                            <tr class="bg-white d-flex align-items-center gap-1 flex-wrap">

                
               
                      
                
               

                        <?php $delivery_note = App\SysDeliveryNote::where('deal_id',$deal->deal_id)->get(); ?>
                         <td>
                        <div>
                            @foreach ($delivery_note as $dn)<a class="btn-sm btn-light" href="{{url('delivery-note/'.$dn->id.'/view')}}" target="_blank">&nbsp;{{ $dn->doc_number }}&nbsp;</a>
                            @endforeach
                        </div>
                        </td>

                        

                            </tr>
                        


                    </table>

                    <div>

                        
                        

                    </div>

                </div>
            </div>
            <div class="col p-1 pt-2">

               
            @if ($deal->technical == 1)
                <div class="card mb-3" style="border-radius: 16px">

                  
            
                    <table class="detail-item-table-sm " width="100%" style="table-layout: fixed;width:100%">
                    <tr><td  class="mb-2" ><b>Professional Service</b></td></tr></table>
                    <div class="text-center ">
                        @if ($deal->tech == 1)
                            <span class="badge bg-success">Approved</span>
                        @elseif ($deal->tech == 2)
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-warning">Waiting For Approval</span>
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
                        <th class="{{ $receivables_status }} d-flex align-items-center justify-content-between gap-1">

                         
                        <div class="d-flex align-items-center justify-content-center flex-grow-1 gap-1">
                                <b>Recievable</b>
                           
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
                                     @if ($deal->receivables_approval == 1)

                               
                            
                            @if(count($check_jv) > 0)
                                <div>
                                    @foreach ($check_jv as $cr)
                                        <a class="btn-sm btn-light" href="{{url('journalvoucher/'.$cr->id.'/view')}}" target="_blank">&nbsp;{{ $cr->doc_number }}&nbsp;</a>
                                    @endforeach
                                </div>

                            @endif


                                @if(count($check_receipt) == 0)
                                   
                                @else

                                @if(count($check_receipt))
                                <div>
                                    @foreach ($check_receipt as $cr)
                                        <a class="btn-sm btn-light" href="{{url('receipt/'.$cr->id.'/view')}}" target="_blank">&nbsp;{{ $cr->doc_number }}&nbsp;</a>
                                    @endforeach
                                </div>
                                @endif

                                    @if($check_receipt->sum('amount') < ($t_taxableamount+$t_vatamount-$deal_discount_sum_amount))
                                        <i class="ico icon-outline-add-square text-success title-15" onclick="window.location.href='{{ url('receipt-add-deal/'.$deal->deal_id.'/'.$deal->payment_mode) }}'"></i>
                                    @endif
                                    
                                @endif
                            @endif
                                </td>
                            </tr>

                    </table>

                </div>
            </div>

</div>


<?php 

}catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
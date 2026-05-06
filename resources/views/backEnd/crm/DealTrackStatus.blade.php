        <div class="row">
            <div class="col-lg-2 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-1">Account Status</h2>
                    <div>
                        @if ($dealtrack->accounts==1)
                        <button class="btn btn-success btn-block mb-1 p-0">Approved</button>
                        @elseif ($dealtrack->accounts==2)
                        <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                        @elseif ($dealtrack->accounts==3)
                        <button class="btn btn-warning btn-block mb-1 p-0">Pending</button>
                        @else
                        <button class="btn btn-info btn-block mb-1 p-0">Waiting For Approval</button>
                        @endif

                        @if(count($accounts)>0)
                            @foreach ($accounts as $val)
                            <p class="my-1 mb-3"><b>Customer Status</b> : @if($val->customer_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->customer_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                            
                                <p class="my-1 mb-3"><b>Credit Limit</b> : @if($val->credit_limit == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->credit_limit == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                            
                                    <p class="my-1 mb-3"><b>Payment Terms</b> : @if($val->payment_terms == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_terms == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                            
                                        <p class="my-1 mb-3"><b>Overdue Payment</b> : @if($val->pending_payment == 1) No <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->pending_payment == 2) Yes <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                            
                                            <p class="my-1 mb-3"><b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                            
                            @if($val->remarks != "")<p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}</p>@endif
                            <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                            <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
            <div class="col-lg-2 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-1">Sales Status</h2>
                    <div>
                        @if ($dealtrack->sales==1)
                        <button class="btn btn-success btn-block mb-1 p-0">Approved</button>
                        @elseif ($dealtrack->sales==2)
                        <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                        @elseif ($dealtrack->sales==3)
                        <button class="btn btn-warning btn-block mb-1 p-0">Pending</button>
                        @else
                        <button class="btn btn-info btn-block mb-1 p-0">Waiting For Approval</button>
                        @endif
                        
                        @if(count($sales)>0)
                        @foreach ($sales as $val)
                        <p class="my-1 mb-3"><b>Margin</b> : @if($val->margin == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->margin == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                            <p class="my-1 mb-3"><b>Stock</b> : @if($val->stock == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->stock == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                                <p class="my-1 mb-3"><b>Purchase Quote</b> : @if($val->purcease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purcease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                                    <p class="my-1 mb-3"><b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                        @if($val->remarks != "")<p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}</p>@endif
                        <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                        <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                        @endforeach
                        @endif
                    </div>

                </div>
            </div>
            <div class="col-lg-2 mb-3">
                <div class="card p-4 h-100">
                    <h2 class="page-heading mb-1">Purchase Status</h2>
                    <div>
                        @if ($dealtrack->purchease==1)                    
                        @if ($dealtrack->purchease==1 && count($purchease)==0)
                        <button class="btn btn-info btn-block mb-1 p-0">Not Applicable</button>
                        @else
                        <button class="btn btn-success btn-block mb-1 p-0">Approved</button>
                        @endif
                    @elseif ($dealtrack->purchease==2)
                    <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                    @elseif ($dealtrack->purchease==3)
                    <button class="btn btn-info btn-block mb-1 p-0">Pending</button>
                    @elseif ($dealtrack->purchease==4)
                    <button class="btn btn-primary btn-block mb-1 p-0">Partial Delivery</button>
                    @else
                    <button class="btn btn-warning btn-block mb-1 p-0">Waiting For Approval</button>
                    @endif

                    @if(count($purchease)>0)
                    @foreach ($purchease as $val)
                    <p class="my-1 mb-3"><b>Purchase Quote</b> : @if($val->purchease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purchease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                        <p class="my-1 mb-3"><b>3Quote Qequest</b> : @if($val->three_quote_request == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->three_quote_request == 3) Not Required <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->three_quote_request == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                            <p class="my-1 mb-3"><b>Purchase Status</b> : @if($val->validation == 1) Purchase Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 3) Under Purchase <i class="fa fa-clock text-warning" aria-hidden="true"></i>@elseif($val->validation == 4) Partial Delivery <i class="fa fa-check text-success" aria-hidden="true"></i> @elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                    
                    @if($val->validation == 3)                    
                        @if($val->lpo_no != "")<p class="my-1 mb-3"><b>LPO No</b> : {{ $val->lpo_no }}</p>@endif
                        @if($val->delivery_date != "" && $val->delivery_date != "1970-01-01")<p class="my-1 mb-3"><b>Expected Delivery</b> : {{ date('d/m/Y', strtotime($val->delivery_date)) }}</p>@endif
                    @endif
                    

                    <p class="my-1 mb-3"><b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                    @if(session('logged_session_data.designation_id')==20 || Auth::user()->role_id == 1)
                    @if($val->fileone != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->fileone }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 1</a></p>@endif
                    @if($val->filetwo != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filetwo }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 2</a></p>@endif
                    @if($val->filethree != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filethree }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 3</a></p>@endif
                    @endif
                    
                    <p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                    @endforeach
                    @endif
                    </div>

                </div>
            </div>
            <div class="col-lg-2 mb-3">
                <div class="card p-4 h-100">
                    <h2 class="page-heading mb-1">Invoice Status</h2>
                    <div>
                    @if ($dealtrack->invoice==1)
                    <button class="btn btn-success btn-block mb-1 p-0">Approved</button>
                    @elseif ($dealtrack->invoice==2)
                    <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                    @elseif ($dealtrack->invoice==3)
                    <button class="btn btn-info btn-block mb-1 p-0">Pending</button>
                    @else
                    <button class="btn btn-warning btn-block mb-1 p-0">Waiting For Approval</button>
                    @endif

                    @if(count($invoice)>0)
                    @foreach ($invoice as $val)
                    <p class="my-1 mb-3"><b>Delivery Advice</b> : @if($val->delivery_advice == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_advice == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                        <p class="my-1 mb-3"><b>Validation</b> : @if($val->validation == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                            <p class="my-1 mb-3"><b>Hold</b> : @if($val->hold == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->hold == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                                <p class="my-1 mb-3"><b>Print</b> : @if($val->print == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->print == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                    @if($val->invoice_no != "")<p class="my-1 mb-3"><b>Invoice No</b> : {{ $val->invoice_no }}</p>@endif
                        
                    @if ($val->partial_invoice != 0)
                        <p class="my-1 mb-3"><b>Partial Invoice</b> : Yes</p>
                        <p class="my-1 mb-3"><b>Partial Invoice Amount</b> : {{ $val->partial_invoice_amount }}</p>
                    @endif
                    
                    @if($val->remarks != "")<p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}</p>@endif
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                    @endforeach
                    @endif

                    </div>

                </div>
            </div>
            <div class="col-lg-2 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-1">Delivery Status</h2>
                    <div>
                    @if ($dealtrack->delivery==1)
                    <button class="btn btn-success btn-block mb-1 p-0">Delivery Completed</button>
                    @elseif ($dealtrack->delivery==2)
                    <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                    @elseif ($dealtrack->delivery==3)
                    <button class="btn btn-primary btn-block mb-1 p-0">Out For Delivery</button>
                    @elseif ($dealtrack->delivery==5)
                    <button class="btn btn-primary btn-block mb-1 p-0">Ready For Delivery</button>
                    @elseif ($dealtrack->delivery==4)
                    <button class="btn btn-info btn-block mb-1 p-0">Pending For Delivery</button>
                    @else
                    <button class="btn btn-warning btn-block mb-1 p-0">Waiting For Approval</button>
                    @endif

                    @if(count($delivery)>0)
                    @foreach ($delivery as $val)
                    <p class="my-1 mb-3"><b>DO Status</b> : @if($val->do_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->do_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                    @if($val->do_no != "")<p class="my-1 mb-3"><b>Do No</b> : {{ $val->do_no }}</p>@endif
                    
                    @if($val->print_invoice_no != "")<p class="my-1 mb-3"><b>Print Invoice No</b> : {{ $val->print_invoice_no }}</p>@endif

                    @if($val->cheque_collection != "")<p class="my-1 mb-3"><b>Cheque Collection</b> : @if($val->cheque_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->cheque_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
                    </p>@endif
                    
                    @if($val->cheque_collection_file != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_collection_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a></p>@endif
                    
                        <p class="my-1 mb-3"><b>Delivery Status</b> : @if($val->delivery_status == 1) Delivery Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_status == 2) Pending For Delivery <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->delivery_status == 4) Ready For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> @else Out For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                    
                    @if($val->deliver_by != "")<p class="my-1 mb-3"><b>Deliver By</b> : {{ $val->deliver_by }}
                    @if($val->driver != ""), {{ $val->driver }}</p>@endif @endif
                    
                    @if($val->cash_collected != "")<p class="my-1 mb-3"><b>Cash Collected</b> : {{ $val->cash_collected }}</p>@endif
                    
                    @if($val->id_no != "")<p class="my-1 mb-3"><b>ID No</b> : {{ $val->id_no }}</p>@endif
                    @if($val->contact_no != "")<p class="my-1 mb-3"><b>Contact No</b> : {{ $val->contact_no }}</p>@endif
                    @if($val->awb_no != "")<p class="my-1 mb-3"><b>AWB No</b> : {{ $val->awb_no }}</p>@endif
                    @if($val->attach_file != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->attach_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Attachment</a></p>@endif
                
                    @if($val->remarks != "")<p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}</p>@endif
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                    @endforeach
                    @endif

                    </div>

                </div>
            </div>
            <div class="col-lg-2 mb-3">                
                
                @if ($dealtrack->technical == 1)
                <div class="card p-4">
                    <h2 class="page-heading mb-1">Professional Service</h2>
                    <div>
                        @if ($dealtrack->tech == 1)
                            <button class="btn btn-success btn-block mb-1 p-0">Approved</button>
                        @elseif ($dealtrack->tech == 2)
                            <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                        @else
                            <button class="btn btn-warning btn-block mb-1 p-0">Waiting For Approval</button>
                        @endif

                        @if (count($tech) > 0)
                        @foreach ($tech as $val)
                        @if ($val->remarks != '')
                        <p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}</p>
                    @endif
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At :
                            {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                    <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By :
                            {{ $val->createdby->full_name }}</span></p>
                        @endforeach
                    @endif
                    
                    </div>
                </div>
                <br />
                @endif

                <div class="card p-4">
                    <h2 class="page-heading mb-1">Receivables Status</h2>
                    <div>
                        @if ($dealtrack->receivables==1)
                        <button class="btn btn-success btn-block mb-1 p-0">Payment Received</button>
                        @elseif ($dealtrack->receivables==2)
                        <button class="btn btn-danger btn-block mb-1 p-0">Rejected</button>
                        @elseif ($dealtrack->receivables==3)
                        <button class="btn btn-info btn-block mb-1 p-0">Payment Pending</button>
                        @elseif ($dealtrack->receivables==4)
                        <button class="btn btn-dark btn-block mb-1 p-0">Order Cancelled</button>
                        @else
                        <button class="btn btn-warning btn-block mb-1 p-0">Waiting For Approval</button>
                        @endif

                        @if(count($receivables)>0)
                        @foreach ($receivables as $val)
                        @if($val->payment_collection == 3)
                        <p class="my-1 mb-3"><b>Credit Note No : {{ $val->credit_note }}</b></p>
                        @else
                        <p class="my-1 mb-3"><b>Payment Collection</b> : @if($val->payment_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->payment_collection == 3) Order Cancelled <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                            <p class="my-1 mb-3"><b>Payment Status</b> : @if($val->payment_status == 1) Payment Received <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_status == 2) Pending <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> </p>@endif
                        
                        @if($val->amount != "")<p class="my-1 mb-3"><b>Amount</b> : {{ $val->amount }}</p>@endif
                        @if($val->amount2 != "")<p class="my-1 mb-3"><b>Amount</b> : {{ $val->amount2 }}</p>@endif
                        @if($val->amount3 != "")<p class="my-1 mb-3"><b>Amount</b> : {{ $val->amount3 }}</p>@endif
                        <p class="my-1 mb-3"><b>Payment Mode</b> :    
                        @if($val->paymenttype == 1) Cash @endif
                        @if($val->paymenttype == 2) Cheque @endif
                        @if($val->paymenttype == 3) Bank Transfer @endif
                        @if($val->paymenttype == 4) Open Credit @endif
                        @if($val->paymenttype == 5) Credit Card @endif
                        @if($val->paymenttype == 6) Bank TT @endif
                        </p>
                        
                        @if($val->cash_date != "" && $val->cash_date != "1970-01-01")<p class="my-1 mb-3"><b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date)) }}</p>@endif
                        @if($val->cash_date2 != "" && $val->cash_date2 != "1970-01-01")<p class="my-1 mb-3"><b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date2)) }}</p>@endif
                        @if($val->cash_date3 != "" && $val->cash_date3 != "1970-01-01")<p class="my-1 mb-3"><b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date3)) }}</p>@endif

                        @if($val->cheque_no != "")<p class="my-1 mb-3"><b>Cheque No</b> : {{ $val->cheque_no }}</p>@endif
                        @if($val->cheque_date != "1970-01-01" && $val->cheque_date != "")<p class="my-1 mb-3"><b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date)) }}</p>@endif
                        @if($val->cheque_no2 != "")<p class="my-1 mb-3"><b>Cheque No</b> : {{ $val->cheque_no2 }}</p>@endif
                        @if($val->cheque_date2 != "1970-01-01" && $val->cheque_date2 != "")<p class="my-1 mb-3"><b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date2)) }}</p>@endif
                        @if($val->cheque_no3 != "")<p class="my-1 mb-3"><b>Cheque No</b> : {{ $val->cheque_no3 }}</p>@endif
                        @if($val->cheque_date3 != "1970-01-01" && $val->cheque_date3 != "")<p class="my-1 mb-3"><b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date3)) }}</p>@endif

                        @if($val->cheque_copy != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a></p>@endif

                        @if($val->bank_name != "")<p class="my-1 mb-3"><b>Bank Name</b> : {{ $val->bank_name }}</p>@endif
                        @if($val->deposit_date != "" && $val->deposit_date != "1970-01-01")<p class="my-1 mb-3"><b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date)) }}</p>@endif
                        @if($val->deposit_date2 != "" && $val->deposit_date2 != "1970-01-01")<p class="my-1 mb-3"><b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date2)) }}</p>@endif

                        @if($val->open_credit_date != "" && $val->open_credit_date != "1970-01-01")<p class="my-1 mb-3"><b>Open Credit</b> : {{ date('d/m/Y', strtotime($val->open_credit_date)) }}</p>@endif

                        @if($val->credit_card_type != "")<p class="my-1 mb-3"><b>Credit Card</b> : {{ $val->credit_card_type }}</p>@endif
                        @if($val->payment_date != "" && $val->payment_date != "1970-01-01")<p class="my-1 mb-3"><b>Payment Date</b> : {{ date('d/m/Y', strtotime($val->payment_date)) }}</p>@endif
                        @if($val->credit_card_deposit_date != "" && $val->credit_card_deposit_date != "1970-01-01")<p class="my-1 mb-3"><b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->credit_card_deposit_date)) }}</p>@endif

                        @if($val->banktt_date != "" && $val->banktt_date != "1970-01-01")<p class="my-1 mb-3"><b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date)) }}</p>@endif
                        @if($val->banktt_date2 != "" && $val->banktt_date2 != "1970-01-01")<p class="my-1 mb-3"><b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date2)) }}</p>@endif
                        @if($val->banktt_date3 != "" && $val->banktt_date3 != "1970-01-01")<p class="my-1 mb-3"><b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date3)) }}</p>>@endif
                        @if($val->banktt_copy != "")<p class="my-1 mb-3"><a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->banktt_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> BankTT Copy</a></p>@endif
                        @endif
                        @if($val->remarks != "")<p class="my-1 mb-3"><b>Remarks</b> : {!! $val->remarks !!}@endif
                            <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span></p>
                                <p class="my-1 mb-3"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span></p>
                        @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
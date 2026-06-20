
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

        #data-details .green-heading {
            text-align: center !important;
        }
        
        #data-details .green-heading p {
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

    @php
        $isRequestScreen = request()->is('crm-reimbursement-request*') || request('context') == 'request';
        $reimbursementPermissions = $reimbursementPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
        $reimbursementTrackPermissions = $reimbursementTrackPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
        $ownsReimbursement = (int) $selectedReimbursement->created_by === (int) Auth::id() || (int) $selectedReimbursement->employee_id === (int) Auth::id();
        $canEditReimbursement = (!empty($reimbursementPermissions['edit']) && $ownsReimbursement) || ($ownsReimbursement && (int) $selectedReimbursement->approval_status === 0) || in_array(Auth::user()->role_id, [1, 2]);
    @endphp

    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
        <h4 class="purchase-order-content-header-left">
            {{ $selectedReimbursement->reimbursement_no }}
        </h4>
        <div class="purchase-order-content-header-right">
            @if ($isRequestScreen)
                @if ($canEditReimbursement)
                    <button type="button" class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#ModalService" onclick='fun_edit({{ $selectedReimbursement->id }}, @json($selectedReimbursement->reimbursement_no))'>
                        <i class="ico icon-outline-pen-2 text-success"></i> Edit
                    </button>
                @endif
                @if (!empty($reimbursementPermissions['create']))
                    <button type="button" class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#ModalService" onclick="prepareAddReimbursementForm()">
                        <i class="ico icon-outline-add-square text-success"></i> Add
                    </button>
                @endif
            @else
                @if (!empty($reimbursementPermissions['view']))
                    <a href="{{ url('crm-reimbursement-request') }}" class="btn btn-light text-dark">
                        <i class="ico icon-outline-document-text text-success"></i> View
                    </a>
                @endif
            @endif
            <div class="dropdown" style="display: inline-block; margin-left: 5px;">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    @if ($isRequestScreen && !empty($reimbursementTrackPermissions['view']))
                        <li><a class="dropdown-item" href="{{ url('crm-reimbursement-track') }}"><i class="ico icon-outline-list-down"></i> Reimbursement Track</a></li>
                    @endif
                </ul>
            </div> 
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                <div class="row">
                    <div class="col-2 mb-2">
                        <label class="form-label">Employee Name</label> 
                        <div class="form-control-plaintext truncate-text-custom">
                            @php
                                $name = $staff->full_name ?? 'N/A';
                                $nameParts = explode(' ', trim($name));
                                $shortName = count($nameParts) > 1 ? $nameParts[0] . ' ' . end($nameParts) : $name;
                            @endphp
                            {{ $shortName }}
                        </div>
                    </div>
                    <div class="col-2 mb-2">
                        <label class="form-label">Department</label> 
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $staff->departments->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-2 mb-2">
                        <label class="form-label">Designation</label> 
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $staff->designations->title ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-2 mb-2">
                        <label class="form-label">Amount</label> 
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ number_format($selectedReimbursement->amount, 2) }} {{ $selectedReimbursement->currencycode->code ?? '' }}
                        </div>
                    </div>
                      <div class="col-2 mb-2">
                          <label class="form-label">Submitted By</label> 
                          <div class="form-control-plaintext truncate-text-custom">
                              {{ $submitter->full_name ?? 'N/A' }}
                          </div>
                      </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #purchaseDetailsTabsContent .tab-content { display: flex; }
        #purchaseDetailsTabsContent .tab-pane { flex: 1; min-height: 135px; }
    </style>
    
    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="expense-details-tab" data-bs-toggle="tab" data-bs-target="#expense-details" type="button" role="tab" aria-controls="expense-details" aria-selected="true">Expense Details</button>
            </li>
        </ul>
        
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <!-- Expense Details Tab -->
            <div class="tab-pane fade show active" id="expense-details" role="tabpanel" aria-labelledby="expense-details-tab">
                <div class="row text-start">
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Expense Date</p>
                        {{ $selectedReimbursement->date ? date('d/m/Y', strtotime($selectedReimbursement->date)) : '' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Request Date</p>
                        {{ $selectedReimbursement->created_at ? date('d/m/Y', strtotime($selectedReimbursement->created_at)) : '' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Invoice No.</p>
                        {{ $selectedReimbursement->invoice_no }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Invoice Date</p>
                        {{ $selectedReimbursement->invoice_date ? date('d/m/Y', strtotime($selectedReimbursement->invoice_date)) : '' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Amount</p>
                        {{ number_format((float)$selectedReimbursement->amount, 2) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Reimbursable Amount</p>
                        {{ number_format((float)$selectedReimbursement->reimbursable_amount, 2) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Payment Method</p>
                        {{ $selectedReimbursement->payment_method }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Expense Category</p>
                        {{ $selectedReimbursement->remarks }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Deal ID</p>
                        {{ $selectedReimbursement->deal_code->code ?? '' }}
                    </div>

                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Customer Name</p>
                        {{ $selectedReimbursement->deal_code->customername->name ?? '' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Vendor Name</p>
                        {{ $selectedReimbursement->vendor_name }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Currency</p>
                        {{ $selectedReimbursement->currencycode->code ?? '' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Head Count</p>
                        {{ $selectedReimbursement->head_count_name }}
                    </div>
                    <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Scope of Work</p>
                        {{ $selectedReimbursement->scope_of_work }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Attachment</p>
                        @php
                            $attachments = @$selectedReimbursement->attachmant ?: @$selectedReimbursement->attachment;
                            $attachmentFiles = $attachments ? explode('|', $attachments) : [];
                        @endphp
                        @if (count($attachmentFiles) > 0)
                            @foreach ($attachmentFiles as $f)
                                @php
                                    $f = trim($f);
                                    $attachmentUrl = strpos($f, '/') !== false ? asset($f) : asset('public/uploads/crm_amc_doc/' . $f);
                                @endphp
                                @if ($f != '')
                                    <a class="text-success" href="{{ $attachmentUrl }}" target="_blank">{{ basename($f) }}</a>@if (!$loop->last), @endif
                                @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                        <p class="font-weight-600 mb-0">Remarks</p>
                        {{ $selectedReimbursement->attachment_remarks ?: '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ((int) $selectedReimbursement->approval_status !== 0)
        @include('backEnd.amc.ReimbursementTrackApprovalStatus')
    @endif

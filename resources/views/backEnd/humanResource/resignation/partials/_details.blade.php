@php
    $employee = $resignation->employee;
    $notice = $resignation->notice;
    $handover = $resignation->handover;
    $assetClearance = $resignation->assetClearance;
    $finance = $resignation->finance;
    $finalSettlement = $resignation->finalSettlement;
    $exitInterview = $resignation->exitInterview;
    $storedRequestNo = trim((string) $resignation->request_no);
    $companyId = optional($resignation->employee)->company_id ?: session('logged_session_data.company_id') ?: (Auth::user()->company_id ?? 1);
    $companyCode = DB::table('sys_company')->where('id', $companyId)->value('other_code') ?: 'D';
    $requestPrefix = 'ES' . $companyCode . '-';
    if (!empty($resignation->display_request_no)) {
        $requestNo = $resignation->display_request_no;
    } elseif (preg_match('/^RE[A-Z0-9]*-(\d+)$/i', $storedRequestNo, $match)) {
        $requestNo = $requestPrefix . $match[1];
    } elseif ($storedRequestNo !== '' && !preg_match('/^RES-\d+$/i', $storedRequestNo)) {
        $requestNo = $storedRequestNo;
    } else {
        $requestNo = $requestPrefix . (1000 + (int) $resignation->id);
    }
    $status = ucfirst($resignation->status ?: 'draft');
    $badgeColor = $resignation->status === 'approved' || $resignation->status === 'completed'
        ? 'success'
        : ($resignation->status === 'rejected' ? 'danger' : ($resignation->status === 'submitted' ? 'warning' : 'primary'));
    $fmtDate = function ($value) {
        if (empty($value)) {
            return 'N/A';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    };
    $fmtText = function ($value) {
        if ($value === null || $value === '') {
            return 'N/A';
        }
        return ucwords(str_replace('_', ' ', $value));
    };
    $money = function ($value) {
        return is_numeric($value) ? number_format((float) $value, 2) : 'N/A';
    };
    $permissionRoutes = collect($permissions ?? [])->pluck('route')->filter()->toArray();
    $hasRoutePermissions = count($permissionRoutes) > 0;
    $canAddResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.add', $permissionRoutes);
    $canEditResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.edit', $permissionRoutes);
    $downloadUrl = function (array $params = []) use ($resignation) {
        $url = route('staff.resignation.downloadAttachment', $resignation->id);
        return $params ? $url . '?' . http_build_query($params) : $url;
    };
    $legalDocuments = [
        ['label' => 'MOHRE Document', 'field' => 'mohre_clearance_document', 'path' => optional($finalSettlement)->mohre_clearance_document],
        ['label' => 'Visa Document', 'field' => 'visa_cancellation_document', 'path' => optional($finalSettlement)->visa_cancellation_document],
        ['label' => 'Labour Document', 'field' => 'labour_cancellation_document', 'path' => optional($finalSettlement)->labour_cancellation_document],
    ];
@endphp

<style>
    #resignation-detail-panel label,
    #resignation-detail-panel .green-heading p {
        font-weight: 600 !important;
        background-color: #deebe1 !important;
        margin-bottom: 3px !important;
        text-align: center !important;
        color: #212529 !important;
    }

    #resignation-detail-panel .form-control-plaintext {
        text-align: center !important;
    }

    #resignation-detail-panel .truncate-text-custom {
        min-height: 24px;
        padding: 3px 4px;
        word-break: break-word;
    }
</style>

<div id="resignation-detail-panel">
    <div class="purchase-order-content-header sticky-top" style="background-color:#f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left mb-0">{{ $requestNo }}</h4>
            <div class="pipeline-arrow {{ $badgeColor }}">{{ $status }}</div>
        </div>
        <div class="purchase-order-content-header-right d-flex align-items-center">
            @if($canAddResignation)
                <a href="{{ route('staff.resignation.add') }}"
                    class="btn btn-light text-dark d-inline-flex align-items-center">
                    <i class="ico icon-outline-add-square text-success"></i><span class="btn-text ms-1">Add</span>
                </a>
            @endif
            @if($canEditResignation && $resignation)
                <a href="{{ route('staff.resignation.edit', $resignation->id) }}"
                    class="btn btn-light text-dark d-inline-flex align-items-center ms-2">
                    <i class="ico icon-outline-pen-2 text-success btn-icon"></i><span class="btn-text ms-1">Edit</span>
                </a>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-2 mb-2">
                    <label class="form-label">Request Number</label>
                    <div class="form-control-plaintext truncate-text-custom">{{ $requestNo }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Employee</label>
                    <div class="form-control-plaintext truncate-text-custom">{{ optional($employee)->full_name ?: 'N/A' }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Department</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ optional(optional($employee)->departments)->name ?: 'N/A' }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Designation</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ optional(optional($employee)->designations)->title ?: 'N/A' }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext truncate-text-custom">{{ $status }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Submitted On</label>
                    <div class="form-control-plaintext truncate-text-custom">{{ $fmtDate($resignation->created_at) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-wrap mb-3">
                <ul class="nav nav-tabs" id="eosDetailTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#detail-resignation" type="button">Resignation Details</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-notice"
                            type="button">Notice Period</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-handover"
                            type="button">Handover</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-asset"
                            type="button">Asset Clearance</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-it"
                            type="button">IT & Access</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-eos"
                            type="button">EOS Calculation</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-final"
                            type="button">Final Settlement</button></li>

                </ul>
            <div class="tab-content mb-3">
                <div class="tab-pane fade show active" id="detail-resignation">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Separation Type</p>
    {{ $fmtText($resignation->separation_type) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Resignation Type</p>
    {{ $fmtText($resignation->resignation_type) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Initiated By</p>
    {{ $fmtText($resignation->initiated_by) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Reason</p>
    {{ $fmtText($resignation->reason_category) }}
</div>
                        <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 green-heading">
    <p class="mb-0">Detailed Reason</p>
    {{ $resignation->detailed_reason ?: 'N/A' }}
</div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-notice">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Submitted Date</p>
    {{ $fmtDate(optional($notice)->resignation_submitted_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Notice Served</p>
    {{ $fmtText(optional($notice)->notice_period_served) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Notice Days</p>
    {{ optional($notice)->notice_period_days ?: 'N/A' }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Last Working Day</p>
    {{ $fmtDate(optional($notice)->last_working_day) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Relieving Date</p>
    {{ $fmtDate(optional($notice)->relieving_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Notice Waiver</p>
    {{ $fmtText(optional($notice)->notice_waiver) }}
</div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-handover">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">KT Required</p>
    {{ $fmtText(optional($handover)->knowledge_transfer_required) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Start Date</p>
    {{ $fmtDate(optional($handover)->handover_start_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">End Date</p>
    {{ $fmtDate(optional($handover)->handover_end_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Successor</p>
    {{ optional($handover)->successor_employee_id ?: 'N/A' }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Checklist</p>
    {{ $fmtText(optional($handover)->handover_checklist_completed) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Manager Approval</p>
    {{ $fmtText(optional($handover)->manager_handover_approval) }}
</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3 green-heading">
    <p class="mb-0">Handover Notes</p>
    {{ optional($handover)->handover_notes ?: 'N/A' }}
</div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-asset">
                    <div class="row text-center">
                        <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading">
    <p class="mb-0">Clearance Status</p>
    {{ $fmtText(optional($assetClearance)->clearance_status) }}
</div>
                        <div class="col-xxl-9 col-lg-8 col-md-12 col-12 mb-3 green-heading">
    <p class="mb-0">Remarks</p>
    {{ optional($assetClearance)->remarks ?: 'N/A' }}
</div>
                    </div>
                    @if($assetClearance && $assetClearance->assets && $assetClearance->assets->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Asset</th>
                                        <th>Applicable</th>
                                        <th>Serial</th>
                                        <th>Return Date</th>
                                        <th>Condition</th>
                                        <th>Recovery</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetClearance->assets as $asset)
                                        <tr>
                                            <td>{{ $asset->asset_name ?: 'N/A' }}</td>
                                            <td>{{ $fmtText($asset->applicable) }}</td>
                                            <td>{{ $asset->serial_number ?: 'N/A' }}</td>
                                            <td>{{ $fmtDate($asset->asset_return_date) }}</td>
                                            <td>{{ $fmtText($asset->asset_condition) }}</td>
                                            <td>{{ $money($asset->asset_recovery_amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted p-2">No asset items saved.</div>
                    @endif
                </div>

                <div class="tab-pane fade" id="detail-it">
                    <div class="text-muted p-2">IT and access clearance details are not saved in the current EOS tables.
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-eos">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Leave Balance</p>
    {{ optional($finance)->leave_balance_at_exit ?: 'N/A' }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Encash Eligible</p>
    {{ $fmtText(optional($finance)->leave_encashment_eligible) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Encash Amount</p>
    {{ $money(optional($finance)->leave_encashment_amount) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Gratuity</p>
    {{ $money(optional($finance)->gratuity_amount) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Deductions</p>
    {{ $money(optional($finance)->total_deductions) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Net Payable</p>
    {{ $money(optional($finance)->net_eos_payable) }}
</div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-final">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Visa Type</p>
    {{ $fmtText(optional($finalSettlement)->visa_type) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Visa Cancel Req.</p>
    {{ $fmtText(optional($finalSettlement)->visa_cancellation_required) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Visa Cancel Date</p>
    {{ $fmtDate(optional($finalSettlement)->visa_cancellation_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Labour Cancel Date</p>
    {{ $fmtDate(optional($finalSettlement)->labour_card_cancellation_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Immigration</p>
    {{ $fmtText(optional($finalSettlement)->immigration_clearance_status) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Exit Permit</p>
    {{ $fmtText(optional($finalSettlement)->exit_permit_issued) }}
</div>
                    </div>
                    </div>
            </div>
        </div>

        <div class="tab-wrap mb-3 mt-4">

                <ul class="nav nav-tabs" id="eosDetailTab2" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#detail-legal" type="button">Legal & Compliance</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-exit"
                            type="button">Exit Interview</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#detail-approval"
                            type="button">Approval Status</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#detail-documents" type="button">Documents</button></li>
                </ul>
            <div class="tab-content mb-3">
                <div class="tab-pane fade show active" id="detail-legal">
                    <div class="row text-center">
                        @foreach($legalDocuments as $legalDocument)
                            <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 green-heading">
                                <p class="mb-0">{{ $legalDocument['label'] }}</p>
                                @if(!empty($legalDocument['path']) && \Illuminate\Support\Facades\Storage::exists($legalDocument['path']))
                                    <a href="{{ $downloadUrl(['field' => $legalDocument['field']]) }}" class="text-success">
                                        <i class="ico icon-bold-download-minimalistic text-success"></i> Download
                                    </a>
                                @else
                                    N/A
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-exit">
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Conducted</p>
    {{ $fmtText(optional($exitInterview)->exit_interview_conducted) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Date</p>
    {{ $fmtDate(optional($exitInterview)->exit_interview_date) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Mode</p>
    {{ $fmtText(optional($exitInterview)->interview_mode) }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Rating</p>
    {{ optional($exitInterview)->overall_satisfaction_rating ?: 'N/A' }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">Manager Feedback</p>
    {{ optional($exitInterview)->manager_feedback ?: 'N/A' }}
</div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
    <p class="mb-0">HR Feedback</p>
    {{ optional($exitInterview)->hr_feedback ?: 'N/A' }}
</div>
                    </div>
                </div>

                <div class="tab-pane fade" id="detail-approval">
                    @if($resignation->approvals && $resignation->approvals->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>Approver</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resignation->approvals as $approval)
                                        <tr>
                                            <td>{{ $approval->approval_level ?: 'N/A' }}</td>
                                            <td>{{ $approval->approver_id ?: 'N/A' }}</td>
                                            <td>{{ $fmtText($approval->approval_status) }}</td>
                                            <td>{{ $fmtDate($approval->approval_date) }}</td>
                                            <td>{{ $approval->remarks ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted p-2">No approval records saved.</div>
                    @endif
                </div>

                <div class="tab-pane fade" id="detail-documents">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Document</th>
                                    <th>Date</th>
                                    <th>Attachment</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($legalDocuments as $legalDocument)
                                    <tr>
                                        <td>{{ $legalDocument['label'] }}</td>
                                        <td>N/A</td>
                                        <td>
                                            @if(!empty($legalDocument['path']) && \Illuminate\Support\Facades\Storage::exists($legalDocument['path']))
                                                <a href="{{ $downloadUrl(['field' => $legalDocument['field']]) }}" class="text-success">
                                                    <i class="ico icon-bold-download-minimalistic text-success"></i> Download
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>N/A</td>
                                    </tr>
                                @endforeach
                                @if($resignation->documents && $resignation->documents->count())
                                    @foreach($resignation->documents as $document)
                                        <tr>
                                            <td>{{ $document->document_name ?: 'N/A' }}</td>
                                            <td>{{ $fmtDate($document->document_date) }}</td>
                                            <td>
                                                @if(!empty($document->attachment) && \Illuminate\Support\Facades\Storage::exists($document->attachment))
                                                    <a href="{{ $downloadUrl(['document_id' => $document->id]) }}" class="text-success">
                                                        <i class="ico icon-bold-download-minimalistic text-success"></i> Download
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $document->remarks ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

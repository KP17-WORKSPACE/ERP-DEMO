@php
    $employee = $compensation->employee ?? null;
    $statusText = ucfirst($compensation->status ?? 'Unknown');
    
    // Compatible with older PHP versions
    $status = $compensation->status ?? '';
    if ($status == 'approved') {
        $statusClass = 'success';
    } elseif ($status == 'pending') {
        $statusClass = 'warning';
    } elseif ($status == 'rejected') {
        $statusClass = 'danger';
    } elseif ($status == 'draft') {
        $statusClass = 'secondary';
    } else {
        $statusClass = 'secondary';
    }
    
    $changeTypeText = ucfirst(str_replace('_', ' ', $compensation->change_type ?? 'N/A'));
    $effectiveDate = $compensation->effective_date ? \Carbon\Carbon::parse($compensation->effective_date)->format('d/m/Y') : '—';
    $createdDate = $compensation->created_at ? $compensation->created_at->format('d/m/Y H:i') : '—';
@endphp

<style>
    .view-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 2px;
    }
    .view-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: #212529;
        min-height: 24px;
    }
    .view-card {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
    }
    .table-view th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .table-view td {
        font-size: 0.85rem;
    }
</style>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        Compensation & Role Change - {{ $compensation->doc_no ?? 'N/A' }}
        <span class="badge bg-{{ $statusClass }} ms-2">{{ $statusText }}</span>
    </h4>
    <div class="purchase-order-content-header-right">
        @if(in_array($compensation->status, ['pending', 'draft']))
            <a href="#" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
        @endif
        <a href="{{ route('staff.compensation.create') }}" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add New
        </a>
        <a href="{{ route('staff.compensation.list') }}" class="btn btn-light">
            <i class="ico icon-outline-arrow-left text-primary"></i> Back
        </a>
    </div>
</div>

{{-- Header Card with Employee Photo and Basic Info --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row g-3 align-items-start">
            {{-- Employee Photo --}}
            <div class="col-md-2 text-center">
                <img src="{{ $employee->staff_photo_public_url ?? asset('public/uploads/staff/demo/staff.png') }}" 
                     alt="Employee Photo" 
                     class="img-fluid rounded mb-2"
                     style="max-width:120px; height:120px; object-fit:cover;" 
                     loading="lazy">
                <div class="badge bg-primary">{{ $changeTypeText }}</div>
            </div>

            {{-- Quick Info --}}
            <div class="col-md-10">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="view-label">Employee ID</div>
                        <div class="view-value fw-bold text-success">{{ $employee->user_id ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Employee Name</div>
                        <div class="view-value">{{ $employee->full_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Department</div>
                        <div class="view-value">{{ optional($employee->departments)->department_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Designation</div>
                        <div class="view-value">{{ optional($employee->designations)->title ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Email</div>
                        <div class="view-value text-truncate" title="{{ $employee->email }}">{{ $employee->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Phone</div>
                        <div class="view-value">{{ $employee->phone ?? $employee->mobile ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Change Type</div>
                        <div class="view-value">{{ $changeTypeText }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Effective Date</div>
                        <div class="view-value">{{ $effectiveDate }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Current Salary</div>
                        <div class="view-value">{{ $employee->basic_salary ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Join Date</div>
                        <div class="view-value">{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Created Date</div>
                        <div class="view-value">{{ $createdDate }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Compensation Details Tabs --}}
<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="compensationDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="change-detail-tab" data-bs-toggle="tab" data-bs-target="#change-details-view" type="button" role="tab">Change Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approval-detail-tab" data-bs-toggle="tab" data-bs-target="#approval-details-view" type="button" role="tab">Approval & History</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="acknowledgment-detail-tab" data-bs-toggle="tab" data-bs-target="#acknowledgment-details-view" type="button" role="tab">Employee Acknowledgment</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="documents-detail-tab" data-bs-toggle="tab" data-bs-target="#documents-details-view" type="button" role="tab">Supporting Documents</button>
        </li>
    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white" id="compensationDetailTabsContent">
        
        {{-- CHANGE DETAILS TAB --}}
        <div class="tab-pane fade show active" id="change-details-view" role="tabpanel">
            <div class="accordion" id="changeDetailsViewAccordion">
                
                {{-- 1. Basic Change Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewBasicChangeInfo" aria-expanded="true">
                            <span class="me-2">1</span> Basic Change Information
                        </button>
                    </h2>
                    <div id="viewBasicChangeInfo" class="accordion-collapse collapse show" data-bs-parent="#changeDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="view-label">Document No</div>
                                    <div class="view-value fw-bold">{{ $compensation->doc_no ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Change Type</div>
                                    <div class="view-value">{{ $changeTypeText }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Effective Date</div>
                                    <div class="view-value">{{ $effectiveDate }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Status</div>
                                    <div class="view-value">
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="view-label">Reason</div>
                                    <div class="view-value">{{ $compensation->reason ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Created By</div>
                                    <div class="view-value">{{ $compensation->created_by ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Last Updated</div>
                                    <div class="view-value">{{ $compensation->updated_at ? $compensation->updated_at->format('d/m/Y H:i') : '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Specific Change Details --}}
                @if($compensation->change_type == 'promotion' && $compensation->promotionDetails)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewPromotionDetails">
                                <span class="me-2">2</span> Promotion Details
                            </button>
                        </h2>
                        <div id="viewPromotionDetails" class="accordion-collapse collapse" data-bs-parent="#changeDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Department</div>
                                        <div class="view-value">{{ $compensation->promotionDetails->previous_department ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Department</div>
                                        <div class="view-value fw-bold text-success">{{ $compensation->promotionDetails->new_department ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Designation</div>
                                        <div class="view-value">{{ $compensation->promotionDetails->previous_designation ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Designation</div>
                                        <div class="view-value fw-bold text-success">{{ $compensation->promotionDetails->new_designation ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Salary</div>
                                        <div class="view-value">{{ $compensation->promotionDetails->previous_salary ? number_format($compensation->promotionDetails->previous_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Salary</div>
                                        <div class="view-value fw-bold text-success">{{ $compensation->promotionDetails->new_salary ? number_format($compensation->promotionDetails->new_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Salary Increment</div>
                                        <div class="view-value fw-bold text-info">
                                            @if($compensation->promotionDetails->new_salary && $compensation->promotionDetails->previous_salary)
                                                {{ number_format($compensation->promotionDetails->new_salary - $compensation->promotionDetails->previous_salary, 2) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Increment %</div>
                                        <div class="view-value fw-bold text-info">
                                            @if($compensation->promotionDetails->new_salary && $compensation->promotionDetails->previous_salary && $compensation->promotionDetails->previous_salary > 0)
                                                {{ number_format((($compensation->promotionDetails->new_salary - $compensation->promotionDetails->previous_salary) / $compensation->promotionDetails->previous_salary) * 100, 1) }}%
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    @if($compensation->promotionDetails->justification)
                                        <div class="col-12">
                                            <div class="view-label">Justification</div>
                                            <div class="view-card">{{ $compensation->promotionDetails->justification }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($compensation->change_type == 'demotion' && $compensation->demotionDetails)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewDemotionDetails">
                                <span class="me-2">2</span> Demotion Details
                            </button>
                        </h2>
                        <div id="viewDemotionDetails" class="accordion-collapse collapse" data-bs-parent="#changeDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Department</div>
                                        <div class="view-value">{{ $compensation->demotionDetails->previous_department ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Department</div>
                                        <div class="view-value text-warning">{{ $compensation->demotionDetails->new_department ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Designation</div>
                                        <div class="view-value">{{ $compensation->demotionDetails->previous_designation ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Designation</div>
                                        <div class="view-value text-warning">{{ $compensation->demotionDetails->new_designation ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Salary</div>
                                        <div class="view-value">{{ $compensation->demotionDetails->previous_salary ? number_format($compensation->demotionDetails->previous_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Salary</div>
                                        <div class="view-value text-warning">{{ $compensation->demotionDetails->new_salary ? number_format($compensation->demotionDetails->new_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Nature of Demotion</div>
                                        <div class="view-value">{{ $compensation->demotionDetails->nature_of_demotion ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Salary Reduction</div>
                                        <div class="view-value text-danger">
                                            @if($compensation->demotionDetails->previous_salary && $compensation->demotionDetails->new_salary)
                                                {{ number_format($compensation->demotionDetails->previous_salary - $compensation->demotionDetails->new_salary, 2) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    @if($compensation->demotionDetails->reason)
                                        <div class="col-12">
                                            <div class="view-label">Reason for Demotion</div>
                                            <div class="view-card">{{ $compensation->demotionDetails->reason }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($compensation->change_type == 'salary_increment' && $compensation->salaryIncrementDetails)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewIncrementDetails">
                                <span class="me-2">2</span> Salary Increment Details
                            </button>
                        </h2>
                        <div id="viewIncrementDetails" class="accordion-collapse collapse" data-bs-parent="#changeDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="view-label">Previous Salary</div>
                                        <div class="view-value">{{ $compensation->salaryIncrementDetails->previous_salary ? number_format($compensation->salaryIncrementDetails->previous_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">New Salary</div>
                                        <div class="view-value fw-bold text-success">{{ $compensation->salaryIncrementDetails->new_salary ? number_format($compensation->salaryIncrementDetails->new_salary, 2) : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Increment Amount</div>
                                        <div class="view-value fw-bold text-info">
                                            @if($compensation->salaryIncrementDetails->new_salary && $compensation->salaryIncrementDetails->previous_salary)
                                                {{ number_format($compensation->salaryIncrementDetails->new_salary - $compensation->salaryIncrementDetails->previous_salary, 2) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Increment Percentage</div>
                                        <div class="view-value fw-bold text-info">
                                            @if($compensation->salaryIncrementDetails->new_salary && $compensation->salaryIncrementDetails->previous_salary && $compensation->salaryIncrementDetails->previous_salary > 0)
                                                {{ number_format((($compensation->salaryIncrementDetails->new_salary - $compensation->salaryIncrementDetails->previous_salary) / $compensation->salaryIncrementDetails->previous_salary) * 100, 1) }}%
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="view-label">Performance Rating</div>
                                        <div class="view-value">{{ $compensation->salaryIncrementDetails->performance_rating ?? '—' }}</div>
                                    </div>
                                    @if($compensation->salaryIncrementDetails->justification)
                                        <div class="col-12">
                                            <div class="view-label">Justification</div>
                                            <div class="view-card">{{ $compensation->salaryIncrementDetails->justification }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- APPROVAL & HISTORY TAB --}}
        <div class="tab-pane fade" id="approval-details-view" role="tabpanel">
            <div class="accordion" id="approvalDetailsViewAccordion">
                
                {{-- Approvals List --}}
                @if($compensation->approvals && $compensation->approvals->count() > 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewApprovals" aria-expanded="true">
                                <span class="me-2">1</span> Approval Status
                            </button>
                        </h2>
                        <div id="viewApprovals" class="accordion-collapse collapse show" data-bs-parent="#approvalDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-view table-striped">
                                        <thead>
                                            <tr>
                                                <th>Approver</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($compensation->approvals as $approval)
                                                <tr>
                                                    <td>{{ optional($approval->approver)->full_name ?? '—' }}</td>
                                                    <td>{{ optional($approval->approver)->designations->title ?? '—' }}</td>
                                                    <td>
                                                        @if($approval->status == 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($approval->status == 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $approval->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->format('d/m/Y H:i') : '—' }}</td>
                                                    <td>{{ $approval->comments ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewApprovals" aria-expanded="true">
                                <span class="me-2">1</span> Approval Status
                            </button>
                        </h2>
                        <div id="viewApprovals" class="accordion-collapse collapse show" data-bs-parent="#approvalDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="text-center text-muted py-3">
                                    <p>No approval records found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Approval History --}}
                @if($compensation->approvalHistory && $compensation->approvalHistory->count() > 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewApprovalHistory">
                                <span class="me-2">2</span> Approval History
                            </button>
                        </h2>
                        <div id="viewApprovalHistory" class="accordion-collapse collapse" data-bs-parent="#approvalDetailsViewAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-view table-striped">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>By</th>
                                                <th>Previous Status</th>
                                                <th>New Status</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($compensation->approvalHistory as $history)
                                                <tr>
                                                    <td>{{ $history->action ?? '—' }}</td>
                                                    <td>{{ optional($history->user)->full_name ?? '—' }}</td>
                                                    <td>{{ $history->previous_status ?? '—' }}</td>
                                                    <td>{{ $history->new_status ?? '—' }}</td>
                                                    <td>{{ $history->created_at ? $history->created_at->format('d/m/Y H:i') : '—' }}</td>
                                                    <td>{{ $history->remarks ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- EMPLOYEE ACKNOWLEDGMENT TAB --}}
        <div class="tab-pane fade" id="acknowledgment-details-view" role="tabpanel">
            <div class="accordion" id="acknowledgmentDetailsViewAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewAcknowledgment" aria-expanded="true">
                            <span class="me-2">1</span> Employee Acknowledgment
                        </button>
                    </h2>
                    <div id="viewAcknowledgment" class="accordion-collapse collapse show" data-bs-parent="#acknowledgmentDetailsViewAccordion">
                        <div class="accordion-body">
                            @if($compensation->acknowledgement)
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="view-label">Acknowledgment Status</div>
                                        <div class="view-value">
                                            @if($compensation->acknowledgement->is_acknowledged)
                                                <span class="badge bg-success">Acknowledged</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Acknowledgment Date</div>
                                        <div class="view-value">{{ $compensation->acknowledgement->acknowledged_at ? \Carbon\Carbon::parse($compensation->acknowledgement->acknowledged_at)->format('d/m/Y H:i') : '—' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="view-label">Digital Signature</div>
                                        <div class="view-value">
                                            @if($compensation->acknowledgement->digital_signature)
                                                <span class="badge bg-info">Provided</span>
                                            @else
                                                <span class="badge bg-secondary">Not provided</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($compensation->acknowledgement->employee_comments)
                                        <div class="col-12">
                                            <div class="view-label">Employee Comments</div>
                                            <div class="view-card">{{ $compensation->acknowledgement->employee_comments }}</div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <p>No acknowledgment record found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUPPORTING DOCUMENTS TAB --}}
        <div class="tab-pane fade" id="documents-details-view" role="tabpanel">
            <div class="accordion" id="documentsDetailsViewAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewDocuments" aria-expanded="true">
                            <span class="me-2">1</span> Supporting Documents
                        </button>
                    </h2>
                    <div id="viewDocuments" class="accordion-collapse collapse show" data-bs-parent="#documentsDetailsViewAccordion">
                        <div class="accordion-body">
                            @if(!empty($compensation->supporting_documents))
                                <div class="table-responsive">
                                    <table class="table table-view">
                                        <thead>
                                            <tr>
                                                <th>Document Name</th>
                                                <th>Type</th>
                                                <th>Size</th>
                                                <th>Upload Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $documents = is_string($compensation->supporting_documents) ? json_decode($compensation->supporting_documents, true) : $compensation->supporting_documents;
                                            @endphp
                                            @if(is_array($documents))
                                                @foreach($documents as $document)
                                                    <tr>
                                                        <td>{{ $document['name'] ?? 'Document' }}</td>
                                                        <td>{{ $document['type'] ?? '—' }}</td>
                                                        <td>{{ isset($document['size']) ? number_format($document['size'] / 1024, 2) . ' KB' : '—' }}</td>
                                                        <td>{{ isset($document['uploaded_at']) ? \Carbon\Carbon::parse($document['uploaded_at'])->format('d/m/Y H:i') : '—' }}</td>
                                                        <td>
                                                            <a href="{{ $document['url'] ?? '#' }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="ico icon-outline-eye"></i> View
                                                            </a>
                                                            <a href="{{ $document['url'] ?? '#' }}" class="btn btn-sm btn-outline-success" download>
                                                                <i class="ico icon-outline-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <p>No supporting documents uploaded</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
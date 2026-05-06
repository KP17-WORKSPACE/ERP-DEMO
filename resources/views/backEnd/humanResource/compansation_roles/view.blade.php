@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">View Compensation & Role Change</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item">HR</li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.compensation.list') }}">Compensation</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Document: {{ $compensation->doc_no }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('staff.compensation.create', $compensation->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('staff.compensation.list') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Document Header -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <strong>Document No:</strong>
                                    <p>{{ $compensation->doc_no }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Document Date:</strong>
                                    <p>{{ $compensation->doc_date ? \Carbon\Carbon::parse($compensation->doc_date)->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Employee:</strong>
                                    <p>{{ $compensation->employee->full_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Current Status:</strong>
                                    <p>
                                        @switch($compensation->current_status)
                                            @case('draft')
                                                <span class="badge badge-secondary">Draft</span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- Main Information -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <strong>Department:</strong>
                                    <p>{{ $compensation->employee->departments->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Designation:</strong>
                                    <p>{{ $compensation->employee->designations->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Transaction Type:</strong>
                                    <p>
                                        <span class="badge badge-info">
                                            {{ ucfirst(str_replace('_', ' ', $compensation->transaction_type)) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Effective Date:</strong>
                                    <p>{{ $compensation->effective_date ? \Carbon\Carbon::parse($compensation->effective_date)->format('d-m-Y') : 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Promotion Details -->
                            @if($compensation->transaction_type == 'promotion' && $compensation->promotionDetails)
                                <div class="card mt-3 mb-3">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title text-white">Promotion Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Promotion Type:</strong> {{ $compensation->promotionDetails->promotion_type ?? 'N/A' }}</p>
                                                <p><strong>Current Grade:</strong> {{ $compensation->promotionDetails->current_grade ?? 'N/A' }}</p>
                                                <p><strong>New Grade:</strong> {{ $compensation->promotionDetails->new_grade ?? 'N/A' }}</p>
                                                <p><strong>Proposed Salary:</strong> {{ $compensation->promotionDetails->proposed_salary ? number_format($compensation->promotionDetails->proposed_salary, 2) : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Promotion Reason:</strong> {{ $compensation->promotionDetails->promotion_reason ?? 'N/A' }}</p>
                                                <p><strong>Position Availability:</strong> {{ $compensation->promotionDetails->position_availability ?? 'N/A' }}</p>
                                                <p><strong>New Reporting Manager:</strong> {{ $compensation->promotionDetails->reportingManager->full_name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        @if($compensation->promotionDetails->promotion_letter_path)
                                            <p><a href="{{ asset('storage/' . $compensation->promotionDetails->promotion_letter_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Download Promotion Letter
                                            </a></p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Demotion Details -->
                            @if($compensation->transaction_type == 'demotion' && $compensation->demotionDetails)
                                <div class="card mt-3 mb-3">
                                    <div class="card-header bg-warning">
                                        <h5 class="card-title text-white">Demotion Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Demotion Type:</strong> {{ $compensation->demotionDetails->demotion_type ?? 'N/A' }}</p>
                                                <p><strong>Nature of Demotion:</strong> {{ $compensation->demotionDetails->nature_of_demotion ?? 'N/A' }}</p>
                                                <p><strong>Revised Grade:</strong> {{ $compensation->demotionDetails->revised_grade ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Reason for Demotion:</strong> {{ $compensation->demotionDetails->reason_for_demotion ?? 'N/A' }}</p>
                                                <p><strong>Consent Status:</strong> {{ $compensation->demotionDetails->consent_status ?? 'N/A' }}</p>
                                                <p><strong>Appeal Option:</strong> {{ $compensation->demotionDetails->appeal_option ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Salary Increment Details -->
                            @if($compensation->transaction_type == 'increment' && $compensation->salaryIncrementDetails)
                                <div class="card mt-3 mb-3">
                                    <div class="card-header bg-success">
                                        <h5 class="card-title text-white">Salary Increment Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Increment Category:</strong> {{ $compensation->salaryIncrementDetails->increment_category ?? 'N/A' }}</p>
                                                <p><strong>Increment Trigger:</strong> {{ $compensation->salaryIncrementDetails->increment_trigger ?? 'N/A' }}</p>
                                                <p><strong>Increment Percentage:</strong> {{ $compensation->salaryIncrementDetails->increment_percentage ? $compensation->salaryIncrementDetails->increment_percentage . '%' : 'N/A' }}</p>
                                                <p><strong>Current Basic Salary:</strong> {{ $compensation->salaryIncrementDetails->current_basic_salary ? number_format($compensation->salaryIncrementDetails->current_basic_salary, 2) : 'N/A' }}</p>
                                                <p><strong>Revised Basic Salary:</strong> {{ $compensation->salaryIncrementDetails->revised_basic_salary ? number_format($compensation->salaryIncrementDetails->revised_basic_salary, 2) : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Current Gross Salary:</strong> {{ $compensation->salaryIncrementDetails->current_gross_salary ? number_format($compensation->salaryIncrementDetails->current_gross_salary, 2) : 'N/A' }}</p>
                                                <p><strong>Revised Gross Salary:</strong> {{ $compensation->salaryIncrementDetails->revised_gross_salary ? number_format($compensation->salaryIncrementDetails->revised_gross_salary, 2) : 'N/A' }}</p>
                                                <p><strong>Monthly Cost Impact:</strong> {{ $compensation->salaryIncrementDetails->monthly_cost_impact ? number_format($compensation->salaryIncrementDetails->monthly_cost_impact, 2) : 'N/A' }}</p>
                                                <p><strong>Annual Cost Impact:</strong> {{ $compensation->salaryIncrementDetails->annual_cost_impact ? number_format($compensation->salaryIncrementDetails->annual_cost_impact, 2) : 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Approvals -->
                            @if($compensation->approvals)
                                <div class="card mt-3 mb-3">
                                    <div class="card-header bg-primary">
                                        <h5 class="card-title text-white">Approval History</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Approval Level</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($compensation->approvals as $approval)
                                                    <tr>
                                                        <td>{{ ucfirst($approval->approval_level) }}</td>
                                                        <td>
                                                            @if($approval->approval_status == 'approve')
                                                                <span class="badge badge-success">Approved</span>
                                                            @elseif($approval->approval_status == 'reject')
                                                                <span class="badge badge-danger">Rejected</span>
                                                            @else
                                                                <span class="badge badge-warning">{{ ucfirst($approval->approval_status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $approval->approval_date ? \Carbon\Carbon::parse($approval->approval_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                                        <td>{{ $approval->remarks ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Employee Acknowledgement -->
                            @if($compensation->acknowledgement)
                                <div class="card mt-3 mb-3">
                                    <div class="card-header bg-secondary">
                                        <h5 class="card-title text-white">Employee Acknowledgement</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>
                                            <strong>Status:</strong>
                                            @if($compensation->acknowledgement->employee_acknowledged)
                                                <span class="badge badge-success">Acknowledged</span>
                                                <br>
                                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($compensation->acknowledgement->acknowledgement_date)->format('d-m-Y H:i') }}
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

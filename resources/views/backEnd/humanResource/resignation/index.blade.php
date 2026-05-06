@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <style>
        .form-scroll {
            overflow-y: auto;
            padding-right: 6px;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #dc3545;
        }

        .small-dropdown-font option {
            font-size: 10px !important;
        }

        .small-dropdown-font {
            font-size: 10px !important;
        }

        .select2-results__option {
            font-size: 11px !important;
        }

        .tab-wrap .nav.nav-tabs {
            padding: 0 15px !important;
            gap: 10px !important;
        }
    </style>
    @php
        $editMode = isset($editMode) && $editMode === true;
        $staffData = isset($editData) ? $editData : null;
        $job = isset($jobRow) ? $jobRow : null;
    @endphp
    <div class="form-scroll">
        <form id="goods-receipt-note-store-form" novalidate action="{{ route('staff.resignation.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_staff_id" name="staff_id" value="{{ $staffData->id ?? '' }}">
            @if($editMode && isset($eosData['main']))
                <input type="hidden" name="eos_id" value="{{ $eosData['main']->id }}">
            @endif

            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="endOfServiceTabContent">
                    <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                Staff End of Service {{ $editMode ? '(Edit Mode)' : '(New)' }}
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right">
                                <button type="submit"
                                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2" id="btnSaveAll">
                                    <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                                    <span class="btn-text">Save</span>
                                </button>
                                <a class="btn btn-light" href="{{ route('staff.resignation.list') }}">Resignation List</a>
                                <a class="btn btn-light" href="{{ url('staff-directory') }}">Staff Directory</a>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                        <div class="white-box">
                                            <div class="staff">
                                                <input type="hidden" name="url" id="url"
                                                    value="{{ URL::to('/') }}">
                                                <div class="row mb-30">
                                                    <div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Employee Name --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Employee Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="employee_id" id="employee_id" required>
                                                                        <option value="">Select Employee</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}" {{ (isset($eosData['main']) && $eosData['main']->employee_id == $staff->id) ? 'selected' : '' }}>
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Department --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Department</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="department_id" id="department_id">
                                                                        <option value="">Select Department</option>
                                                                        @foreach ($departments as $department)
                                                                            <option value="{{ $department->id }}">
                                                                                {{ $department->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Designation --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Designation</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="designation_id" id="designation_id">
                                                                        <option value="">Select Designation</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Reporting Manager --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Reporting Manager</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="reporting_manager" id="reporting_manager">
                                                                        <option value="">Select Manager</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}">
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Separation Type --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Separation Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="separation_type" id="separation_type"
                                                                        >
                                                                        <option value="">Select Type</option>
                                                                        <option value="resignation" {{ (isset($eosData['main']) && strtolower($eosData['main']->separation_type) == 'resignation') ? 'selected' : '' }}>Resignation</option>
                                                                        <option value="termination" {{ (isset($eosData['main']) && strtolower($eosData['main']->separation_type) == 'termination') ? 'selected' : '' }}>Termination</option>
                                                                        <option value="end_of_contract" {{ (isset($eosData['main']) && strtolower(str_replace(' ', '_', $eosData['main']->separation_type)) == 'end_of_contract') ? 'selected' : '' }}>End of Contract
                                                                        </option>
                                                                        <option value="retirement" {{ (isset($eosData['main']) && strtolower($eosData['main']->separation_type) == 'retirement') ? 'selected' : '' }}>Retirement</option>
                                                                        <option value="absconding" {{ (isset($eosData['main']) && strtolower($eosData['main']->separation_type) == 'absconding') ? 'selected' : '' }}>Absconding</option>
                                                                        <option value="death" {{ (isset($eosData['main']) && strtolower($eosData['main']->separation_type) == 'death') ? 'selected' : '' }}>Death</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Resignation Type --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Resignation Type</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="resignation_type" id="resignation_type">
                                                                        <option value="">Select Type</option>
                                                                        <option value="voluntary" {{ (isset($eosData['main']) && strtolower($eosData['main']->resignation_type) == 'voluntary') ? 'selected' : '' }}>Voluntary</option>
                                                                        <option value="involuntary" {{ (isset($eosData['main']) && strtolower($eosData['main']->resignation_type) == 'involuntary') ? 'selected' : '' }}>Involuntary</option>
                                                                        <option value="mutual_separation" {{ (isset($eosData['main']) && strtolower(str_replace(' ', '_', $eosData['main']->resignation_type)) == 'mutual_separation') ? 'selected' : '' }}>Mutual Separation
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- Second Row: Initiated By, Reason Category, Detailed Reason --}}
                                                    <div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Initiated By --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Initiated By</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="initiated_by" id="initiated_by">
                                                                        <option value="">Select</option>
                                                                        <option value="employee">Employee</option>
                                                                        <option value="company">Company</option>
                                                                        <option value="management">Management</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Reason Category --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Reason Category</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="reason_category" id="reason_category">
                                                                        <option value="">Select Category</option>
                                                                        <option value="personal">Personal</option>
                                                                        <option value="performance">Performance</option>
                                                                        <option value="misconduct">Misconduct</option>
                                                                        <option value="redundancy">Redundancy</option>
                                                                        <option value="health">Health</option>
                                                                        <option value="relocation">Relocation</option>
                                                                        <option value="better_opportunity">Better
                                                                            Opportunity</option>
                                                                        <option value="other">Other</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Detailed Reason --}}
                                                            <div class="col-lg-8">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Detailed Reason</label>
                                                                    <textarea class="form-control form-control-sm" name="detailed_reason" id="detailed_reason" rows="2"
                                                                        placeholder="Provide detailed reason for separation"></textarea>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- TABS SECTION --}}
                                                    <div class="tab-wrap mb-3">
                                                        <ul class="nav nav-tabs mt-4" id="eosTab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="notice-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#notice"
                                                                    type="button" role="tab">
                                                                    Resignation & Notice Period
                                                                </button>
                                                            </li>
                                                            <li  class="nav-item" role="presentation">
                                                                <button class="nav-link" id="handover-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#handover"
                                                                    type="button" role="tab">
                                                                    Handover Process
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="asset-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#asset"
                                                                    type="button" role="tab">
                                                                    Asset Clearance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="it-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#it"
                                                                    type="button" role="tab">
                                                                    IT & Access Clearance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="eos-calc-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#eos-calc"
                                                                    type="button" role="tab">
                                                                    EOS Calculation
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="final-settlement-tab"
                                                                    data-bs-toggle="tab"
                                                                    data-bs-target="#final-settlement" type="button"
                                                                    role="tab">
                                                                    Final Settlement
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="legal-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#legal"
                                                                    type="button" role="tab">
                                                                    Legal & Compliance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="exit-interview-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#exit-interview"
                                                                    type="button" role="tab">
                                                                    Exit Interview
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="approval-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#approval"
                                                                    type="button" role="tab">
                                                                    Approval Status
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="documents-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#documents"
                                                                    type="button" role="tab">
                                                                    Documents
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="tab-content mt-3" id="eosTabContent">
                                                        {{-- TAB 1: Resignation & Notice Period --}}
                                                        <div class="tab-pane fade show active" id="notice"
                                                            role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Notice Waiver --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice
                                                                            Waiver</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_waiver">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Waiver Approved By --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Waiver
                                                                            Approved By</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_waiver_approved_by">
                                                                            <option value="">Select</option>
                                                                            <option value="manager">Manager</option>
                                                                            <option value="hr">HR</option>
                                                                            <option value="management">Management</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Period Served --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Period
                                                                            Served</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_period_served">
                                                                            <option value="">Select</option>
                                                                            <option value="full">Full</option>
                                                                            <option value="partial">Partial</option>
                                                                            <option value="not_served">Not Served</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Resignation Submitted Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Resignation
                                                                            Submitted Date <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="resignation_submitted_date" 
                                                                            value="{{ isset($eosData['notice']) ? $eosData['notice']->resignation_submitted_date : '' }}" >
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Period (Days) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Period (Days)
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number"
                                                                            class="form-control form-control-sm"
                                                                            name="notice_period_days" placeholder="30"
                                                                            value="{{ isset($eosData['notice']) ? $eosData['notice']->notice_period_days : '' }}" >
                                                                    </div>
                                                                </div>

                                                                {{-- Last Working Day --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Last Working
                                                                            Day</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="last_working_day">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Garden Leave Applicable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave
                                                                            Applicable</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="garden_leave_applicable">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Garden Leave Start Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave Start
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="garden_leave_start_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Garden Leave End Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave End
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="garden_leave_end_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Relieving Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Relieving
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="relieving_date">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 3: Handover Process --}}
                                                        <div class="tab-pane fade" id="handover" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Knowledge Transfer Required --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Knowledge Transfer
                                                                            Required</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="knowledge_transfer_required" >
                                                                            <option value="">Select</option>
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Start Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover Start
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="handover_start_date" >
                                                                    </div>
                                                                </div>

                                                                {{-- Handover End Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover End
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="handover_end_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Handover To (Employee) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover To
                                                                            (Employee)</label>
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="handover_to_employee">
                                                                            <option value="">Select Employee</option>
                                                                            <option value="1">John Smith</option>
                                                                            <option value="2">Sarah Johnson</option>
                                                                            <option value="3">Michael Brown</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Successor Assigned --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Successor
                                                                            Assigned</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="successor_assigned">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Successor Name --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Successor
                                                                            Name</label>
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="successor_name">
                                                                            <option value="">Select</option>
                                                                            <option value="1">John Smith</option>
                                                                            <option value="2">Sarah Johnson</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Client/Project Handover Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Client/Project
                                                                            Handover Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="client_handover_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- SOP/Documentation Shared --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">SOP/Documentation
                                                                            Shared</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="sop_shared">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Checklist Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover Checklist
                                                                            Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="handover_checklist_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Manager Handover Approval --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Manager Handover
                                                                            Approval</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="manager_handover_approval">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Notes --}}
                                                                <div class="col-lg-4">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover
                                                                            Notes</label>
                                                                        <textarea class="form-control form-control-sm" name="handover_notes" rows="2" placeholder="Handover notes"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 4: Asset Clearance --}}
                                                        <div class="tab-pane fade" id="asset" role="tabpanel">
                                                            <div class="mb-2">
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    id="addAssetRow">
                                                                    <i class="fa fa-plus"></i> Add Asset
                                                                </button>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm"
                                                                    id="assetTable">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Name of Assets</th>
                                                                            <th>Applicable</th>
                                                                            <th>Serial Number</th>
                                                                            <th>Asset Return Date</th>
                                                                            <th>Asset Condition</th>
                                                                            <th>Asset Recovery Amount</th>
                                                                            <th>Verified By</th>
                                                                            <th>Damage Remarks</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[0][name]"
                                                                                    value="Laptop Returned"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[0][applicable]">
                                                                                    <option value="na">N/A</option>
                                                                                    <option value="yes">Yes</option>
                                                                                    <option value="no">No</option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[0][serial_number]"></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="assets[0][return_date]"></td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-select form-select-sm asset-condition"
                                                                                    name="assets[0][condition]">
                                                                                    <option value="good">Good</option>
                                                                                    <option value="damaged">Damaged
                                                                                    </option>
                                                                                    <option value="missing">Missing
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="number" step="0.01"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[0][recovery_amount]"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[0][verified_by]">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">IT Admin
                                                                                    </option>
                                                                                    <option value="2">HR Manager
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control form-control-sm damage-remarks" name="assets[0][damage_remarks]" rows="1"
                                                                                    placeholder="Mandatory if damaged/missing"></textarea>
                                                                            </td>
                                                                            <td><button type="button"
                                                                                    class="btn btn-sm btn-danger remove-asset-row"><i
                                                                                        class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[1][name]"
                                                                                    value="Mobile Phone Returned"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[1][applicable]">
                                                                                    <option value="na">N/A</option>
                                                                                    <option value="yes">Yes</option>
                                                                                    <option value="no">No</option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[1][serial_number]"></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="assets[1][return_date]"></td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-select form-select-sm asset-condition"
                                                                                    name="assets[1][condition]">
                                                                                    <option value="good">Good</option>
                                                                                    <option value="damaged">Damaged
                                                                                    </option>
                                                                                    <option value="missing">Missing
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="number" step="0.01"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[1][recovery_amount]"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[1][verified_by]">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">IT Admin
                                                                                    </option>
                                                                                    <option value="2">HR Manager
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control form-control-sm damage-remarks" name="assets[1][damage_remarks]" rows="1"></textarea>
                                                                            </td>
                                                                            <td><button type="button"
                                                                                    class="btn btn-sm btn-danger remove-asset-row"><i
                                                                                        class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[2][name]"
                                                                                    value="Access Card / ID Returned"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[2][applicable]">
                                                                                    <option value="na">N/A</option>
                                                                                    <option value="yes">Yes</option>
                                                                                    <option value="no">No</option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[2][serial_number]"></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="assets[2][return_date]"></td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-select form-select-sm asset-condition"
                                                                                    name="assets[2][condition]">
                                                                                    <option value="good">Good</option>
                                                                                    <option value="damaged">Damaged
                                                                                    </option>
                                                                                    <option value="missing">Missing
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td><input type="number" step="0.01"
                                                                                    class="form-control form-control-sm"
                                                                                    name="assets[2][recovery_amount]"></td>
                                                                            <td>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="assets[2][verified_by]">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Security
                                                                                    </option>
                                                                                    <option value="2">HR Manager
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control form-control-sm damage-remarks" name="assets[2][damage_remarks]" rows="1"></textarea>
                                                                            </td>
                                                                            <td><button type="button"
                                                                                    class="btn btn-sm btn-danger remove-asset-row"><i
                                                                                        class="fa fa-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 5: IT & Access Clearance --}}
                                                        <div class="tab-pane fade" id="it" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Email Access Disabled --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Email Access
                                                                            Disabled</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="email_access_disabled">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Triggered after HR
                                                                            approval</small>
                                                                    </div>
                                                                </div>

                                                                {{-- ERP/System Access Revoked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">ERP/System Access
                                                                            Revoked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="erp_access_revoked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">SAP / ERP / CRM /
                                                                            HRMS</small>
                                                                    </div>
                                                                </div>

                                                                {{-- SIM Deactivation Confirmed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">SIM Deactivation
                                                                            Confirmed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="sim_deactivation">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Telecom
                                                                            confirmation</small>
                                                                    </div>
                                                                </div>

                                                                {{-- VPN Access Revoked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">VPN Access
                                                                            Revoked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="vpn_access_revoked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Remote access
                                                                            closure</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Data Backup Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Data Backup
                                                                            Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="data_backup_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Business data backed
                                                                            up</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Passwords Handed Over --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Passwords Handed
                                                                            Over</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="passwords_handed_over">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Admin / system
                                                                            credentials</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Asset Return Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset Return
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="it_asset_return_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Asset Damage/Missing --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset
                                                                            Damage/Missing</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="it_asset_damage">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Asset Recovery Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset Recovery
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm"
                                                                            name="it_recovery_amount" placeholder="0.00">
                                                                    </div>
                                                                </div>

                                                                {{-- Clearance Completed Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Clearance Completed
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="clearance_completed_date">
                                                                        <small class="text-muted">Locks F&F
                                                                            processing</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Final Clearance Approved By --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Final Clearance
                                                                            Approved By</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="final_clearance_approved_by">
                                                                            <option value="">Select</option>
                                                                            <option value="1">IT Head</option>
                                                                            <option value="2">System Admin</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 6: EOS Calculation --}}
                                                        <div class="tab-pane fade" id="eos-calc" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Leave Balance at Exit --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Balance at
                                                                            Exit</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_balance_at_exit" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Eligible --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Eligible</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="leave_encashment_eligible">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Days --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Days</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_encashment_days" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_encashment_amount" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- EOS Eligibility --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">EOS
                                                                            Eligibility</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="eos_eligibility" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- EOS Calculation Method --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">EOS Calculation
                                                                            Method</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="eos_calculation_method" readonly
                                                                            placeholder="Auto (UAE Law / Contract)">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2 mb-3">
                                                                {{-- Basic Salary for EOS --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Basic Salary for
                                                                            EOS</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="basic_salary_for_eos" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Gratuity Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Gratuity
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="gratuity_amount" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Other Allowances Payable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Other Allowances
                                                                            Payable</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="other_allowances_payable" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Loan / Advance Outstanding --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Loan / Advance
                                                                            Outstanding</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="loan_advance_outstanding" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Deductions (Fines, Assets, Notice) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Deductions (Fines,
                                                                            Assets, Notice)</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="deductions_total" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Total Deductions --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Total
                                                                            Deductions</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="total_deductions" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Net EOS Payable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Net EOS
                                                                            Payable</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light fw-bold"
                                                                            name="net_eos_payable" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Payroll Closure Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payroll Closure
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="payroll_closure_status">
                                                                            <option value="">Select</option>
                                                                            <option value="open">Open</option>
                                                                            <option value="processing">Processing</option>
                                                                            <option value="closed">Closed</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 7: Final Settlement --}}
                                                        <div class="tab-pane fade" id="final-settlement" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Full & Final Settlement Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Full & Final
                                                                            Settlement Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="fnf_settlement_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="processed">Processed</option>
                                                                            <option value="paid">Paid</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Mode of Payment --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Mode of
                                                                            Payment</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="mode_of_payment">
                                                                            <option value="">Select</option>
                                                                            <option value="bank_transfer">Bank Transfer
                                                                            </option>
                                                                            <option value="cheque">Cheque</option>
                                                                            <option value="cash">Cash</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Payment Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payment Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="payment_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Bank / Cheque Reference No. --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Bank / Cheque
                                                                            Reference No.</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="bank_cheque_reference"
                                                                            placeholder="Reference No.">
                                                                    </div>
                                                                </div>

                                                                {{-- Final Settlement Sheet --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Final Settlement
                                                                            Sheet</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="final_settlement_sheet"
                                                                            accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                        <small class="text-muted">PDF, DOC, XLS</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Payslip (Final Month) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payslip (Final
                                                                            Month)</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="final_payslip" accept=".pdf,.doc,.docx">
                                                                        <small class="text-muted">PDF, DOC</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 8: Legal & Compliance --}}
                                                        <div class="tab-pane fade" id="legal" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Visa Type --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Type</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="visa_type">
                                                                            <option value="">Select</option>
                                                                            <option value="company">Company</option>
                                                                            <option value="partner">Partner</option>
                                                                            <option value="family">Family</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Required --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Required</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="visa_cancellation_required">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="visa_cancellation_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Labour Card Cancellation Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Labour Card
                                                                            Cancellation Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="labour_card_cancellation_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Immigration Clearance Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Immigration
                                                                            Clearance Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="immigration_clearance_status">
                                                                            <option value="">Select</option>
                                                                            <option value="pending">Pending</option>
                                                                            <option value="in_progress">In Progress
                                                                            </option>
                                                                            <option value="completed">Completed</option>
                                                                            <option value="not_applicable">Not Applicable
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Permit Issued --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Permit
                                                                            Issued</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_permit_issued">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- MOHRE Clearance Uploaded --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">MOHRE Clearance
                                                                            Uploaded</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="mohre_clearance_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Document --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Document</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="visa_cancellation_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Labour Cancellation Document --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Labour Cancellation
                                                                            Document</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="labour_cancellation_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 9: Exit Interview --}}
                                                        <div class="tab-pane fade" id="exit-interview" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Exit Interview Conducted --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Interview
                                                                            Conducted</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_interview_conducted"
                                                                            id="exit_interview_conducted">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Interview Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Interview
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="exit_interview_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Interview Mode --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Interview
                                                                            Mode</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="interview_mode">
                                                                            <option value="">Select</option>
                                                                            <option value="in_person">In-Person</option>
                                                                            <option value="online">Online</option>
                                                                            <option value="phone">Phone</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Overall Satisfaction Rating --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Overall Satisfaction
                                                                            Rating</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="satisfaction_rating">
                                                                            <option value="">Select (1-5)</option>
                                                                            <option value="1">1 - Very Dissatisfied
                                                                            </option>
                                                                            <option value="2">2 - Dissatisfied
                                                                            </option>
                                                                            <option value="3">3 - Neutral</option>
                                                                            <option value="4">4 - Satisfied</option>
                                                                            <option value="5">5 - Very Satisfied
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Manager Feedback --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Manager
                                                                            Feedback</label>
                                                                        <textarea class="form-control form-control-sm" name="manager_feedback" rows="2"
                                                                            placeholder="Manager feedback"></textarea>
                                                                    </div>
                                                                </div>

                                                                {{-- HR Feedback --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">HR Feedback</label>
                                                                        <textarea class="form-control form-control-sm" name="hr_feedback" rows="2" placeholder="HR feedback"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 10: Approval Status --}}
                                                        <div class="tab-pane fade" id="approval" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- HR Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">HR Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="hr_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Finance Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Finance Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="finance_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Management Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Management Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="management_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Closed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Closed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_closed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Closure Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Closure
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="exit_closure_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Record Locked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Record
                                                                            Locked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="record_locked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Confidential HR Remarks --}}
                                                                <div class="col-lg-6">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Confidential HR
                                                                            Remarks</label>
                                                                        <textarea class="form-control form-control-sm" name="confidential_hr_remarks" rows="3"
                                                                            placeholder="Confidential HR remarks (internal use only)"></textarea>
                                                                    </div>
                                                                </div>

                                                                {{-- Attachment --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Attachment
                                                                            (Approval)</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="hr_remarks_attachment"
                                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, DOC, JPG,
                                                                            PNG</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 11: Documents --}}
                                                        <div class="tab-pane fade" id="documents" role="tabpanel">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm"
                                                                    id="documentsTable">
                                                                    <thead class="table-light">
                                                                        <tr class="text-center">
                                                                            <th style="width: 35%;">Document Name</th>
                                                                            <th style="width: 15%;">Date</th>
                                                                            <th style="width: 25%;">Attachment</th>
                                                                            <th style="width: 25%;">Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        {{-- A. Resignation / Termination Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>A. Resignation /
                                                                                    Termination Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Resignation Letter / Email</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_resignation_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_resignation_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_resignation_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Termination Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_termination_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_termination_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_termination_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Mutual Separation Agreement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_mutual_separation_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mutual_separation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mutual_separation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Notice Period Waiver Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_notice_waiver_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_notice_waiver"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_notice_waiver_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Garden Leave Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_garden_leave_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_garden_leave"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_garden_leave_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- B. Disciplinary & Performance Records --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>B. Disciplinary &
                                                                                    Performance Records</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Warning Letters (1st / 2nd / Final)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_warning_letters_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_warning_letters"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                                    multiple></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_warning_letters_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Show Cause Notice</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_show_cause_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_show_cause"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_show_cause_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Disciplinary Action Record</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_disciplinary_action_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_disciplinary_action"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_disciplinary_action_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Performance Improvement Plan (PIP)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_pip_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_pip"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_pip_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- C. Medical & Personal Justification --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>C. Medical &
                                                                                    Personal Justification</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Medical Certificate</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_medical_cert_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_medical_cert"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_medical_cert_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Fitness / Unfitness Report</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_fitness_report_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fitness_report"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fitness_report_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Compassionate / Emergency Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_emergency_proof_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_emergency_proof"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_emergency_proof_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- D. Employment Confirmation Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>D. Employment
                                                                                    Confirmation Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Service Certificate</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_service_cert_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_service_cert"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_service_cert_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Experience Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_experience_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_experience_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_experience_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Relieving Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_relieving_letter_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_relieving_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_relieving_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>No Objection Certificate (NOC)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_noc_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_noc"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_noc_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- E. Knowledge Transfer & Handover --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>E. Knowledge
                                                                                    Transfer & Handover</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Handover Checklist</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_handover_checklist_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_handover_checklist"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_handover_checklist_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Knowledge Transfer Sign-off</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_kt_signoff_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_kt_signoff"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_kt_signoff_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Successor Acceptance Form</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_successor_form_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_successor_form"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_successor_form_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- F. Asset & Access Clearance Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>F. Asset & Access
                                                                                    Clearance Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Asset Return Acknowledgement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_asset_return_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_asset_return"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_asset_return_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>IT Access Revocation Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_it_revocation_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_it_revocation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_it_revocation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>SIM / Email Deactivation Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_sim_email_deactivation_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_sim_email_deactivation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_sim_email_deactivation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- G. Payroll & Financial Settlement --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>G. Payroll &
                                                                                    Financial Settlement</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Full & Final Settlement Statement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_fnf_statement_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fnf_statement"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fnf_statement_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Gratuity Calculation Sheet</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_gratuity_sheet_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_gratuity_sheet"
                                                                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_gratuity_sheet_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Leave Encashment Calculation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_leave_encashment_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_leave_encashment"
                                                                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_leave_encashment_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Salary Deduction Approval</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_salary_deduction_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_salary_deduction"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_salary_deduction_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Final Payslip</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_final_payslip_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_final_payslip"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_final_payslip_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- H. Compliance & Legal Records --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>H. Compliance &
                                                                                    Legal Records</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Exit Interview Form</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_exit_interview_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_exit_interview"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_exit_interview_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Legal Clearance Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_legal_clearance_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_legal_clearance"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_legal_clearance_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Labour / MOHRE Acknowledgement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_mohre_ack_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mohre_ack"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mohre_ack_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Immigration / Visa Cancellation Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_visa_cancel_proof_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_visa_cancel_proof"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_visa_cancel_proof_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- I. Miscellaneous / Supporting --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>I. Miscellaneous /
                                                                                    Supporting</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Other Supporting Documents</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_other_supporting_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_other_supporting"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                                    multiple></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_other_supporting_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>HR Remarks / Internal Notes</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_hr_notes_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_hr_notes"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_hr_notes_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    {{-- END TABS SECTION --}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
                    // Initialize Select2
                    $('.js-example-basic-single').select2();

                    // Form validation
                    $('#endOfServiceForm').on('submit', function(e) {
                        let isValid = true;

                        // Check required fields
                        $('[required]').each(function() {
                            if (!$(this).val()) {
                                isValid = false;
                                $(this).addClass('is-invalid');
                            } else {
                                $(this).removeClass('is-invalid');
                            }
                        });

                        if (!isValid) {
                            e.preventDefault();
                            toastr.error('Please fill all required fields');
                        }
                    });

                    // Department change - load designations
                    $('#department_id').on('change', function() {
                        var departmentId = $(this).val();
                        var designationSelect = $('#designation_id');

                        // Clear current designations
                        designationSelect.empty();
                        designationSelect.append('<option value="">Select Designation</option>');

                        if (departmentId) {
                            $.ajax({
                                url: "{{ route('staff.resignation.getDesignations') }}",
                                type: 'POST',
                                data: {
                                    department_id: departmentId,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        $.each(response.data, function(index, designation) {
                                            designationSelect.append('<option value="' +
                                                designation.id + '">' + designation.title +
                                                '</option>');
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error loading designations');
                                }
                            });
                        }
                    });

                    // Asset table - Add row
                    var assetRowIndex = 3;
                    $('#addAssetRow').on('click', function() {
                        var newRow = `
                    <tr>
                        <td><input type="text" class="form-control form-control-sm" name="assets[${assetRowIndex}][name]" placeholder="Asset Name"></td>
                        <td>
                            <select class="form-select form-select-sm" name="assets[${assetRowIndex}][applicable]">
                                <option value="na">N/A</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control form-control-sm" name="assets[${assetRowIndex}][serial_number]"></td>
                        <td><input type="text" class="form-control form-control-sm date-picker" name="assets[${assetRowIndex}][return_date]"></td>
                        <td>
                            <select class="form-select form-select-sm asset-condition" name="assets[${assetRowIndex}][condition]">
                                <option value="good">Good</option>
                                <option value="damaged">Damaged</option>
                                <option value="missing">Missing</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm" name="assets[${assetRowIndex}][recovery_amount]"></td>
                        <td>
                            <select class="form-select form-select-sm" name="assets[${assetRowIndex}][verified_by]">
                                <option value="">Select</option>
                                <option value="1">IT Admin</option>
                                <option value="2">HR Manager</option>
                            </select>
                        </td>
                        <td><textarea class="form-control form-control-sm damage-remarks" name="assets[${assetRowIndex}][damage_remarks]" rows="1"></textarea></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-asset-row"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `;
                        $('#assetTable tbody').append(newRow);
                        assetRowIndex++;
                    });

                    // Asset table - Remove row
                    $(document).on('click', '.remove-asset-row', function() {
                        $(this).closest('tr').remove();
                    });

                    // Asset condition change - make remarks mandatory if damaged/missing
                    $(document).on('change', '.asset-condition', function() {
                                var remarksField = $(this).closest('tr').find('.damage-remarks');
                                if ($(this).val() === 'damaged' || $(this).val() === 'missing') {
                                    remarksField.attr('required', true);
                                    remarksField.addClass('border-warning');
                                } else {
                                    remarksField.attr('required', false);
                                    remarksField.removeClass('border-warning');
    </script>


    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for crm-deals-form
            FormValidator.init('goods-receipt-note-store-form', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>
@endsection

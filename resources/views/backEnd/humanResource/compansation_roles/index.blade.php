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

       
    </style>
    @php
        $editMode = isset($editMode) && $editMode === true;
        $staffData = isset($editData) ? $editData : null;
    @endphp
    <div class="form-scroll">
        <form id="goods-receipt-note-store-form" novalidate action="{{ route('staff.compensation.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_staff_id" name="staff_id" value="{{ $staffData->id ?? '' }}">
            @if($editMode && isset($compensationData['main']))
                <input type="hidden" name="compensation_id" value="{{ $compensationData['main']->id }}">
            @endif

            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="compensationTabContent">
                    <div class="" role="tabpanel" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                Compensation & Role Changes {{ $editMode ? '(Edit Mode)' : '(New)' }}
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right">
                                <button type="submit"
                                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2" id="btnSaveAll">
                                    <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                                    <span class="btn-text">Save</span>
                                </button>
                                <a class="btn btn-light" href="{{ route('staff.compensation.list') }}">Compensation List</a>
                                {{-- <a class="btn btn-light" href="{{ url('staff-directory') }}">Staff Directory</a> --}}
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

                                                {{-- First Row: Doc No., Doc Date, Employee Name, Department, Designation, Reporting Manager --}}
                                                <div class="row mb-30">
                                                    <div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Doc No. (Auto-generated) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Doc No. <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control form-control-sm"
                                                                        name="doc_no" id="doc_no" 
                                                                        value="{{ $compensationData['main']->doc_no ?? '' }}"
                                                                        readonly>
                                                                </div>
                                                            </div>

                                                            {{-- Doc Date (Auto-generated) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Doc Date <span class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="doc_date" id="doc_date"
                                                                        value="{{ $compensationData['main']->doc_date ?? date('Y-m-d') }}"
                                                                        >
                                                                </div>
                                                            </div>

                                                            {{-- Employee Name (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Employee Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="employee_id" id="employee_id" required>
                                                                        <option value="">Select Employee</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}" {{ (isset($compensationData['main']) && $compensationData['main']->employee_id == $staff->id) ? 'selected' : '' }}>
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Department (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Department</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="department_id" id="department_id" disabled>
                                                                        <option value="">Auto-fetch</option>
                                                                        @foreach ($departments as $department)
                                                                            <option value="{{ $department->id }}">
                                                                                {{ $department->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Designation (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Designation</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="designation_id" id="designation_id" disabled>
                                                                        <option value="">Auto-fetch</option>
                                                                        @foreach ($designations as $designation)
                                                                            <option value="{{ $designation->id }}">
                                                                                {{ $designation->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Reporting Manager (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Reporting Manager</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="reporting_manager" id="reporting_manager" disabled>
                                                                        <option value="">Auto-fetch</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}">
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- Second Row: Grade, Employment Type, Date of Joining, Transaction Type, Effective Date, Current Status --}}
                                                    <div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Grade (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Grade</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="grade" id="grade" disabled>
                                                                        <option value="">Auto-fetch</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Employment Type (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Employment Type</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="employment_type" id="employment_type" disabled>
                                                                        <option value="">Auto-fetch</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Date of Joining (Auto-fetch) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Date of Joining</label>
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="date_of_joining" id="date_of_joining" 
                                                                        disabled>
                                                                </div>
                                                            </div>

                                                            {{-- Transaction Type (Dropdown) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Transaction Type <span  class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="transaction_type" id="transaction_type" >
                                                                        <option value="">Select Type</option>
                                                                        <option value="promotion" {{ (isset($compensationData['main']) && $compensationData['main']->transaction_type == 'promotion') ? 'selected' : '' }}>Promotion</option>
                                                                        <option value="demotion" {{ (isset($compensationData['main']) && $compensationData['main']->transaction_type == 'demotion') ? 'selected' : '' }}>Demotion</option>
                                                                        <option value="increment" {{ (isset($compensationData['main']) && $compensationData['main']->transaction_type == 'increment') ? 'selected' : '' }}>Increment</option>
                                                                        <option value="increment_promotion" {{ (isset($compensationData['main']) && $compensationData['main']->transaction_type == 'increment_promotion') ? 'selected' : '' }}>Increment & Promotion</option>
                                                                        <option value="decrement_demotion" {{ (isset($compensationData['main']) && $compensationData['main']->transaction_type == 'decrement_demotion') ? 'selected' : '' }}>Decrement & Demotion</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Effective Date (Date Picker) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Effective Date <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control form-control-sm"
                                                                        name="effective_date" id="effective_date"
                                                                        value="{{ $compensationData['main']->effective_date ?? '' }}"
                                                                        >
                                                                </div>
                                                            </div>

                                                            {{-- Current Status (Dropdown) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Current Status <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="current_status" id="current_status">
                                                                        <option value="">Select Status</option>
                                                                        <option value="draft" {{ (isset($compensationData['main']) && $compensationData['main']->current_status == 'draft') ? 'selected' : '' }}>Draft</option>
                                                                        <option value="pending" {{ (isset($compensationData['main']) && $compensationData['main']->current_status == 'pending') ? 'selected' : '' }}>Pending</option>
                                                                        <option value="approved" {{ (isset($compensationData['main']) && $compensationData['main']->current_status == 'approved') ? 'selected' : '' }}>Approved</option>
                                                                        <option value="rejected" {{ (isset($compensationData['main']) && $compensationData['main']->current_status == 'rejected') ? 'selected' : '' }}>Rejected</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- TABS SECTION FOR PROMOTION FIELDS --}}
                                                    <div class="tab-wrap mb-3 mt-4">
                                                        <ul class="nav nav-tabs" id="compensationTabs" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="promotion-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#promotion"
                                                                    type="button" role="tab">
                                                                    Promotion Fields
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="demotion-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#demotion"
                                                                    type="button" role="tab">
                                                                    Demotion Fields
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="salary-increment-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#salary-increment"
                                                                    type="button" role="tab">
                                                                    Salary Increment Fields
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="approval-workflow-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#approval-workflow"
                                                                    type="button" role="tab">
                                                                    Approval & Workflow
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="employee-self-service-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#employee-self-service"
                                                                    type="button" role="tab">
                                                                    Employee Self-Service (Read-Only)
                                                                </button>
                                                            </li>
                                                        </ul>

                                                        {{-- Tab Content --}}
                                                        <div class="tab-content" id="compensationTabsContent">
                                                            {{-- PROMOTION FIELDS TAB --}}
                                                            <div class="tab-pane fade show active" id="promotion" role="tabpanel" aria-labelledby="promotion-tab">
                                                                <div class="">
                                                                    <div class="">
                                                                        <div class="white-box">
                                                                            {{-- Row 1: Promotion Type, Promotion Reason, Current Department, New Department, Current Designation, New Designation --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Promotion Type --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Promotion Type</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="promotion_type" id="promotion_type">
                                                                                                    <option value="">Select Type</option>
                                                                                                    <option value="vertical">Vertical</option>
                                                                                                    <option value="lateral">Lateral</option>
                                                                                                    <option value="acting">Acting</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Promotion Reason --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Promotion Reason</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="promotion_reason" id="promotion_reason">
                                                                                                    <option value="">Select Reason</option>
                                                                                                    <option value="performance">Performance</option>
                                                                                                    <option value="vacancy">Vacancy</option>
                                                                                                    <option value="restructure">Restructure</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Current Department --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Current Department</label>
                                                                                                <input type="text" class="form-control form-control-sm"
                                                                                                    name="promo_current_department" id="promo_current_department"
                                                                                                    disabled>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- New Department --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">New Department</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="promo_new_department_id" id="promo_new_department_id">
                                                                                                    <option value="">Select Department</option>
                                                                                                    @foreach ($departments as $department)
                                                                                                        <option value="{{ $department->id }}">
                                                                                                            {{ $department->name }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Current Designation --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Current Designation</label>
                                                                                                <input type="text" class="form-control form-control-sm"
                                                                                                    name="promo_current_designation" id="promo_current_designation"
                                                                                                    disabled>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- New Designation --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">New Designation</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="promo_new_designation_id" id="promo_new_designation_id">
                                                                                                    <option value="">Select Designation</option>
                                                                                                    @foreach ($designations as $designation)
                                                                                                        <option value="{{ $designation->id }}">
                                                                                                            {{ $designation->title }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Row 2: Current Grade, New Grade, New Reporting Manager, Position Availability, Min Band Salary, Max Band Salary --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Current Grade --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Current Grade</label>
                                                                                                <input type="text" class="form-control form-control-sm"
                                                                                                    name="promo_current_grade" id="promo_current_grade"
                                                                                                    disabled>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- New Grade --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">New Grade</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="promo_new_grade_id" id="promo_new_grade_id">
                                                                                                    <option value="">Select Grade</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- New Reporting Manager --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">New Reporting Manager</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="promo_new_reporting_manager_id" id="promo_new_reporting_manager_id">
                                                                                                    <option value="">Select Manager</option>
                                                                                                    @foreach ($staffs as $staff)
                                                                                                        <option value="{{ $staff->id }}">
                                                                                                            {{ $staff->full_name }}
                                                                                                            ({{ $staff->staff_no }})</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Position Availability --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Position Availability</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="position_availability" id="position_availability">
                                                                                                    <option value="">Select</option>
                                                                                                    <option value="yes">Yes</option>
                                                                                                    <option value="no">No</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Minimum Band Salary --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Min Band Salary</label>
                                                                                                <input type="number" class="form-control form-control-sm"
                                                                                                    name="min_band_salary" id="min_band_salary"
                                                                                                    step="0.01" disabled>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Maximum Band Salary --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Max Band Salary</label>
                                                                                                <input type="number" class="form-control form-control-sm"
                                                                                                    name="max_band_salary" id="max_band_salary"
                                                                                                    step="0.01" disabled>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Row 3: Proposed Salary, Promotion Justification, Promotion Letter, Updated Job Description, Training/Development Plan --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Proposed Salary --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Proposed Salary</label>
                                                                                                <input type="number" class="form-control form-control-sm"
                                                                                                    name="proposed_salary" id="proposed_salary"
                                                                                                    step="0.01">
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Promotion Justification --}}
                                                                                        <div class="col-lg-3">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Promotion Justification</label>
                                                                                                <textarea class="form-control form-control-sm" name="promotion_justification" 
                                                                                                    id="promotion_justification" rows="3"
                                                                                                    ></textarea>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Promotion Letter --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Promotion Letter</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="promotion_letter" id="promotion_letter"
                                                                                                    accept=".pdf,.doc,.docx">
                                                                                                <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Updated Job Description --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Updated Job Description</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="updated_job_description" id="updated_job_description"
                                                                                                    accept=".pdf,.doc,.docx">
                                                                                                <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Training / Development Plan --}}
                                                                                        <div class="col-lg-3">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Training / Dev Plan</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="training_development_plan" id="training_development_plan"
                                                                                                    accept=".pdf,.doc,.docx">
                                                                                                <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- DEMOTION FIELDS TAB --}}
                                                            <div class="tab-pane fade" id="demotion" role="tabpanel" aria-labelledby="demotion-tab">
                                                                <div class="card mb-3 mt-3">
                                                                    <div class="card-body">
                                                                        <div class="white-box">
                                                                            {{-- Row 1: Demotion Type, Demotion Nature, Reason for Demotion, Revised Department, Revised Designation, Revised Grade --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Demotion Type --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Demotion Type</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="demotion_type" id="demotion_type">
                                                                                                    <option value="">Select Type</option>
                                                                                                    <option value="performance">Performance</option>
                                                                                                    <option value="disciplinary">Disciplinary</option>
                                                                                                    <option value="restructure">Restructure</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Demotion Nature --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Demotion Nature</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="demotion_nature" id="demotion_nature">
                                                                                                    <option value="">Select Nature</option>
                                                                                                    <option value="temporary">Temporary</option>
                                                                                                    <option value="permanent">Permanent</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Reason for Demotion --}}
                                                                                        <div class="col-lg-3">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Reason for Demotion</label>
                                                                                                <textarea class="form-control form-control-sm" name="demotion_reason" 
                                                                                                    id="demotion_reason" rows="3"
                                                                                                    ></textarea>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Revised Department --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Revised Department</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="demotion_new_department_id" id="demotion_new_department_id">
                                                                                                    <option value="">Select Department</option>
                                                                                                    @foreach ($departments as $department)
                                                                                                        <option value="{{ $department->id }}">
                                                                                                            {{ $department->name }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Revised Designation --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Revised Designation</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="demotion_new_designation_id" id="demotion_new_designation_id">
                                                                                                    <option value="">Select Designation</option>
                                                                                                    @foreach ($designations as $designation)
                                                                                                        <option value="{{ $designation->id }}">
                                                                                                            {{ $designation->title }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Revised Grade --}}
                                                                                        <div class="col-lg-1">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Revised Grade</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="demotion_new_grade_id" id="demotion_new_grade_id">
                                                                                                    <option value="">Select Grade</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Row 2: Legal Compliance Status, Employee Consent Status, Appeal Option, Warning Letters, Demotion Letter --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Legal Compliance Status --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Legal Compliance Status</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="legal_compliance_status" id="legal_compliance_status" disabled>
                                                                                                    <option value="">Auto (Yes/No)</option>
                                                                                                    <option value="yes">Yes</option>
                                                                                                    <option value="no">No</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Employee Consent Status --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Employee Consent Status</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="employee_consent_status" id="employee_consent_status">
                                                                                                    <option value="">Select Status</option>
                                                                                                    <option value="accepted">Accepted</option>
                                                                                                    <option value="rejected">Rejected</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Appeal Option Provided --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Appeal Option Provided</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="appeal_option_provided" id="appeal_option_provided">
                                                                                                    <option value="">Select</option>
                                                                                                    <option value="yes">Yes</option>
                                                                                                    <option value="no">No</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Warning Letters --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Warning Letters</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="warning_letters" id="warning_letters"
                                                                                                    accept=".pdf,.doc,.docx">
                                                                                                <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Demotion Letter --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Demotion Letter</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="demotion_letter" id="demotion_letter"
                                                                                                    accept=".pdf,.doc,.docx">
                                                                                                <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- SALARY INCREMENT FIELDS TAB --}}
                                                            <div class="tab-pane fade" id="salary-increment" role="tabpanel" aria-labelledby="salary-increment-tab">
                                                                <div class="card mb-3 mt-3">
                                                                    <div class="card-body">
                                                                        <div class="white-box">
                                                                            {{-- Row 1: Increment Category, Increment Trigger, Last Increment Date, Eligibility Status, Eligibility Remarks, Increment Method --}}
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-12 mb-3">
                                                                                    <div class="row gy-2">

                                                                                        {{-- Increment Category --}}
                                                                                        <div class="col-lg-2">
                                                                                            <div class="input-effect">
                                                                                                <label class="form-label mb-1">Increment Category</label>
                                                                                                <select class="form-select form-select-sm"
                                                                                                    name="increment_category" id="increment_category">
                                                                                            <option value="">Select Category</option>
                                                                                            <option value="annual">Annual</option>
                                                                                            <option value="performance">Performance</option>
                                                                                            <option value="retention">Retention</option>
                                                                                            <option value="market">Market</option>
                                                                                            <option value="special">Special</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Increment Trigger --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Increment Trigger</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="increment_trigger" id="increment_trigger">
                                                                                            <option value="">Select Trigger</option>
                                                                                            <option value="appraisal">Appraisal</option>
                                                                                            <option value="management">Management</option>
                                                                                            <option value="hr">HR</option>
                                                                                            <option value="market">Market</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Last Increment Date --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Last Increment Date</label>
                                                                                        <input type="date" class="form-control form-control-sm"
                                                                                            name="last_increment_date" id="last_increment_date"
                                                                                            disabled>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Eligibility Status --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Eligibility Status</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="eligibility_status" id="eligibility_status" disabled>
                                                                                            <option value="">Auto</option>
                                                                                            <option value="yes">Yes</option>
                                                                                            <option value="no">No</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Eligibility Remarks --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Eligibility Remarks</label>
                                                                                        <input type="text" class="form-control form-control-sm"
                                                                                            name="eligibility_remarks" id="eligibility_remarks"
                                                                                            disabled>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Increment Method --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Increment Method</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="increment_method" id="increment_method">
                                                                                            <option value="">Select Method</option>
                                                                                            <option value="basic_percentage">Basic %</option>
                                                                                            <option value="gross_percentage">Gross %</option>
                                                                                            <option value="fixed_amount">Fixed Amount</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Row 2: Increment (%), Current Basic, Current HRA, Current Transport, Current Other, Gross Salary --}}
                                                                    <div class="row mb-4">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <h6 class="mb-3"><strong>Current Salary Structure</strong></h6>
                                                                            <div class="row gy-2">

                                                                                {{-- Increment (% or Amount) --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Increment (% or Amount)</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_value" id="increment_value"
                                                                                            step="0.01">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Current Basic Salary --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Current Basic Salary</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_current_basic" id="increment_current_basic"
                                                                                            step="0.01" disabled placeholder="Calculation">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Current HRA --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Current HRA</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_current_hra" id="increment_current_hra"
                                                                                            step="0.01" disabled placeholder="Calculation">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Current Transport Allowance --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Current Transport</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_current_transport" id="increment_current_transport"
                                                                                            step="0.01" disabled placeholder="Calculation">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Current Other Allowances --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Current Other Allowances</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_current_other" id="increment_current_other"
                                                                                            step="0.01" disabled placeholder="Calculation">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Current Gross Salary --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Current Gross Salary</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_current_gross" id="increment_current_gross"
                                                                                            step="0.01" disabled placeholder="Calculation">
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Row 3: Increment Amount, Revised Basic, Revised HRA, Revised Transport, Revised Other, Revised Gross --}}
                                                                    <div class="row mb-4">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <h6 class="mb-3"><strong>Revised Salary Structure</strong></h6>
                                                                            <div class="row gy-2">

                                                                                {{-- Increment Amount --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Increment Amount</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_amount" id="increment_amount"
                                                                                            step="0.01" disabled placeholder="Auto-calculated">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Revised Basic Salary --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Revised Basic Salary</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_revised_basic" id="increment_revised_basic"
                                                                                            step="0.01" disabled placeholder="System">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Revised HRA --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Revised HRA</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_revised_hra" id="increment_revised_hra"
                                                                                            step="0.01" disabled placeholder="System">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Revised Transport Allowance --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Revised Transport</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_revised_transport" id="increment_revised_transport"
                                                                                            step="0.01" disabled placeholder="System">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Revised Other Allowances --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Revised Other Allowances</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_revised_other" id="increment_revised_other"
                                                                                            step="0.01" disabled placeholder="System">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Revised Gross Salary --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Revised Gross Salary</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="increment_revised_gross" id="increment_revised_gross"
                                                                                            step="0.01" disabled placeholder="System">
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Row 4: Monthly Cost Impact, Annual Cost Impact, Budget Availability, Manager Justification, HR Remarks, Supporting Documents --}}
                                                                    <div class="row mb-4">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <h6 class="mb-3"><strong>Budget & Approval</strong></h6>
                                                                            <div class="row gy-2">

                                                                                {{-- Monthly Cost Impact --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Monthly Cost Impact</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="monthly_cost_impact" id="monthly_cost_impact"
                                                                                            step="0.01" disabled placeholder="Auto-calculated">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Annual Cost Impact --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Annual Cost Impact</label>
                                                                                        <input type="number" class="form-control form-control-sm"
                                                                                            name="annual_cost_impact" id="annual_cost_impact"
                                                                                            step="0.01" disabled placeholder="Auto-calculated">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Budget Availability --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Budget Availability</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="budget_availability" id="budget_availability">
                                                                                            <option value="">Select</option>
                                                                                            <option value="available">Available</option>
                                                                                            <option value="exceeded">Exceeded</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Manager Justification --}}
                                                                                <div class="col-lg-3">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Manager Justification</label>
                                                                                        <textarea class="form-control form-control-sm" name="manager_justification" 
                                                                                            id="manager_justification" rows="3"
                                                                                            ></textarea>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- HR Remarks --}}
                                                                                <div class="col-lg-3">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">HR Remarks</label>
                                                                                        <textarea class="form-control form-control-sm" name="increment_hr_remarks" 
                                                                                            id="increment_hr_remarks" rows="3"
                                                                                            placeholder="HR remarks and notes"></textarea>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Supporting Documents --}}
                                                                                <div class="col-lg-2">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Supporting Documents</label>
                                                                                        <input type="file" class="form-control form-control-sm"
                                                                                            name="supporting_documents" id="supporting_documents"
                                                                                            accept=".pdf,.doc,.docx">
                                                                                        <small class="text-muted d-block mt-1">PDF, DOC, DOCX</small>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- APPROVAL & WORKFLOW FIELDS TAB --}}
                                                    <div class="tab-pane fade" id="approval-workflow" role="tabpanel" aria-labelledby="approval-workflow-tab">
                                                        <div class="card mb-3 mt-3">
                                                            <div class="card-body">
                                                                <div class="white-box">
                                                                    {{-- Row 1: Reporting Manager Approval, Manager Remarks, Finance Approval --}}
                                                                    <div class="row mb-4">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <h6 class="mb-3"><strong>Approval Chain</strong></h6>
                                                                            <div class="row gy-2">

                                                                                {{-- Reporting Manager Approval --}}
                                                                                <div class="col-lg-3">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Reporting Manager Approval</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="reporting_manager_approval" id="reporting_manager_approval">
                                                                                            <option value="">Select Action</option>
                                                                                            <option value="approve">Approve</option>
                                                                                            <option value="reject">Reject</option>
                                                                                            <option value="recommend">Recommend</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Manager Remarks --}}
                                                                                <div class="col-lg-5">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Manager Remarks</label>
                                                                                        <textarea class="form-control form-control-sm" name="manager_remarks_approval" 
                                                                                            id="manager_remarks_approval" rows="3"
                                                                                            placeholder="Provide manager remarks on this proposal"></textarea>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Finance Approval --}}
                                                                                <div class="col-lg-3">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Finance Approval</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="finance_approval" id="finance_approval">
                                                                                            <option value="">Select Status</option>
                                                                                            <option value="approve">Approve</option>
                                                                                            <option value="reject">Reject</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Row 2: Management Approval, Approval Date, Approval Level --}}
                                                                    <div class="row mb-4">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <h6 class="mb-3"><strong>Final Approval</strong></h6>
                                                                            <div class="row gy-2">

                                                                                {{-- Management Approval --}}
                                                                                <div class="col-lg-4">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Management Approval</label>
                                                                                        <select class="form-select form-select-sm"
                                                                                            name="management_approval" id="management_approval">
                                                                                            <option value="">Select Action</option>
                                                                                            <option value="approve">Approve</option>
                                                                                            <option value="reject">Reject</option>
                                                                                            <option value="skip">Skip</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Approval Date --}}
                                                                                <div class="col-lg-4">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Approval Date</label>
                                                                                        <input type="date" class="form-control form-control-sm"
                                                                                            name="approval_date" id="approval_date"
                                                                                            disabled placeholder="Auto-generated">
                                                                                    </div>
                                                                                </div>

                                                                                {{-- Approval Level --}}
                                                                                <div class="col-lg-4">
                                                                                    <div class="input-effect">
                                                                                        <label class="form-label mb-1">Approval Level</label>
                                                                                        <input type="text" class="form-control form-control-sm"
                                                                                            name="approval_level" id="approval_level"
                                                                                            disabled placeholder="Auto-tracked">
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- EMPLOYEE SELF-SERVICE TAB --}}
                                                        <div class="tab-pane fade" id="employee-self-service" role="tabpanel" aria-labelledby="employee-self-service-tab">
                                                            <div class="card mb-3 mt-3">
                                                                <div class="card-body">
                                                                    <div class="white-box">
                                                                        {{-- Row 1: Employee Acknowledgement, Acknowledgement Date, Letter Download, View Approval History --}}
                                                                        <div class="row mb-4">
                                                                            <div class="col-lg-12 mb-3">
                                                                                <div class="row gy-2">
                                                                                    {{-- Employee Acknowledgement --}}
                                                                                    <div class="col-lg-3">
                                                                                        <label for="employee_acknowledgement" class="form-label">Employee Acknowledgement</label>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="checkbox" name="employee_acknowledgement" id="employee_acknowledgement" disabled>
                                                                                            <label class="form-check-label" for="employee_acknowledgement">
                                                                                                Acknowledged
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- Acknowledgement Date --}}
                                                                                    <div class="col-lg-3">
                                                                                        <label for="acknowledgement_date" class="form-label">Acknowledgement Date</label>
                                                                                        <input type="date" class="form-control form-control-sm" name="acknowledgement_date" id="acknowledgement_date" disabled>
                                                                                    </div>

                                                                                    {{-- Letter Download --}}
                                                                                    <div class="col-lg-3">
                                                                                        <label for="letter_download" class="form-label">Letter Download</label>
                                                                                        <div>
                                                                                            <a href="#" class="btn btn-sm btn-primary" id="letter_download" disabled>
                                                                                                <i class="fa fa-download"></i> Download
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- View Approval History --}}
                                                                                    <div class="col-lg-3">
                                                                                        <label for="approval_history" class="form-label">View Approval History</label>
                                                                                        <div>
                                                                                            <a href="#" class="btn btn-sm btn-info" id="approval_history" disabled>
                                                                                                <i class="fa fa-eye"></i> View
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Select2
            $('.js-example-basic-single').select2({
                width: '100%',
                allowClear: true,
            });

            // Auto-fetch employee details when employee is selected
            $('#employee_id').on('change', function () {
                let employeeId = $(this).val();
                if (employeeId) {
                    fetchEmployeeDetails(employeeId);
                }
            });

            // Calculate salary increase percentage
            $('#new_basic_salary').on('change', function () {
                calculateSalaryIncrease();
            });

            // Handle Transaction Type selection to show/hide tabs
            $('#transaction_type').on('change', function () {
                let transactionType = $(this).val();
                
                // Get tab buttons
                let demotionTab = $('#demotion-tab');
                let salaryIncrementTab = $('#salary-increment-tab');
                let promotionTab = $('#promotion-tab');
                
                if (transactionType === 'promotion') {
                    // Hide Demotion and Salary Increment tabs for Promotion
                    demotionTab.closest('li').hide();
                    salaryIncrementTab.closest('li').hide();
                    // Show and activate Promotion tab
                    promotionTab.closest('li').show();
                    promotionTab.tab('show');
                } else if (transactionType === 'demotion') {
                    // Hide Promotion and Salary Increment tabs for Demotion
                    promotionTab.closest('li').hide();
                    salaryIncrementTab.closest('li').hide();
                    // Show and activate Demotion tab
                    demotionTab.closest('li').show();
                    demotionTab.tab('show');
                } else if (transactionType === 'increment') {
                    // Hide Promotion and Demotion tabs for Increment
                    promotionTab.closest('li').hide();
                    demotionTab.closest('li').hide();
                    // Show and activate Salary Increment tab
                    salaryIncrementTab.closest('li').show();
                    salaryIncrementTab.tab('show');
                } else if (transactionType === 'increment_promotion' || transactionType === 'decrement_demotion') {
                    // Show all tabs for combined transactions
                    promotionTab.closest('li').show();
                    demotionTab.closest('li').show();
                    salaryIncrementTab.closest('li').show();
                } else {
                    // Show all tabs by default
                    promotionTab.closest('li').show();
                    demotionTab.closest('li').show();
                    salaryIncrementTab.closest('li').show();
                }
            });
        });

        function fetchEmployeeDetails(employeeId) {
            $.ajax({
                url: '{{ url("/api/staff/") }}' + employeeId + '/details',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        // Populate auto-fetch fields
                        $('#department_id').val(response.data.department_id).trigger('change');
                        $('#designation_id').val(response.data.designation_id).trigger('change');
                        $('#reporting_manager').val(response.data.reporting_manager_id).trigger('change');
                        $('#grade').val(response.data.grade || '').trigger('change');
                        $('#employment_type').val(response.data.employment_type || '').trigger('change');
                        $('#date_of_joining').val(response.data.date_of_joining || '');
                        $('#current_basic_salary').val(response.data.current_basic_salary || '');
                        $('#current_designation').val(response.data.designation || '');
                        $('#current_department').val(response.data.department || '');
                    }
                },
                error: function (error) {
                    console.error('Error fetching employee details:', error);
                }
            });
        }

        function calculateSalaryIncrease() {
            let currentSalary = parseFloat($('#current_basic_salary').val()) || 0;
            let newSalary = parseFloat($('#new_basic_salary').val()) || 0;

            if (currentSalary > 0) {
                let percentage = ((newSalary - currentSalary) / currentSalary * 100).toFixed(2);
                $('#salary_increase_percentage').val(percentage);
            }
        }

        // Form submission
       
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

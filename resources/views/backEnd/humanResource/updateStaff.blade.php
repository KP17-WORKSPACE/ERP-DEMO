@extends('backEnd.newmasterpage')
@section('mainContent')
    <style>
        .form-scroll {
            overflow-y: auto;
            padding-right: 6px;
            /* thin scrollbar overlap fix */
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #dc3545;
        }

        .nav-link.tab-has-error {
            color: #dc3545 !important;
        }

        .badge.tab-error-badge {
            font-size: 10px;
            vertical-align: top;
        }
    </style>
    @php
        $staffRow = $staffRow ?? ($editData ?? null);
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <div class="form-scroll">
        <form id="staffAllForm" action="{{ route('staff.basic.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_staff_id" name="staff_id" value="{{ $staffRow->id ?? '' }}">

            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                    <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                Add Employee
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right">
                                <button type="button" class="btn btn-light text-dark" id="btnSaveAll">

                                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                                    Save All</button>


                                {{-- <button type="submit" name="customer_action" class="btn btn-light" type="submit" id="btnSaveAll">
                        <i class="ico icon-outline-add-square text-success"></i> Save
                        </button> --}}
                                <a class="btn btn-light" href="{{ url('staff-directory') }}">User List
                                </a>
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
                                                        {{-- keep this only as an internal holder; no name so it won't submit on first save --}}

                                                       @php
    // Date display (d/m/Y)
    $dobDisplay = optional($staffRow)->date_of_birth
        ? \Carbon\Carbon::parse(optional($staffRow)->date_of_birth)->format('d/m/Y')
        : '';

    // Religion / Gender selected values
    $selReligion = old('religion', $staffRow->religion);
    $selGenderId = (string) old('gender_id', $staffRow->gender_id);

    // Nationality:
    // If your DB stores country *id*, this will just match.
    // If it stores country *name*, we map name -> id for preselect.
    $natOld = old('nationality');
    $natModelRaw = $staffRow->nationality ?? null; // could be id or name
    $natFromName =
        optional(
            $countries->firstWhere(
                'country_name',
                $natModelRaw ?? ($staffRow->country_name ?? null)
            )
        )->id ??
        optional(
            $countries->firstWhere(
                'name',
                $natModelRaw ?? null
            )
        )->id; // covers either column
    $selNatId = is_numeric($natOld ?? '')
        ? $natOld
        : (is_numeric($natModelRaw ?? '')
            ? $natModelRaw
            : $natFromName);
@endphp




                                                        <div class="row gy-2">

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('User')
                                                                        @lang('lang.no_') <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="staff_code"
                                                                        value="{{ old('staff_code', $staffRow->staff_no) }}"
                                                                        readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('lang.first')
                                                                        @lang('lang.name') <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="first_name"
                                                                        value="{{ old('first_name', $staffRow->first_name) }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Middle Name</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="middle_name"
                                                                        value="{{ old('middle_name', $staffRow->middle_name) }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('lang.last')
                                                                        @lang('lang.name')</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="last_name"
                                                                        value="{{ old('last_name', $staffRow->last_name) }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Father's Name</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="fathers_name"
                                                                        value="{{ old('fathers_name', $staffRow->fathers_name) }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mother's Name</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="mothers_name"
                                                                        value="{{ old('mothers_name', $staffRow->mothers_name) }}">
                                                                </div>
                                                            </div>

                                                            {{-- Date of Birth --}}
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Date of Birth <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm date-picker"
                                                                    name="date_of_birth"
                                                                    value="{{ old('date_of_birth', $dobDisplay) }}"
                                                                    required>
                                                            </div>

                                                            {{-- Religion --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Religion</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="religion">
                                                                        <option value="">Select</option>
                                                                        @foreach ([
            'bahai' => 'Baháʼí Faith',
            'buddhism' => 'Buddhism',
            'caodaism' => 'Caodaism',
            'cheondoism' => 'Cheondoism',
            'christianity' => 'Christianity',
            'confucianism' => 'Confucianism',
            'druze' => 'Druze',
            'folk' => 'Folk religion',
            'hinduism' => 'Hinduism',
            'hoahaoism' => 'Hoahaoism',
            'islam' => 'Islam',
            'jainism' => 'Jainism',
            'judaism' => 'Judaism',
            'muism' => 'Mu-ism',
            'shinto' => 'Shinto',
            'sikhism' => 'Sikhism',
            'spiritism' => 'Spiritism',
            'taoism' => 'Taoism',
            'tenriism' => 'Tenriism',
            'voodoo' => 'Voodoo',
            'yoruba' => 'Yoruba Religion',
            'other' => 'Other',
        ] as $val => $label)
                                                                            <option value="{{ $val }}"
                                                                                {{ $selReligion === $val ? 'selected' : '' }}>
                                                                                {{ $label }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Gender --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('Gender')
                                                                        <span>*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="gender_id" required>
                                                                        <option value="">Select</option>
                                                                        @foreach ($genders as $gender)
                                                                            <option value="{{ $gender->id }}"
                                                                                {{ (string) $selGenderId === (string) $gender->id ? 'selected' : '' }}>
                                                                                {{ $gender->base_setup_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('lang.mobile')
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="mobile" placeholder="+"
                                                                        value="{{ old('mobile', $staffRow->mobile) }}"
                                                                        inputmode="numeric" maxlength="15" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email ID
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="email"
                                                                        value="{{ old('email', $staffRow->email) }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Marital Status</label>
                                                                    @php $selMarital = old('marital_status', $staffRow->marital_status); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        name="marital_status">
                                                                        <option value="">Select</option>
                                                                        <option value="single"
                                                                            {{ $selMarital === 'single' ? 'selected' : '' }}>
                                                                            Single</option>
                                                                        <option value="married"
                                                                            {{ $selMarital === 'married' ? 'selected' : '' }}>
                                                                            Married</option>
                                                                        <option value="divorced"
                                                                            {{ $selMarital === 'divorced' ? 'selected' : '' }}>
                                                                            Divorced</option>
                                                                        <option value="widowed"
                                                                            {{ $selMarital === 'widowed' ? 'selected' : '' }}>
                                                                            Widowed</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Nationality (ID or name supported) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Nationality
                                                                        <span>*</span></label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="nationality" required>
                                                                        <option value="">-Select-</option>
                                                                        @foreach ($countries as $value)
                                                                            @php $id = (string)$value->id; @endphp
                                                                            <option value="{{ $id }}"
                                                                                {{ (string) $selNatId === $id ? 'selected' : '' }}>
                                                                                {{ $value->country_name ?? $value->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Emergency Contact Name
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="emergency_contact_name"
                                                                        value="{{ old('emergency_contact_name', $staffRow->emergency_contact_name) }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Emergency Contact
                                                                        Relationship <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text"
                                                                        name="emergency_contact_relationship"
                                                                        value="{{ old('emergency_contact_relationship', $staffRow->emergency_contact_relationship) }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Emergency Contact Number
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="emergency_contact_number"
                                                                        value="{{ old('emergency_contact_number', $staffRow->emergency_mobile) }}"
                                                                        inputmode="numeric" maxlength="15" required>
                                                                </div>
                                                            </div>

                                                            {{-- Password (optional on edit) --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Password</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="password" name="password"
                                                                        autocomplete="new-password"
                                                                        placeholder="Leave blank to keep current">
                                                                </div>
                                                            </div>

                                                            {{-- Photo upload (optional). You can also show a small preview if present --}}
                                                            <div class="col-lg-2">
                                                                <div class="row g-2 input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('User Photo')</label>
                                                                            <input type="file"
                                                                                class="form-control form-control-sm"
                                                                                name="staff_photo" id="staff_photo"
                                                                                accept="image/*">
                                                                            @if (!empty($staffRow->staff_photo))
                                                                                <a href="{{ asset($staffRow->staff_photo) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-link p-0 mt-1">View
                                                                                    current</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Permanent Address --}}
                                                            <div class="col-lg-4">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Permanent
                                                                        Address</label>
                                                                    <textarea class="form-control form-control-sm" name="permanent_address" rows="2">{{ old('permanent_address', $staffRow->permanent_address) }}</textarea>
                                                                </div>
                                                            </div>

                                                            {{-- UAE Address (stored as current_address) --}}
                                                            <div class="col-lg-4">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">UAE Address</label>
                                                                    <textarea class="form-control form-control-sm" name="uae_address" rows="2">{{ old('uae_address', $staffRow->current_address) }}</textarea>
                                                                </div>
                                                            </div>

                                                        </div>


                                                        <div class="mt-3">
                                                            <span id="saveMsg" class="ms-2"></span>
                                                        </div>
                                                    </div>

                                                    {{-- ======================= EMPLOYEE MASTER – TABS (Drop-in) ======================= --}}
                                                    <div class="row">
                                                        <div class="col-12">

                                                            <h6 class="mb-3">Employee Details</h6>

                                                            <div class="tab-wrap mb-3">
                                                                <ul class="nav nav-tabs" id="hrTabs" role="tablist">
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link active" id="job-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#job-details" type="button"
                                                                            role="tab" aria-controls="job-details"
                                                                            aria-selected="true">
                                                                            Job Details
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="bank-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#bank-details" type="button"
                                                                            role="tab" aria-controls="bank-details"
                                                                            aria-selected="false">
                                                                            Bank Details
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="edu-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#educational-qualification"
                                                                            type="button" role="tab"
                                                                            aria-controls="educational-qualification"
                                                                            aria-selected="false">
                                                                            Educational Qualification
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="exp-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#professional-experience"
                                                                            type="button" role="tab"
                                                                            aria-controls="professional-experience"
                                                                            aria-selected="false">
                                                                            Professional Experience
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="docs-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#documentation" type="button"
                                                                            role="tab" aria-controls="documentation"
                                                                            aria-selected="false">
                                                                            Documentation
                                                                        </button>
                                                                    </li>
                                                                </ul>

                                                                <div class="tab-content border p-3 bg-white"
                                                                    id="hrTabsContent">

                                                                    {{-- ======================= TAB: JOB DETAILS ======================= --}}
                                                                  @php


    // ---- helpers ----
    $fmtD = function ($d) {
        if (empty($d)) {
            return '';
        }
        try {
            return Carbon::parse($d)->format('d/m/Y');
        } catch (\Exception $e) {
            return $d;
        }
    };

    $diskUrl = function (?string $p) {
        if (!$p) {
            return null;
        }
        $p = preg_replace(
            '#^(public/|storage/|public/storage/)+#',
            '',
            ltrim(str_replace('\\', '/', $p), '/')
        );
        return Storage::disk('public')->url($p);
    };

    // ---- selected arrays (handle json/array/null) ----
    $selectedManagers = old(
        'reporting_manager',
        is_string($jobRow->reporting_manager ?? null)
            ? json_decode($jobRow->reporting_manager, true) ?? []
            : (array) ($jobRow->reporting_manager ?? [])
    );

    $selectedCompanyAccess = old(
        'company_access',
        is_string($jobRow->company_access ?? null)
            ? json_decode($jobRow->company_access, true) ?? []
            : (array) ($jobRow->company_access ?? [])
    );

    $selectedBrands = old(
        'brands',
        is_string($jobRow->brands ?? null)
            ? json_decode($jobRow->brands, true) ?? []
            : (array) ($jobRow->brands ?? [])
    );

    // ---- simple scalars ----
    $doj = old(
        'date_of_joining_2',
        $fmtD($jobRow->date_of_joining ?? null)
    );
    $roleId = old('role_id', $jobRow->role_id ?? '');
    $desigId = old('designation_id', $jobRow->designation_id ?? '');
    $deptId = old('department_id', $jobRow->department_id ?? '');
    $empType = old('employment_type', $jobRow->employment_type ?? '');
    $weekOff = old('week_off', $jobRow->week_off ?? '');
    $workLoc = old('work_location', $jobRow->work_location ?? '');
    $workHours = old('work_hours', $jobRow->work_hours ?? '');
    $extNo2 = old('ext_no_2', $staffRow->ext_no ?? '');
    $visaCo = old('visa_company_name', $jobRow->visa_company_name ?? '');
    $workCo = old('working_company_name', $jobRow->working_company_name ?? '');
    $isTarget = old('is_target', $jobRow->is_target ?? 0);

    // ---- money ----
    $salBasic = old('salary_basic', $jobRow->salary_basic ?? '');
    $salAllow = old('salary_allowances', $jobRow->salary_allowances ?? '');
    $salOther = old('salary_other_allowances', $jobRow->salary_other_allowances ?? '');
    $salGross = old('salary_gross', $jobRow->salary_gross ?? '');

    // ---- attachments (existing) ----
    $resumeUrl = $diskUrl($jobRow->att_resume ?? null);
    $offerUrl = $diskUrl($jobRow->att_offer_letter ?? null);
    $contractUrl = $diskUrl($jobRow->att_signed_contract ?? null);
@endphp

                                                                    <div class="tab-pane fade show active"
                                                                        id="job-details" role="tabpanel"
                                                                        aria-labelledby="job-tab">
                                                                        <div class="row gy-2">
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Date of
                                                                                    Joining <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="date_of_joining_2"
                                                                                    value="{{ $doj }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Role')
                                                                                        <span>*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="role_id" id="role_id"
                                                                                        required onchange="checkRole()">
                                                                                        <option value="">-- Select
                                                                                            Role --</option>
                                                                                        @foreach ($roles as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ (string) $roleId === (string) $value->id ? 'selected' : '' }}>
                                                                                                {{ $value->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Designation')
                                                                                        <span>*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="designation_id"
                                                                                        id="designation_id" required>
                                                                                        <option value=""></option>
                                                                                        @foreach ($designations as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ (string) $desigId === (string) $value->id ? 'selected' : '' }}>
                                                                                                {{ $value->title }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Department')
                                                                                        <span>*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="department_id"
                                                                                        id="department_id" required>
                                                                                        <option value=""></option>
                                                                                        @foreach ($departments as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ (string) $deptId === (string) $value->id ? 'selected' : '' }}>
                                                                                                {{ $value->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Employment
                                                                                    Type <span
                                                                                        class="text-danger">*</span></label>
                                                                                <select class="form-select form-select-sm"
                                                                                    name="employment_type" required>
                                                                                    <option value="">-Select-
                                                                                    </option>
                                                                                    <option value="full_time"
                                                                                        {{ $empType === 'full_time' ? 'selected' : '' }}>
                                                                                        Full-Time</option>
                                                                                    <option value="part_time"
                                                                                        {{ $empType === 'part_time' ? 'selected' : '' }}>
                                                                                        Part-Time</option>
                                                                                    <option value="contract"
                                                                                        {{ $empType === 'contract' ? 'selected' : '' }}>
                                                                                        Contract</option>
                                                                                    <option value="intern"
                                                                                        {{ $empType === 'intern' ? 'selected' : '' }}>
                                                                                        Intern</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Reporting Managers')</label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-multiple"
                                                                                        name="reporting_manager[]"
                                                                                        id="reporting_manager" multiple
                                                                                        data-placeholder="Select one or more managers">
                                                                                        @foreach ($staff as $value)
                                                                                            <option
                                                                                                value="{{ $value->user_id }}"
                                                                                                {{ in_array((string) $value->user_id, array_map('strval', $selectedManagers), true) ? 'selected' : '' }}>
                                                                                                {{ $value->first_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Work
                                                                                    Location / Branch</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="work_location"
                                                                                    value="{{ $workLoc }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Work Hours /
                                                                                    Shift</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="work_hours"
                                                                                    value="{{ $workHours }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Basic
                                                                                    Salary</label>
                                                                                <input type="number" step="any"
                                                                                    class="form-control form-control-sm"
                                                                                    name="salary_basic"
                                                                                    value="{{ $salBasic }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">HRA</label>
                                                                                <input type="number" step="any"
                                                                                    class="form-control form-control-sm"
                                                                                    name="salary_allowances"
                                                                                    value="{{ $salAllow }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Other
                                                                                    Allowances</label>
                                                                                <input type="number" step="any"
                                                                                    class="form-control form-control-sm"
                                                                                    name="salary_other_allowances"
                                                                                    value="{{ $salOther }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Salary -
                                                                                    Gross</label>
                                                                                <input type="number" step="any"
                                                                                    class="form-control form-control-sm"
                                                                                    name="salary_gross"
                                                                                    value="{{ $salGross }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label class="form-label mb-1">Week Off
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="week_off" id="week_off"
                                                                                        required>
                                                                                        <option value="">-- Select --
                                                                                        </option>
                                                                                        <option value="sat_sun"
                                                                                            {{ $weekOff === 'sat_sun' ? 'selected' : '' }}>
                                                                                            Saturday/Sunday</option>
                                                                                        <option value="sunday"
                                                                                            {{ $weekOff === 'sunday' ? 'selected' : '' }}>
                                                                                            Sunday</option>
                                                                                        <option value="fri_sat"
                                                                                            {{ $weekOff === 'fri_sat' ? 'selected' : '' }}>
                                                                                            Friday/Saturday</option>
                                                                                        <option value="friday"
                                                                                            {{ $weekOff === 'friday' ? 'selected' : '' }}>
                                                                                            Friday</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Ext
                                                                                    No</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="ext_no_2"
                                                                                    value="{{ $extNo2 }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Company')
                                                                                        <span>*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="visa_company_name"
                                                                                        id="company_id" required>
                                                                                        <option value="">Select
                                                                                        </option>
                                                                                        @foreach ($company as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ (string) $visaCo === (string) $value->id ? 'selected' : '' }}>
                                                                                                {{ $value->company_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Main Company')
                                                                                        <span>*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="working_company_name"
                                                                                        id="main_company" required>
                                                                                        <option value=""></option>
                                                                                        @foreach ($company as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ (string) $workCo === (string) $value->id ? 'selected' : '' }}>
                                                                                                {{ $value->company_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-4">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Company Access')
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="company_access[]"
                                                                                        id="company_access" multiple
                                                                                        required>
                                                                                        @foreach ($company as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ in_array((string) $value->id, array_map('strval', $selectedCompanyAccess), true) ? 'selected' : '' }}>
                                                                                                {{ $value->company_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Resume --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Resume
                                                                                    (Attachment)</label>
                                                                                @if ($resumeUrl)
                                                                                    <div class="mb-1">
                                                                                        <a href="{{ $resumeUrl }}"
                                                                                            target="_blank"
                                                                                            class="btn btn-outline-primary btn-sm">View</a>
                                                                                        <div
                                                                                            class="form-check d-inline-block ms-2">
                                                                                            <input
                                                                                                class="form-check-input toggle-replace"
                                                                                                type="checkbox"
                                                                                                data-target="#att_resume_input">
                                                                                            <label
                                                                                                class="form-check-label">Replace</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm d-none"
                                                                                        id="att_resume_input"
                                                                                        name="att_resume">
                                                                                    <input type="hidden"
                                                                                        name="att_resume_existing"
                                                                                        value="{{ $jobRow->att_resume }}">
                                                                                @else
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="att_resume">
                                                                                @endif
                                                                            </div>

                                                                            {{-- Offer Letter --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Offer Letter
                                                                                    (Attachment)</label>
                                                                                @if ($offerUrl)
                                                                                    <div class="mb-1">
                                                                                        <a href="{{ $offerUrl }}"
                                                                                            target="_blank"
                                                                                            class="btn btn-outline-primary btn-sm">View</a>
                                                                                        <div
                                                                                            class="form-check d-inline-block ms-2">
                                                                                            <input
                                                                                                class="form-check-input toggle-replace"
                                                                                                type="checkbox"
                                                                                                data-target="#att_offer_input">
                                                                                            <label
                                                                                                class="form-check-label">Replace</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm d-none"
                                                                                        id="att_offer_input"
                                                                                        name="att_offer_letter">
                                                                                    <input type="hidden"
                                                                                        name="att_offer_letter_existing"
                                                                                        value="{{ $jobRow->att_offer_letter }}">
                                                                                @else
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="att_offer_letter">
                                                                                @endif
                                                                            </div>

                                                                            {{-- Signed Contract --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Signed
                                                                                    Contract (Attachment)</label>
                                                                                @if ($contractUrl)
                                                                                    <div class="mb-1">
                                                                                        <a href="{{ $contractUrl }}"
                                                                                            target="_blank"
                                                                                            class="btn btn-outline-primary btn-sm">View</a>
                                                                                        <div
                                                                                            class="form-check d-inline-block ms-2">
                                                                                            <input
                                                                                                class="form-check-input toggle-replace"
                                                                                                type="checkbox"
                                                                                                data-target="#att_contract_input">
                                                                                            <label
                                                                                                class="form-check-label">Replace</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm d-none"
                                                                                        id="att_contract_input"
                                                                                        name="att_signed_contract">
                                                                                    <input type="hidden"
                                                                                        name="att_signed_contract_existing"
                                                                                        value="{{ $jobRow->att_signed_contract }}">
                                                                                @else
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="att_signed_contract">
                                                                                @endif
                                                                            </div>

                                                                            {{-- Sales Target toggle --}}
                                                                            <div class="col-lg-2" id="sales_target_div"
                                                                                style="{{ (int) $isTarget === 1 ? '' : 'display:none;' }}">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Set Sales Target')</label>
                                                                                    <select
                                                                                        class="form-select form-select-sm"
                                                                                        name="is_target" id="is_target">
                                                                                        <option value="0"
                                                                                            {{ (int) $isTarget === 0 ? 'selected' : '' }}>
                                                                                            No</option>
                                                                                        <option value="1"
                                                                                            {{ (int) $isTarget === 1 ? 'selected' : '' }}>
                                                                                            Yes</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-4" id="brands_div"
                                                                                style="{{ (int) $isTarget === 1 ? '' : 'display:none;' }}">
                                                                                <div class="input-effect">
                                                                                    <label
                                                                                        class="form-label mb-1">@lang('Brands')</label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="brands[]" id="brands"
                                                                                        multiple>
                                                                                        @foreach ($brand_list as $value)
                                                                                            <option
                                                                                                value="{{ $value->id }}"
                                                                                                {{ in_array((string) $value->id, array_map('strval', $selectedBrands), true) ? 'selected' : '' }}>
                                                                                                {{ $value->title }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Targets block 1 --}}
                                                                            <div class="row gy-2" id="target_div1"
                                                                                style="{{ (int) $isTarget === 1 ? '' : 'display:none;' }}">
                                                                                <div class="col-12"><b>Sales Target</b>
                                                                                    <hr class="my-2" />
                                                                                </div>

                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">Revenue
                                                                                        Target Weekly <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="revenue_target_weekly"
                                                                                        type="number" step="any"
                                                                                        name="revenue_target_weekly"
                                                                                        value="{{ old('revenue_target_weekly', $jobRow->revenue_target_weekly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">Revenue
                                                                                        Target Monthly
                                                                                        <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="revenue_target_monthly"
                                                                                        type="number" step="any"
                                                                                        name="revenue_target_monthly"
                                                                                        value="{{ old('revenue_target_monthly', $jobRow->revenue_target_monthly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">Revenue
                                                                                        Target Quarterly
                                                                                        <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="revenue_target_quaterly"
                                                                                        type="number" step="any"
                                                                                        name="revenue_target_quaterly"
                                                                                        value="{{ old('revenue_target_quaterly', $jobRow->revenue_target_quaterly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">Revenue
                                                                                        Target Yearly <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="revenue_target_yearly"
                                                                                        type="number" step="any"
                                                                                        name="revenue_target_yearly"
                                                                                        value="{{ old('revenue_target_yearly', $jobRow->revenue_target_yearly ?? '') }}">
                                                                                </div>
                                                                            </div>

                                                                            {{-- Targets block 2 --}}
                                                                            <div class="row gy-2" id="target_div2"
                                                                                style="{{ (int) $isTarget === 1 ? '' : 'display:none;' }}">
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">GP
                                                                                        Target Weekly <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="gp_target_weekly"
                                                                                        type="number" step="any"
                                                                                        name="gp_target_weekly"
                                                                                        value="{{ old('gp_target_weekly', $jobRow->gp_target_weekly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">GP
                                                                                        Target Monthly
                                                                                        <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="gp_target_monthly"
                                                                                        type="number" step="any"
                                                                                        name="gp_target_monthly"
                                                                                        value="{{ old('gp_target_monthly', $jobRow->gp_target_monthly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">GP
                                                                                        Target Quarterly
                                                                                        <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="gp_target_quaterly"
                                                                                        type="number" step="any"
                                                                                        name="gp_target_quaterly"
                                                                                        value="{{ old('gp_target_quaterly', $jobRow->gp_target_quaterly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">GP
                                                                                        Target Yearly <span>*</span></label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="gp_target_yearly"
                                                                                        type="number" step="any"
                                                                                        name="gp_target_yearly"
                                                                                        value="{{ old('gp_target_yearly', $jobRow->gp_target_yearly ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                    <label class="form-label mb-1">Target
                                                                                        From Date</label>
                                                                                    <input
                                                                                        class="form-control form-control-sm"
                                                                                        id="target_month_from"
                                                                                        type="month"
                                                                                        name="target_month_from"
                                                                                        value="{{ old('target_month_from', $jobRow->target_month_from ?? '') }}">
                                                                                </div>
                                                                                <div class="col-lg-9">
                                                                                    <label class="form-label mb-1">Combine
                                                                                        User</label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="combind_user_id[]" multiple>
                                                                                        <option value=""></option>
                                                                                      @php
    $combinedRaw = $jobRow->combind_user_id ?? null;

    if (is_string($combinedRaw)) {
        $combinedDefault = json_decode($combinedRaw, true) ?? [];
    } else {
        $combinedDefault = (array) $combinedRaw;
    }

    $combined = old('combind_user_id', $combinedDefault);
@endphp

                                                                                       @php
    // Ensure $combined is an array and map all its values to strings only once
    $combined = is_array($combined) ? array_map('strval', $combined) : [];
@endphp

@foreach ($staff as $value)
    <option value="{{ $value->user_id }}"
        {{ in_array((string) $value->user_id, $combined, true) ? 'selected' : '' }}>
        {{ $value->full_name }}
    </option>
@endforeach

                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                                                    <div class="tab-pane fade" id="bank-details"
                                                                        role="tabpanel" aria-labelledby="bank-tab">
                                                                       @php

    // small helper to build a public URL from stored path
    $diskUrl = function (?string $p) {
        if (!$p) {
            return null;
        }
        $p = preg_replace(
            '#^(public/|storage/|public/storage/)+#',
            '',
            ltrim(str_replace('\\', '/', $p), '/')
        );
        return Storage::disk('public')->url($p); // => /storage/...
    };

    $bankName   = old('bank_name', $bankRow->bank_name ?? '');
    $bankBranch = old('bank_branch', $bankRow->bank_branch ?? '');
    $acHolder   = old('bank_ac_holder', $bankRow->bank_ac_holder ?? '');
    $acNumber   = old('bank_ac_number', $bankRow->bank_ac_number ?? '');
    $iban       = old('iban_number', $bankRow->iban_number ?? '');
    $swift      = old('swift_code', $bankRow->swift_code ?? '');
    $currency   = old('bank_currency', $bankRow->bank_currency ?? '');

    $ibanUrl = $diskUrl($bankRow->att_iban_letter ?? null);
@endphp


                                                                        <div class="row gy-2">

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Bank Name
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="bank_name"
                                                                                    value="{{ $bankName }}" required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Branch
                                                                                    Name</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="bank_branch"
                                                                                    value="{{ $bankBranch }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Account
                                                                                    Holder Name <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="bank_ac_holder"
                                                                                    value="{{ $acHolder }}" required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Bank Account
                                                                                    Number</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="bank_ac_number"
                                                                                    value="{{ $acNumber }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">IBAN Number
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="iban_number"
                                                                                    value="{{ $iban }}" required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">SWIFT
                                                                                    Code</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="swift_code"
                                                                                    value="{{ $swift }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label
                                                                                    class="form-label mb-1">Currency</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="bank_currency"
                                                                                    value="{{ $currency }}">
                                                                            </div>

                                                                            {{-- IBAN Letter --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">IBAN Letter
                                                                                    (Upload)</label>
                                                                                @if ($ibanUrl)
                                                                                    <div class="mb-1">
                                                                                        <a href="{{ $ibanUrl }}"
                                                                                            target="_blank"
                                                                                            class="btn btn-outline-primary btn-sm">View</a>
                                                                                        <div
                                                                                            class="form-check d-inline-block ms-2">
                                                                                            <input
                                                                                                class="form-check-input toggle-replace"
                                                                                                type="checkbox"
                                                                                                data-target="#att_iban_letter_input">
                                                                                            <label
                                                                                                class="form-check-label">Replace</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm d-none"
                                                                                        id="att_iban_letter_input"
                                                                                        name="att_iban_letter">
                                                                                    <input type="hidden"
                                                                                        name="att_iban_letter_existing"
                                                                                        value="{{ $bankRow->att_iban_letter }}">
                                                                                @else
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="att_iban_letter">
                                                                                @endif
                                                                            </div>

                                                                        </div>

                                                                        {{-- if you don’t already have this globally --}}
                                                                        <script>
                                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                                document.querySelectorAll('.toggle-replace').forEach(cb => {
                                                                                    const target = cb.getAttribute('data-target');
                                                                                    const input = target ? document.querySelector(target) : null;
                                                                                    cb.addEventListener('change', () => {
                                                                                        if (!input) return;
                                                                                        if (cb.checked) input.classList.remove('d-none');
                                                                                        else {
                                                                                            input.value = '';
                                                                                            input.classList.add('d-none');
                                                                                        }
                                                                                    });
                                                                                });
                                                                            });
                                                                        </script>

                                                                    </div>

                                                                    {{-- education qualification --}}
                                                                  @php
    use Illuminate\Support\Facades\Storage;

    $diskUrl = function (?string $p) {
        if (!$p) {
            return null;
        }
        $p = preg_replace(
            '#^(public/|storage/|public/storage/)+#',
            '',
            ltrim(str_replace('\\', '/', $p), '/')
        );
        return Storage::disk('public')->url($p); // -> /storage/...
    };

    $eduIterable = ($eduRows ?? collect())->count()
        ? $eduRows
        : collect([null]);
@endphp


                                                                    <div class="tab-pane fade"
                                                                        id="educational-qualification" role="tabpanel"
                                                                        aria-labelledby="edu-tab">
                                                                        <div class="mb-2 d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-light btn-sm"
                                                                                id="addEduRow">
                                                                                <i
                                                                                    class="ico icon-outline-add-square text-success"></i>
                                                                                Add Row
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-bordered align-middle"
                                                                                id="eduTable">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width:170px;">Highest
                                                                                            Qualification <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th>Board / University*</th>
                                                                                        <th style="width:150px;">
                                                                                            Specialization</th>
                                                                                        <th style="width:110px;">Year of
                                                                                            Completion</th>
                                                                                        <th style="width:140px;">Result
                                                                                        </th>
                                                                                        <th style="width:110px;">GPA / CGPA
                                                                                        </th>
                                                                                        <th style="width:150px;">Mode of
                                                                                            Study</th>
                                                                                        <th style="width:140px;">Country of
                                                                                            Study</th>
                                                                                        <th style="width:140px;">Duration
                                                                                            (Years)</th>
                                                                                        <th style="width:180px;">
                                                                                            Certificate Upload <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th style="width:60px;">Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($eduIterable as $i => $row)
                                                                                       @php
    $q   = old("education.$i.qualification", optional($row)->qualification);
    $uni = old("education.$i.university", optional($row)->university);
    $sp  = old("education.$i.specialization", optional($row)->specialization);
    $yr  = old("education.$i.year", optional($row)->year);
    $res = old("education.$i.result", optional($row)->result);
    $gpa = old("education.$i.gpa", optional($row)->gpa);
    $mod = old("education.$i.mode", optional($row)->mode);
    $cty = old("education.$i.country", optional($row)->country);
    $dur = old("education.$i.duration", optional($row)->duration);

    // certificate column name might be certificate or certificate_path depending on your table
    $certStored = optional($row)->certificate ?? optional($row)->certificate_path;
    $certUrl    = $diskUrl($certStored);

    $inputId = "edu_cert_$i";
@endphp

                                                                                        <tr>
                                                                                            <td>
                                                                                                <select
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][qualification]">
                                                                                                    <option value="">
                                                                                                        -Select-</option>
                                                                                                    @foreach (['High School', 'Diploma', 'Bachelor', 'Master', 'Certification'] as $opt)
                                                                                                        <option
                                                                                                            value="{{ $opt }}"
                                                                                                            {{ $q === $opt ? 'selected' : '' }}>
                                                                                                            {{ $opt }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][university]"
                                                                                                    value="{{ $uni }}">
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][specialization]"
                                                                                                    value="{{ $sp }}">
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][year]"
                                                                                                    value="{{ $yr }}"
                                                                                                    placeholder="YYYY">
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][result]"
                                                                                                    value="{{ $res }}"
                                                                                                    placeholder="Pass / Division">
                                                                                            </td>
                                                                                            <td><input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][gpa]"
                                                                                                    value="{{ $gpa }}">
                                                                                            </td>
                                                                                            <td>
                                                                                                <select
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][mode]">
                                                                                                    <option value="">
                                                                                                        -Select-</option>
                                                                                                    @foreach (['Full-Time', 'Part-Time', 'Distance', 'Online'] as $opt)
                                                                                                        <option
                                                                                                            value="{{ $opt }}"
                                                                                                            {{ $mod === $opt ? 'selected' : '' }}>
                                                                                                            {{ $opt }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][country]"
                                                                                                    value="{{ $cty }}">
                                                                                            </td>
                                                                                            <td><input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control"
                                                                                                    name="education[{{ $i }}][duration]"
                                                                                                    value="{{ $dur }}">
                                                                                            </td>

                                                                                            {{-- Certificate: View/Replace if existing, else plain required file on create row --}}
                                                                                            <td>
                                                                                                @if ($certUrl)
                                                                                                    <div class="mb-1">
                                                                                                        <a href="{{ $certUrl }}"
                                                                                                            target="_blank"
                                                                                                            class="btn btn-outline-primary btn-sm">View</a>
                                                                                                        <div
                                                                                                            class="form-check d-inline-block ms-2">
                                                                                                            <input
                                                                                                                class="form-check-input toggle-replace"
                                                                                                                type="checkbox"
                                                                                                                data-target="#{{ $inputId }}">
                                                                                                            <label
                                                                                                                class="form-check-label">Replace</label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <input type="file"
                                                                                                        class="form-control d-none"
                                                                                                        id="{{ $inputId }}"
                                                                                                        name="education[{{ $i }}][certificate]">
                                                                                                    <input type="hidden"
                                                                                                        name="education[{{ $i }}][certificate_existing]"
                                                                                                        value="{{ $certStored }}">
                                                                                                @else
                                                                                                    <input type="file"
                                                                                                        class="form-control"
                                                                                                        name="education[{{ $i }}][certificate]">
                                                                                                @endif
                                                                                            </td>

                                                                                            <td class="text-center">
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger btn-sm delEduRow">
                                                                                                    <i
                                                                                                        class="ico icon-bold-trash-bin-minimalistic-2"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>


                                                                    {{-- educational qualification end --}}

                                                                    {{-- professional experience --}}
                                                                    @php
                                                                        $expIterable = ($expRows ?? collect())->count()
                                                                            ? $expRows
                                                                            : collect([null]);
                                                                    @endphp

                                                                    <div class="tab-pane fade"
                                                                        id="professional-experience" role="tabpanel"
                                                                        aria-labelledby="exp-tab">
                                                                        <div class="mb-2 d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-light btn-sm"
                                                                                id="addExpRow">
                                                                                <i
                                                                                    class="ico icon-outline-add-square text-success"></i>
                                                                                Add Row
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-bordered align-middle"
                                                                                id="expTable">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Previous Organization <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th>Previous Designation</th>
                                                                                        <th style="width:180px;">Employment
                                                                                            Duration (Y, M)</th>
                                                                                        <th>Key Responsibilities</th>
                                                                                        <th style="width:200px;">Experience
                                                                                            Certificate (Attachment)</th>
                                                                                        <th style="width:60px;">Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                  @foreach ($expIterable as $i => $row)
    @php
        $org   = old("experience.$i.organization", optional($row)->organization);
        $desig = old("experience.$i.designation", optional($row)->designation);
        $yrs   = old("experience.$i.years", optional($row)->years);
        $mos   = old("experience.$i.months", optional($row)->months);
        $resp  = old("experience.$i.responsibilities", optional($row)->responsibilities);

        // certificate may be stored as 'certificate' or 'certificate_path'
        $certStored = optional($row)->certificate ?? optional($row)->certificate_path;
        $certUrl = $diskUrl($certStored);
        $inputId = "exp_cert_$i";
    @endphp

    <tr>
        <td>
            <input type="text" class="form-control"
                name="experience[{{ $i }}][organization]" value="{{ $org }}">
        </td>
        <td>
            <input type="text" class="form-control"
                name="experience[{{ $i }}][designation]" value="{{ $desig }}">
        </td>
        <td>
            <div class="d-flex gap-2">
                <input type="number" min="0" class="form-control"
                    name="experience[{{ $i }}][years]" value="{{ $yrs }}" placeholder="Years">
                <input type="number" min="0" max="11" class="form-control"
                    name="experience[{{ $i }}][months]" value="{{ $mos }}" placeholder="Months">
            </div>
        </td>
        <td>
            <input type="text" class="form-control"
                name="experience[{{ $i }}][responsibilities]" value="{{ $resp }}">
        </td>
        <td>
            @if ($certUrl)
                <div class="mb-1">
                    <a href="{{ $certUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    <div class="form-check d-inline-block ms-2">
                        <input class="form-check-input toggle-replace" type="checkbox" data-target="#{{ $inputId }}">
                        <label class="form-check-label">Replace</label>
                    </div>
                </div>
                <input type="file" class="form-control d-none"
                    id="{{ $inputId }}" name="experience[{{ $i }}][certificate]">
                <input type="hidden" name="experience[{{ $i }}][certificate_existing]"
                    value="{{ $certStored }}">
            @else
                <input type="file" class="form-control" name="experience[{{ $i }}][certificate]">
            @endif
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm delExpRow">
                <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
            </button>
        </td>
    </tr>
@endforeach

                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                    {{-- professional experience end --}}


                                                                    {{-- documents tab --}}
                                                                    <div class="tab-pane fade" id="documentation"
                                                                        role="tabpanel" aria-labelledby="docs-tab">

                                                                        {{-- 1. JOINING DOCUMENTS --}}
                                                                        <h6 class="mt-1">1. Joining Documents</h6>
                                                                        <div class="table-responsive mb-3">
                                                                            <table
                                                                                class="table table-bordered align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width:260px;">Document
                                                                                        </th>
                                                                                        <th style="width:220px;">Attachment
                                                                                        </th>
                                                                                        <th style="width:160px;">Expiry
                                                                                            Date</th>
                                                                                        <th>Remarks</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    {{-- Required ones you always want new uploads for --}}
                                                                                    <tr>
                                                                                        <td>Passport Copy with Visa <span
                                                                                                class="text-danger">*</span>
                                                                                        </td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][passport_visa][file]"
                                                                                                required></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control date-picker"
                                                                                                name="docs[joining][passport_visa][expiry]">
                                                                                        </td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][passport_visa][remarks]"
                                                                                                placeholder="Passport bio page + UAE visa page">
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Emirates ID <span
                                                                                                class="text-danger">*</span>
                                                                                        </td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][emirates_id][file]"
                                                                                                required></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control date-picker"
                                                                                                name="docs[joining][emirates_id][expiry]">
                                                                                        </td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][emirates_id][remarks]"
                                                                                                placeholder="Both sides">
                                                                                        </td>
                                                                                    </tr>

                                                                                    {{-- These are auto-prefilled from earlier tabs if available; user can Replace --}}
                                                                                    <tr>
                                                                                        <td>Photograph (Passport size)</td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][photo][file]">
                                                                                        </td>
                                                                                        <td></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][photo][remarks]"
                                                                                                placeholder="For ID card / records">
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>CV</td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][cv][file]">
                                                                                        </td>
                                                                                        <td></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][cv][remarks]"
                                                                                                placeholder="Resume at the time of joining">
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Offer Letter</td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][offer_letter][file]">
                                                                                        </td>
                                                                                        <td></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][offer_letter][remarks]"
                                                                                                placeholder="Signed by employee & HR">
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Bank Account Details (IBAN
                                                                                            Letter)</td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][iban_letter][file]">
                                                                                        </td>
                                                                                        <td></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][iban_letter][remarks]"
                                                                                                placeholder="Mandatory for payroll/WPS">
                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td>Professional Certifications</td>
                                                                                        <td>
                                                                                            {{-- prefill will show existing certs and keep this visible to add more --}}
                                                                                            <input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][prof_certs][file]">
                                                                                        </td>
                                                                                        <td></td>
                                                                                        <td><input type="text"
                                                                                                class="form-control"
                                                                                                name="docs[joining][prof_certs][remarks]"
                                                                                                placeholder="Optional for technical roles">
                                                                                        </td>
                                                                                    </tr>



                                                                                    @php
                                                                                        $moreJoiningDocs = [
                                                                                            [
                                                                                                'key' => 'emp_contract',
                                                                                                'label' =>
                                                                                                    'Employment Contract',
                                                                                                'remarks' =>
                                                                                                    'MOHRE / Free Zone contract',
                                                                                                'expiry' => true,
                                                                                                'required' => false,
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'academic',
                                                                                                'label' =>
                                                                                                    'Academic Certificates',
                                                                                                'remarks' =>
                                                                                                    'Verified/attested copies',
                                                                                                'expiry' => false,
                                                                                                'required' => false,
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'medical_fit',
                                                                                                'label' =>
                                                                                                    'Medical Fitness Certificate',
                                                                                                'remarks' =>
                                                                                                    'Required for visa processing',
                                                                                                'expiry' => false,
                                                                                                'required' => false,
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'prev_emp_pf',
                                                                                                'label' =>
                                                                                                    'Proof of Previous Employment',
                                                                                                'remarks' =>
                                                                                                    'Relieving/experience letter',
                                                                                                'expiry' => false,
                                                                                                'required' => false,
                                                                                            ],
                                                                                        ];
                                                                                    @endphp

                                                                                    @foreach ($moreJoiningDocs as $doc)
                                                                                        <tr>
                                                                                            <td>{{ $doc['label'] }}</td>

                                                                                            {{-- Attachment --}}
                                                                                            <td>
                                                                                                <input type="file"
                                                                                                    class="form-control"
                                                                                                    name="docs[joining][{{ $doc['key'] }}][file]"
                                                                                                    {{ $doc['required'] ? 'required' : '' }}>
                                                                                                {{-- When prefilled, JS will inject:
                                                                                            <input type="hidden" name="docs[joining][key][existing]" value="..."> --}}
                                                                                            </td>

                                                                                            {{-- Expiry (only for those that need it) --}}
                                                                                            <td>
                                                                                                @if ($doc['expiry'])
                                                                                                    <input type="text"
                                                                                                        class="form-control date-picker"
                                                                                                        name="docs[joining][{{ $doc['key'] }}][expiry]">
                                                                                                @endif
                                                                                            </td>

                                                                                            {{-- Remarks --}}
                                                                                            <td>
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="docs[joining][{{ $doc['key'] }}][remarks]"
                                                                                                    placeholder="{{ $doc['remarks'] }}">
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach


                                                                                    {{-- Add your other rows (emp_contract, medical_fit, academic, etc.) the same way --}}
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        {{-- 2. EMPLOYMENT DOCUMENTS --}}
                                                                     @php
    /** Build a public URL from whatever is stored (public path, storage path, or plain relative) */
    $diskUrl = function (?string $p) {
        if (!$p) {
            return null;
        }
        $p = preg_replace(
            '#^(public/|storage/|public/storage/)+#',
            '',
            ltrim(
                str_replace('\\', '/', $p),
                '/'
            )
        );
        return Storage::disk('public')->url($p); // => /storage/...
    };

    /**
     * Expect (if available) a structure like:
     * $existingDocs = [
     *   'employment' => [
     *      'appraisals' => ['file' => 'staff_docs/...', 'remarks' => '...'],
     *      ...
     *   ],
     *   'others' => [
     *      ['name'=>'Driving License Copy','file'=>'staff_docs/...','remarks'=>'...'],
     *      ...
     *   ]
     * ];
     */
    $existingDocs = $existingDocs ?? []; // from your controller if you pass it
    $empExisting = $existingDocs['employment'] ?? [];
    $otherExisting = $existingDocs['others'] ?? [];
@endphp

                                                                        {{-- =========================
     2. EMPLOYMENT DOCUMENTS
     ========================= --}}
                                                                        <h6 class="mt-3">2. Employment Documents</h6>
                                                                        <div class="table-responsive mb-3">
                                                                            <table
                                                                                class="table table-bordered align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width:260px;">Document
                                                                                        </th>
                                                                                        <th style="width:220px;">Attachment
                                                                                        </th>
                                                                                        <th>Remarks</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php
    $empDocs = [
        ['key' => 'appraisals', 'label' => 'Performance Appraisals', 'remarks' => 'Annual or probation evaluation forms'],
        ['key' => 'insurance', 'label' => 'Insurance Card', 'remarks' => 'Health insurance copy'],
        ['key' => 'training', 'label' => 'Training Certificates', 'remarks' => 'Internal/external training records'],
        ['key' => 'policies', 'label' => 'Policy Acknowledgements', 'remarks' => 'Signed HR policies, NDA, IT usage policy'],
        ['key' => 'assets', 'label' => 'Assets Assignment Form', 'remarks' => 'Laptop, SIM, access card issued'],
        ['key' => 'change_terms', 'label' => 'Change in Employment Terms', 'remarks' => 'Salary revision/promotion letters'],
        ['key' => 'warnings', 'label' => 'Warnings (If any)', 'remarks' => 'Written warning/disciplinary record'],
    ];
@endphp

@foreach ($empDocs as $doc)
    @php
        $k = $doc['key'];
        $label = $doc['label'];
        $ph = $doc['remarks'];

        $row = $empExisting[$k] ?? null;

        // Determine stored file path (array or object)
        $stored = is_array($row) ? ($row['file'] ?? null) : (optional($row)->file ?? optional($row)->path);

        $url = $diskUrl($stored);

        $remarks = old(
            "docs.employment.$k.remarks",
            is_array($row) ? ($row['remarks'] ?? '') : (optional($row)->remarks ?? '')
        );

        $inputId = "emp_{$k}_file";
    @endphp

    <tr>
        <td>{{ $label }}</td>
        <td>
            @if ($url)
                <div class="mb-1">
                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    <div class="form-check d-inline-block ms-2">
                        <input class="form-check-input toggle-replace" type="checkbox" data-target="#{{ $inputId }}">
                        <label class="form-check-label">Replace</label>
                    </div>
                </div>
                <input type="file" class="form-control d-none" id="{{ $inputId }}" name="docs[employment][{{ $k }}][file]">
                <input type="hidden" name="docs[employment][{{ $k }}][existing]" value="{{ $stored }}">
            @else
                <input type="file" class="form-control" name="docs[employment][{{ $k }}][file]">
            @endif
        </td>
        <td>
            <input type="text" class="form-control" name="docs[employment][{{ $k }}][remarks]" value="{{ $remarks }}" placeholder="{{ $ph }}">
        </td>
    </tr>
@endforeach


                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        {{-- =========================
     3. OTHERS (OPTIONAL)
     ========================= --}}
                                                                        <h6 class="mt-3">3. Others (Optional /
                                                                            Case-specific)</h6>

                                                                     @php
    // If there are saved "others", use them; else seed with your presets.
    $otherRows = collect($otherExisting);

    if ($otherRows->isEmpty()) {
        $otherRows = collect([
            [
                'name' => 'Driving License Copy',
                'file' => null,
                'remarks' => 'If company provides vehicle',
            ],
            [
                'name' => 'Trade License Copy (if under dependent visa)',
                'file' => null,
                'remarks' => 'For compliance',
            ],
            [
                'name' => 'Power of Attorney (if authorized signatory)',
                'file' => null,
                'remarks' => 'Case-based',
            ],
        ]);
    }
@endphp

                                                                        <div class="mb-2 d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-light btn-sm"
                                                                                id="addOtherDoc">
                                                                                <i
                                                                                    class="ico icon-outline-add-square text-success"></i>
                                                                                Add Row
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-bordered align-middle"
                                                                                id="otherDocsTable">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width:260px;">Document
                                                                                        </th>
                                                                                        <th style="width:220px;">Attachment
                                                                                        </th>
                                                                                        <th>Remarks</th>
                                                                                        <th style="width:60px;">Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($otherRows->values() as $i => $row)
@php
    $nm = old(
        "docs.others.$i.name",
        is_array($row) ? ($row['name'] ?? '') : (optional($row)->name ?? '')
    );
    $rmk = old(
        "docs.others.$i.remarks",
        is_array($row) ? ($row['remarks'] ?? '') : (optional($row)->remarks ?? '')
    );
    $stored = is_array($row)
        ? ($row['file'] ?? null)
        : (optional($row)->file ?? optional($row)->path);
    $url = $diskUrl($stored);
    $fid = "other_file_$i";
@endphp

    <tr>
        <td>
            <input type="text"
                class="form-control"
                name="docs[others][{{ $i }}][name]"
                value="{{ $nm }}">
        </td>
        <td>
            @if ($url)
                <div class="mb-1">
                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    <div class="form-check d-inline-block ms-2">
                        <input class="form-check-input toggle-replace"
                               type="checkbox"
                               data-target="#{{ $fid }}">
                        <label class="form-check-label">Replace</label>
                    </div>
                </div>
                <input type="file"
                       class="form-control d-none"
                       id="{{ $fid }}"
                       name="docs[others][{{ $i }}][file]">
                <input type="hidden"
                       name="docs[others][{{ $i }}][existing]"
                       value="{{ $stored }}">
            @else
                <input type="file"
                       class="form-control"
                       name="docs[others][{{ $i }}][file]">
            @endif
        </td>
        <td>
            <input type="text"
                class="form-control"
                name="docs[others][{{ $i }}][remarks]"
                value="{{ $rmk }}">
        </td>
        <td class="text-center">
            <button type="button"
                    class="btn btn-danger btn-sm delOtherRow">
                <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
            </button>
        </td>
    </tr>
@endforeach

                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        {{-- Make sure the global toggle handler exists once on the page --}}
                                                                        <script>
                                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                                document.querySelectorAll('.toggle-replace').forEach(cb => {
                                                                                    const target = cb.getAttribute('data-target');
                                                                                    const input = target ? document.querySelector(target) : null;
                                                                                    cb.addEventListener('change', () => {
                                                                                        if (!input) return;
                                                                                        if (cb.checked) input.classList.remove('d-none');
                                                                                        else {
                                                                                            input.value = '';
                                                                                            input.classList.add('d-none');
                                                                                        }
                                                                                    });
                                                                                });
                                                                            });
                                                                        </script>

                                                                        {{-- documents tab end --}}



        </form>



    </div> {{-- /.tab-content --}}
    </div> {{-- /.tab-wrap --}}


    </div>
    </div>
    {{-- ======================= / EMPLOYEE MASTER – TABS ======================= --}}

    {{-- Minimal JS: add/remove rows for Education, Experience, Other Docs --}}



    </div>

    <script>
        $(document).ready(function() {
            // Update values based on Weekly input
            $('#revenue_target_weekly').on('input', function() {
                var weekly = parseFloat($(this).val());
                if (!isNaN(weekly)) {
                    var monthly = (weekly * 4).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (weekly * 13).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (weekly * 52).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#revenue_target_monthly').val(monthly);
                    $('#revenue_target_quaterly').val(quarterly);
                    $('#revenue_target_yearly').val(yearly);
                }
            });

            // Update values based on Monthly input
            $('#revenue_target_monthly').on('input', function() {
                var monthly = parseFloat($(this).val());
                if (!isNaN(monthly)) {
                    var weekly = (monthly / 4).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (monthly * 3).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (monthly * 12).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#revenue_target_weekly').val(weekly);
                    $('#revenue_target_quaterly').val(quarterly);
                    $('#revenue_target_yearly').val(yearly);
                }
            });

            // Update values based on Quarterly input
            $('#revenue_target_quaterly').on('input', function() {
                var quarterly = parseFloat($(this).val());
                if (!isNaN(quarterly)) {
                    var weekly = (quarterly / 13).toFixed(@json(session('logged_session_data.decimal_point')));
                    var monthly = (quarterly / 3).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (quarterly * 4).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#revenue_target_weekly').val(weekly);
                    $('#revenue_target_monthly').val(monthly);
                    $('#revenue_target_yearly').val(yearly);
                }
            });

            // Update values based on Yearly input
            $('#revenue_target_yearly').on('input', function() {
                var yearly = parseFloat($(this).val());
                if (!isNaN(yearly)) {
                    var weekly = (yearly / 52).toFixed(@json(session('logged_session_data.decimal_point')));
                    var monthly = (yearly / 12).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (yearly / 4).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#revenue_target_weekly').val(weekly);
                    $('#revenue_target_monthly').val(monthly);
                    $('#revenue_target_quaterly').val(quarterly);
                }
            });

            // Update values based on Weekly input
            $('#gp_target_weekly').on('input', function() {
                var weekly = parseFloat($(this).val());
                if (!isNaN(weekly)) {
                    var monthly = (weekly * 4).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (weekly * 13).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (weekly * 52).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#gp_target_monthly').val(monthly);
                    $('#gp_target_quaterly').val(quarterly);
                    $('#gp_target_yearly').val(yearly);
                }
            });

            // Update values based on Monthly input
            $('#gp_target_monthly').on('input', function() {
                var monthly = parseFloat($(this).val());
                if (!isNaN(monthly)) {
                    var weekly = (monthly / 4).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (monthly * 3).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (monthly * 12).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#gp_target_weekly').val(weekly);
                    $('#gp_target_quaterly').val(quarterly);
                    $('#gp_target_yearly').val(yearly);
                }
            });

            // Update values based on Quarterly input
            $('#gp_target_quaterly').on('input', function() {
                var quarterly = parseFloat($(this).val());
                if (!isNaN(quarterly)) {
                    var weekly = (quarterly / 13).toFixed(@json(session('logged_session_data.decimal_point')));
                    var monthly = (quarterly / 3).toFixed(@json(session('logged_session_data.decimal_point')));
                    var yearly = (quarterly * 4).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#gp_target_weekly').val(weekly);
                    $('#gp_target_monthly').val(monthly);
                    $('#gp_target_yearly').val(yearly);
                }
            });

            // Update values based on Yearly input
            $('#gp_target_yearly').on('input', function() {
                var yearly = parseFloat($(this).val());
                if (!isNaN(yearly)) {
                    var weekly = (yearly / 52).toFixed(@json(session('logged_session_data.decimal_point')));
                    var monthly = (yearly / 12).toFixed(@json(session('logged_session_data.decimal_point')));
                    var quarterly = (yearly / 4).toFixed(@json(session('logged_session_data.decimal_point')));

                    $('#gp_target_weekly').val(weekly);
                    $('#gp_target_monthly').val(monthly);
                    $('#gp_target_quaterly').val(quarterly);
                }
            });
        });
    </script>

    </div>
    <div class="row mt-40">

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

    <script>
        $(function() {
            // 0) Never let the browser submit the form (Enter key, stray submit btn)
            $(document).on('submit', '#staffAllForm', function(e) {
                e.preventDefault();
            });

            // 1) Make sure all inputs belong to the single form (works across tabs)
            $('#data-details                 [name]').attr({
                'form': 'staffAllForm'
            });
            $('#job-details                  [name]').attr({
                'form': 'staffAllForm'
            });
            $('#bank-details                 [name]').attr({
                'form': 'staffAllForm'
            });
            $('#educational-qualification    [name]').attr({
                'form': 'staffAllForm'
            });
            $('#professional-experience      [name]').attr({
                'form': 'staffAllForm'
            });
            $('#documentation                [name]').attr({
                'form': 'staffAllForm'
            });

            // ---------- Helpers ----------
            const ALL_TABS = [
                '#data-details', '#job-details', '#bank-details',
                '#educational-qualification', '#professional-experience', '#documentation'
            ];

            function getForm() {
                const $f = $('#staffAllForm');
                if (!$f.length) throw new Error('#staffAllForm not found');
                return $f;
            }

            function clearTabBadges() {
                ALL_TABS.forEach(id => setTabBadge(id, 0));
            }

            function setTabBadge(tabId, count) {
                const btn = document.querySelector('[data-bs-target="' + tabId + '"], [href="' + tabId + '"]');
                if (!btn) return;
                let badge = btn.querySelector('.tab-error-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge bg-danger ms-1 tab-error-badge';
                    btn.appendChild(badge);
                }
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }

            function clearErrors($root) {
                $root.find('.is-invalid').removeClass('is-invalid');
                $root.find('.invalid-feedback').remove();
                clearTabBadges();
            }

            function ensureHolder($f) {
                if (!$f.next().hasClass('invalid-feedback')) {
                    $f.after(
                        '<div class="invalid-feedback" style="display:block;font-size:12px;color:#dc3545;"></div>'
                    );
                }
                return $f.next('.invalid-feedback');
            }

            function openTab(tabId) {
                const btn = document.querySelector('[data-bs-target="' + tabId + '"]') || document.querySelector(
                    '[href="' + tabId + '"]');
                if (!btn) return;
                if (window.bootstrap?.Tab) bootstrap.Tab.getOrCreateInstance(btn).show();
                else $(btn).tab('show');
            }

            function dotToBracket(path) {
                let out = path.replace(/\.(\d+)/g, '[$1]');
                out = out.replace(/\.([^\.\[\]]+)/g, '[$1]');
                return out;
            }

            function showErrors($root, errs) {
                let firstField = null;
                const counts = Object.fromEntries(ALL_TABS.map(id => [id, 0]));
                Object.keys(errs || {}).forEach(function(name) {
                    let $f = $root.find('[name="' + name + '"]');
                    if (!$f.length && name.includes('.')) $f = $root.find('[name="' + dotToBracket(name) +
                        '"]');
                    if (!$f.length) return;
                    $f.addClass('is-invalid');
                    ensureHolder($f).text(errs[name][0]);
                    if (!firstField) firstField = $f.get(0);
                    let tabId = '#data-details';
                    const pane = $f.closest('.tab-pane');
                    if (pane.length) tabId = '#' + pane.attr('id');
                    if (counts[tabId] !== undefined) counts[tabId] += 1;
                });
                Object.keys(counts).forEach(tabId => setTabBadge(tabId, counts[tabId]));
                if (firstField) {
                    const pane = $(firstField).closest('.tab-pane');
                    if (pane.length) openTab('#' + pane.attr('id'));
                    const y = firstField.getBoundingClientRect().top + window.pageYOffset - 120;
                    window.scrollTo({
                        top: y,
                        behavior: 'smooth'
                    });
                    firstField.focus({
                        preventScroll: true
                    });
                }
            }
            // include disabled inputs in FormData (browser omits them)
            function appendDisabledFields(fd) {
                getForm().find(':input:disabled[name]').each(function() {
                    const el = this,
                        name = el.name;
                    if (!name) return;
                    if (el.tagName === 'SELECT' && el.multiple) {
                        Array.from(el.options).forEach(opt => {
                            if (opt.selected) fd.append(name, opt.value);
                        });
                    } else if (el.type !== 'file') {
                        fd.set(name, $(el).val());
                    }
                });
            }

            function buildFD() {
                const $form = getForm();
                const fd = new FormData($form[0]);
                fd.set('staff_id', $('#saved_staff_id').val() || '');
                appendDisabledFields(fd);
                return fd;
            }

            // ---------- AJAX calls ----------
            function saveBasic() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            if (resp && resp.ok) {
                                // persist id in both DOM & next FD builds
                                $('#saved_staff_id').val(resp.staff_id);
                                resolve(resp.staff_id);
                            } else reject({
                                generic: 'Could not save Basic info.'
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveJob() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('staff.job.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Job details.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveBank() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('staff.bank.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Bank details.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveEducation() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('staff.education.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Educational details.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveExperience() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('staff.experience.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Experience details.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveDocs() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('staff.docs.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Documentation.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            // ---------- One-button flow (reentry + double-bind safe) ----------
            let savingAll = false;

            $('#btnSaveAll').off('click.saveAll').on('click.saveAll', async function() {
                if (savingAll) return; // reentry guard
                savingAll = true;

                const $btn = $(this);
                if ($btn.data('busy')) {
                    savingAll = false;
                    return;
                } // extra guard
                $btn.data('busy', true).prop('disabled', true).text('Saving...');
                $('#saveAllMsg').text('');

                try {
                    // 1) Basic: create or update; will set #saved_staff_id on first run
                    let staffId = $('#saved_staff_id').val();
                    staffId = await saveBasic();

                    // 2) Rest of the tabs
                    await saveJob();
                    await saveBank();
                    await saveEducation();
                    await saveExperience();
                    await saveDocs();

                    $('#saveAllMsg').text('All saved ✓ (Staff ID: ' + staffId + ')');
                } catch (e) {
                    $('#saveAllMsg').text(e && e.generic ? e.generic :
                        'Error saving. Please check highlighted fields.');
                    console.error('SaveAll failed:', e);
                } finally {
                    $btn.data('busy', false).prop('disabled', false).text('Save All');
                    savingAll = false;
                }
            });
        });
    </script>


    <script>
        $(function() {
            let docsPrefilled = false;

            function renderSingleExisting(key, item) {
                // key: photo | cv | offer | iban
                if (!item) return;
                const $file = $(`[name="docs[joining][${keyMap[key]}][file]"]`);
                if (!$file.length || $file.data('prefilled')) return;

                const id = `doc-${key}-file`;
                const $td = $file.closest('td');

                // top row: View + Replace
                const block = $(`
      <div class="mb-2" data-doc="${key}">
        <a href="${item.url}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
        <div class="form-check d-inline-block ms-2">
          <input class="form-check-input toggle-replace" type="checkbox" data-target="#${id}">
          <label class="form-check-label">Replace</label>
        </div>
      </div>
    `);
                $td.prepend(block);

                // hide file until "Replace"
                $file.attr('id', id).addClass('d-none').data('prefilled', true);

                // hidden existing path so controller can keep it
                $td.append(
                    `<input type="hidden" name="docs[joining][${keyMap[key]}][existing]" value="${item.path}">`);
            }

            function renderProfExisting(list) {
                // multiple certs; keep existing + allow uploading more
                const $file = $(`[name="docs[joining][prof_certs][file]"]`);
                if (!$file.length || $file.data('prefilled')) return;

                const $td = $file.closest('td');
                if (Array.isArray(list) && list.length) {
                    const wrap = $('<div class="mb-2"></div>');
                    list.forEach((it, idx) => {
                        wrap.append(`
          <div class="mb-1">
            <a href="${it.url}" target="_blank" class="btn btn-outline-secondary btn-sm">Cert ${idx+1}</a>
            <input type="hidden" name="docs[joining][prof_certs][existing][]" value="${it.path}">
          </div>
        `);
                    });
                    $td.prepend(wrap);
                    $file.data('prefilled', true); // we’re only marking once
                    // leave the file input visible so user can add more
                }
            }

            // Map from short key -> your docs[joining][KEY]
            const keyMap = {
                photo: 'photo',
                cv: 'cv',
                offer: 'offer_letter',
                iban: 'iban_letter'
            };

            async function loadDocsPrefill() {
                if (docsPrefilled) return;
                const sid = $('#saved_staff_id').val();
                if (!sid) return; // nothing saved yet

                try {
                    const resp = await $.getJSON("{{ route('staff.docs.peek') }}", {
                        staff_id: sid
                    });
                    if (!resp || !resp.ok) return;

                    renderSingleExisting('photo', resp.photo);
                    renderSingleExisting('cv', resp.cv);
                    renderSingleExisting('offer', resp.offer);
                    renderSingleExisting('iban', resp.iban);
                    renderProfExisting(resp.prof);

                    docsPrefilled = true;
                } catch (e) {
                    // no-op if nothing yet
                }
            }

            // When the Documentation tab is shown, prefill once
            $(document).on('shown.bs.tab', '[data-bs-target="#documentation"], a[href="#documentation"]',
                loadDocsPrefill);

            // Also refresh after a successful Save All (in case user uploads later in the flow)
            // Hook into your success message mutation:
            const origText = $('#saveAllMsg').text;
            // Simple: call prefill again after Save All success
            $(document).on('DOMSubtreeModified', '#saveAllMsg', function() {
                if ($(this).text().startsWith('All saved')) {
                    docsPrefilled = false;
                    loadDocsPrefill();
                }
            });

            // Replace toggle (shared with previous script)
            $(document).on('change', '.toggle-replace', function() {
                const target = $(this).data('target');
                if (!target) return;
                $(target).toggleClass('d-none', !this.checked);
                if (!this.checked) $(target).val('');
            });
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.js-example-basic-single').select2({
                width: '100%'
            });
            $('.js-example-basic-multiple').select2({
                width: '100%',
                placeholder: $('.js-example-basic-multiple').data('placeholder'),
                allowClear: true
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Replace toggle for existing files
            document.querySelectorAll('.toggle-replace').forEach(cb => {
                const target = cb.getAttribute('data-target');
                const input = target ? document.querySelector(target) : null;
                cb.addEventListener('change', () => {
                    if (!input) return;
                    if (cb.checked) input.classList.remove('d-none');
                    else {
                        input.value = '';
                        input.classList.add('d-none');
                    }
                });
            });

            // is_target show/hide
            const sel = document.getElementById('is_target');
            const reqIds = [
                '#revenue_target_weekly', '#revenue_target_monthly', '#revenue_target_quaterly',
                '#revenue_target_yearly',
                '#gp_target_weekly', '#gp_target_monthly', '#gp_target_quaterly', '#gp_target_yearly',
                '#target_month_from'
            ];

            function toggleTarget() {
                const on = sel && sel.value === '1';
                document.getElementById('target_div1')?.classList.toggle('d-none', !on);
                document.getElementById('target_div2')?.classList.toggle('d-none', !on);
                document.getElementById('brands_div')?.classList.toggle('d-none', !on);
                reqIds.forEach(id => {
                    const el = document.querySelector(id);
                    if (el) el.required = on;
                });
            }
            toggleTarget();
            sel && sel.addEventListener('change', toggleTarget);
        });
    </script>
@endsection

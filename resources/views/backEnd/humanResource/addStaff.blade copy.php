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

        /* Tab error highlighting with visual indicator */
        .nav-link.tab-error {
            color: #dc3545 !important;
            position: relative;
        }

        /* Small font size for dropdown options */
        .small-dropdown-font option {
            font-size: 10px !important;
        }

        .small-dropdown-font {
            font-size: 10px !important;
        }

        /* Select2 dropdown options font size */
        .select2-results__option {
            font-size: 11px !important;
        }

        .select2-results__option--selectable {
            font-size: 11px !important;
        }

        .select2-results__option--highlighted {
            font-size: 11px !important;
        }

        .target-amount-input {
            font-weight: normal;
            color: #2c3e50;
        }
    </style>
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        $editMode = isset($editMode) && $editMode === true;
        $staffData = isset($editData) ? $editData : null;
        $job = isset($jobRow) ? $jobRow : null;

        // Helper function to get field value (old input > edit data > default)
        function getFieldValue($fieldName, $editData = null, $jobData = null, $default = '')
        {
            // First check old input
            if (old($fieldName) !== null) {
                return old($fieldName);
            }
            // Then check staff data
            if ($editData && isset($editData->$fieldName)) {
                return $editData->$fieldName;
            }
            // Then check job data
            if ($jobData && isset($jobData->$fieldName)) {
                return $jobData->$fieldName;
            }
            return $default;
        }
    @endphp
    <div class="form-scroll">
        <form id="staffAllForm" action="{{ route('staff.basic.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_staff_id" name="staff_id" value="{{ $staffData->id ?? '' }}">
            @if ($editMode)
                <input type="hidden" name="edit_mode" value="1">
            @endif

            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                    <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                {{ $editMode ? 'Edit Employee' : 'Add Employee' }}
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right">
                                <button type="button"
                                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2" id="btnSaveAll"
                                    data-busy-text="Saving...">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                                    <span class="btn-text">Save</span>
                                </button>


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

                                                        <div class="row gy-2">

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('User')
                                                                        @lang('lang.no_') <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text"
                                                                        value="{{ $editMode ? $staffData->staff_no ?? '' : @App\SysHelper::get_new_staff_code() }}"
                                                                        readonly name="staff_code">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Salutation
                                                                        <span>*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="salutation" required>
                                                                        <option value="">Select</option>
                                                                        <option value="Mr"
                                                                            {{ (old('salutation') ?? ($staffData->salutation ?? '')) == 'Mr' ? 'selected' : '' }}>
                                                                            Mr</option>
                                                                        <option value="Mrs"
                                                                            {{ (old('salutation') ?? ($staffData->salutation ?? '')) == 'Mrs' ? 'selected' : '' }}>
                                                                            Mrs</option>
                                                                        <option value="Miss"
                                                                            {{ (old('salutation') ?? ($staffData->salutation ?? '')) == 'Miss' ? 'selected' : '' }}>
                                                                            Miss</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('lang.first')
                                                                        @lang('lang.name') <span>*</span></label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="first_name"
                                                                        value="{{ old('first_name', $staffData->first_name ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('lang.last')
                                                                        @lang('lang.name')</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="last_name"
                                                                        value="{{ old('last_name', $staffData->last_name ?? '') }}">
                                                                </div>
                                                            </div>



                                                            {{-- Date of Birth --}}
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Date of Birth <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm date-picker"
                                                                    name="date_of_birth"
                                                                    value="{{ old('date_of_birth', $staffData->date_of_birth ?? '') }}"
                                                                    required>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Place of Birth <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="place_of_birth"
                                                                    value="{{ old('place_of_birth', $staffData->place_of_birth ?? '') }}"
                                                                    required>
                                                            </div>

                                                            {{-- Religion --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Religion</label>
                                                                    @php $religionVal = old('religion', $staffData->religion ?? ''); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        name="religion">
                                                                        <option value="">Select</option>
                                                                        <option value="islam"
                                                                            {{ $religionVal == 'islam' ? 'selected' : '' }}>
                                                                            Islam</option>
                                                                        <option value="christianity"
                                                                            {{ $religionVal == 'christianity' ? 'selected' : '' }}>
                                                                            Christianity</option>

                                                                        <option value="hinduism"
                                                                            {{ $religionVal == 'hinduism' ? 'selected' : '' }}>
                                                                            Hinduism</option>
                                                                        <option value="Buddhism"
                                                                            {{ $religionVal == 'Buddhism' ? 'selected' : '' }}>
                                                                            Buddhism</option>


                                                                        <option value="sikhism"
                                                                            {{ $religionVal == 'sikhism' ? 'selected' : '' }}>
                                                                            Sikhism</option>

                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Gender --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">@lang('Gender')
                                                                        <span>*</span></label>
                                                                    @php $genderVal = old('gender_id', $staffData->gender_id ?? ''); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        name="gender_id" required>
                                                                        <option value="">Select</option>
                                                                        @foreach ($genders as $gender)
                                                                            <option value="{{ @$gender->id }}"
                                                                                {{ $genderVal == @$gender->id ? 'selected' : '' }}>
                                                                                {{ @$gender->base_setup_name }}
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
                                                                        type="tel" name="mobile"
                                                                        value="{{ old('mobile', $staffData->mobile ?? '') }}"
                                                                        inputmode="numeric" maxlength="15" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email ID
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="email"
                                                                        value="{{ old('email', $staffData->email ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Marital Status</label>
                                                                    @php $maritalVal = old('marital_status', $staffData->marital_status ?? ''); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        id="marital_status" name="marital_status">
                                                                        <option value="">Select</option>
                                                                        <option value="single"
                                                                            {{ $maritalVal == 'single' ? 'selected' : '' }}>
                                                                            Single</option>
                                                                        <option value="married"
                                                                            {{ $maritalVal == 'married' ? 'selected' : '' }}>
                                                                            Married</option>
                                                                        <option value="divorced"
                                                                            {{ $maritalVal == 'divorced' ? 'selected' : '' }}>
                                                                            Divorced</option>
                                                                        <option value="widowed"
                                                                            {{ $maritalVal == 'widowed' ? 'selected' : '' }}>
                                                                            Widowed</option>
                                                                    </select>
                                                                </div>
                                                            </div>




                                                            <div class="col-lg-2 mb-4">
                                                                <div class="row g-2 input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('User Photo')</label>
                                                                            <input type="file"
                                                                                class="form-control form-control-sm"
                                                                                name="staff_photo" id="staff_photo"
                                                                                accept="image/*">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            {{-- <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Nationality
                                                                        <span>*</span></label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="nationality">
                                                                        <option value="">-Select-</option>
                                                                        @foreach ($countries as $value)
                                                                            <option value="{{ @$value->id }}">
                                                                                {{ @$value->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div> --}}








                                                            {{-- <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Father's Name</label>
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text"
                                                                            style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Mr</span>
                                                                        <input class="form-control form-control-sm"
                                                                            type="text" name="fathers_name"
                                                                            value="{{ old('fathers_name', $staffData->fathers_name ?? '') }}"
                                                                            placeholder="Enter father's name">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mother's Name</label>
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text"
                                                                            style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Ms</span>
                                                                        <input class="form-control form-control-sm"
                                                                            type="text" name="mothers_name"
                                                                            value="{{ old('mothers_name', $staffData->mothers_name ?? '') }}"
                                                                            placeholder="Enter mother's name">
                                                                    </div>
                                                                </div>
                                                            </div> --}}





                                                            {{-- Father Details Section --}}
                                                            <div class="col-lg-2 mb-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Father Details</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">First Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="father_first_name"
                                                                        value="{{ old('father_first_name', $staffData->father_first_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Last Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="father_last_name"
                                                                        value="{{ old('father_last_name', $staffData->father_last_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="father_mobile"
                                                                        value="{{ old('father_mobile', $staffData->father_mobile ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="father_email"
                                                                        value="{{ old('father_email', $staffData->father_email ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Attachment</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="file" name="father_attachment"
                                                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                                </div>
                                                            </div>

                                                            {{-- Mother Details Section --}}
                                                            <div class="col-lg-2 mb-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Mother Details</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">First Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="mother_first_name"
                                                                        value="{{ old('mother_first_name', $staffData->mother_first_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Last Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="mother_last_name"
                                                                        value="{{ old('mother_last_name', $staffData->mother_last_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="mother_mobile"
                                                                        value="{{ old('mother_mobile', $staffData->mother_mobile ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="mother_email"
                                                                        value="{{ old('mother_email', $staffData->mother_email ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Attachment</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="file" name="mother_attachment"
                                                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                                </div>
                                                            </div>

                                                            {{-- Spouse Details Section --}}
                                                            <div class="col-lg-2 mb-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Spouse Details</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">First Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="spouse_first_name"
                                                                        value="{{ old('spouse_first_name', $staffData->spouse_first_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Last Name</label>
                                                                    <input
                                                                        class="form-control form-control-sm capitalize-input"
                                                                        type="text" name="spouse_last_name"
                                                                        value="{{ old('spouse_last_name', $staffData->spouse_last_name ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="spouse_mobile"
                                                                        value="{{ old('spouse_mobile', $staffData->spouse_mobile ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="spouse_email"
                                                                        value="{{ old('spouse_email', $staffData->spouse_email ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2 spouse-section">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Attachment</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="file" name="spouse_attachment"
                                                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                                </div>
                                                            </div>

                                                            <script>
                                                                $(document).ready(function() {
                                                                    function toggleSpouseSection() {
                                                                        var maritalStatus = $('#marital_status').val();
                                                                        if (maritalStatus === 'married') {
                                                                            $('.spouse-section').show();
                                                                        } else {
                                                                            $('.spouse-section').hide();
                                                                        }
                                                                    }

                                                                    // Initial check on page load
                                                                    toggleSpouseSection();

                                                                    // Listen for changes in marital status
                                                                    $('#marital_status').change(function() {
                                                                        toggleSpouseSection();
                                                                    });
                                                                });
                                                            </script>

                                                            {{-- End Spouse Details Section --}}
                                                            {{-- Emergency Contact Section --}}
                                                            <div class="col-lg-2 mb-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Emergency Contact 1
                                                                        :</label>
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Salutation
                                                                        <span>*</span></label>
                                                                    @php $emergSalVal = old('emergency_salutation', $staffData->emergency_salutation ?? ''); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        name="emergency_salutation">
                                                                        <option value="">Select</option>
                                                                        <option value="Mr"
                                                                            {{ $emergSalVal == 'Mr' ? 'selected' : '' }}>
                                                                            Mr</option>
                                                                        <option value="Mrs"
                                                                            {{ $emergSalVal == 'Mrs' ? 'selected' : '' }}>
                                                                            Mrs</option>
                                                                        <option value="Miss"
                                                                            {{ $emergSalVal == 'Miss' ? 'selected' : '' }}>
                                                                            Miss</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1"> Name
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="emergency_contact_name"
                                                                        value="{{ old('emergency_contact_name', $staffData->emergency_contact_name ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>



                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="emergency_contact_number"
                                                                        value="{{ old('emergency_contact_number', $staffData->emergency_mobile ?? '') }}"
                                                                        inputmode="numeric" maxlength="15">
                                                                </div>
                                                            </div>



                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="emergency_contact_email"
                                                                        value="{{ old('emergency_contact_email', $staffData->emergency_contact_email ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">
                                                                        Relationship <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text"
                                                                        name="emergency_contact_relationship"
                                                                        value="{{ old('emergency_contact_relationship', $staffData->emergency_contact_relationship ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            {{-- Emergency Contact Section --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Emergency Contact 2
                                                                        :</label>
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Salutation
                                                                        <span>*</span></label>
                                                                    @php $emergSalVal2 = old('emergency_salutation_2', $staffData->emergency_salutation_2 ?? ''); @endphp
                                                                    <select class="form-select form-select-sm"
                                                                        name="emergency_salutation_2">
                                                                        <option value="">Select</option>
                                                                        <option value="Mr"
                                                                            {{ $emergSalVal2 == 'Mr' ? 'selected' : '' }}>
                                                                            Mr</option>
                                                                        <option value="Mrs"
                                                                            {{ $emergSalVal2 == 'Mrs' ? 'selected' : '' }}>
                                                                            Mrs</option>
                                                                        <option value="Miss"
                                                                            {{ $emergSalVal2 == 'Miss' ? 'selected' : '' }}>
                                                                            Miss</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Name
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="emergency_contact_name_2"
                                                                        value="{{ old('emergency_contact_name_2', $staffData->emergency_contact_name_2 ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>



                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="tel" name="emergency_contact_number_2"
                                                                        value="{{ old('emergency_contact_number_2', $staffData->emergency_contact_number_2 ?? '') }}"
                                                                        inputmode="numeric" maxlength="15">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="email" name="emergency_contact_email_2"
                                                                        value="{{ old('emergency_contact_email_2', $staffData->emergency_contact_email_2 ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Relationship
                                                                        <span>*</span></label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text"
                                                                        name="emergency_contact_relationship_2"
                                                                        value="{{ old('emergency_contact_relationship_2', $staffData->emergency_contact_relationship_2 ?? '') }}"
                                                                        required>
                                                                </div>
                                                            </div>



                                                            <div class="col-lg-2 mb-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Permanent Address</label>

                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Country</label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="perm_country" id="perm_country">
                                                                        <option value="">-Select-</option>
                                                                        @foreach ($countries as $value)
                                                                            <option value="{{ @$value->id }}"
                                                                                {{ (old('perm_country') ?? ($staffData->perm_country ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                {{ @$value->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">State</label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="perm_state" id="perm_state">
                                                                        <option value="">-Select-</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">City</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="perm_city"
                                                                        value="{{ old('perm_city', $staffData->perm_city ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Area</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="perm_area"
                                                                        value="{{ old('perm_area', $staffData->perm_area ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1"
                                                                        style="font-size: 12px;">Building Name</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="perm_building_no"
                                                                        value="{{ old('perm_building_no', $staffData->perm_building_no ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1"
                                                                        style="font-size: 12px;"> Flat/Office No
                                                                    </label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="perm_flat_house_no"
                                                                        value="{{ old('perm_flat_house_no', $staffData->perm_flat_house_no ?? '') }}">
                                                                </div>
                                                            </div>


                                                            {{-- Current Address Section --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1 mt-4"
                                                                        style="font-weight: bold">Current Address</label>

                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Country</label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="curr_country" id="curr_country">
                                                                        <option value="">-Select-</option>
                                                                        @foreach ($countries as $value)
                                                                            <option value="{{ @$value->id }}"
                                                                                {{ (old('curr_country') ?? ($staffData->curr_country ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                {{ @$value->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">State</label>
                                                                    <select class="form-control js-example-basic-single"
                                                                        name="curr_state" id="curr_state">
                                                                        <option value="">-Select-</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">City</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="curr_city"
                                                                        value="{{ old('curr_city', $staffData->curr_city ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Area</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="curr_area"
                                                                        value="{{ old('curr_area', $staffData->curr_area ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1"
                                                                        style="font-size: 12px">Building Name</label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="curr_building_no"
                                                                        value="{{ old('curr_building_no', $staffData->curr_building_no ?? '') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-1">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1"
                                                                        style="font-size: 12px;">Flat/Office No
                                                                    </label>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" name="curr_flat_house_no"
                                                                        value="{{ old('curr_flat_house_no', $staffData->curr_flat_house_no ?? '') }}">
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

                                                                    {{-- COMMENTED: Not used in Add Staff, will be used elsewhere --}}
                                                                    {{-- <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="exp-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#attendance-leave-configuration"
                                                                            type="button" role="tab"
                                                                            aria-controls="attendance-leave-configuration"
                                                                            aria-selected="false">
                                                                            Attendance / Leave Configuration
                                                                        </button>
                                                                    </li> --}}


                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="docs-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#resignation-details"
                                                                            type="button" role="tab"
                                                                            aria-controls="resignation-details"
                                                                            aria-selected="false">
                                                                            Resignation Details
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

                                                                    <div class="tab-pane fade show active"
                                                                        id="job-details" role="tabpanel"
                                                                        aria-labelledby="job-tab">

                                                                        <div class="accordion" id="jobDetailsAccordion">

                                                                            {{-- 1. Job Information --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingJobInfo">
                                                                                    <button class="accordion-button"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseJobInfo"
                                                                                        aria-expanded="true"
                                                                                        aria-controls="collapseJobInfo">
                                                                                        <span class="me-2">1</span> Job
                                                                                        Information
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseJobInfo"
                                                                                    class="accordion-collapse collapse show"
                                                                                    aria-labelledby="headingJobInfo">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Date
                                                                                                    of
                                                                                                    Joining
                                                                                                    <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker"
                                                                                                    name="date_of_joining_2"
                                                                                                    value="{{ old('date_of_joining_2', $job->date_of_joining ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Probation
                                                                                                    End Date</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker"
                                                                                                    name="probation_end_date"
                                                                                                    value="{{ old('probation_end_date', $job->probation_end_date ?? '') }}"
                                                                                                    placeholder="Optional">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                                                                        <span>@lang('Department')
                                                                                                            <span
                                                                                                                class="text-danger">*</span></span>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-sm p-0 ms-2"
                                                                                                            style="border:none;background:none;"
                                                                                                            data-bs-toggle="modal"
                                                                                                            data-bs-target="#departmentAddModal">
                                                                                                            <i class="ico icon-outline-add-square text-success"
                                                                                                                style="font-size:18px;"></i>
                                                                                                        </button>
                                                                                                    </label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="department_id"
                                                                                                        id="department_id"
                                                                                                        required>
                                                                                                        <option
                                                                                                            value="">
                                                                                                        </option>
                                                                                                        @foreach ($departments as $key => $value)
                                                                                                            <option
                                                                                                                value="{{ @$value->id }}"
                                                                                                                {{ (old('department_id') ?? ($job->department_id ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                                                {{ @$value->name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                                                                        <span>@lang('Designation')
                                                                                                            <span
                                                                                                                class="text-danger">*</span></span>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-sm p-0 ms-2"
                                                                                                            style="border:none;background:none;"
                                                                                                            data-bs-toggle="modal"
                                                                                                            data-bs-target="#adddesignationModal2">
                                                                                                            <i class="ico icon-outline-add-square text-success"
                                                                                                                style="font-size:18px;"></i>
                                                                                                        </button>
                                                                                                    </label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="designation_id"
                                                                                                        id="designation_id"
                                                                                                        required>
                                                                                                        <option
                                                                                                            value="">
                                                                                                        </option>
                                                                                                        @foreach ($designations as $key => $value)
                                                                                                            <option
                                                                                                                value="{{ @$value->id }}"
                                                                                                                {{ (old('designation_id') ?? ($job->designation_id ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                                                {{ @$value->title }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Reporting Managers')</label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-multiple"
                                                                                                        name="reporting_manager[]"
                                                                                                        id="reporting_manager"
                                                                                                        multiple
                                                                                                        data-placeholder="Select one or more managers">
                                                                                                        @foreach ($staff as $value)
                                                                                                            <option @if(in_array(@$value->user_id, old('reporting_manager', isset($staffData->reporting_manager) ? explode(',', $staffData->reporting_manager) : []))) selected @endif
                                                                                                                value="{{ @$value->user_id }}">
                                                                                                                {{ @$value->full_name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Employment
                                                                                                    Type
                                                                                                    <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                @php $empTypeVal = old('employment_type') ?? ($job->employment_type ?? ''); @endphp
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="employment_type"
                                                                                                    required>
                                                                                                    <option value="">
                                                                                                        -Select-
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="full_time"
                                                                                                        {{ $empTypeVal == 'full_time' ? 'selected' : '' }}>
                                                                                                        Full-Time</option>
                                                                                                    <option
                                                                                                        value="part_time"
                                                                                                        {{ $empTypeVal == 'part_time' ? 'selected' : '' }}>
                                                                                                        Part-Time</option>
                                                                                                    <option
                                                                                                        value="contract"
                                                                                                        {{ $empTypeVal == 'contract' ? 'selected' : '' }}>
                                                                                                        Contract</option>
                                                                                                    <option value="intern"
                                                                                                        {{ $empTypeVal == 'intern' ? 'selected' : '' }}>
                                                                                                        Intern</option>
                                                                                                </select>
                                                                                            </div>



                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 2. Company Information --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingCompanyInfo">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseCompanyInfo"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseCompanyInfo">
                                                                                        <span class="me-2">2</span>
                                                                                        Company Information
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseCompanyInfo"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingCompanyInfo">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">




                                                                                            @php
                                                                                                $visaCompanyVal = old(
                                                                                                    'visa_company_name',
                                                                                                    $job->visa_company_name ??
                                                                                                        ($staffData->company_id ??
                                                                                                            1),
                                                                                                );
                                                                                            @endphp
                                                                                            <div class="col-lg-3">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Company')
                                                                                                        (Visa)
                                                                                                        <span>*</span></label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="visa_company_name"
                                                                                                        id="company_id"
                                                                                                        required>
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Select
                                                                                                        </option>
                                                                                                        @foreach ($company as $key => $value)
                                                                                                            <option
                                                                                                                value="{{ @$value->id }}"
                                                                                                                {{ $visaCompanyVal == @$value->id ? 'selected' : '' }}>
                                                                                                                {{ @$value->company_name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            @php
                                                                                                $workingCompanyVal = old(
                                                                                                    'working_company_name',
                                                                                                    $job->working_company_name ??
                                                                                                        ($staffData->main_company ??
                                                                                                            ''),
                                                                                                );
                                                                                            @endphp
                                                                                            <div class="col-lg-3">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Company')
                                                                                                        (Working)
                                                                                                        <span>*</span></label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="working_company_name"
                                                                                                        id="main_company"
                                                                                                        required>
                                                                                                        <option
                                                                                                            value="">
                                                                                                        </option>
                                                                                                        @foreach ($company as $key => $value)
                                                                                                            <option
                                                                                                                value="{{ @$value->id }}"
                                                                                                                {{ $workingCompanyVal == @$value->id ? 'selected' : '' }}>
                                                                                                                {{ @$value->company_name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            @php
                                                                                                $companyAccessVal = old(
                                                                                                    'company_access',
                                                                                                    $staffData->company_access_ids ??
                                                                                                        [],
                                                                                                );
                                                                                                if (
                                                                                                    !is_array(
                                                                                                        $companyAccessVal,
                                                                                                    )
                                                                                                ) {
                                                                                                    $companyAccessVal = [];
                                                                                                }
                                                                                            @endphp
                                                                                            <div class="col-lg-3">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Company')
                                                                                                        (Access)
                                                                                                        <span
                                                                                                            class="text-danger">*</span></label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="company_access[]"
                                                                                                        id="company_access"
                                                                                                        multiple required>
                                                                                                        @foreach ($company as $value)
                                                                                                            <option
                                                                                                                value="{{ $value->id }}"
                                                                                                                {{ in_array($value->id, $companyAccessVal) ? 'selected' : '' }}>
                                                                                                                {{ $value->company_name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>


                                                                                            <div class="col-lg-2">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Role')
                                                                                                        (Access)
                                                                                                        <span>*</span></label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="role_id"
                                                                                                        id="role_id"
                                                                                                        required
                                                                                                        onchange="checkRole()">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            -- Select
                                                                                                            Role
                                                                                                            --</option>
                                                                                                        @foreach ($roles as $key => $value)
                                                                                                            <option
                                                                                                                value="{{ $value->id }}"
                                                                                                                {{ (old('role_id') ?? ($job->role_id ?? '')) == $value->id ? 'selected' : '' }}>
                                                                                                                {{ $value->name }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-1">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">Password
                                                                                                        <span>*</span></label>
                                                                                                    <input
                                                                                                        class="form-control form-control-sm"
                                                                                                        type="password"
                                                                                                        name="password"
                                                                                                        autocomplete="new-password"
                                                                                                        required>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>



                                                                            {{-- 3. Work Details --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingWorkDetails">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseWorkDetails"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseWorkDetails">
                                                                                        <span class="me-2">3</span> Work
                                                                                        Details
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseWorkDetails"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingWorkDetails">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">



                                                                                            {{-- Toggle targets based on is_target --}}
                                                                                            <script>
                                                                                                function fn_role_id() {
                                                                                                    if ($('#is_target').val() == 1) {
                                                                                                        $('#target_div1,#target_div2').show();
                                                                                                        $('#revenue_target_weekly,#revenue_target_monthly,#revenue_target_quaterly,#revenue_target_yearly,#gp_target_weekly,#gp_target_monthly,#gp_target_quaterly,#gp_target_yearly,#target_month_from')
                                                                                                            .prop('required', true);
                                                                                                    } else {
                                                                                                        $('#target_div1,#target_div2').hide();
                                                                                                        $('#revenue_target_weekly,#revenue_target_monthly,#revenue_target_quaterly,#revenue_target_yearly,#gp_target_weekly,#gp_target_monthly,#gp_target_quaterly,#gp_target_yearly,#target_month_from')
                                                                                                            .prop('required', false);
                                                                                                    }
                                                                                                }
                                                                                            </script>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Work
                                                                                                    Location /
                                                                                                    Branch</label>
                                                                                                <input type="text"
                                                                                                    id="work_location"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="work_location"
                                                                                                    value="{{ old('work_location', $job->work_location ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Work
                                                                                                    Hours /
                                                                                                    Shift</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="work_hours"
                                                                                                    value="{{ old('work_hours', $job->work_hours ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1 hr-payroll-labels">Weekly
                                                                                                    Off</label>
                                                                                                <select
                                                                                                    name="hr_weekly_off[]"
                                                                                                    multiple
                                                                                                    class="form-select form-select-sm setting-input js-example-basic-multiple small-dropdown-font">
                                                                                                    @php
                                                                                                        $selectedWeeklyOffs = old(
                                                                                                            'hr_weekly_off',
                                                                                                            is_string(
                                                                                                                $settings->hr_weekly_off ??
                                                                                                                    '',
                                                                                                            )
                                                                                                                ? explode(
                                                                                                                    ',',
                                                                                                                    $settings->hr_weekly_off ??
                                                                                                                        '',
                                                                                                                )
                                                                                                                : $settings->hr_weekly_off ??
                                                                                                                    [],
                                                                                                        );
                                                                                                        if (
                                                                                                            !is_array(
                                                                                                                $selectedWeeklyOffs,
                                                                                                            )
                                                                                                        ) {
                                                                                                            $selectedWeeklyOffs = [
                                                                                                                $selectedWeeklyOffs,
                                                                                                            ];
                                                                                                        }
                                                                                                    @endphp
                                                                                                    <option
                                                                                                        value="monday_all"
                                                                                                        {{ in_array('monday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Monday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="tuesday_all"
                                                                                                        {{ in_array('tuesday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Tuesday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="wednesday_all"
                                                                                                        {{ in_array('wednesday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Wednesday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="thursday_all"
                                                                                                        {{ in_array('thursday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Thursday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="friday_all"
                                                                                                        {{ in_array('friday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Friday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_monday"
                                                                                                        {{ in_array('1_3_monday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Monday (Only 1
                                                                                                        & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_monday"
                                                                                                        {{ in_array('2_4_monday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Monday (Only 2
                                                                                                        & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_tuesday"
                                                                                                        {{ in_array('1_3_tuesday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Tuesday (Only
                                                                                                        1 & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_tuesday"
                                                                                                        {{ in_array('2_4_tuesday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Tuesday (Only
                                                                                                        2 & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_wednesday"
                                                                                                        {{ in_array('1_3_wednesday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Wednesday
                                                                                                        (Only 1 & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_wednesday"
                                                                                                        {{ in_array('2_4_wednesday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Wednesday
                                                                                                        (Only 2 & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_thursday"
                                                                                                        {{ in_array('1_3_thursday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Thursday (Only
                                                                                                        1 & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_thursday"
                                                                                                        {{ in_array('2_4_thursday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Thursday (Only
                                                                                                        2 & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_friday"
                                                                                                        {{ in_array('1_3_friday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Friday (Only 1
                                                                                                        & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_friday"
                                                                                                        {{ in_array('2_4_friday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Friday (Only 2
                                                                                                        & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="saturday_all"
                                                                                                        {{ in_array('saturday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Saturday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_saturday"
                                                                                                        {{ in_array('1_3_saturday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Saturday (Only
                                                                                                        1 & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_saturday"
                                                                                                        {{ in_array('2_4_saturday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Saturday (Only
                                                                                                        2 & 4)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="sunday_all"
                                                                                                        {{ in_array('sunday_all', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        Sunday (All)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="1_3_sunday"
                                                                                                        {{ in_array('1_3_sunday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        1 & 3 Sunday (Only 1
                                                                                                        & 3)
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="2_4_sunday"
                                                                                                        {{ in_array('2_4_sunday', $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                                        2 & 4 Sunday (Only 2
                                                                                                        & 4)
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Ext
                                                                                                    No</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="ext_no_2"
                                                                                                    value="{{ old('ext_no_2', $job->ext_no ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">
                                                                                                    Email ID</label>
                                                                                                <input type="email"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="company_email"
                                                                                                    value="{{ old('company_email', $job->company_email ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">
                                                                                                    Mobile No</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="company_mobile"
                                                                                                    value="{{ old('company_mobile', $job->company_mobile ?? '') }}">
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 4. Salary Details --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingSalaryDetails">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseSalaryDetails"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseSalaryDetails">
                                                                                        <span class="me-2">4</span>
                                                                                        Salary Details
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseSalaryDetails"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingSalaryDetails">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Basic
                                                                                                    Salary</label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm salary-component text-end"
                                                                                                    id="salary_basic"
                                                                                                    name="salary_basic"
                                                                                                    value="{{ old('salary_basic', $job->salary_basic ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">HRA</label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm salary-component text-end"
                                                                                                    id="salary_allowances"
                                                                                                    name="salary_allowances"
                                                                                                    value="{{ old('salary_allowances', $job->salary_hra ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Other
                                                                                                    Allowances</label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm salary-component text-end"
                                                                                                    id="salary_other_allowances"
                                                                                                    name="salary_other_allowances"
                                                                                                    value="{{ old('salary_other_allowances', $job->salary_other_allowances ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Transport
                                                                                                    Allowance</label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm salary-component text-end"
                                                                                                    id="transport_allowance"
                                                                                                    name="transport_allowance"
                                                                                                    value="{{ old('transport_allowance', $job->salary_transport ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Other
                                                                                                    Benefits</label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm salary-component text-end"
                                                                                                    id="other_benefits"
                                                                                                    name="other_benefits"
                                                                                                    value="{{ old('other_benefits', $job->salary_other_benefits ?? '') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Gross
                                                                                                    Salary

                                                                                                </label>
                                                                                                <input type="number"
                                                                                                    step="any"
                                                                                                    class="form-control form-control-sm text-end"
                                                                                                    id="salary_gross"
                                                                                                    name="salary_gross"
                                                                                                    value="{{ old('salary_gross', $job->salary_total ?? '') }}"
                                                                                                    readonly>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>





                                                                            {{-- 5. Sales & Performance Targets --}}
                                                                            <div class="accordion-item"
                                                                                id="salesTargetsAccordion"
                                                                                style="display:none;">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingSalesTargets">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseSalesTargets"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseSalesTargets">
                                                                                        <span class="me-2">5</span> Sales
                                                                                        & Performance Targets
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseSalesTargets"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingSalesTargets">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">

                                                                                            <div class="col-lg-1"
                                                                                                id="sales_target_div"
                                                                                                style="display:none;">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Sales Target')</label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm"
                                                                                                        name="is_target"
                                                                                                        id="is_target"
                                                                                                        onchange="fn_role_id()">
                                                                                                        <option
                                                                                                            value="0">
                                                                                                            No</option>
                                                                                                        <option
                                                                                                            value="1">
                                                                                                            Yes</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-lg-1"
                                                                                                id="target_from_date_div"
                                                                                                style="display:none;">
                                                                                                <label
                                                                                                    class="form-label mb-1">Target
                                                                                                    From </label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker flatpickr-input"
                                                                                                    name="target_month_from"
                                                                                                    id="target_month_from"
                                                                                                    value="{{ old('target_month_from', $job->target_month_from ?? '') }}">
                                                                                            </div>

                                                                                            {{-- <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Combine
                                                                                                    User</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                                    name="combind_user_id[]"
                                                                                                    multiple>
                                                                                                    <option value="">
                                                                                                    </option>
                                                                                                    @foreach ($staff as $value)
                                                                                                        <option
                                                                                                            value="{{ @$value->user_id }}">
                                                                                                            {{ @$value->full_name }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div> --}}

                                                                                            <div class="col-lg-1"
                                                                                                id="target_type_div"
                                                                                                style="display:none;">
                                                                                                <label
                                                                                                    class="form-label mb-1">Type</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="target_type"
                                                                                                    id="target_type"
                                                                                                    onchange="toggleTargetInputs()">
                                                                                                    <option value="">
                                                                                                        -Select-</option>
                                                                                                    <option
                                                                                                        value="revenue">
                                                                                                        Revenue</option>
                                                                                                    <option value="gp">
                                                                                                        GP</option>
                                                                                                    <option value="both">
                                                                                                        Both</option>
                                                                                                </select>
                                                                                            </div>



                                                                                            <div class="col-lg-2"
                                                                                                id="target_period_div"
                                                                                                style="display:none;">
                                                                                                <label
                                                                                                    class="form-label mb-1">Target
                                                                                                    Period</label>
                                                                                                @php $targetPeriodVal = old('target_period') ?? ($job->target_period ?? ''); @endphp
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="target_period">
                                                                                                    <option value="">
                                                                                                        -Select-</option>
                                                                                                    <option value="yearly"
                                                                                                        {{ $targetPeriodVal == 'yearly' ? 'selected' : '' }}>
                                                                                                        Yearly</option>
                                                                                                    <option
                                                                                                        value="halfyear"
                                                                                                        {{ $targetPeriodVal == 'halfyear' ? 'selected' : '' }}>
                                                                                                        Half Year</option>
                                                                                                    <option
                                                                                                        value="quarterly"
                                                                                                        {{ $targetPeriodVal == 'quarterly' ? 'selected' : '' }}>
                                                                                                        Quarterly</option>
                                                                                                    <option value="monthly"
                                                                                                        {{ $targetPeriodVal == 'monthly' ? 'selected' : '' }}>
                                                                                                        Monthly</option>
                                                                                                    <option value="weekly"
                                                                                                        {{ $targetPeriodVal == 'weekly' ? 'selected' : '' }}>
                                                                                                        Weekly</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-2"
                                                                                                id="revenue_target_input"
                                                                                                style="display:none;">
                                                                                                <label
                                                                                                    class="form-label mb-1"
                                                                                                    style="font-weight: normal;">Revenue
                                                                                                    Target</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm text-end target-amount-input"
                                                                                                    name="revenue_target"
                                                                                                    value="{{ old('revenue_target', $job->revenue_target ?? '10,000,000.00') }}"
                                                                                                    placeholder="0.00">
                                                                                            </div>

                                                                                            <div class="col-lg-2"
                                                                                                id="gp_target_input"
                                                                                                style="display:none;">
                                                                                                <label
                                                                                                    class="form-label mb-1"
                                                                                                    style="font-weight: normal;">GP
                                                                                                    Target</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm text-end target-amount-input"
                                                                                                    name="gp_target"
                                                                                                    value="{{ old('gp_target', $job->gp_target ?? '') }}"
                                                                                                    placeholder="0.00">
                                                                                            </div>

                                                                                            <div class="col-lg-1">
                                                                                                <label
                                                                                                    class="form-label mb-1">Segment</label>
                                                                                                @php $channelDistVal = old('channel_distribution') ?? ($job->channel_distribution ?? ''); @endphp
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="channel_distribution">
                                                                                                    <option value="">
                                                                                                        -Select-</option>
                                                                                                    <option value="Channel"
                                                                                                        {{ $channelDistVal == 'Channel' ? 'selected' : '' }}>
                                                                                                        Channel</option>
                                                                                                    <option
                                                                                                        value="Distribution"
                                                                                                        {{ $channelDistVal == 'Distribution' ? 'selected' : '' }}>
                                                                                                        Distribution
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-2"
                                                                                                id="brands_div"
                                                                                                style="display:none;">
                                                                                                <div class="input-effect">
                                                                                                    <label
                                                                                                        class="form-label mb-1">@lang('Brands')</label>
                                                                                                    <select
                                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                                        name="brands[]"
                                                                                                        id="brands"
                                                                                                        multiple>
                                                                                                        <option
                                                                                                            value="all">
                                                                                                            All</option>
                                                                                                        @foreach ($brand_list as $value)
                                                                                                            <option
                                                                                                                value="{{ $value->id }}">
                                                                                                                {{ $value->title }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- <div class="col-12"
                                                                                                id="target_div1"
                                                                                                style="display:none;">
                                                                                                <div class="row gy-2">
                                                                                                    <div
                                                                                                        class="col-12 mb-2">
                                                                                                        <strong>Revenue
                                                                                                            Targets</strong>
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">Revenue
                                                                                                            Target Weekly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="revenue_target_weekly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="revenue_target_weekly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">Revenue
                                                                                                            Target Monthly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="revenue_target_monthly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="revenue_target_monthly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">Revenue
                                                                                                            Target Quarterly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="revenue_target_quaterly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="revenue_target_quaterly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">Revenue
                                                                                                            Target Yearly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="revenue_target_yearly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="revenue_target_yearly">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-12"
                                                                                                id="target_div2"
                                                                                                style="display:none;">
                                                                                                <div class="row gy-2">
                                                                                                    <div
                                                                                                        class="col-12 mb-2">
                                                                                                        <strong>GP
                                                                                                            Targets</strong>
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">GP
                                                                                                            Target Weekly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="gp_target_weekly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="gp_target_weekly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">GP
                                                                                                            Target Monthly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="gp_target_monthly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="gp_target_monthly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">GP
                                                                                                            Target Quarterly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="gp_target_quaterly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="gp_target_quaterly">
                                                                                                    </div>
                                                                                                    <div class="col-lg-3">
                                                                                                        <label
                                                                                                            class="form-label mb-1">GP
                                                                                                            Target Yearly
                                                                                                            <span>*</span></label>
                                                                                                        <input
                                                                                                            class="form-control form-control-sm text-end"
                                                                                                            id="gp_target_yearly"
                                                                                                            type="number"
                                                                                                            step="any"
                                                                                                            name="gp_target_yearly">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div> --}}

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 6. Document Attachments --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingDocAttachments">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseDocAttachments"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseDocAttachments">
                                                                                        <span class="me-2">5</span>
                                                                                        Document Attachments
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseDocAttachments"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingDocAttachments">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-2">

                                                                                            <div class="col-lg-3">
                                                                                                <label
                                                                                                    class="form-label mb-1">Resume
                                                                                                    (Attachment)</label>
                                                                                                <input type="file"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="att_resume">
                                                                                            </div>

                                                                                            <div class="col-lg-3">
                                                                                                <label
                                                                                                    class="form-label mb-1">Offer
                                                                                                    Letter
                                                                                                    (Attachment)</label>
                                                                                                <input type="file"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="att_offer_letter">
                                                                                            </div>

                                                                                            <div class="col-lg-3">
                                                                                                <label
                                                                                                    class="form-label mb-1">Signed
                                                                                                    Contract
                                                                                                    (Attachment)</label>
                                                                                                <input type="file"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="att_signed_contract">
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>


                                                                    {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                                                    <div class="tab-pane fade" id="bank-details"
                                                                        role="tabpanel" aria-labelledby="bank-tab">

                                                                        <div class="d-flex justify-content-end mb-2">
                                                                            <button type="button" id="addBankBtn"
                                                                                class="btn btn-sm btn-success"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#bankModal">
                                                                                <i class="ico icon-outline-add-square"></i>
                                                                                Add Bank Account
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-hover table-bordered align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Bank Name</th>
                                                                                        <th>Branch</th>
                                                                                        <th>Account Holder</th>
                                                                                        <th>Account Number</th>
                                                                                        <th>IBAN Number</th>
                                                                                        <th>SWIFT Code</th>
                                                                                        <th>Currency</th>
                                                                                        <th>IBAN Letter</th>
                                                                                        <th>Actions</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="bankTableBody">
                                                                                    @php $banks = session('staff_banks', []); @endphp
                                                                                    @forelse($banks as $bank)
                                                                                        <tr>
                                                                                            <td>{{ $bank['bank_name'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['branch_name'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['account_holder'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['account_number'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['iban_number'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['swift_code'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bank['currency'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>
                                                                                                @if (!empty($bank['iban_letter']))
                                                                                                    <a href="{{ asset($bank['iban_letter']) }}"
                                                                                                        target="_blank"
                                                                                                        class="btn btn-sm btn-light">View</a>
                                                                                                @else
                                                                                                    -
                                                                                                @endif
                                                                                            </td>
                                                                                            <td>
                                                                                                <button type="button"
                                                                                                    class="btn btn-light btn-sm editBankBtn"
                                                                                                    data-id="{{ $bank['id'] }}">Edit</button>
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger btn-sm deleteBankBtn"
                                                                                                    data-id="{{ $bank['id'] }}">Delete</button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr class="no-bank-row">
                                                                                            <td colspan="9"
                                                                                                class="text-center text-muted">
                                                                                                No bank accounts added yet.
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                    {{-- education qualification --}}

                                                                    <div class="tab-pane fade"
                                                                        id="educational-qualification" role="tabpanel"
                                                                        aria-labelledby="edu-tab">

                                                                        <div class="d-flex justify-content-end mb-2">
                                                                            <button type="button" id="addEducationBtn"
                                                                                class="btn btn-sm btn-success"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#educationModal">
                                                                                <i class="ico icon-outline-add-square"></i>
                                                                                Add Education
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-hover table-bordered align-middle"
                                                                                style="table-layout: fixed;width:100%">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width: 150px;">
                                                                                            Qualification <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th>Board / University <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th style="width: 130px;">
                                                                                            Specialization</th>
                                                                                        <th style="width: 100px;">Year</th>
                                                                                        <th style="width: 100px;">Result
                                                                                        </th>
                                                                                        <th style="width: 80px;">GPA</th>
                                                                                        <th style="width: 100px;">Mode</th>
                                                                                        <th style="width: 120px;">Country
                                                                                        </th>
                                                                                        <th style="width: 90px;">Duration
                                                                                        </th>
                                                                                        <th style="width: 100px;">
                                                                                            Certificate</th>
                                                                                        <th style="width: 120px;">Action
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="educationTableBody">
                                                                                    @php $educations = session('staff_educations', []); @endphp
                                                                                    @forelse($educations as $edu)
                                                                                        <tr
                                                                                            id="eduRow_{{ $edu['id'] }}">
                                                                                            <td>{{ $edu['qualification'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['university'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['specialization'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['year'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['result'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['gpa'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['mode'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['country'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $edu['duration'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>
                                                                                                @if (isset($edu['certificate']) && $edu['certificate'])
                                                                                                    <a href="{{ asset('storage/' . $edu['certificate']) }}"
                                                                                                        target="_blank"
                                                                                                        class="btn btn-sm btn-light">View</a>
                                                                                                @else
                                                                                                    -
                                                                                                @endif
                                                                                            </td>
                                                                                            <td>
                                                                                                <div class="d-flex gap-1">
                                                                                                    <button type="button"
                                                                                                        class="btn btn-light btn-sm editEducationBtn"
                                                                                                        data-id="{{ $edu['id'] }}">Edit</button>
                                                                                                    <button type="button"
                                                                                                        class="btn btn-danger btn-sm deleteEducationBtn"
                                                                                                        data-id="{{ $edu['id'] }}">Delete</button>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr class="no-education-row">
                                                                                            <td colspan="11"
                                                                                                class="text-center text-muted">
                                                                                                No education records added
                                                                                                yet.</td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                    {{-- educational qualification end --}}

                                                                    {{-- professional experience --}}
                                                                    <div class="tab-pane fade"
                                                                        id="professional-experience" role="tabpanel"
                                                                        aria-labelledby="exp-tab">

                                                                        <div class="d-flex justify-content-end mb-2">
                                                                            <button type="button" id="addExperienceBtn"
                                                                                class="btn btn-sm btn-success"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#experienceModal">
                                                                                <i
                                                                                    class="ico icon-outline-add-square"></i>
                                                                                Add Experience
                                                                            </button>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table table-hover table-bordered align-middle"
                                                                                style="table-layout: fixed;width:100%">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Previous Organization <span
                                                                                                class="text-danger">*</span>
                                                                                        </th>
                                                                                        <th>Previous Designation</th>
                                                                                        <th style="width: 180px;">
                                                                                            Employment Duration (Y, M)</th>
                                                                                        <th>Key Responsibilities</th>
                                                                                        <th style="width: 120px;">
                                                                                            Certificate</th>
                                                                                        <th style="width: 120px;">Action
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="experienceTableBody">
                                                                                    @php $experiences = session('staff_experiences', []); @endphp
                                                                                    @forelse($experiences as $exp)
                                                                                        <tr
                                                                                            id="expRow_{{ $exp['id'] }}">
                                                                                            <td>{{ $exp['organization'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $exp['designation'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ ($exp['years'] ?? 0) . ' Y, ' . ($exp['months'] ?? 0) . ' M' }}
                                                                                            </td>
                                                                                            <td>{{ $exp['responsibilities'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>
                                                                                                @if (isset($exp['certificate']) && $exp['certificate'])
                                                                                                    <a href="{{ asset('storage/' . $exp['certificate']) }}"
                                                                                                        target="_blank"
                                                                                                        class="btn btn-sm btn-light">View</a>
                                                                                                @else
                                                                                                    -
                                                                                                @endif
                                                                                            </td>
                                                                                            <td>
                                                                                                <div class="d-flex gap-1">
                                                                                                    <button type="button"
                                                                                                        class="btn btn-light btn-sm editExperienceBtn"
                                                                                                        data-id="{{ $exp['id'] }}">Edit</button>
                                                                                                    <button type="button"
                                                                                                        class="btn btn-danger btn-sm deleteExperienceBtn"
                                                                                                        data-id="{{ $exp['id'] }}">Delete</button>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr class="no-experience-row">
                                                                                            <td colspan="6"
                                                                                                class="text-center text-muted">
                                                                                                No experience records added
                                                                                                yet.</td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    {{-- professional experience end --}}

                                                                    {{--
                                                                    ========================================================================
                                                                    ATTENDANCE / LEAVE CONFIGURATION TAB - COMMENTED OUT
                                                                    Not used in Add Staff form. Will be used elsewhere.
                                                                    Contains fields for: Attendance Policy, Working Hours, Grace Period,
                                                                    Shift Times, Weekly Off Days, Leave Policy, Annual/Sick/Casual Leave,
                                                                    Comp-Off, Carry Forward, Leave Encashment, etc.
                                                                    ========================================================================
                                                                    --}}


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
                                                                                        <th style="width:220px;">
                                                                                            Attachment
                                                                                        </th>
                                                                                        <th style="width:160px;">Expiry
                                                                                            Date</th>
                                                                                        <th>Remarks</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    {{-- Required ones you always want new uploads for --}}
                                                                                    <tr>
                                                                                        <td>Passport Copy with Address<span
                                                                                                class="text-danger">*</span>
                                                                                        </td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][passport_visa][file]">
                                                                                        </td>
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
                                                                                        <td>Visa
                                                                                        </td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][passport_visa][file]">
                                                                                        </td>
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
                                                                                        <td>Emirates ID
                                                                                        </td>
                                                                                        <td><input type="file"
                                                                                                class="form-control"
                                                                                                name="docs[joining][emirates_id][file]">
                                                                                        </td>
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
                                                                                        <td>Resume</td>
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
                                                                        <h6 class="mt-3">2. Employment Documents</h6>
                                                                        <div class="table-responsive mb-3">
                                                                            <table
                                                                                class="table table-bordered align-middle">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th style="width: 260px;">Document
                                                                                        </th>
                                                                                        <th style="width: 220px;">
                                                                                            Attachment
                                                                                        </th>
                                                                                        <th>Remarks</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php
                                                                                        $empDocs = [
                                                                                            [
                                                                                                'key' => 'appraisals',
                                                                                                'label' =>
                                                                                                    'Performance Appraisals',
                                                                                                'remarks' =>
                                                                                                    'Annual or probation evaluation forms',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'insurance',
                                                                                                'label' =>
                                                                                                    'Insurance Card',
                                                                                                'remarks' =>
                                                                                                    'Health insurance copy',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'training',
                                                                                                'label' =>
                                                                                                    'Training Certificates',
                                                                                                'remarks' =>
                                                                                                    'Internal/external training records',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'policies',
                                                                                                'label' =>
                                                                                                    'Policy Acknowledgements',
                                                                                                'remarks' =>
                                                                                                    'Signed HR policies, NDA, IT usage policy',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'assets',
                                                                                                'label' =>
                                                                                                    'Assets Assignment Form',
                                                                                                'remarks' =>
                                                                                                    'Laptop, SIM, access card issued',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'change_terms',
                                                                                                'label' =>
                                                                                                    'Change in Employment Terms',
                                                                                                'remarks' =>
                                                                                                    'Salary revision/promotion letters',
                                                                                            ],
                                                                                            [
                                                                                                'key' => 'warnings',
                                                                                                'label' =>
                                                                                                    'Warnings (If any)',
                                                                                                'remarks' =>
                                                                                                    'Written warning/disciplinary record',
                                                                                            ],
                                                                                        ];
                                                                                    @endphp
                                                                                    @foreach ($empDocs as $doc)
                                                                                        <tr>
                                                                                            <td>{{ $doc['label'] }}</td>
                                                                                            <td>
                                                                                                <input type="file"
                                                                                                    class="form-control"
                                                                                                    name="docs[employment][{{ $doc['key'] }}][file]">
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    name="docs[employment][{{ $doc['key'] }}][remarks]"
                                                                                                    placeholder="{{ $doc['remarks'] }}">
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach

                                                                                </tbody>
                                                                            </table>
                                                                        </div>



                                                                        {{-- 4. OTHERS (OPTIONAL) --}}
                                                                        <h6 class="mt-3">3. Others (Optional /
                                                                            Case-specific)
                                                                        </h6>
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
                                                                                        <th style="width: 260px;">Document
                                                                                        </th>
                                                                                        <th style="width: 220px;">
                                                                                            Attachment
                                                                                        </th>
                                                                                        <th>Remarks</th>
                                                                                        <th style="width: 60px;">Action
                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php
                                                                                        $otherPreset = [
                                                                                            [
                                                                                                'Driving License Copy',
                                                                                                'If company provides vehicle',
                                                                                            ],
                                                                                            [
                                                                                                'Trade License Copy (if under dependent visa)',
                                                                                                'For compliance',
                                                                                            ],
                                                                                            [
                                                                                                'Power of Attorney (if authorized signatory)',
                                                                                                'Case-based',
                                                                                            ],
                                                                                        ];
                                                                                    @endphp
                                                                                    @foreach ($otherPreset as $k => $row)
                                                                                        <tr>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="docs[others][{{ $k }}][name]"
                                                                                                    value="{{ $row[0] }}">
                                                                                            </td>
                                                                                            <td><input type="file"
                                                                                                    class="form-control"
                                                                                                    name="docs[others][{{ $k }}][file]">
                                                                                            </td>
                                                                                            <td><input type="text"
                                                                                                    class="form-control"
                                                                                                    name="docs[others][{{ $k }}][remarks]"
                                                                                                    value="{{ $row[1] }}">
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <button type="button"
                                                                                                    class="btn btn-light text-dark btn-sm delOtherRow">
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
                                                                    {{-- documents tab end --}}

                                                                    {{-- ======================= TAB: RESIGNATION DETAILS ======================= --}}

                                                                    <div class="tab-pane fade" id="resignation-details"
                                                                        role="tabpanel"
                                                                        aria-labelledby="resignation-tab">

                                                                        <div class="accordion"
                                                                            id="resignationDetailsAccordion">

                                                                            {{-- 1. Resignation Information --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingResignationInfo">
                                                                                    <button class="accordion-button"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseResignationInfo"
                                                                                        aria-expanded="true"
                                                                                        aria-controls="collapseResignationInfo">
                                                                                        <span class="me-2">1</span>
                                                                                        Resignation Information
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseResignationInfo"
                                                                                    class="accordion-collapse collapse show"
                                                                                    aria-labelledby="headingResignationInfo"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Resignation
                                                                                                    Type <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="resignation_type">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Type</option>
                                                                                                    <option
                                                                                                        value="voluntary"
                                                                                                        {{ getFieldValue('resignation_type') == 'voluntary' ? 'selected' : '' }}>
                                                                                                        Voluntary</option>
                                                                                                    <option
                                                                                                        value="involuntary"
                                                                                                        {{ getFieldValue('resignation_type') == 'involuntary' ? 'selected' : '' }}>
                                                                                                        Involuntary /
                                                                                                        Termination</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Reason
                                                                                                    for Resignation <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="resignation_reason">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Reason
                                                                                                    </option>
                                                                                                    <option
                                                                                                        value="personal"
                                                                                                        {{ getFieldValue('resignation_reason') == 'personal' ? 'selected' : '' }}>
                                                                                                        Personal</option>
                                                                                                    <option
                                                                                                        value="better_opportunity"
                                                                                                        {{ getFieldValue('resignation_reason') == 'better_opportunity' ? 'selected' : '' }}>
                                                                                                        Better Opportunity
                                                                                                    </option>
                                                                                                    <option value="health"
                                                                                                        {{ getFieldValue('resignation_reason') == 'health' ? 'selected' : '' }}>
                                                                                                        Health</option>
                                                                                                    <option
                                                                                                        value="relocation"
                                                                                                        {{ getFieldValue('resignation_reason') == 'relocation' ? 'selected' : '' }}>
                                                                                                        Relocation</option>
                                                                                                    <option value="other"
                                                                                                        {{ getFieldValue('resignation_reason') == 'other' ? 'selected' : '' }}>
                                                                                                        Other</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-8">
                                                                                                <label
                                                                                                    class="form-label mb-1">Remarks</label>
                                                                                                <textarea class="form-control form-control-sm" name="resignation_remarks" rows="3"
                                                                                                    placeholder="Additional details about resignation">{{ getFieldValue('resignation_remarks') }}</textarea>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 2. Important Dates --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingResignationDates">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseResignationDates"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseResignationDates">
                                                                                        <span class="me-2">2</span>
                                                                                        Important Dates
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseResignationDates"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingResignationDates"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Resignation
                                                                                                    Submitted Date <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker"
                                                                                                    name="resignation_submitted_date"
                                                                                                    value="{{ getFieldValue('resignation_submitted_date') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Notice
                                                                                                    Period (Days)</label>
                                                                                                <input type="number"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="notice_period_days"
                                                                                                    value="{{ getFieldValue('notice_period_days') }}"
                                                                                                    placeholder="30">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Last
                                                                                                    Working Day</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker"
                                                                                                    name="last_working_day"
                                                                                                    value="{{ getFieldValue('last_working_day') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Relieving
                                                                                                    Date</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm date-picker"
                                                                                                    name="relieving_date"
                                                                                                    value="{{ getFieldValue('relieving_date') }}">
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 3. Handover & Assets --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingHandoverAssets">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseHandoverAssets"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseHandoverAssets">
                                                                                        <span class="me-2">3</span>
                                                                                        Handover & Assets
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseHandoverAssets"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingHandoverAssets"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Knowledge
                                                                                                    Transfer
                                                                                                    Completed</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="knowledge_transfer_completed">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Status
                                                                                                    </option>
                                                                                                    <option value="yes"
                                                                                                        {{ getFieldValue('knowledge_transfer_completed') == 'yes' ? 'selected' : '' }}>
                                                                                                        Yes</option>
                                                                                                    <option value="no"
                                                                                                        {{ getFieldValue('knowledge_transfer_completed') == 'no' ? 'selected' : '' }}>
                                                                                                        No</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Assets
                                                                                                    Returned</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="assets_returned">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Status
                                                                                                    </option>
                                                                                                    <option value="yes"
                                                                                                        {{ getFieldValue('assets_returned') == 'yes' ? 'selected' : '' }}>
                                                                                                        Yes</option>
                                                                                                    <option value="no"
                                                                                                        {{ getFieldValue('assets_returned') == 'no' ? 'selected' : '' }}>
                                                                                                        No</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-4">
                                                                                                <label
                                                                                                    class="form-label mb-1">Handover
                                                                                                    To (Employee
                                                                                                    Name)</label>
                                                                                                <input type="text"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="handover_to"
                                                                                                    value="{{ getFieldValue('handover_to') }}"
                                                                                                    placeholder="Employee name receiving handover">
                                                                                            </div>

                                                                                            <div class="col-lg-4">
                                                                                                <label
                                                                                                    class="form-label mb-1">Handover
                                                                                                    Notes</label>
                                                                                                <textarea class="form-control form-control-sm" name="handover_notes" rows="3"
                                                                                                    placeholder="Details of knowledge transfer and handover process">{{ getFieldValue('handover_notes') }}</textarea>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 4. Exit Formalities --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingExitFormalities">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseExitFormalities"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseExitFormalities">
                                                                                        <span class="me-2">4</span> Exit
                                                                                        Formalities
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseExitFormalities"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingExitFormalities"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Exit
                                                                                                    Interview
                                                                                                    Conducted</label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="exit_interview_conducted">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Status
                                                                                                    </option>
                                                                                                    <option value="yes"
                                                                                                        {{ getFieldValue('exit_interview_conducted') == 'yes' ? 'selected' : '' }}>
                                                                                                        Yes</option>
                                                                                                    <option value="no"
                                                                                                        {{ getFieldValue('exit_interview_conducted') == 'no' ? 'selected' : '' }}>
                                                                                                        No</option>
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="col-lg-10">
                                                                                                <label
                                                                                                    class="form-label mb-1">Exit
                                                                                                    Interview
                                                                                                    Feedback</label>
                                                                                                <textarea class="form-control form-control-sm" name="exit_interview_feedback" rows="3"
                                                                                                    placeholder="Optional feedback from exit interview">{{ getFieldValue('exit_interview_feedback') }}</textarea>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 5. Full & Final Settlement --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingSettlement">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseSettlement"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseSettlement">
                                                                                        <span class="me-2">5</span> Full
                                                                                        & Final Settlement
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseSettlement"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingSettlement"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-3">
                                                                                                <label
                                                                                                    class="form-label mb-1">Settlement
                                                                                                    Amount</label>
                                                                                                <input type="number"
                                                                                                    step="0.01"
                                                                                                    class="form-control form-control-sm"
                                                                                                    name="settlement_amount"
                                                                                                    value="{{ getFieldValue('settlement_amount') }}"
                                                                                                    placeholder="0.00">
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 6. Attachments --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingResignationAttachments">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseResignationAttachments"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseResignationAttachments">
                                                                                        <span class="me-2">6</span>
                                                                                        Attachments
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseResignationAttachments"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingResignationAttachments"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="table-responsive">
                                                                                            <table
                                                                                                class="table table-bordered align-middle">
                                                                                                <thead
                                                                                                    class="table-light">
                                                                                                    <tr>
                                                                                                        <th
                                                                                                            style="width: 260px;">
                                                                                                            Document</th>
                                                                                                        <th
                                                                                                            style="width: 220px;">
                                                                                                            Attachment</th>
                                                                                                        <th>Remarks</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td>Resignation
                                                                                                            Letter / Email
                                                                                                            <span
                                                                                                                class="text-danger">*</span>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input
                                                                                                                type="file"
                                                                                                                class="form-control"
                                                                                                                name="resignation_letter"
                                                                                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input
                                                                                                                type="text"
                                                                                                                class="form-control"
                                                                                                                name="resignation_letter_remarks"
                                                                                                                placeholder="Original resignation submission">
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Other Supporting
                                                                                                            Documents</td>
                                                                                                        <td>
                                                                                                            <input
                                                                                                                type="file"
                                                                                                                class="form-control"
                                                                                                                name="other_resignation_docs"
                                                                                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                                                                multiple>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input
                                                                                                                type="text"
                                                                                                                class="form-control"
                                                                                                                name="other_resignation_docs_remarks"
                                                                                                                placeholder="Additional supporting documents">
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            {{-- 7. Status & Audit --}}
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="headingResignationStatus">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#collapseResignationStatus"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="collapseResignationStatus">
                                                                                        <span class="me-2">7</span>
                                                                                        Status & Audit
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="collapseResignationStatus"
                                                                                    class="accordion-collapse collapse"
                                                                                    aria-labelledby="headingResignationStatus"
                                                                                    data-bs-parent="#resignationDetailsAccordion">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row gy-3">

                                                                                            <div class="col-lg-2">
                                                                                                <label
                                                                                                    class="form-label mb-1">Resignation
                                                                                                    Status <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <select
                                                                                                    class="form-select form-select-sm"
                                                                                                    name="resignation_status">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        Select Status
                                                                                                    </option>
                                                                                                    <option value="draft"
                                                                                                        {{ getFieldValue('resignation_status') == 'draft' ? 'selected' : '' }}>
                                                                                                        Draft</option>
                                                                                                    <option
                                                                                                        value="submitted"
                                                                                                        {{ getFieldValue('resignation_status') == 'submitted' ? 'selected' : '' }}>
                                                                                                        Submitted</option>
                                                                                                    <option
                                                                                                        value="approved"
                                                                                                        {{ getFieldValue('resignation_status') == 'approved' ? 'selected' : '' }}>
                                                                                                        Approved</option>
                                                                                                    <option
                                                                                                        value="withdrawn"
                                                                                                        {{ getFieldValue('resignation_status') == 'withdrawn' ? 'selected' : '' }}>
                                                                                                        Withdrawn</option>
                                                                                                    <option
                                                                                                        value="completed"
                                                                                                        {{ getFieldValue('resignation_status') == 'completed' ? 'selected' : '' }}>
                                                                                                        Completed</option>
                                                                                                </select>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                    {{-- resignation details tab end --}}



        </form>



    </div> {{-- /.tab-content --}}
    </div> {{-- /.tab-wrap --}}


    </div>
    </div>
    {{-- ======================= / EMPLOYEE MASTER – TABS ======================= --}}

    {{-- Minimal JS: add/remove rows for Education, Experience, Other Docs --}}



    </div>

    <script>
        // Check role and show/hide sales target fields
        function checkRole() {
            var selectedRole = $('#role_id option:selected').text().trim().toLowerCase();

            if (selectedRole.includes('sales')) {
                $('#sales_target_div').show();
                $('#brands_div').show();
                $('#salesTargetsAccordion').show();
                // If "Set Sales Target" is already set to "Yes", show the target divs
                if ($('#is_target').val() == '1') {
                    $('#target_div1, #target_div2').show();
                }
            } else {
                $('#sales_target_div').hide();
                $('#brands_div').hide();
                $('#salesTargetsAccordion').hide();
                $('#target_div1, #target_div2').hide();
                // Reset the "Set Sales Target" dropdown to "No" when hiding
                $('#is_target').val('0');
            }
        }

        // Toggle target input fields based on target type selection
        function toggleTargetInputs() {
            var targetType = $('#target_type').val();

            // Hide all target input fields first
            $('#revenue_target_input').hide();
            $('#gp_target_input').hide();

            // Show appropriate input fields based on selection
            if (targetType === 'revenue') {
                $('#revenue_target_input').show();
            } else if (targetType === 'gp') {
                $('#gp_target_input').show();
            } else if (targetType === 'both') {
                $('#revenue_target_input').show();
                $('#gp_target_input').show();
            }
        }

        // Handle Set Sales Target change
        function fn_role_id() {
            var setSalesTarget = $('#is_target').val();

            if (setSalesTarget === '1') {
                // Show Target From Date, Type, and Target Period fields
                $('#target_from_date_div').show();
                $('#target_type_div').show();
                $('#target_period_div').show();
                $('#target_div1, #target_div2').show();
            } else {
                // Hide Target From Date, Type, and Target Period fields
                $('#target_from_date_div').hide();
                $('#target_type_div').hide();
                $('#target_period_div').hide();
                $('#target_div1, #target_div2').hide();

                // Reset selections and hide target inputs
                $('#target_type').val('');
                $('select[name="target_period"]').val('');
                $('#revenue_target_input').hide();
                $('#gp_target_input').hide();
            }
        }

        $(document).ready(function() {
            // Auto-calculate Gross Salary
            $('.salary-component').on('input', function() {
                var basic = parseFloat($('#salary_basic').val()) || 0;
                var hra = parseFloat($('#salary_allowances').val()) || 0;
                var otherAllowances = parseFloat($('#salary_other_allowances').val()) || 0;
                var transport = parseFloat($('#transport_allowance').val()) || 0;
                var otherBenefits = parseFloat($('#other_benefits').val()) || 0;

                var gross = basic + hra + otherAllowances + transport + otherBenefits;
                $('#salary_gross').val(gross.toFixed(2));
            });

            // Format salary fields on blur to show .00
            $('.salary-component').on('blur', function() {
                var val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    $(this).val(val.toFixed(2));
                }
            });

            // Format target amount inputs with real-time .00 formatting
            $('.target-amount-input').on('input', function() {
                var input = $(this);
                var val = input.val().replace(/[^\d.]/g,
                    ''); // Remove non-numeric characters except decimal

                if (val !== '') {
                    // Split by decimal to handle formatting
                    var parts = val.split('.');
                    var integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g,
                        ','); // Add commas for thousands

                    if (parts.length > 1) {
                        // If decimal exists, limit to 2 decimal places
                        var decimalPart = parts[1].substring(0, 2);
                        input.val(integerPart + '.' + decimalPart);
                    } else {
                        // No decimal, add .00
                        input.val(integerPart + '.00');
                    }
                } else if (val === '') {
                    input.val('');
                }
            });

            // Format target amount inputs on blur to ensure .00 format
            $('.target-amount-input').on('blur', function() {
                var input = $(this);
                var val = input.val().replace(/,/g, ''); // Remove commas for parsing
                var numVal = parseFloat(val);

                if (!isNaN(numVal)) {
                    var formatted = numVal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    input.val(formatted);
                } else if (val === '' || val === '0' || val === '0.') {
                    input.val('0.00');
                }
            });

            // Handle focus to remove commas for easier editing
            $('.target-amount-input').on('focus', function() {
                var input = $(this);
                var val = input.val().replace(/,/g, '');
                if (val !== '0.00') {
                    input.val(val);
                }
            });

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
            // $('#attendance-leave-configuration [name]').attr({
            //     'form': 'staffAllForm'
            // });

            // ---------- Helpers ----------
            const ALL_TABS = [
                '#data-details', '#job-details', '#bank-details',
                '#educational-qualification', '#professional-experience',
                // '#attendance-leave-configuration', // Not used in Add Staff
                '#documentation'
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


            function markInvalid($f, message) {
                $f.addClass('is-invalid');
                // Also mark the Select2's visible selection
                if ($f.hasClass('select2-hidden-accessible') || ($f.is(':hidden') && $f.next('.select2').length)) {
                    $f.next('.select2').find('.select2-selection').addClass('is-invalid');
                }
                ensureHolder($f).text(message);
            }

            function clearErrors($root) {
                $root.find('.is-invalid').removeClass('is-invalid');
                $root.find('.invalid-feedback').remove();
                $root.find('.select2 .select2-selection').removeClass('is-invalid');
                if (typeof clearTabBadges === 'function') clearTabBadges();
            }

            function ensureHolder($f) {
                let $target = $f;

                // Select2 (hidden original + rendered .select2 next)
                if ($f.hasClass('select2-hidden-accessible') || ($f.is(':hidden') && $f.next('.select2').length)) {
                    $target = $f.next('.select2');
                }
                // Input groups
                else if ($f.closest('.input-group').length) {
                    $target = $f.closest('.input-group');
                }
                // Radios / checkboxes
                else if ($f.closest('.form-check').length) {
                    $target = $f.closest('.form-check');
                }

                let $holder = $target.next('.invalid-feedback');
                if (!$holder.length) {
                    $holder = $(
                        '<div class="invalid-feedback" style="display:block;font-size:12px;color:#dc3545;"></div>'
                    );
                    $holder.insertAfter($target);
                }
                return $holder;
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

                    // A) dot notation -> bracket notation (education.0.year etc.)
                    if (!$f.length && name.includes('.')) {
                        const bracket = dotToBracket(name); // reporting_manager[0]
                        $f = $root.find('[name="' + bracket + '"]');

                        // If still not found, map to root array control (reporting_manager[])
                        if (!$f.length) {
                            const base = name.split('.')[0]; // reporting_manager
                            $f = $root.find('[name="' + base + '[]"]');
                        }
                    }

                    // B) Server key without [] but DOM has [] (reporting_manager)
                    if (!$f.length && !name.includes('.')) {
                        $f = $root.find('[name="' + name + '[]"]');
                    }

                    if (!$f.length) return;

                    markInvalid($f, errs[name][0]);
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
                // Bank data is already saved in session via the modal.
                // This function just confirms the session data exists.
                return new Promise(function(resolve, reject) {
                    // Check if there are banks in the session
                    if (banks && banks.length > 0) {
                        resolve({
                            ok: true,
                            message: 'Bank data already in session'
                        });
                    } else {
                        // No banks added - this is fine, not all staff need bank accounts during creation
                        resolve({
                            ok: true,
                            message: 'No bank accounts to save'
                        });
                    }
                });
            }

            function saveEducation() {
                // Education data is already saved in session via the modal.
                // This function just confirms the session data exists.
                return new Promise(function(resolve, reject) {
                    // educations variable is populated from session at page load
                    // No need to submit - data is already in session from modal
                    resolve({
                        ok: true,
                        message: 'Education data already in session'
                    });
                });
            }

            function saveExperience() {
                // Experience data is already saved in session via the modal.
                // This function just confirms the session data exists.
                return new Promise(function(resolve, reject) {
                    // experiences variable is populated from session at page load
                    // No need to submit - data is already in session from modal
                    resolve({
                        ok: true,
                        message: 'Experience data already in session'
                    });
                });
            }

            // COMMENTED: Not used in Add Staff, will be used elsewhere
            // function saveAttendanceLeave() {
            //     const $form = getForm();
            //     clearErrors($form);
            //     const fd = buildFD();
            //     return new Promise(function(resolve, reject) {
            //         $.ajax({
            //             url: "{{ route('staff.attendance_leave.store') }}", // ⬅️ new route
            //             method: 'POST',
            //             data: fd,
            //             processData: false,
            //             contentType: false,
            //             headers: {
            //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //             },
            //             success: resp => resp && resp.ok ? resolve(resp) : reject({
            //                 generic: 'Could not save Attendance & Leave config.'
            //             }),
            //             error: xhr => {
            //                 if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
            //                     $form, xhr.responseJSON.errors);
            //                 reject(xhr);
            //             }
            //         });
            //     });
            // }



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

            // Tab Error Highlighting Helpers
            function highlightTabError(tabId) {
                const tab = document.querySelector(`#${tabId}`);
                console.log('Highlighting tab:', tabId, 'Element found:', !!tab);
                if (tab) {
                    tab.classList.add('tab-error');
                    console.log('Added tab-error class to:', tabId);
                }
            }

            function clearTabError(tabId) {
                const tab = document.querySelector(`#${tabId}`);
                if (tab) {
                    tab.classList.remove('tab-error');
                }
            }

            function clearAllTabErrors() {
                document.querySelectorAll('.nav-link.tab-error').forEach(tab => {
                    tab.classList.remove('tab-error');
                });
            }

            let savingAll = false;
            $('#btnSaveAll').off('click.saveAll').on('click.saveAll', async function() {
                if (savingAll) return;
                savingAll = true;

                const $btn = $(this);
                if ($btn.data('busy')) {
                    savingAll = false;
                    return;
                }
                $btn.data('busy', true);

                // cache original HTML once
                if (!$btn.data('origHtml')) $btn.data('origHtml', $btn.html());
                // temporary saving UI
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $('#saveAllMsg').text('');

                try {
                    // Step 1: Save basic info first (server validates all form fields)
                    let staffId = await saveBasic();

                    // Step 2: Check if Job Information accordion is filled (required fields)
                    const dateOfJoining = $('[name="date_of_joining_2"]').val();
                    const departmentId = $('[name="department_id"]').val();
                    const designationId = $('[name="designation_id"]').val();
                    const employmentType = $('[name="employment_type"]').val();

                    if (!dateOfJoining || !departmentId || !designationId || !employmentType) {
                        // Jump to Job Details tab and expand Job Information accordion
                        const jobTab = document.querySelector('#job-tab');
                        if (jobTab) {
                            jobTab.click();
                        }

                        // Ensure Job Information accordion is expanded
                        const jobInfoCollapse = document.querySelector('#collapseJobInfo');
                        if (jobInfoCollapse && !jobInfoCollapse.classList.contains('show')) {
                            const jobInfoBtn = document.querySelector(
                                '[data-bs-target="#collapseJobInfo"]');
                            if (jobInfoBtn) {
                                jobInfoBtn.click();
                            }
                        }

                        // Scroll to and highlight missing fields
                        if (!dateOfJoining) {
                            $('[name="date_of_joining_2"]').addClass('is-invalid');
                        }
                        if (!departmentId) {
                            $('[name="department_id"]').addClass('is-invalid');
                        }
                        if (!designationId) {
                            $('[name="designation_id"]').addClass('is-invalid');
                        }
                        if (!employmentType) {
                            $('[name="employment_type"]').addClass('is-invalid');
                        }

                        throw new Error('Job Information is required');
                    }

                    // Clear any previous validation errors on Job Information fields
                    $('[name="date_of_joining_2"]').removeClass('is-invalid');
                    $('[name="department_id"]').removeClass('is-invalid');
                    $('[name="designation_id"]').removeClass('is-invalid');
                    $('[name="employment_type"]').removeClass('is-invalid');

                    // Step 3: After basic info is valid, check if required tabs have at least one record
                    let hasValidationErrors = false;
                    let firstErrorTab = null;

                    // Clear previous tab errors
                    clearAllTabErrors();

                    if (!banks || banks.length === 0) {
                        highlightTabError('bank-tab');
                        hasValidationErrors = true;
                        if (!firstErrorTab) firstErrorTab = 'bank-tab';
                    }

                    if (!educations || educations.length === 0) {
                        highlightTabError('edu-tab');
                        hasValidationErrors = true;
                        if (!firstErrorTab) firstErrorTab = 'edu-tab';
                    }

                    if (!experiences || experiences.length === 0) {
                        highlightTabError('exp-tab');
                        hasValidationErrors = true;
                        if (!firstErrorTab) firstErrorTab = 'exp-tab';
                    }

                    if (hasValidationErrors) {
                        // Jump to the first tab with error
                        if (firstErrorTab) {
                            const errorTab = document.querySelector(`#${firstErrorTab}`);
                            if (errorTab) {
                                errorTab.click();
                            }
                        }

                        $('#saveAllMsg').html(
                            '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Please complete all required tabs (highlighted in red)</span>'
                        );
                        throw new Error('Please complete all required tabs');
                    }

                    // All validations passed, clear any lingering errors
                    clearAllTabErrors();

                    // Step 3: Save remaining data
                    await saveJob();
                    await saveBank();
                    await saveEducation();
                    await saveExperience();
                    // await saveAttendanceLeave(); // Not used in Add Staff
                    await saveDocs();
                    $('#saveAllMsg').html('<span class="text-success">All saved ✓ (Staff ID: ' +
                        staffId + ')</span>');
                } catch (e) {
                    // Enhanced error logging
                    console.error('SaveAll failed:', e);
                    console.error('Error details:', {
                        message: e.message,
                        generic: e.generic,
                        status: e.status,
                        responseJSON: e.responseJSON,
                        responseText: e.responseText
                    });

                    // Show user-friendly error message
                    let errorMsg = 'Error saving. Please check the form.';

                    if (e.message) {
                        errorMsg = e.message;
                    } else if (e.generic) {
                        errorMsg = e.generic;
                    } else if (e.responseJSON && e.responseJSON.message) {
                        errorMsg = e.responseJSON.message;
                    } else if (e.responseText) {
                        try {
                            const parsed = JSON.parse(e.responseText);
                            errorMsg = parsed.message || errorMsg;
                        } catch (parseErr) {
                            // Keep default message
                        }
                    }

                    if (!$('#saveAllMsg').text()) {
                        $('#saveAllMsg').html('<span class="text-danger">' + errorMsg + '</span>');
                    }
                } finally {
                    // restore original icon + text
                    $btn.html($btn.data('origHtml')).prop('disabled', false).data('busy', false);
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

            // When main company changes, copy the selected company name into #work_location input
            $('#main_company').on('change', function() {
                const txt = $(this).find('option:selected').text() || '';
                $('#work_location').val(txt.trim());
            });

            // Initialize work_location based on current selection (if any)
            (function initWorkLocation() {
                const sel = $('#main_company option:selected').text() || '';
                if (sel.trim()) $('#work_location').val(sel.trim());
            })();

            // Load designations for a department (dependency dropdown)
            window.loadDesignationsForDepartment = function loadDesignationsForDepartment(deptId, selectedId = null) {
                const $des = $('#designation_id');
                if (!$des.length) return;

                $des.prop('disabled', true).html('<option>Loading...</option>');

                if (!deptId) {
                    $des.html('<option value="">-Select-</option>').prop('disabled', false).trigger('change');
                    return;
                }

                $.get("{{ url('get-designations-by-department') }}/" + deptId)
                    .done(function(resp) {
                        $des.empty().append($('<option>', { value: '', text: '-Select-' }));
                        if (resp && resp.success && resp.designations && resp.designations.length) {
                            resp.designations.forEach(function(d) {
                                $des.append($('<option>', { value: d.id, text: d.title }));
                            });
                            if (selectedId) $des.val(selectedId).trigger('change');
                        }
                    })
                    .fail(function() {
                        $des.html('<option value="">-Select-</option>');
                    })
                    .always(function() {
                        $des.prop('disabled', false);
                        if ($des.hasClass('js-example-basic-single')) $des.trigger('change.select2');
                    });
            };

            // When department in Job Info changes, reload designations
            $('#department_id').on('change', function() {
                // support a temporary preselect id stored via data attribute (used when creating a new designation)
                const preselect = $(this).data('select-designation-after-load') || null;
                // remove the temporary marker
                $(this).removeData('select-designation-after-load');
                loadDesignationsForDepartment($(this).val(), preselect);
            });

            // Initialize designation list if a department is preselected (e.g., edit mode)
            (function initDesignationList() {
                const initDept = $('#department_id').val();
                const preselect = @json(old('designation_id', $job->designation_id ?? ''));
                if (initDept) loadDesignationsForDepartment(initDept, preselect);
            })();

            // Accordion behavior: make each item independent (don't auto-close others)
            // and only toggle when its header button is clicked. Prevent clicks inside the body
            // or on inner header controls (like the add buttons) from toggling.
            (function setupJobAccordion() {
                const $acc = $('#jobDetailsAccordion');

                // If any stray data-bs-parent attributes exist, Bootstrap will still treat them exclusive;
                // we've removed them from the DOM above, but ensure collapse behavior is independent by
                // leaving them out.

                // Prevent clicks inside the accordion item content from toggling
                $acc.on('click', '.accordion-item', function(e) {
                    // If click happened inside the header button (or its children), allow it
                    if ($(e.target).closest('[data-bs-toggle="collapse"]').length) return;
                    // If click happened inside an inner interactive control (modal trigger, button, link), allow it
                    if ($(e.target).closest('button, a, [data-bs-toggle="modal"]').length) return;

                    // Otherwise stop propagation so collapse won't react
                    e.stopPropagation();
                });

                // Prevent clicks on header inner buttons (like small add buttons) from toggling
                $acc.on('click', '.accordion-header .btn, .accordion-header [data-bs-toggle="modal"]', function(
                    e) {
                    e.stopPropagation();
                });
            })();

            // Generic function to handle country to state dependency
            function setupCountryStateChange(countrySelector, stateSelector) {
                $(document).on("change", countrySelector, function() {
                    const id = $(this).val();
                    const $stateSelect = $(stateSelector);

                    if (!id) {
                        $stateSelect.html("<option value=''>-Select-</option>");
                        return;
                    }

                    $stateSelect.html("<option>Loading...</option>");

                    $.get("{{ url('/get_state_company') }}?country_id=" + id, function(res) {
                        $stateSelect.empty().append('<option value="">-Select-</option>');
                        let states = Array.isArray(res[0]) ? res[0] : res;
                        states.forEach(s => {
                            $stateSelect.append(
                                `<option value="${s.id}">${s.name}</option>`);
                        });
                    }).fail(function() {
                        $stateSelect.html("<option value=''>-Select-</option>");
                    });
                });
            }

            // Setup country-state dependencies for all address sections
            setupCountryStateChange("#perm_country", "#perm_state");
            setupCountryStateChange("#curr_country", "#curr_state");
        });
    </script>


    {{-- Department Add Modal --}}
    <div class="modal side-panel fade" id="departmentAddModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="departmentAddModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="departmentAddModalLabel">Add Department</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form id="departmentAddForm">
                    @csrf

                    <div class="modal-body pt-3">

                        <!-- Department Name -->
                        <label class="form-label">
                            Department Name <span class="text-danger">*</span>
                        </label>

                        <input type="text" class="form-control" id="department_name" name="title" required
                            autocomplete="off" style="padding: 2px 5px;">

                        <!-- Footer -->
                        <div class="modal-footer d-flex justify-content-center p-0 pt-3">

                            <button type="submit" id="saveDepartmentBtn"
                                class="btn btn-light add-btn d-flex align-items-center gap-2"
                                style="
                                color: var(--color-btn-light);
                                border: 1px solid var(--color-btn-light-border);
                                background-color: var(--color-btn-light-bg);
                                font-size: 12px;
                                padding: 3px 10px;
                                border-radius: 8px;
                                min-height: 25px;
                            "
                                data-busy-text="Saving...">

                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>

                                <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i>

                                <span class="btn-text">Submit</span>
                            </button>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal side-panel  fade" id="adddesignationModal2" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Designation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">

                    <label class="form-label">Department <span class="text-danger">*</span></label>

                    <select class="form-control js-example-basic-single" name="department_modal2"
                        id="department_modal2">

                        @php
                            $department_modal = @App\SmHumanDepartment::select('id', 'name')
                                ->where('active_status', 1)
                                ->orderby('name', 'asc')
                                ->get();

                        @endphp

                        @if (count($department_modal) > 0)
                            @foreach ($department_modal as $val)
                                <option value="{{ $val->id }}">{{ $val->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <style>
                        #saveDesignation2 {
                            color: var(--color-btn-light);
                            border: 1px solid var(--color-btn-light-border);
                            background-color: var(--color-btn-light-bg);
                        }

                        #saveDesignation2 {
                            display: flex;
                            align-items: center;
                            font-size: 12px;
                            padding: 3px 10px;
                            gap: 5px;
                            border-radius: 8px;
                            min-height: 25px;
                        }
                    </style>

                    <label class="form-label mt-3">Designation <span class="text-danger">*</span></label>
                    <input type="text" id="designation_title2" name="name" class="form-control"
                        required="" autocomplete="off" style="    padding: 2px 5px;">

                    <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                        <button type="button" id="saveDesignation2"
                            style="color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);"
                            class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal PO --}}

    <script>
        $(document).on('click', '#saveDesignation2', function() {

            let title = $('#designation_title2').val().trim();
            let input = $('#designation_title2');
            let department_id = $('#department_modal2').val();
            let department_text = $('#department_modal2 option:selected').text();



            input.removeClass('is-invalid');
            input.next('.invalid-feedback').text('');

            if (!title) {
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text('Designation term is required');
                return;
            }

            $.ajax({
                url: "{{ url('designation-store-ajax') }}", // adjust route
                type: "POST",
                data: {
                    title: title,
                    department_id: department_id,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loading_bg').show();

                },
                success: function(res) {

                    if (res.status) {

                        console.log('AJAX Response:', res);


                        // ✅ NEW ID AVAILABLE HERE
                        console.log('New ID:', res.data.id);



                        // Refresh designations for the department and select the newly created one
                        // Ensure main department select matches the new designation's department
                        $('#department_id').val(res.data.department_id);
                        if ($('#department_id').hasClass('js-example-basic-single')) $('#department_id').trigger('change.select2');

                        if (typeof window.loadDesignationsForDepartment === 'function') {
                            window.loadDesignationsForDepartment(res.data.department_id, res.data.id);
                        } else {
                            // Fallback: set temporary marker so department change handler can preselect
                            $('#department_id').data('select-designation-after-load', res.data.id).trigger('change');
                        }

                        // Close modal and clear input
                        $('#adddesignationModal2').modal('hide');
                        $('#designation_title2').val('');

                        toastr.success(res.message, 'Success');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(errors.title[0]);
                        }
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        });


        $(document).on('submit', '#departmentAddForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            // Disable button to prevent double submit
            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ url('department-store-ajax') }}",
                method: "POST",
                data: form.serialize(),
                dataType: "json",

                success: function(response) {
                    if (response.status === true) {

                        // Append department to dropdown
                        const option = new Option(
                            response.data.name,
                            response.data.id,
                            true,
                            true
                        );

                        const option2 = new Option(
                            response.data.name,
                            response.data.id,
                            true,
                            true
                        );

                        $('#department_id').append(option).trigger('change');
                        $('#department_modal2').append(option2).trigger('change');

                        // Reset form & close modal
                        form[0].reset();
                        $('#departmentAddModal').modal('hide');

                        toastr.success(response.message);
                    }
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },

                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).text('Save');
                }
            });
        });
    </script>

    {{-- Education Modal --}}
    <div class="modal fade" id="educationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="educationForm">
                    @csrf
                    <input type="hidden" name="education_id" id="education_id">

                    <div class="modal-body">
                        <div class="row gy-2">
                            <div class="col-md-6">
                                <label>Highest Qualification <span class="text-danger">*</span></label>
                                <select class="form-control" name="qualification" required>
                                    <option value="">-Select-</option>
                                    <option>High School</option>
                                    <option>Diploma</option>
                                    <option>Bachelor</option>
                                    <option>Master</option>
                                    <option>Certification</option>
                                    <option>PhD</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Board / University <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="university" required>
                            </div>

                            <div class="col-md-6">
                                <label>Specialization</label>
                                <input type="text" class="form-control" name="specialization">
                            </div>

                            <div class="col-md-6">
                                <label>Year of Completion</label>
                                <input type="text" class="form-control" name="year" placeholder="YYYY">
                            </div>

                            <div class="col-md-6">
                                <label>Result</label>
                                <input type="text" class="form-control" name="result"
                                    placeholder="Pass / Division">
                            </div>

                            <div class="col-md-6">
                                <label>GPA / CGPA</label>
                                <input type="number" step="any" class="form-control" name="gpa">
                            </div>

                            <div class="col-md-6">
                                <label>Mode of Study</label>
                                <select class="form-control" name="mode">
                                    <option value="">-Select-</option>
                                    <option>Full-Time</option>
                                    <option>Part-Time</option>
                                    <option>Distance</option>
                                    <option>Online</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Country of Study</label>
                                <input type="text" class="form-control" name="country">
                            </div>

                            <div class="col-md-6">
                                <label>Duration (Years)</label>
                                <input type="number" step="any" class="form-control" name="duration">
                            </div>

                            <div class="col-md-6">
                                <label>Certificate Upload <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="certificate">
                                <small class="text-muted" id="existingCertificate"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="educationSaveBtn"
                            class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                            data-busy-text="Saving...">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                            <span class="btn-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });

        let educations = @json(array_values(session('staff_educations', [])));

        function renderEducationTable() {
            let body = $("#educationTableBody");
            body.empty();

            if (!educations || educations.length === 0) {
                body.append(
                    '<tr class="no-education-row"><td colspan="11" class="text-center text-muted">No education records added yet.</td></tr>'
                );
                return;
            }

            educations.forEach(function(edu) {
                let certView = edu.certificate ?
                    `<a href="${BASE_URL}/storage/${edu.certificate}" target="_blank" class="btn btn-sm btn-light">View</a>` :
                    '-';

                let row = `
                    <tr id="eduRow_${edu.id}">
                        <td>${edu.qualification || '-'}</td>
                        <td>${edu.university || '-'}</td>
                        <td>${edu.specialization || '-'}</td>
                        <td>${edu.year || '-'}</td>
                        <td>${edu.result || '-'}</td>
                        <td>${edu.gpa || '-'}</td>
                        <td>${edu.mode || '-'}</td>
                        <td>${edu.country || '-'}</td>
                        <td>${edu.duration || '-'}</td>
                        <td>${certView}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-light btn-sm editEducationBtn" data-id="${edu.id}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm deleteEducationBtn" data-id="${edu.id}">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
                body.append(row);
            });
        }

        // Add/Edit Education
        $(document).on("click", "#educationSaveBtn", function(e) {
            e.preventDefault();

            let formData = new FormData(document.getElementById("educationForm"));

            $.ajax({
                url: "{{ route('staff.education.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp.success) {
                        educations = resp.educations || [];
                        renderEducationTable();

                        // Clear tab error if user has added at least one record
                        if (educations.length > 0) {
                            clearTabError('edu-tab');
                        }

                        $("#educationModal").modal("hide");
                        $("#educationForm")[0].reset();
                        $("#education_id").val("");
                        toastr.success(resp.message || 'Education saved to session');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error saving education');
                    console.error(xhr);
                }
            });
        });

        // Edit Education
        $(document).on("click", ".editEducationBtn", function() {
            let id = $(this).data("id");
            let edu = educations.find(e => e.id == id);

            if (edu) {
                $("#education_id").val(edu.id);
                $("[name='qualification']").val(edu.qualification);
                $("[name='university']").val(edu.university);
                $("[name='specialization']").val(edu.specialization);
                $("[name='year']").val(edu.year);
                $("[name='result']").val(edu.result);
                $("[name='gpa']").val(edu.gpa);
                $("[name='mode']").val(edu.mode);
                $("[name='country']").val(edu.country);
                $("[name='duration']").val(edu.duration);

                if (edu.certificate) {
                    $("#existingCertificate").text("Current: " + edu.certificate.split('/').pop());
                } else {
                    $("#existingCertificate").text("");
                }

                $("#educationModal").modal("show");
            }
        });

        // Delete Education
        $(document).on("click", ".deleteEducationBtn", function() {
            if (!confirm("Delete this education record?")) return;

            let id = $(this).data("id");

            $.post("{{ route('staff.education.delete') }}", {
                _token: "{{ csrf_token() }}",
                education_id: id
            }, function(resp) {
                if (resp.success) {
                    educations = resp.educations || [];
                    renderEducationTable();
                    toastr.success('Education deleted');
                }
            }).fail(function() {
                toastr.error('Error deleting education');
            });
        });

        // Reset modal on close
        $('#educationModal').on('hidden.bs.modal', function() {
            $("#educationForm")[0].reset();
            $("#education_id").val("");
            $("#existingCertificate").text("");
        });
    </script>

    {{-- Experience Modal --}}
    <div class="modal fade" id="experienceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Professional Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="experienceForm">
                    @csrf
                    <input type="hidden" name="experience_id" id="experience_id">

                    <div class="modal-body">
                        <div class="row gy-2">
                            <div class="col-md-6">
                                <label>Previous Organization <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="organization" required>
                            </div>

                            <div class="col-md-6">
                                <label>Previous Designation</label>
                                <input type="text" class="form-control" name="designation">
                            </div>

                            <div class="col-md-3">
                                <label>Years</label>
                                <input type="number" min="0" class="form-control" name="years"
                                    placeholder="Years">
                            </div>

                            <div class="col-md-3">
                                <label>Months</label>
                                <input type="number" min="0" max="11" class="form-control"
                                    name="months" placeholder="Months">
                            </div>

                            <div class="col-md-6">
                                <label>Key Responsibilities</label>
                                <textarea class="form-control" name="responsibilities" rows="3"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label>Experience Certificate (Attachment)</label>
                                <input type="file" class="form-control" name="certificate">
                                <small class="text-muted" id="existingExpCertificate"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="experienceSaveBtn"
                            class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                            data-busy-text="Saving...">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                            <span class="btn-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let experiences = @json(array_values(session('staff_experiences', [])));

        function renderExperienceTable() {
            let body = $("#experienceTableBody");
            body.empty();

            if (!experiences || experiences.length === 0) {
                body.append(
                    '<tr class="no-experience-row"><td colspan="6" class="text-center text-muted">No experience records added yet.</td></tr>'
                );
                return;
            }

            experiences.forEach(function(exp) {
                let certView = exp.certificate ?
                    `<a href="${BASE_URL}/storage/${exp.certificate}" target="_blank" class="btn btn-sm btn-light">View</a>` :
                    '-';

                let duration = (exp.years || 0) + ' Y, ' + (exp.months || 0) + ' M';

                let row = `
                    <tr id="expRow_${exp.id}">
                        <td>${exp.organization || '-'}</td>
                        <td>${exp.designation || '-'}</td>
                        <td>${duration}</td>
                        <td>${exp.responsibilities || '-'}</td>
                        <td>${certView}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-light btn-sm editExperienceBtn" data-id="${exp.id}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm deleteExperienceBtn" data-id="${exp.id}">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
                body.append(row);
            });
        }

        // Add/Edit Experience
        $(document).on("click", "#experienceSaveBtn", function(e) {
            e.preventDefault();

            let formData = new FormData(document.getElementById("experienceForm"));

            $.ajax({
                url: "{{ route('staff.experience.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp.success) {
                        experiences = resp.experiences || [];
                        renderExperienceTable();

                        // Clear tab error if user has added at least one record
                        if (experiences.length > 0) {
                            clearTabError('exp-tab');
                        }

                        $("#experienceModal").modal("hide");
                        $("#experienceForm")[0].reset();
                        $("#experience_id").val("");
                        toastr.success(resp.message || 'Experience saved to session');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error saving experience');
                    console.error(xhr);
                }
            });
        });

        // Edit Experience
        $(document).on("click", ".editExperienceBtn", function() {
            let id = $(this).data("id");
            let exp = experiences.find(e => e.id == id);

            if (exp) {
                $("#experience_id").val(exp.id);
                $("[name='organization']").val(exp.organization);
                $("[name='designation']").val(exp.designation);
                $("[name='years']").val(exp.years);
                $("[name='months']").val(exp.months);
                $("[name='responsibilities']").val(exp.responsibilities);

                if (exp.certificate) {
                    $("#existingExpCertificate").text("Current: " + exp.certificate.split('/').pop());
                } else {
                    $("#existingExpCertificate").text("");
                }

                $("#experienceModal").modal("show");
            }
        });

        // Delete Experience
        $(document).on("click", ".deleteExperienceBtn", function() {
            if (!confirm("Delete this experience record?")) return;

            let id = $(this).data("id");

            $.post("{{ route('staff.experience.delete') }}", {
                _token: "{{ csrf_token() }}",
                experience_id: id
            }, function(resp) {
                if (resp.success) {
                    experiences = resp.experiences || [];
                    renderExperienceTable();
                    toastr.success('Experience deleted');
                }
            }).fail(function() {
                toastr.error('Error deleting experience');
            });
        });

        // Reset modal on close
        $('#experienceModal').on('hidden.bs.modal', function() {
            $("#experienceForm")[0].reset();
            $("#experience_id").val("");
            $("#existingExpCertificate").text("");
        });
    </script>

    {{-- Bank Modal --}}
    <div class="modal fade" id="bankModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="bankForm">
                    @csrf
                    <input type="hidden" name="bank_id" id="bank_id">

                    <div class="modal-body">
                        <div class="row gy-2">
                            <div class="col-md-6">
                                <label>Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                            </div>

                            <div class="col-md-6">
                                <label>Branch Name</label>
                                <input type="text" class="form-control" id="branch_name" name="branch_name">
                            </div>

                            <div class="col-md-6">
                                <label>Account Holder Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="account_holder" name="account_holder"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label>Bank Account Number</label>
                                <input type="text" class="form-control" id="account_number"
                                    name="account_number">
                            </div>

                            <div class="col-md-6">
                                <label>IBAN Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="iban_number" name="iban_number"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label>SWIFT Code</label>
                                <input type="text" class="form-control" id="swift_code" name="swift_code">
                            </div>

                            <div class="col-md-6">
                                <label>Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency"
                                    placeholder="e.g., AED, USD">
                            </div>

                            <div class="col-md-6">
                                <label>IBAN Letter (Attachment)</label>
                                <input type="file" class="form-control" id="iban_letter" name="iban_letter">
                                <small class="text-muted" id="existingIbanLetter"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="bankSaveBtn"
                            class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                            data-busy-text="Saving...">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                            <span class="btn-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const BASE_URL = "{{ url('/') }}";
        let banks = @json(array_values(session('staff_banks', [])));

        function renderBankTable() {
            let body = $("#bankTableBody");
            body.empty();

            if (!banks || banks.length === 0) {
                body.append(
                    '<tr class="no-bank-row"><td colspan="9" class="text-center text-muted">No bank accounts added yet.</td></tr>'
                );
                return;
            }

            banks.forEach(function(bank) {
                let ibanLetterView = bank.iban_letter ?
                    `<a href="${BASE_URL}/${bank.iban_letter}" target="_blank" class="btn btn-sm btn-light">View</a>` :
                    '-';

                let row = `
                    <tr id="bankRow_${bank.id}">
                        <td>${bank.bank_name || '-'}</td>
                        <td>${bank.branch_name || '-'}</td>
                        <td>${bank.account_holder || '-'}</td>
                        <td>${bank.account_number || '-'}</td>
                        <td>${bank.iban_number || '-'}</td>
                        <td>${bank.swift_code || '-'}</td>
                        <td>${bank.currency || '-'}</td>
                        <td>${ibanLetterView}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-light btn-sm editBankBtn" data-id="${bank.id}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm deleteBankBtn" data-id="${bank.id}">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
                body.append(row);
            });
        }

        // Add/Edit Bank
        $(document).on("click", "#bankSaveBtn", function(e) {
            e.preventDefault();

            // Validate required fields
            let form = document.getElementById("bankForm");
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let formData = new FormData(form);

            $.ajax({
                url: "{{ route('staff.bank.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp.success) {
                        banks = resp.banks || [];
                        renderBankTable();

                        // Clear tab error if exists (user has now added at least one record)
                        if (banks.length > 0) {
                            clearTabError('bank-tab');
                        }

                        // Close modal using Bootstrap 5 API
                        let modalElement = document.getElementById('bankModal');
                        let modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }

                        // Reset form
                        $("#bankForm")[0].reset();
                        $("#bank_id").val("");
                        $("#existingIbanLetter").text("");

                        toastr.success(resp.message || 'Bank account saved to session');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = Object.values(errors).flat().join('<br>');
                        toastr.error(errorMsg);
                    } else {
                        toastr.error('Error saving bank account');
                    }
                    console.error(xhr);
                }
            });
        });

        // Edit Bank
        $(document).on("click", ".editBankBtn", function() {
            let id = $(this).data("id");
            let bank = banks.find(b => b.id == id);

            if (bank) {
                $("#bank_id").val(bank.id);
                $("#bank_name").val(bank.bank_name);
                $("#branch_name").val(bank.branch_name);
                $("#account_holder").val(bank.account_holder);
                $("#account_number").val(bank.account_number);
                $("#iban_number").val(bank.iban_number);
                $("#swift_code").val(bank.swift_code);
                $("#currency").val(bank.currency);

                if (bank.iban_letter) {
                    $("#existingIbanLetter").text("Current: " + bank.iban_letter.split('/').pop());
                }

                $("#bankModal").modal("show");
            }
        });

        // Delete Bank
        $(document).on("click", ".deleteBankBtn", function() {
            if (!confirm("Delete this bank account?")) return;

            let id = $(this).data("id");

            $.post("{{ route('staff.bank.delete') }}", {
                _token: "{{ csrf_token() }}",
                bank_id: id
            }, function(resp) {
                if (resp.success) {
                    banks = resp.banks || [];
                    renderBankTable();
                    toastr.success('Bank account deleted');
                }
            }).fail(function() {
                toastr.error('Error deleting bank account');
            });
        });

        // Reset modal on close
        $('#bankModal').on('hidden.bs.modal', function() {
            $("#bankForm")[0].reset();
            $("#bank_id").val("");
            $("#existingIbanLetter").text("");
        });

        // Render on page load
        $(document).ready(function() {
            renderBankTable();
        });

        // Auto-capitalize all text inputs and textareas (first letter of each word).
        // Use class 'no-capitalize' or attribute 'data-no-capitalize' to opt-out on specific fields.
        $(document).ready(function() {
            $(document).on('input', 'input[type="text"], textarea', function(e) {
                const $el = $(this);

                // Opt-out if explicitly requested
                if ($el.is('[data-no-capitalize], .no-capitalize')) return;

                // Skip inputs where capitalization is not appropriate
                const type = ($el.attr('type') || '').toLowerCase();
                if (['email', 'password', 'number', 'tel', 'search', 'url'].includes(type)) return;

                const el = this;
                // Preserve selection positions (capitalization doesn't change length)
                const start = el.selectionStart;
                const end = el.selectionEnd;

                const oldVal = el.value;
                const newVal = oldVal.replace(/\b\w/g, function(match) {
                    return match.toUpperCase();
                });
                if (newVal !== oldVal) {
                    el.value = newVal;
                    try {
                        el.setSelectionRange(start, end);
                    } catch (err) {
                        /* ignore */
                    }
                }
            });

            // Move focus to next input/select/textarea when user presses Enter.
            // For textarea, Shift+Enter inserts newline; use data-allow-newline to keep Enter for newline.
            $(document).on('keydown',
                'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select',
                function(e) {
                    if (e.key !== 'Enter') return;

                    const $el = $(this);

                    // Ignore Enter inside select2 search fields or datepickers
                    if ($el.closest('.select2-container').length) return;
                    if ($el.closest('.datepicker, .flatpickr-calendar').length) return; // common datepickers

                    // Opt-out if explicitly requested
                    if ($el.is('[data-no-enter-next], .no-enter-next')) return;

                    // If textarea and user wants newline with Shift+Enter or allowed by attribute, do not navigate
                    if ($el.is('textarea')) {
                        if (e.shiftKey || $el.is('[data-allow-newline]')) return;
                    }

                    // Prevent default Enter (including form submit)
                    e.preventDefault();

                    // Determine focusable controls within the form
                    const $form = $el.closest('form#staffAllForm');
                    const $focusables = $form.length ? $form.find(
                        'input:visible:enabled, select:visible:enabled, textarea:visible:enabled, button:visible:enabled'
                    ).not('[type="hidden"]').filter(':not(:disabled)') : $(
                        'input:visible:enabled, select:visible:enabled, textarea:visible:enabled, button:visible:enabled'
                    ).not('[type="hidden"]');

                    // Find current index and move to next
                    const idx = $focusables.index(this);
                    let next = null;
                    if (idx >= 0 && idx < $focusables.length - 1) {
                        next = $focusables.eq(idx + 1);
                    } else {
                        // wrap to first or do nothing
                        next = $focusables.eq(0);
                    }

                    if (next && next.length) {
                        next.focus();
                        // open select2 dropdown if applicable
                        if (next.hasClass('js-example-basic-single') || next.hasClass(
                                'select2-hidden-accessible')) {
                            try {
                                next.select2('open');
                            } catch (err) {}
                        }
                    }
                });
        });

        // Auto-calculate probation end date (6 months after joining date)
        $(document).ready(function() {
            $('input[name="date_of_joining_2"]').on('change', function() {
                let joiningDate = $(this).val();
                if (joiningDate) {
                    // Parse the date (assuming DD/MM/YYYY or MM/DD/YYYY format)
                    let dateParts = joiningDate.split(/[\/\-]/);
                    let parsedDate;

                    // Try to parse in different formats
                    if (dateParts.length === 3) {
                        // Assume DD/MM/YYYY format first
                        parsedDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                        // If invalid, try MM/DD/YYYY format
                        if (isNaN(parsedDate.getTime())) {
                            parsedDate = new Date(dateParts[2], dateParts[0] - 1, dateParts[1]);
                        }
                    }

                    if (!isNaN(parsedDate.getTime())) {
                        // Add 6 months to the joining date
                        let probationEndDate = new Date(parsedDate);
                        probationEndDate.setMonth(probationEndDate.getMonth() + 6);

                        // Format the date as DD/MM/YYYY
                        let day = String(probationEndDate.getDate()).padStart(2, '0');
                        let month = String(probationEndDate.getMonth() + 1).padStart(2, '0');
                        let year = probationEndDate.getFullYear();

                        let formattedDate = day + '/' + month + '/' + year;

                        // Set the probation end date field
                        $('input[name="probation_end_date"]').val(formattedDate);
                    }
                }
            });
        });
    </script>
@endsection

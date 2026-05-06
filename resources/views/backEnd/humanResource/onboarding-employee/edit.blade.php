@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        // for compatibility with older staff-edit partials that expect $staffRow
        $staffRow = $employee ?? null;
    @endphp


    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <form id="staffAllFormEdit" action="{{ url('onboarding-employee-update/' . $employee->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <!-- DATA DETAILS -->
                <div role="tabpanel" aria-labelledby="data-tab" id="data-details" class="tab-pane show active">
                    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
                        <h4 class="purchase-order-content-header-left">Edit ({{ $employee->document_number }})</h4>
                        <div class="purchase-order-content-header-right">

                            {{-- <form action="{{ route('onboard-employee.approve', ['id' => $employee->id]) }}" method="POST"> --}}
                            {{-- @csrf --}}
                            @if ($employee->approved_by)
                                <a class="btn btn-light text-dark"
                                    href="{{ url('staff-directory/' . $employee->staff_rec_id) }}">
                                    <i class="ico icon-outline-user
 text-success"></i> {{ $employee->staff_no }}
                                </a>
                            @else
                                <button type="button" title="Click to copy link"
                                    class="btn btn-light text-dark approve-btn"
                                    data-url="{{ route('onboard-employee.approve', ['id' => $employee->id]) }}">
                                    <i class="ico icon-outline-check-circle text-success"></i> Approve
                                </button>
                            @endif


                            <!-- Approval confirmation modal -->
                            <div class="modal fade" id="approvalConfirmModal" data-bs-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="approvalConfirmModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document" style="    top: 37%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approvalConfirmModalLabel">Confirm Approval</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            Are you sure you want to approve this onboarding?
                                        </div>
                                        <div class="modal-footer p-1 ">
                                        
                                            <button type="button" id="confirmApproveBtn" class="btn btn-light">Approve</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Show confirmation modal when approve is clicked; only proceed when user confirms
                                $(document).on('click', '.approve-btn', function() {
                                    let url = $(this).data('url');
                                    // store the url on the modal confirm button
                                    $('#confirmApproveBtn').data('url', url);
                                    var modalEl = document.getElementById('approvalConfirmModal');
                                    var modal = new bootstrap.Modal(modalEl);
                                    modal.show();
                                });

                                // When user confirms, perform the approval AJAX
                                $(document).on('click', '#confirmApproveBtn', function() {
                                    let $btn = $(this);
                                    let url = $btn.data('url');
                                    $btn.prop('disabled', true).text('Approving...');

                                    $.ajax({
                                        url: url,
                                        type: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                        },
                                        success: function(res) {
                                            $btn.prop('disabled', false).text('Approve');
                                            var modalEl = document.getElementById('approvalConfirmModal');
                                            var modal = bootstrap.Modal.getInstance(modalEl);
                                            if (modal) modal.hide();

                                            if (res.ok) {
                                                toastr.success(res.message || 'Onboarding approved successfully.');
                                                if (res.redirect) {
                                                    window.location.href = res.redirect;
                                                }
                                            } else {
                                                alert(res.message || 'Failed to approve onboarding.');
                                            }
                                        },
                                        error: function(xhr) {
                                            $btn.prop('disabled', false).text('Approve');
                                            toastr.error('An error occurred while approving onboarding.');
                                        }
                                    });
                                });
                            </script>

                            {{-- </form> --}}

                            <button type="submit" class="btn btn-light" id="btnSaveOnboard"><i
                                    class="ico icon-outline-bookmark-opened text-warning"></i> Update</button>

                            <script>
                                // Copy onboard link to clipboard when icon/heading clicked
                                $(document).on('click', '.copy-onboard-url', function(e) {
                                    var url = $(this).data('copy-url');
                                    if (!url) return;

                                    function showSuccess() {
                                        if (typeof toastr !== 'undefined') {
                                            toastr.success('Link copied to clipboard');
                                        } else {
                                            alert('Link copied to clipboard: ' + url);
                                        }
                                    }

                                    function fallbackCopy(text) {
                                        var $temp = $('<textarea>');
                                        $('body').append($temp);
                                        $temp.val(text).select();
                                        try {
                                            document.execCommand('copy');
                                            showSuccess();
                                        } catch (err) {
                                            alert('Could not copy text');
                                        } finally {
                                            $temp.remove();
                                        }
                                    }

                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                        navigator.clipboard.writeText(url).then(function() {
                                            showSuccess();
                                        }).catch(function() {
                                            fallbackCopy(url);
                                        });
                                    } else {
                                        fallbackCopy(url);
                                    }
                                });
                            </script>


                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu"
                                    style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(-124px, 30px);"
                                    data-popper-placement="bottom-end">
                                    <li><a href="{{ url('onboarding-employee-list') }}"
                                            class="dropdown-item d-flex align-items-center text-dark "><i
                                                class="ico icon-outline-document-text text-success  title-15 me-2"></i>
                                            Onboarding Employee List</a>
                                    </li>

                                    <li><a data-copy-url="{{ url('onboard-employee/' . session('logged_session_data.company_id')) }}"
                                            title="Click to copy link"
                                            class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i
                                                class="ico icon-outline-user-plus text-success  title-15 me-2"></i> Onboard
                                            Employee
                                            Link</a>
                                    </li>
                                    <li><a href="{{ url('staff-directory') }}"
                                            class="dropdown-item d-flex align-items-center text-dark "><i
                                                class="ico icon-outline-document-text text-success  title-15 me-2"></i>
                                            Staff List</a>
                                    </li>


                                </ul>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-3">
                        <strong style="margin-top: 5px;
    margin-left: 11px;">Personal Details</strong>

                        <div class="card-body">
                            <div class="row gy-3">

                                <input type="hidden" name="staff_id" value="{{ $employee->id }}">


                                <div class="col-lg-2">
                                    <label class="form-label">Salutation<span>*</span></label>
                                    <select class="form-select form-select-sm js-example-basic-single" name="salutation">
                                        <option value="">Select</option>
                                        <option value="Mr."
                                            {{ old('salutation', $employee->employee_salutation ?? '') == 'Mr.' ? 'selected' : '' }}>
                                            Mr.</option>
                                        <option value="Mrs."
                                            {{ old('salutation', $employee->employee_salutation ?? '') == 'Mrs.' ? 'selected' : '' }}>
                                            Mrs.</option>
                                        <option value="Miss."
                                            {{ old('salutation', $employee->employee_salutation ?? '') == 'Miss.' ? 'selected' : '' }}>
                                            Miss.</option>
                                    </select>
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">First Name<span>*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="first_name"
                                        value="{{ old('first_name', $employee->first_name ?? '') }}" required>
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Last Name</label>
                                    <input class="form-control form-control-sm" type="text" name="last_name"
                                        value="{{ old('last_name', $employee->last_name ?? '') }}">
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Date of Birth<span>*</span></label>
                                    <input type="text" class="form-control flatpickr-input date-picker"
                                        name="date_of_birth"
                                        value="{{ old('date_of_birth', isset($staffRow->date_of_birth) ? \Carbon\Carbon::parse($staffRow->date_of_birth)->format('d/m/Y') : '') }}">
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Place of Birth<span>*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="place_of_birth"
                                        value="{{ old('place_of_birth', $staffRow->place_of_birth ?? '') }}">
                                </div>

                                <div class="col-lg-2" style="margin-top:5px">
                                    <label class="form-label mb-1 d-flex justify-content-between align-items-center">
                                        <span>@lang('Religion')
                                        </span>
                                        <button type="button" class="btn btn-sm p-0 ms-2"
                                            style="border:none;background:none;" data-bs-toggle="modal"
                                            data-bs-target="#religionAddModal">
                                            <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                        </button>
                                    </label>
                                    @php
                                        $religions = @App\SmBaseSetup::where('base_group_id', 2)->get();

                                    @endphp
                                    <select class="form-select form-select-sm js-example-basic-single" id="religion"
                                        name="religion">
                                        <option value="">Select</option>
                                        @foreach ($religions as $religion)
                                            <option @if ($staffRow && $staffRow->religion == $religion->base_setup_name) selected @endif
                                                value="{{ $religion->base_setup_name }}">
                                                {{ $religion->base_setup_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Gender<span>*</span></label>
                                    <select class="form-select form-select-sm js-example-basic-single" name="gender_id">
                                        <option value="">Select</option>
                                        <option value="1"
                                            {{ old('gender_id', $staffRow->gender_id ?? '') == 1 ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="2"
                                            {{ old('gender_id', $staffRow->gender_id ?? '') == 2 ? 'selected' : '' }}>
                                            Female</option>
                                        <option value="3"
                                            {{ old('gender_id', $staffRow->gender_id ?? '') == 3 ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Mobile<span>*</span></label>
                                    <input class="form-control form-control-sm" type="tel" name="mobile"
                                        placeholder="+" value="{{ old('mobile', $staffRow->mobile ?? '+') }}">
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Email<span>*</span></label>
                                    <input class="form-control form-control-sm" type="email" name="email" required
                                        value="{{ old('email', $staffRow->email ?? '') }}">
                                </div>

                                <div class="col-lg-2">
                                    <label class="form-label">Marital Status</label>
                                    <select class="form-select form-select-sm js-example-basic-single" id="marital_status"
                                        name="marital_status">
                                        <option value="">Select</option>
                                        <option value="single"
                                            {{ old('marital_status', $staffRow->marital_status ?? '') == 'single' ? 'selected' : '' }}>
                                            Single</option>
                                        <option value="married"
                                            {{ old('marital_status', $staffRow->marital_status ?? '') == 'married' ? 'selected' : '' }}>
                                            Married</option>
                                        <option value="divorced"
                                            {{ old('marital_status', $staffRow->marital_status ?? '') == 'divorced' ? 'selected' : '' }}>
                                            Divorced</option>
                                        <option value="widowed"
                                            {{ old('marital_status', $staffRow->marital_status ?? '') == 'widowed' ? 'selected' : '' }}>
                                            Widowed</option>
                                    </select>
                                </div>

                                <div class="col-1">
                                    <label class="form-label">User Photo </label>
                                    <input type="file" class="form-control form-control-sm" name="staff_photo" id="staff_photo_input"
                                        accept="image/*">

                                    @php
                                        // compute a fallback and current photo URL to always render a preview element
                                        $photoUrl = asset('public/uploads/staff/demo/staff.png');
                                        if (!empty($staffRow->staff_photo)) {
                                            if (strpos($staffRow->staff_photo, 'public/') === 0) {
                                                $photoUrl = asset($staffRow->staff_photo);
                                            } elseif (Storage::disk('public')->exists($staffRow->staff_photo)) {
                                                $photoUrl = asset('storage/app/public/' . $staffRow->staff_photo);
                                            } elseif (file_exists(public_path($staffRow->staff_photo))) {
                                                $photoUrl = asset($staffRow->staff_photo);
                                            }
                                        }
                                    @endphp

                                    <div class="mt-1">
                                        <img id="staff_photo_preview" src="{{ $photoUrl }}" data-original="{{ $photoUrl }}" alt="Staff Photo" style="height:60px; width:auto; border-radius:4px; display:inline-block;">
                                    </div>
                                </div>

                                <div class="col-1">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" class="form-control form-control-sm" name="blood_group"
                                        value="{{ old('blood_group', $staffRow->blood_group ?? '') }}">
                                </div>


                                <div class="col-lg-2 position-relative">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control form-control-sm" name="password"
                                        id="password" autocomplete="new-password">
                                    {{-- Floating password validator (hidden by default) --}}
                                    <div id="password-validator" class="password-validator d-none" role="status"
                                        aria-live="polite">
                                        <div class="pv-arrow"></div>
                                        <div class="pv-body">
                                            <div class="pv-title">Choose a strong password</div>
                                            <div class="pv-progress" aria-hidden="true">
                                                <div class="pv-progress-bar" style="width:0%"></div>
                                            </div>
                                            <ul class="pv-list">
                                                <li data-criteria="length" class="pv-item invalid"><span
                                                        class="pv-dot"></span><span class="pv-text">Minimum
                                                        <strong>8</strong> characters</span></li>
                                                <li data-criteria="lower" class="pv-item invalid"><span
                                                        class="pv-dot"></span><span class="pv-text">At least <strong>1
                                                            lowercase</strong> letter</span></li>
                                                <li data-criteria="upper" class="pv-item invalid"><span
                                                        class="pv-dot"></span><span class="pv-text">At least <strong>1
                                                            uppercase</strong> letter</span></li>
                                                <li data-criteria="digit" class="pv-item invalid"><span
                                                        class="pv-dot"></span><span class="pv-text">At least <strong>1
                                                            digit</strong></span></li>
                                                <li data-criteria="special" class="pv-item invalid"><span
                                                        class="pv-dot"></span><span class="pv-text">At least <strong>1
                                                            special</strong> character</span></li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cols-6 mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3"><strong>Father Details</strong></div>

                                <div class="col">
                                    <label class="form-label"> First Name</label>
                                    <input class="form-control form-control-sm" type="text" name="father_first_name"
                                        value="{{ old('father_first_name', $staffRow->fathers_first_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Last Name</label>
                                    <input class="form-control form-control-sm" type="text" name="father_last_name"
                                        value="{{ old('father_last_name', $staffRow->fathers_last_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Mobile</label>
                                    <input class="form-control form-control-sm" type="tel" name="father_mobile"
                                        value="{{ old('father_mobile', $staffRow->father_mobile ?? '+') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Email</label>
                                    <input class="form-control form-control-sm" type="email" name="father_email"
                                        value="{{ old('father_email', $staffRow->father_email ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span> Doc (Govt. ID)</span>

                                        @php
                                            $fatherDoc = optional($staffRow->documents)->firstWhere(
                                                'key',
                                                'father_attachment',
                                            );
                                        @endphp

                                        @if ($fatherDoc && $fatherDoc->path)
                                            <a href="{{ asset('storage/app/public/' . $fatherDoc->path) }}"
                                                target="_blank" class="text-success">
                                                View
                                            </a>
                                        @endif
                                    </label>

                                    <input class="form-control form-control-sm" accept=".pdf,image/*" type="file"
                                        name="father_attachment">
                                </div>




                            </div>

                            <div class="row row-cols-6 mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3"><strong>Mother Details</strong></div>

                                <div class="col">
                                    <label class="form-label"> First Name</label>
                                    <input class="form-control form-control-sm" type="text" name="mother_first_name"
                                        value="{{ old('mother_first_name', $staffRow->mothers_first_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Last Name</label>
                                    <input class="form-control form-control-sm" type="text" name="mother_last_name"
                                        value="{{ old('mother_last_name', $staffRow->mothers_last_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Mobile</label>
                                    <input class="form-control form-control-sm" type="tel" name="mother_mobile"
                                        value="{{ old('mother_mobile', $staffRow->mother_mobile ?? '+') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Email</label>
                                    <input class="form-control form-control-sm" type="email" name="mother_email"
                                        value="{{ old('mother_email', $staffRow->mother_email ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span> Doc (Govt. ID)</span>

                                        @php
                                            $motherDoc = optional($staffRow->documents)->firstWhere(
                                                'key',
                                                'mother_attachment',
                                            );
                                        @endphp

                                        @if ($motherDoc && $motherDoc->path)
                                            <a href="{{ asset('storage/app/public/' . $motherDoc->path) }}"
                                                target="_blank" class="text-success">
                                                View
                                            </a>
                                        @endif
                                    </label>

                                    <input class="form-control form-control-sm" accept=".pdf,image/*" type="file"
                                        name="mother_attachment">
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

                            <div class="row row-cols-6 mt-4 spouse-section">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3"><strong>Spouse Details</strong></div>

                                <div class="col">
                                    <label class="form-label"> First Name</label>
                                    <input class="form-control form-control-sm" type="text" name="spouse_first_name"
                                        value="{{ old('spouse_first_name', $staffRow->spouse_first_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Last Name</label>
                                    <input class="form-control form-control-sm" type="text" name="spouse_last_name"
                                        value="{{ old('spouse_last_name', $staffRow->spouse_last_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Mobile</label>
                                    <input class="form-control form-control-sm" type="tel" name="spouse_mobile"
                                        value="{{ old('spouse_mobile', $staffRow->spouse_mobile ?? '+') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Email</label>
                                    <input class="form-control form-control-sm" type="email" name="spouse_email"
                                        value="{{ old('spouse_email', $staffRow->spouse_email ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label"> Doc (Govt. ID)</label>
                                    <input class="form-control form-control-sm" accept=".pdf,image/*" type="file"
                                        name="spouse_attachment">
                                </div>



                            </div>

                            <div class="row row-cols-6 mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3"><strong>Emergency Contact 1 </strong></div>

                                <div class="col-lg-2">
                                    <label class="form-label">Salutation<span>*</span></label>
                                    <select class="form-select form-select-sm js-example-basic-single"
                                        name="emergency1_salutation">
                                        <option value="">Select</option>
                                        <option value="Mr."
                                            {{ old('emergency1_salutation', $staffRow->em1_salutation ?? '') == 'Mr.' ? 'selected' : '' }}>
                                            Mr.</option>
                                        <option value="Mrs."
                                            {{ old('emergency1_salutation', $staffRow->em1_salutation ?? '') == 'Mrs.' ? 'selected' : '' }}>
                                            Mrs.</option>
                                        <option value="Miss."
                                            {{ old('emergency1_salutation', $staffRow->em1_salutation ?? '') == 'Miss.' ? 'selected' : '' }}>
                                            Miss.</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control form-control-sm" type="text" name="emergency1_name"
                                        value="{{ old('emergency1_name', $staffRow->emergency_contact_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Mobile</label>
                                    <input class="form-control form-control-sm" type="tel" name="emergency1_mobile"
                                        value="{{ old('emergency1_mobile', $staffRow->emergency_mobile ?? '+') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Email</label>
                                    <input class="form-control form-control-sm" type="email" name="emergency1_email"
                                        value="{{ old('emergency1_email', $staffRow->emergency_email ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Relationship</label>
                                    <input class="form-control form-control-sm" type="text"
                                        name="emergency1_relationship"
                                        value="{{ old('emergency1_relationship', $staffRow->emergency_contact_relationship ?? '') }}">
                                </div>



                            </div>

                            <div class="row row-cols-6 mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3"><strong>Emergency Contact 2 </strong></div>

                                <div class="col-lg-2">
                                    <label class="form-label">Salutation<span>*</span></label>
                                    <select class="form-select form-select-sm js-example-basic-single"
                                        name="emergency2_salutation">
                                        <option value="">Select</option>
                                        <option value="Mr."
                                            {{ old('emergency2_salutation', $staffRow->em2_salutation ?? '') == 'Mr.' ? 'selected' : '' }}>
                                            Mr.</option>
                                        <option value="Mrs,."
                                            {{ old('emergency2_salutation', $staffRow->em2_salutation ?? '') == 'Mrs.' ? 'selected' : '' }}>
                                            Mrs.</option>
                                        <option value="Miss."
                                            {{ old('emergency2_salutation', $staffRow->em2_salutation ?? '') == 'Miss.' ? 'selected' : '' }}>
                                            Miss.</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control form-control-sm" type="text" name="emergency2_name"
                                        value="{{ old('emergency2_name', $staffRow->emergency2_contact_name ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Mobile</label>
                                    <input class="form-control form-control-sm" type="tel" name="emergency2_mobile"
                                        value="{{ old('emergency2_mobile', $staffRow->emergency2_mobile ?? '+') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Email</label>
                                    <input class="form-control form-control-sm" type="email" name="emergency2_email"
                                        value="{{ old('emergency2_email', $staffRow->emergency2_email ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Relationship</label>
                                    <input class="form-control form-control-sm" type="text"
                                        name="emergency2_relationship"
                                        value="{{ old('emergency2_relationship', $staffRow->emergency2_contact_relationship ?? '') }}">
                                </div>



                            </div>

                            <div class="row  mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3" style="padding-right: 44px"><strong>Permanent
                                        Address</strong></div>

                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">Country</label>
                                        <select class="form-control js-example-basic-single" name="perm_country"
                                            id="perm_country">
                                            <option value="">-Select-</option>

                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('perm_country', $staffRow->permanent_country ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>


                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">State</label>
                                        <div id="perm_state_div">
                                            <select class="form-control js-example-basic-single" name="perm_state"
                                                id="perm_state">
                                                <option value="">-Select-</option>

                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('perm_state', $staffRow->permanent_state ?? '') == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}</option>
                                                @endforeach


                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label"> City </label>
                                    <input class="form-control form-control-sm" type="text" name="perm_city"
                                        value="{{ old('perm_city', $staffRow->permanent_city ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Area</label>
                                    <input class="form-control form-control-sm" type="text" name="perm_area"
                                        value="{{ old('perm_area', $staffRow->permanent_area ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Building Name</label>
                                    <input class="form-control form-control-sm" type="text" name="perm_building_name"
                                        value="{{ old('perm_building_name', $staffRow->permanent_building_no ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Flat/Office No</label>
                                    <input class="form-control form-control-sm" type="text" name="perm_flat_office_no"
                                        value="{{ old('perm_flat_office_no', $staffRow->permanent_flat_no ?? '') }}"
                                        value="">
                                </div>



                            </div>



                            <div class="row  mt-4">

                                <!-- Parent / Spouse details -->
                                <div class="col mt-3" style="padding-right: 44px"><strong>Current
                                        Address</strong></div>

                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">Country</label>
                                        <select class="form-control js-example-basic-single" name="curr_country"
                                            id="curr_country">
                                            <option value="">-Select-</option>

                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('curr_country', $staffRow->current_country ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>


                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">State</label>
                                        <div id="sectionCurrStateDiv">
                                            <select class="form-control js-example-basic-single" name="curr_state"
                                                id="curr_state">
                                                <option value="">-Select-</option>

                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('curr_state', $staffRow->current_state ?? '') == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label"> City </label>
                                    <input class="form-control form-control-sm" type="text" name="curr_city"
                                        value="{{ old('curr_city', $staffRow->current_city ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Area</label>
                                    <input class="form-control form-control-sm" type="text" name="curr_area"
                                        value="{{ old('curr_area', $staffRow->current_area ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Building Name</label>
                                    <input class="form-control form-control-sm" type="text" name="curr_building_name"
                                        value="{{ old('curr_building_name', $staffRow->current_building_no ?? '') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Flat/Office No</label>
                                    <input class="form-control form-control-sm" type="text" name="curr_flat_office_no"
                                        value="{{ old('curr_flat_office_no', $staffRow->current_flat_no ?? '') }}">
                                </div>



                            </div>
                            <br><br>


                            <div class="row mt-4">
                                <div class="col-12">



                                    <div class="tab-wrap mb-3">
                                        <ul class="nav nav-tabs" id="hrTabs" role="tablist">


                                                             <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="job-tab" data-bs-toggle="tab"
                                                    data-bs-target="#job-details" type="button" role="tab"
                                                    aria-controls="job-details" aria-selected="false">
                                                    Job Details
                                                </button>
                                            </li>



                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="bank-tab" data-bs-toggle="tab"
                                                    data-bs-target="#bank-details" type="button" role="tab"
                                                    aria-controls="bank-details" aria-selected="false">
                                                    Bank Details
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="edu-tab" data-bs-toggle="tab"
                                                    data-bs-target="#educational-qualification" type="button"
                                                    role="tab" aria-controls="educational-qualification"
                                                    aria-selected="false">
                                                    Educational Qualification
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="exp-tab" data-bs-toggle="tab"
                                                    data-bs-target="#professional-experience" type="button"
                                                    role="tab" aria-controls="professional-experience"
                                                    aria-selected="false">
                                                    Professional Experience
                                                </button>
                                            </li>



                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="docs-tab" data-bs-toggle="tab"
                                                    data-bs-target="#documentation" type="button" role="tab"
                                                    aria-controls="documentation" aria-selected="false">
                                                    Documentation
                                                </button>
                                            </li>
                                        </ul>

                                       

                                        <div class="tab-content border  bg-white" id="hrTabsContent">


                                            @php
            $departments = @App\SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = @App\SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
            $company = @App\SysCompany::select('id', 'company_name')->get();
            $roles = @App\Role::where('active_status', '=', '1')->orderBy('name', 'asc')->get();
            $brand_list = @App\SysBrand::select('id', 'title')->where('active_status', 1)->orderby('title')->get();
                                                
                                            @endphp


                                              <div class="tab-pane fade show active" id="job-details" role="tabpanel"
                                                aria-labelledby="job-tab">
                                                <div class="accordion accordion-flush" id="jobDetailsAccordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingJobInfo">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseJobInfo" aria-expanded="true"
                                                                aria-controls="collapseJobInfo">
                                                                <span class="me-2">1</span> Job
                                                                Information
                                                            </button>
                                                        </h2>
                                                        <div id="collapseJobInfo" class="accordion-collapse collapse show"
                                                            aria-labelledby="headingJobInfo">
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1 row-cols-7">

                                                                    <div class="col">
                                                                        <label class="form-label mb-1">Date
                                                                            of
                                                                            Joining
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="date_of_joining_2"
                                                                            value="{{ old('date_of_joining_2', $job->date_of_joining ?? '') }}">
                                                                    </div>

                                                                    <div class="col">
                                                                        <label class="form-label mb-1">Probation
                                                                            End Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="probation_end_date"
                                                                            value="{{ old('probation_end_date', $job->probation_end_date ?? '') }}"
                                                                            placeholder="Optional">
                                                                    </div>

                                                                    <div class="col" style="    margin-top: 0px;">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                                                <span>@lang('Department')
                                                                                    <span
                                                                                        class="text-danger">*</span></span>
                                                                                <button type="button"
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
                                                                                name="department_id" id="department_id"
                                                                                >
                                                                                <option value="">
                                                                                </option>
                                                                                @foreach ($departments as $key => $value)
                                                                                    <option value="{{ @$value->id }}"
                                                                                        {{ (old('department_id') ?? ($job->department_id ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                        {{ @$value->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col" style="    margin-top: 0px;">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                                                <span>@lang('Designation')
                                                                                    <span
                                                                                        class="text-danger">*</span></span>
                                                                                <button type="button"
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
                                                                                name="designation_id" id="designation_id"
                                                                                >
                                                                                <option value="">
                                                                                </option>
                                                                                @foreach ($designations as $key => $value)
                                                                                    <option value="{{ @$value->id }}"
                                                                                        {{ (old('designation_id') ?? ($job->designation_id ?? '')) == @$value->id ? 'selected' : '' }}>
                                                                                        {{ @$value->title }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col">
                                                                        <label class="form-label mb-1">Grade</label>
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="grade" id="designation_grade"
                                                                            disabled>
                                                                            <option value="">Select Grade</option>
                                                                            <option value="g1"
                                                                                @if (@$job->grade == 'g1') selected @endif>
                                                                                Grade 1</option>
                                                                            <option value="g2"
                                                                                @if (@$job->grade == 'g2') selected @endif>
                                                                                Grade 2</option>
                                                                            <option value="g3"
                                                                                @if (@$job->grade == 'g3') selected @endif>
                                                                                Grade 3</option>
                                                                            <option value="g4"
                                                                                @if (@$job->grade == 'g4') selected @endif>
                                                                                Grade 4</option>
                                                                            <option value="g5"
                                                                                @if (@$job->grade == 'g5') selected @endif>
                                                                                Grade 5</option>
                                                                            <option value="g6"
                                                                                @if (@$job->grade == 'g6') selected @endif>
                                                                                Grade 6</option>
                                                                        </select>

                                                                        <script>
                                                                            $(document).ready(function() {
                                                                                // Preserve server-provided grade on initial load if present
                                                                                var initialDesignationGrade = @json(old('grade', $job->grade ?? ''));
                                                                                var initialReportingManager = @json(old('reporting_manager', $job->reporting_manager ?? ''));
                                                                                var initialReportingManagerText = $('#reporting_manager option:selected').text() || '';
                                                                                // Fetch grade when designation changes
                                                                                $('#designation_grade').prop('disabled', true);

                                                                                $('#designation_id').on('change', function() {
                                                                                    var id = $(this).val();

                                                                                    // Reset grade
                                                                                    $('#designation_grade')
                                                                                        .val('')
                                                                                        .prop('disabled', true)
                                                                                        .trigger('change');

                                                                                    if (!id) return;

                                                                                    $.get("{{ url('designation') }}/" + id + "/grade")
                                                                                        .done(function(res) {
                                                                                            if (res && res.status) {
                                                                                                var gradeToSet = initialDesignationGrade ? initialDesignationGrade : (res.grade || '');
                                                                                                // Use initialDesignationGrade only once (on first load)
                                                                                                initialDesignationGrade = '';
                                                                                                $('#designation_grade')
                                                                                                    .prop('disabled', false)
                                                                                                    .val(gradeToSet)
                                                                                                    .trigger('change');
                                                                                            }
                                                                                        })
                                                                                        .fail(function() {
                                                                                            $('#designation_grade')
                                                                                                .val('')
                                                                                                .prop('disabled', true)
                                                                                                .trigger('change');
                                                                                        });
                                                                                });

                                                                                $('#designation_grade').on('change', function() {

                                                                                    var grade = $(this).val();
                                                                                    var departmentId = $('#department_id').val();
                                                                                    if (!grade) {
                                                                                        $('#reporting_manager').html('');
                                                                                        return;
                                                                                    }
                                                                                    $.get("{{ url('hrms/reporting-managers') }}", {
                                                                                        grade: grade,
                                                                                        department_id: departmentId
                                                                                    })
                                                                                        .done(function(res) {
                                                                                            if (res && res.status == 'success') {
                                                                                                var options = '';
                                                                                                res.data.forEach(function(s) {
                                                                                                    options += '<option value="' + s.id + '">' + (s.full_name || s.user_id) + '</option>';
                                                                                                });
                                                                                                $('#reporting_manager').html(options);

                                                                                                // Preserve preselected reporting manager if present (apply once)
                                                                                                if (initialReportingManager) {
                                                                                                    var txt = initialReportingManagerText || 'Unknown';
                                                                                                    if ($('#reporting_manager option[value="' + initialReportingManager + '"]').length === 0) {
                                                                                                        $('#reporting_manager').append('<option value="' + initialReportingManager + '">' + txt + '</option>');
                                                                                                    }
                                                                                                    $('#reporting_manager').val(initialReportingManager);
                                                                                                    initialReportingManager = '';
                                                                                                }

                                                                                                if ($('#reporting_manager').hasClass('js-example-basic-single')) {
                                                                                                    $('#reporting_manager').trigger('change.select2');
                                                                                                } else {
                                                                                                    $('#reporting_manager').trigger('change');
                                                                                                }
                                                                                            } else {
                                                                                                $('#reporting_manager').html('');
                                                                                            }
                                                                                        })
                                                                                        .fail(function() {
                                                                                            $('#reporting_manager').html('');
                                                                                        });
                                                                                });

                                                                                // if department changes and grade is selected, refresh managers
                                                                                $('#department_id').on('change', function() {
                                                                                    if ($('#designation_grade').val()) {
                                                                                        $('#designation_grade').trigger('change');
                                                                                    }
                                                                                });
                                                                            });
                                                                        </script>


                                                                    </div>



                                                                    <div class="col-lg-2">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('Reporting Manager')</label>


                                                                            <select
                                                                                class="form-select form-select-sm js-example-basic-single"
                                                                                name="reporting_manager"
                                                                                id="reporting_manager">

                                                                                @if (isset($job) && $job->reporting_manager)
                                                                                    @php
                                                                                        // Lookup the reporting manager directly to avoid collection/closure misuse
                                                                                        $selectedStaff = App\SmStaff::find($job->reporting_manager);
                                                                                        
                                                                                    @endphp

                                                                                    <option
                                                                                        value="{{ $job->reporting_manager }}" selected>
                                                                                        {{ $selectedStaff ? $selectedStaff->full_name : 'Unknown' }}
                                                                                    </option>
                                                                                @endif

                                                                               

                                                                            </select>


                                                                        </div>
                                                                    </div>

                                                                    <div class="col">
                                                                        <label class="form-label mb-1">Employment
                                                                            Type
                                                                            <span class="text-danger">*</span></label>
                                                                        @php $empTypeVal = old('employment_type') ?? ($job->employment_type ?? ''); @endphp
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="employment_type">
                                                                            <option value="">
                                                                                -Select-
                                                                            </option>
                                                                            <option value="full_time"
                                                                                {{ $empTypeVal == 'full_time' ? 'selected' : '' }}>
                                                                                Full-Time</option>
                                                                            <option value="part_time"
                                                                                {{ $empTypeVal == 'part_time' ? 'selected' : '' }}>
                                                                                Part-Time</option>
                                                                            <option value="contract"
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
                                                        <h2 class="accordion-header" id="headingCompanyInfo">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseCompanyInfo"
                                                                aria-expanded="false" aria-controls="collapseCompanyInfo">
                                                                <span class="me-2">2</span>
                                                                Company Information
                                                            </button>
                                                        </h2>
                                                        <div id="collapseCompanyInfo" class="accordion-collapse collapse"
                                                            aria-labelledby="headingCompanyInfo">
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1">




                                                                    @php
                                                                        $visaCompanyVal = old(
                                                                            'visa_company_name',
                                                                            $job->visa_company_name ??
                                                                                ($staffData->company_id ??
                                                                                    session(
                                                                                        'logged_session_data.company_id',
                                                                                    )),
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
                                                                                name="visa_company_name" id="company_id">
                                                                                <option value="">
                                                                                    Select
                                                                                </option>
                                                                                @foreach ($company as $key => $value)
                                                                                    <option value="{{ @$value->id }}"
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
                                                                                    session(
                                                                                        'logged_session_data.company_id',
                                                                                    )),
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
                                                                                id="main_company">
                                                                                <option value="">
                                                                                </option>
                                                                                @foreach ($company as $key => $value)
                                                                                    <option value="{{ @$value->id }}"
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
                                                                            isset($job->company_access)
                                                                                ? (is_array($job->company_access)
                                                                                    ? $job->company_access
                                                                                    : explode(
                                                                                        ',',
                                                                                        $job->company_access,
                                                                                    ))
                                                                                : (session(
                                                                                    'logged_session_data.company_id',
                                                                                )
                                                                                    ? [
                                                                                        session(
                                                                                            'logged_session_data.company_id',
                                                                                        ),
                                                                                    ]
                                                                                    : []),
                                                                        );

                                                                        // Ensure array & type-safe comparison
                                                                        $companyAccessVal = array_map(
                                                                            'intval',
                                                                            (array) $companyAccessVal,
                                                                        );
                                                                    @endphp

                                                                    <div class="col-lg-3">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('Company')
                                                                                (Access)
                                                                                <span class="text-danger">*</span></label>
                                                                            <select
                                                                                class="form-select form-select-sm js-example-basic-single"
                                                                                name="company_access[]"
                                                                                id="company_access" multiple>

                                                                                @foreach ($company as $value)
                                                                                    <option value="{{ $value->id }}"
                                                                                        {{ in_array((int) $value->id, $companyAccessVal, true) ? 'selected' : '' }}>
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
                                                                                class="form-select  js-example-basic-single"
                                                                                name="role_id" id="role_id"
                                                                                onchange="checkRole()">
                                                                                <option value="">
                                                                                    -- Select
                                                                                    Role
                                                                                    --</option>
                                                                                @foreach ($roles as $key => $value)
                                                                                    <option value="{{ $value->id }}"
                                                                                        {{ (old('role_id') ?? ($job->role_id ?? '')) == $value->id ? 'selected' : '' }}>
                                                                                        {{ $value->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    {{-- <div class="col-lg-1">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Password
                                                                            <span>*</span></label>
                                                                        <input class="form-control form-control-sm"
                                                                            type="text" name="password"
                                                                            autocomplete="new-password" >
                                                                    </div>
                                                                </div> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    {{-- 3. Work Details --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingWorkDetails">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseWorkDetails"
                                                                aria-expanded="false" aria-controls="collapseWorkDetails">
                                                                <span class="me-2">3</span> Work
                                                                Details
                                                            </button>
                                                        </h2>
                                                        <div id="collapseWorkDetails" class="accordion-collapse collapse"
                                                            aria-labelledby="headingWorkDetails">
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1">




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
                                                                        <label class="form-label mb-1">Work
                                                                            Location /
                                                                            Branch</label>
                                                                        <input type="text" id="work_location"
                                                                            class="form-control form-control-sm"
                                                                            name="work_location"
                                                                            value="{{ old('work_location', $job->work_location ?? '') }}">
                                                                    </div>

                                                                    {{-- <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Work
                                                                            Hours /
                                                                            Shift</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="work_hours"
                                                                            value="{{ old('work_hours', $job->work_hours ?? '') }}">
                                                                    </div> --}}

                                                                    <div class="col-lg-2">

                                                                        <label
                                                                            class="form-label mb-0 d-flex justify-content-between align-items-center">
                                                                            <span>Working Shift</span>
                                                                            <button type="button"
                                                                                class="btn btn-sm p-0 ms-2"
                                                                                style="border:none;background:none;"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#addShiftModal">
                                                                                <i class="ico icon-outline-add-square text-success"
                                                                                    style="font-size:18px;"></i>
                                                                            </button>
                                                                        </label>
                                                                             @php
                                                                                $working_shifts = @App\WorkingShift::where(
                                                                                'company_id',
                                                                                $workingCompanyVal
                                                                            )->get();
                                                                        @endphp
                                                                        <select
                                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                                            name="shift_id" id="shift_id">
                                                                          

                                                                            @foreach ($working_shifts as $shift)
                                                                                <option value="{{ $shift->id }}"
                                                                                    @if (@$job->shift_id == $shift->id) selected @endif>
                                                                                    {{ $shift->shift_name }}
                                                                                    ({{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}
                                                                                    -
                                                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }})
                                                                                </option>
                                                                            @endforeach


                                                                        </select>
                                                                    </div>


                                                                    @php
                                                                        $selectedWeeklyOffs = old(
                                                                            'hr_weekly_off',
                                                                            isset($job->week_off)
                                                                                ? (is_array($job->week_off)
                                                                                    ? $job->week_off
                                                                                    : explode(',', $job->week_off))
                                                                                : [],
                                                                        );

                                                                        if (!is_array($selectedWeeklyOffs)) {
                                                                            $selectedWeeklyOffs = [$selectedWeeklyOffs];
                                                                        }
                                                                    
                                                                    @endphp


    <div class="col-lg-2">
                                                                        <label class="form-label mb-0 d-flex justify-content-between align-items-center mb-1 hr-payroll-labels">Weekly Off
                                                                            <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;" data-bs-toggle="modal" data-bs-target="#addWeeklyOffModal">
                                                                                <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                                                            </button>
                                                                        </label>
                                                                            @php
                                                                                $weekly_off_days = @App\WeeklyOff::where(
                                                                                    'company_id',
                                                                                    $workingCompanyVal
                                                                                )->get();
                                                                            @endphp
                                                                        <select name="hr_weekly_off[]" id="weeklyoff_select" multiple
                                                                            class="form-select form-select-sm setting-input js-example-basic-single small-dropdown-font">

                                                                            @forelse ($weekly_off_days as $weekoff)
                                                                                <option value="{{ $weekoff->id }}"
                                                                                    {{ in_array((string) $weekoff->id, $selectedWeeklyOffs) ? 'selected' : '' }}>
                                                                                    {{ $weekoff->day_name }}
                                                                                </option>
                                                                            @empty
                                                                            @endforelse

                                                                        </select>

                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Ext
                                                                            No</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="ext_no_2"
                                                                            value="{{ old('ext_no_2', $job->ext_no ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">
                                                                            Email ID</label>
                                                                        <input type="email"
                                                                            class="form-control form-control-sm"
                                                                            name="company_email"
                                                                            value="{{ old('company_email', $job->company_email ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">
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
                                                        <h2 class="accordion-header" id="headingSalaryDetails">
                                                            <button class="accordion-button collapsed" type="button"
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
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1">

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Basic
                                                                            Salary</label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm salary-component text-end"
                                                                            id="salary_basic" name="salary_basic"
                                                                            value="{{ old('salary_basic', $job->salary_basic ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">HRA</label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm salary-component text-end"
                                                                            id="salary_allowances"
                                                                            name="salary_allowances"
                                                                            value="{{ old('salary_allowances', $job->salary_allowances ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Other
                                                                            Allowances</label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm salary-component text-end"
                                                                            id="salary_other_allowances"
                                                                            name="salary_other_allowances"
                                                                            value="{{ old('salary_other_allowances', $job->salary_other_allowances ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Transport
                                                                            Allowance</label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm salary-component text-end"
                                                                            id="transport_allowance"
                                                                            name="transport_allowance"
                                                                            value="{{ old('transport_allowance', $job->transport_allowance ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Other
                                                                            Benefits</label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm salary-component text-end"
                                                                            id="other_benefits" name="other_benefits"
                                                                            value="{{ old('other_benefits', $job->other_benefits ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Gross
                                                                            Salary

                                                                        </label>
                                                                        <input type="number" step="any"
                                                                            class="form-control form-control-sm text-end"
                                                                            id="salary_gross" name="salary_gross"
                                                                            value="{{ old('salary_gross', $job->salary_gross ?? '') }}"
                                                                            readonly>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>





                                                    {{-- 5. Sales & Performance Targets --}}
                                                    <div class="accordion-item" id="salesTargetsAccordion"
                                                        style="display:none;">
                                                        <h2 class="accordion-header" id="headingSalesTargets">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseSalesTargets"
                                                                aria-expanded="false"
                                                                aria-controls="collapseSalesTargets">
                                                                <span class="me-2">5</span> Sales
                                                                & Performance Targets
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSalesTargets" class="accordion-collapse collapse"
                                                            aria-labelledby="headingSalesTargets">
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1">

                                                                    @php
                                                                        $isTargetVal = old('is_target', $job->is_target ?? '0');
                                                                        $targetTypeVal = old('target_type', $job->target_type ?? '');
                                                                        $selectedBrands = old('brands') ?? ($job->brand_ids ?? []);
                                                                        if (!is_array($selectedBrands)) {
                                                                            $selectedBrands = $selectedBrands ? explode(',', $selectedBrands) : [];
                                                                        }
                                                                       
                                                                    @endphp
                                                                    <div class="col-lg-1" id="sales_target_div"
                                                                        style="display:none;">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('Sales Target')</label>
                                                                            <select class="form-select form-select-sm js-example-basic-single"
                                                                                name="is_target" id="is_target"
                                                                                onchange="fn_role_id()">
                                                                                <option value="0" {{ (string) $isTargetVal === '0' ? 'selected' : '' }}>
                                                                                    No</option>
                                                                                <option value="1" {{ (string) $isTargetVal === '1' ? 'selected' : '' }}>
                                                                                    Yes</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-1" id="target_from_date_div"
                                                                        style="display:none;">
                                                                        <label class="form-label mb-1">Target
                                                                            From </label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker flatpickr-input"
                                                                            name="target_month_from"
                                                                            id="target_month_from"
                                                                            value="{{ old('target_month_from', @App\SysHelper::normalizeToDmy($job->target_month_from) ?? '') }}">
                                                                    </div>


                                                                    <div class="col-lg-1" id="target_type_div"
                                                                        style="display:none;">
                                                                        <label class="form-label mb-1">Type</label>
                                                                        <select class="form-select form-select-sm js-example-basic-single"
                                                                            name="target_type" id="target_type"
                                                                            onchange="toggleTargetInputs()">
                                                                            <option value="" {{ $targetTypeVal === '' ? 'selected' : '' }}>-Select-</option>
                                                                            <option value="revenue" {{ $targetTypeVal === 'revenue' ? 'selected' : '' }}>
                                                                                Revenue</option>
                                                                            <option value="gp" {{ $targetTypeVal === 'gp' ? 'selected' : '' }}>
                                                                                GP</option>
                                                                            <option value="both" {{ $targetTypeVal === 'both' ? 'selected' : '' }}>
                                                                                Both</option>
                                                                        </select>
                                                                    </div>



                                                                    <div class="col-lg-2" id="target_period_div"
                                                                        style="display:none;">
                                                                        <label class="form-label mb-1">Target
                                                                            Period</label>
                                                                        @php $targetPeriodVal = old('target_period') ?? ($job->target_period ?? ''); @endphp
                                                                        <select class="form-select form-select-sm js-example-basic-single"
                                                                            name="target_period">
                                                                            <option value="">
                                                                                -Select-</option>
                                                                            <option value="yearly"
                                                                                {{ $targetPeriodVal == 'yearly' ? 'selected' : '' }}>
                                                                                Yearly</option>
                                                                            <option value="halfyear"
                                                                                {{ $targetPeriodVal == 'halfyear' ? 'selected' : '' }}>
                                                                                Half Year</option>
                                                                            <option value="quarterly"
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

                                                                    <div class="col-lg-2" id="revenue_target_input"
                                                                        style="display:none;">
                                                                        <label class="form-label mb-1"
                                                                            style="font-weight: normal;">Revenue
                                                                            Target</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm text-end target-amount-input"
                                                                            name="revenue_target"
                                                                            value="{{ old('revenue_target', $job->revenue_target ?? '0.00') }}"
                                                                            placeholder="0.00">
                                                                    </div>

                                                                    <div class="col-lg-2" id="gp_target_input"
                                                                        style="display:none;">
                                                                        <label class="form-label mb-1"
                                                                            style="font-weight: normal;">GP
                                                                            Target</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm text-end target-amount-input"
                                                                            name="gp_target"
                                                                            value="{{ old('gp_target', $job->gp_target ?? '') }}"
                                                                            placeholder="0.00">
                                                                    </div>

                                                                    <div class="col-lg-1">
                                                                        <label class="form-label mb-1">Segment</label>
                                                                        @php $channelDistVal = old('channel_distribution') ?? ($job->channel_distribution ?? ''); @endphp
                                                                        <select class="form-select form-select-sm js-example-basic-single"
                                                                            name="channel_distribution">
                                                                            <option value="">
                                                                                -Select-</option>
                                                                            <option value="Channel"
                                                                                {{ $channelDistVal == 'Channel' ? 'selected' : '' }}>
                                                                                Channel</option>
                                                                            <option value="Distribution"
                                                                                {{ $channelDistVal == 'Distribution' ? 'selected' : '' }}>
                                                                                Distribution
                                                                            </option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-lg-2" id="brands_div"
                                                                        style="display:none;">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label mb-1">@lang('Brands')</label>
                                                                            <select
                                                                                class="form-select form-select-sm js-example-basic-single"
                                                                                name="brands[]" id="brands" multiple>
                                                                                <option value="all" {{ in_array('all', $selectedBrands) ? 'selected' : '' }}>
                                                                                    All</option>
                                                                                @foreach ($brand_list as $value)
                                                                                    <option value="{{ $value->id }}" {{ in_array((string)$value->id, array_map('strval', $selectedBrands)) ? 'selected' : '' }}>
                                                                                        {{ $value->title }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>



                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- 6. Document Attachments --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingDocAttachments">
                                                            <button class="accordion-button collapsed" type="button"
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
                                                            <div class="accordion-body p-2">
                                                                <div class="row gy-1">

                                                                    <div class="col-lg-3">
                                                                        <label
                                                                            class="form-label d-flex justify-content-between align-items-center mb-1">
                                                                            <span>Resume (Attachment)</span>
                                                                            @if (!empty($job->att_resume))
                                                                                <a href="{{ asset('storage/app/public/' . $job->att_resume) }}"
                                                                                    target="_blank"
                                                                                    class="text-success">View</a>
                                                                            @endif
                                                                        </label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="att_resume">
                                                                    </div>

                                                                    <div class="col-lg-3">
                                                                        <label
                                                                            class="form-label d-flex justify-content-between align-items-center mb-1">
                                                                            <span>Offer Letter (Attachment)</span>
                                                                            @if (!empty($job->att_offer_letter))
                                                                                <a href="{{ asset('storage/app/public/' . $job->att_offer_letter) }}"
                                                                                    target="_blank"
                                                                                    class="text-success">View</a>
                                                                            @endif
                                                                        </label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="att_offer_letter">
                                                                    </div>

                                                                    <div class="col-lg-3">
                                                                        <label
                                                                            class="form-label d-flex justify-content-between align-items-center mb-1">
                                                                            <span>Signed Contract (Attachment)</span>
                                                                            @if (!empty($job->att_signed_contract))
                                                                                <a href="{{ asset('storage/app/public/' . $job->att_signed_contract) }}"
                                                                                    target="_blank"
                                                                                    class="text-success">View</a>
                                                                            @endif
                                                                        </label>
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
                                            <div class="tab-pane fade" id="bank-details" role="tabpanel"
                                                aria-labelledby="bank-tab">

                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" id="addBankBtn" class="btn btn-sm btn-light"
                                                        data-bs-toggle="modal" data-bs-target="#bankModal">
                                                        <i class="ico icon-outline-add-square text-success"></i>
                                                        Add Bank Account
                                                    </button>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered align-middle"
                                                        id="long-list">
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
                                                            @if (!empty($bankRows) && $bankRows->count() > 0)
                                                                @foreach ($bankRows as $i => $bank)
                                                                    <tr class="bank-row">
                                                                        <td>
                                                                            <div class="d-none">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][bank_name]"
                                                                                    value="{{ old("banks.$i.bank_name", $bank->bank_name) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][branch_name]"
                                                                                    value="{{ old("banks.$i.branch_name", $bank->bank_branch) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][account_holder]"
                                                                                    value="{{ old("banks.$i.account_holder", $bank->bank_ac_holder) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][account_number]"
                                                                                    value="{{ old("banks.$i.account_number", $bank->bank_ac_number) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][iban_number]"
                                                                                    value="{{ old("banks.$i.iban_number", $bank->iban_number) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][swift_code]"
                                                                                    value="{{ old("banks.$i.swift_code", $bank->swift_code) }}">
                                                                                <input type="hidden"
                                                                                    name="banks[{{ $i }}][currency]"
                                                                                    value="{{ old("banks.$i.currency", $bank->bank_currency) }}">
                                                                                @if ($bank->att_iban_letter)
                                                                                    <input type="hidden"
                                                                                        name="banks[{{ $i }}][iban_letter_existing]"
                                                                                        value="{{ $bank->att_iban_letter }}">
                                                                                @endif
                                                                            </div>
                                                                            {{ $bank->bank_name }}
                                                                        </td>
                                                                        <td>{{ $bank->bank_branch }}</td>
                                                                        <td>{{ $bank->bank_ac_holder }}</td>
                                                                        <td>{{ $bank->bank_ac_number }}</td>
                                                                        <td>{{ $bank->iban_number }}</td>
                                                                        <td>{{ $bank->swift_code }}</td>
                                                                        <td>{{ optional($bank->currency)->code }}</td>
                                                                        <td>
                                                                            @if ($bank->att_iban_letter)
                                                                                @php $fileName = pathinfo($bank->att_iban_letter, PATHINFO_BASENAME); @endphp
                                                                                <a href="{{ asset('storage/app/public/' . $bank->att_iban_letter) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light btn-delete-bank"><i
                                                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                                    style="font-size: 16px"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr id="no-bank-row">
                                                                    <td colspan="9" class="text-center">No bank
                                                                        accounts added.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="bankInputsContainer" style="display:none"></div>
                                            </div>

                                            {{-- education qualification --}}

                                            <div class="tab-pane fade" id="educational-qualification" role="tabpanel"
                                                aria-labelledby="edu-tab">

                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" id="addEducationBtn"
                                                        class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                        data-bs-target="#educationModal">
                                                        <i class="ico icon-outline-add-square text-success"
                                                            style=""></i>
                                                        Add Education
                                                    </button>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered align-middle"
                                                        style="table-layout: fixed;width:100%" id="long-list">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 150px;">
                                                                    Qualification <span class="text-danger">*</span>
                                                                </th>
                                                                <th>Board / University <span class="text-danger">*</span>
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
                                                            @if (!empty($eduRows) && $eduRows->count() > 0)
                                                                @foreach ($eduRows as $i => $edu)
                                                                    <tr class="education-row">
                                                                        <td>
                                                                            <div class="d-none">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][qualification]"
                                                                                    value="{{ old("educations.$i.qualification", $edu->qualification) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][university]"
                                                                                    value="{{ old("educations.$i.university", $edu->university) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][specialization]"
                                                                                    value="{{ old("educations.$i.specialization", $edu->specialization) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][year]"
                                                                                    value="{{ old("educations.$i.year", $edu->year) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][result]"
                                                                                    value="{{ old("educations.$i.result", $edu->result) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][gpa]"
                                                                                    value="{{ old("educations.$i.gpa", $edu->gpa) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][mode]"
                                                                                    value="{{ old("educations.$i.mode", $edu->mode) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][country]"
                                                                                    value="{{ old("educations.$i.country", $edu->country) }}">
                                                                                <input type="hidden"
                                                                                    name="educations[{{ $i }}][duration]"
                                                                                    value="{{ old("educations.$i.duration", $edu->duration_years) }}">
                                                                                @if ($edu->certificate_path)
                                                                                    <input type="hidden"
                                                                                        name="educations[{{ $i }}][certificate_existing]"
                                                                                        value="{{ $edu->certificate_path }}">
                                                                                @endif
                                                                            </div>
                                                                            {{ $edu->qualification }}
                                                                        </td>
                                                                        <td>{{ $edu->university }}</td>
                                                                        <td>{{ $edu->specialization }}</td>
                                                                        <td>{{ $edu->year }}</td>
                                                                        <td>{{ $edu->result }}</td>
                                                                        <td>{{ $edu->gpa }}</td>
                                                                        <td>{{ $edu->mode }}</td>
                                                                        <td>{{ $countries->firstWhere('id', $edu->country)->name ?? $edu->country }}
                                                                        </td>
                                                                        <td>{{ $edu->duration_years }}</td>
                                                                        <td>
                                                                            @if ($edu->certificate_path)
                                                                                @php $fileName = pathinfo($edu->certificate_path, PATHINFO_BASENAME); @endphp
                                                                                <a href="{{ asset('storage/app/public/' . $edu->certificate_path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light btn-delete-education"><i
                                                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                                    style="font-size: 16px"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr class="no-education-row">
                                                                    <td colspan="11" class="text-center text-muted">No
                                                                        education records added yet.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            {{-- educational qualification end --}}

                                            {{-- professional experience --}}
                                            <div class="tab-pane fade" id="professional-experience" role="tabpanel"
                                                aria-labelledby="exp-tab">

                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" id="addExperienceBtn"
                                                        class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                        data-bs-target="#experienceModal">
                                                        <i class="ico icon-outline-add-square text-success"></i>
                                                        Add Experience
                                                    </button>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered align-middle"
                                                        style="table-layout: fixed;width:100%" id="long-list">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Previous Organization <span
                                                                        class="text-danger">*</span>
                                                                </th>
                                                                <th>Previous Designation</th>
                                                                <th style="width: 180px;">
                                                                    Employment Duration (Y, M)</th>
                                                                <th>Key Responsibilities</th>
                                                                <th>
                                                                    Experience Certificate </th>
                                                                <th style="width: 120px;">Action
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="experienceTableBody">
                                                            @if (!empty($expRows) && $expRows->count() > 0)
                                                                @foreach ($expRows as $i => $exp)
                                                                    <tr class="experience-row">
                                                                        <td>
                                                                            <div class="d-none">
                                                                                <input type="hidden"
                                                                                    name="experiences[{{ $i }}][organization]"
                                                                                    value="{{ old("experiences.$i.organization", $exp->organization) }}">
                                                                                <input type="hidden"
                                                                                    name="experiences[{{ $i }}][designation]"
                                                                                    value="{{ old("experiences.$i.designation", $exp->designation) }}">
                                                                                <input type="hidden"
                                                                                    name="experiences[{{ $i }}][years]"
                                                                                    value="{{ old("experiences.$i.years", $exp->years) }}">
                                                                                <input type="hidden"
                                                                                    name="experiences[{{ $i }}][months]"
                                                                                    value="{{ old("experiences.$i.months", $exp->months) }}">
                                                                                <input type="hidden"
                                                                                    name="experiences[{{ $i }}][responsibilities]"
                                                                                    value="{{ old("experiences.$i.responsibilities", $exp->responsibilities) }}">
                                                                                @if ($exp->certificate_path)
                                                                                    <input type="hidden"
                                                                                        name="experiences[{{ $i }}][certificate_existing]"
                                                                                        value="{{ $exp->certificate_path }}">
                                                                                @endif
                                                                            </div>
                                                                            {{ $exp->organization }}
                                                                        </td>
                                                                        <td>{{ $exp->designation }}</td>
                                                                        <td>{{ $exp->years ?? 0 }} Y,
                                                                            {{ $exp->months ?? 0 }} M</td>
                                                                        <td>{{ $exp->responsibilities }}</td>
                                                                        <td>
                                                                            @if ($exp->certificate_path)
                                                                                @php $fileName = pathinfo($exp->certificate_path, PATHINFO_BASENAME); @endphp
                                                                                <a href="{{ asset('storage/app/public/' . $exp->certificate_path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-light btn-delete-experience"><i
                                                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                                    style="font-size: 16px"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr class="no-experience-row">
                                                                    <td colspan="6" class="text-center text-muted">No
                                                                        experience records added yet.</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            {{-- professional experience end --}}





                                            {{-- documents tab --}}
                                            <div class="tab-pane fade" id="documentation" role="tabpanel"
                                                aria-labelledby="docs-tab">

                                                {{-- 1. JOINING DOCUMENTS --}}
                                                <h6 class="mt-1">Joining Documents</h6>
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-bordered align-middle" id="long-list">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:260px;">Document
                                                                </th>
                                                                <th style="width:160px;">Document Number
                                                                </th>
                                                                <th style="width:160px;">Expiry
                                                                    Date</th>
                                                                <th style="width:220px;">
                                                                    Attachment
                                                                </th>

                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>


                                                            @php
                                                                $docRows = $docRows ?? collect();
                                                                $joiningDocs = $docRows
                                                                    ->where('group', 'joining')
                                                                    ->keyBy('key');
                                                                $getExisting = function ($key) use ($joiningDocs) {
                                                                    return $joiningDocs[$key] ?? null;
                                                                };
                                                            @endphp

                                                            <tr>
                                                                <td class="">Photograph (Passport size)</td>
                                                                <td>

                                                                </td>
                                                                <td class=""></td>
                                                                @php $existing = $getExisting('photo'); @endphp
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][photo][file]">

                                                                    {{-- Preview for joining photo (shows existing image if present) --}}
                                                                    @php
                                                                        $previewSrc = null;
                                                                        if (!empty($existing->path)) {
                                                                            $ext = strtolower(
                                                                                pathinfo(
                                                                                    $existing->path,
                                                                                    PATHINFO_EXTENSION,
                                                                                ),
                                                                            );
                                                                            if (
                                                                                in_array($ext, [
                                                                                    'jpg',
                                                                                    'jpeg',
                                                                                    'png',
                                                                                    'gif',
                                                                                    'webp',
                                                                                ])
                                                                            ) {
                                                                                $previewSrc = asset(
                                                                                    'storage/app/public/' .
                                                                                        $existing->path,
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <img id="joining_photo_preview_edit"
                                                                        src="{{ $previewSrc ?? '' }}"
                                                                        alt="Photo preview"
                                                                        style="max-width:60px; display: {{ $previewSrc ? 'block' : 'none' }}; margin-top:8px;">

                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][photo][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class="">
                                                                    <input type="text" class="form-control"
                                                                        name="docs[joining][photo][remarks]"
                                                                        placeholder="For ID card / records"
                                                                        value="{{ old('docs.joining.photo.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Resume</td>
                                                                <td class=""></td>
                                                                <td class=""></td>
                                                                @php $existing = $getExisting('cv'); @endphp
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][cv][file]">
                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][cv][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class=""><input type="text"
                                                                        class="form-control"
                                                                        name="docs[joining][cv][remarks]"
                                                                        placeholder="Resume at the time of joining"
                                                                        value="{{ old('docs.joining.cv.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>

                                                            {{-- Required ones you always want new uploads for --}}
                                                            <tr>
                                                                <td class="">Passport Copy with Address<span
                                                                        class="text-danger">*</span></td>
                                                                @php $existing = $getExisting('passport_visa'); @endphp
                                                                <td class="">
                                                                    <input type="text" class="form-control"
                                                                        name="docs[joining][passport_visa][number]"
                                                                        placeholder="Passport Number"
                                                                        value="{{ old('docs.joining.passport_visa.number', $existing->document_number ?? '') }}">
                                                                </td>
                                                                <td class=""><input type="text"
                                                                        class="form-control date-picker"
                                                                        name="docs[joining][passport_visa][expiry]"
                                                                        value="{{ old('docs.joining.passport_visa.expiry', App\SysHelper::normalizeToDmy(@$existing->expiry_date)) }}">
                                                                </td>
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][passport_visa][file]">
                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][passport_visa][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class=""><input type="text"
                                                                        class="form-control"
                                                                        name="docs[joining][passport_visa][remarks]"
                                                                        placeholder="Passport bio page + UAE visa page"
                                                                        value="{{ old('docs.joining.passport_visa.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td class="">Offer Letter</td>
                                                                <td class=""></td>
                                                                <td class=""></td>
                                                                @php $existing = $getExisting('offer_letter'); @endphp
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][offer_letter][file]">
                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][offer_letter][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class=""><input type="text"
                                                                        class="form-control"
                                                                        name="docs[joining][offer_letter][remarks]"
                                                                        value="{{ old('docs.joining.offer_letter.remarks', optional($existing)->remarks ?? '') }}">

                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Bank Account Details (IBAN Letter)
                                                                </td>
                                                                @php $existing = $getExisting('iban_letter'); @endphp
                                                                <td>
                                                                    {{-- <input type="text" class="form-control"
                                                                        name="docs[joining][iban_letter][number]"
                                                                        placeholder="IBAN Number"
                                                                        value="{{ old('docs.joining.iban_letter.number', $existing->document_number ?? '') }}"> --}}
                                                                </td>
                                                                <td class=""></td>
                                                                <td class="">
                                                                    @php
                                                                        // Show all existing IBAN docs for this onboarding (if any)
                                                                        $ibanDocs = $docRows
                                                                            ->where('group', 'joining')
                                                                            ->where('key', 'iban_letter');
                                                                    @endphp
                                                                    @if ($ibanDocs && $ibanDocs->count() > 0)
                                                                        @foreach ($ibanDocs as $d)
                                                                            @php $fileName = basename($d->path); @endphp
                                                                            <div class="mb-1 existing-iban-doc"
                                                                                data-doc-id="{{ $d->id }}">
                                                                                <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif

                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][iban_letter][file][]"
                                                                        id="docs_joining_iban_file" multiple>

                                                                    {{-- visible container for IBAN docs (bank-added + manual) --}}
                                                                    <div id="iban_letter_docs_container"
                                                                        style="margin-top:8px"></div>

                                                                    {{-- Note: existing files are shown above; do not duplicate into hidden inputs to avoid changing server behavior here --}}
                                                                </td>

                                                                <td class=""><input type="text"
                                                                        class="form-control"
                                                                        name="docs[joining][iban_letter][remarks]"
                                                                        placeholder="Mandatory for payroll/WPS"
                                                                        value="{{ old('docs.joining.iban_letter.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Professional Certifications</td>
                                                                @php $existing = $getExisting('prof_certs'); @endphp
                                                                <td class="">
                                                                    {{-- <input type="text" class="form-control"
                                                                        name="docs[joining][prof_certs][number]"
                                                                        placeholder="Certification Name/Number"
                                                                        value="{{ old('docs.joining.prof_certs.number', $existing->document_number ?? '') }}"> --}}
                                                                </td>
                                                                <td class=""></td>
                                                                <td class="">
                                                                    @php
                                                                        // Show all existing professional cert docs for this onboarding (if any)
                                                                        $profDocs = $docRows
                                                                            ->where('group', 'joining')
                                                                            ->where('key', 'prof_certs');
                                                                    @endphp
                                                                    @if ($profDocs && $profDocs->count() > 0)
                                                                        @foreach ($profDocs as $d)
                                                                            @php $fileName = basename($d->path); @endphp
                                                                            <div class="mb-1 existing-prof-doc"
                                                                                data-doc-id="{{ $d->id }}">
                                                                                <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif

                                                                    {{-- prefill will show existing certs and keep this visible to add more --}}
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][prof_certs][file][]"
                                                                        id="docs_joining_prof_certs_file" multiple>

                                                                    {{-- visible container for Professional cert docs (experience-added + manual) --}}
                                                                    <div id="prof_certs_docs_container"
                                                                        style="margin-top:8px"></div>

                                                                </td>

                                                                <td class=""><input type="text"
                                                                        class="form-control"
                                                                        name="docs[joining][prof_certs][remarks]"
                                                                        placeholder="Optional for technical roles"
                                                                        value="{{ old('docs.joining.prof_certs.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>






                                                            <tr>
                                                                <td>Police NOC Certificate</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td>
                                                                    @php
                                                                        $policeDocs = $docRows
                                                                            ->where('group', 'joining')
                                                                            ->where('key', 'police_noc');
                                                                    @endphp
                                                                    @if ($policeDocs && $policeDocs->count() > 0)
                                                                        @foreach ($policeDocs as $d)
                                                                            @php $fileName = basename($d->path); @endphp
                                                                            <div class="mb-1 existing-police-doc"
                                                                                data-doc-id="{{ $d->id }}">
                                                                                <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif

                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][police_noc][file]"
                                                                        id="docs_joining_police_noc_file">

                                                                    {{-- visible container for Police NOC manual selections --}}
                                                                    <div id="police_noc_docs_container"
                                                                        style="margin-top:8px"></div>
                                                                </td>

                                                                <td><input type="text" class="form-control"
                                                                        name="docs[joining][police_noc][remarks]"
                                                                        placeholder="If applicable"
                                                                        value="{{ old('docs.joining.police_noc.remarks', isset($policeDocs) ? $policeDocs->pluck('remarks')->filter()->implode('; ') : '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>Relieving Letter</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td>
                                                                    @php
                                                                        $relieveDocs = $docRows
                                                                            ->where('group', 'joining')
                                                                            ->where('key', 'relieving_letter');
                                                                    @endphp
                                                                    @if ($relieveDocs && $relieveDocs->count() > 0)
                                                                        @foreach ($relieveDocs as $d)
                                                                            @php $fileName = basename($d->path); @endphp
                                                                            <div class="mb-1 existing-relieving-doc"
                                                                                data-doc-id="{{ $d->id }}">
                                                                                <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif

                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control" multiple
                                                                        name="docs[joining][relieving_letter][file][]"
                                                                        id="docs_joining_relieving_letter_file">

                                                                    {{-- visible container for Relieving Letter docs (existing + manual) --}}
                                                                    <div id="relieving_letter_docs_container"
                                                                        style="margin-top:8px"></div>
                                                                </td>

                                                                <td><input type="text" class="form-control"
                                                                        name="docs[joining][relieving_letter][remarks]"
                                                                        placeholder="From previous employer(s)"
                                                                        value="{{ old('docs.joining.relieving_letter.remarks', isset($relieveDocs) ? $relieveDocs->pluck('remarks')->filter()->first() : '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Academic Certificates</td>
                                                                @php $existing = $getExisting('academic'); @endphp

                                                                <td class="">
                                                                    {{-- <input type="text" class="form-control"
                                                                        name="docs[joining][academic][number]"
                                                                        placeholder="Degree / Diploma Name/Number"
                                                                        value="{{ old('docs.joining.academic.number', $existing->document_number ?? '') }}"> --}}
                                                                </td>
                                                                <td class="">
                                                                    {{-- <input type="text" class="form-control date-picker"
                                                                        name="docs[joining][academic][expiry]"
                                                                        value="{{ old('docs.joining.academic.expiry', App\SysHelper::normalizeToDmy(@$existing->expiry_date)) }}">
                                                                </td> --}}
                                                                <td class="">
                                                                    @php
                                                                        // Show all existing academic docs for this onboarding (if any)
                                                                        $acadDocs = $docRows
                                                                            ->where('group', 'joining')
                                                                            ->where('key', 'academic');
                                                                    @endphp
                                                                    @if ($acadDocs && $acadDocs->count() > 0)
                                                                        @foreach ($acadDocs as $d)
                                                                            @php $fileName = basename($d->path); @endphp
                                                                            <div class="mb-1 existing-academic-doc"
                                                                                data-doc-id="{{ $d->id }}">
                                                                                <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                                                    target="_blank">{{ $fileName }}</a>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif

                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][academic][file][]"
                                                                        id="docs_joining_academic_file" multiple>

                                                                    {{-- visible container for Academic docs (education-added + manual) --}}
                                                                    <div id="academic_docs_container"
                                                                        style="margin-top:8px"></div>

                                                                    {{-- Note: existing files are shown above; do not duplicate into hidden inputs to avoid changing server behavior here --}}

                                                                </td>

                                                                <td class="">
                                                                    <input type="text" class="form-control"
                                                                        name="docs[joining][academic][remarks]"
                                                                        placeholder="Verified/attested copies"
                                                                        value="{{ old('docs.joining.academic.remarks', isset($acadDocs) ? $acadDocs->pluck('remarks')->filter()->first() : '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Medical Fitness Certificate</td>
                                                                @php $existing = $getExisting('medical_fit'); @endphp
                                                                <td class=""></td>
                                                                <td class="">
                                                                    {{-- <input type="text" class="form-control date-picker"
                                                                        name="docs[joining][medical_fit][expiry]"
                                                                        value="{{ old('docs.joining.medical_fit.expiry', App\SysHelper::normalizeToDmy(@$existing->expiry_date)) }}"> --}}
                                                                </td>
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][medical_fit][file]">
                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][medical_fit][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class="">
                                                                    <input type="text" class="form-control"
                                                                        name="docs[joining][medical_fit][remarks]"
                                                                        placeholder="Required for visa processing"
                                                                        value="{{ old('docs.joining.medical_fit.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="">Employment Contract (Optional)</td>
                                                                @php $existing = $getExisting('emp_contract'); @endphp
                                                                <td class=""></td>
                                                                <td class="">
                                                                    <input type="text" class="form-control date-picker"
                                                                        name="docs[joining][emp_contract][expiry]"
                                                                        value="{{ old('docs.joining.emp_contract.expiry', App\SysHelper::normalizeToDmy(@$existing->expiry_date)) }}">
                                                                </td>
                                                                <td class="">
                                                                    @if ($existing && $existing->path)
                                                                        @php $fileName = basename($existing->path); @endphp
                                                                        <div class="mb-1"><a
                                                                                href="{{ asset('storage/app/public/' . $existing->path) }}"
                                                                                target="_blank">{{ $fileName }}</a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" accept=".pdf,image/*"
                                                                        class="form-control"
                                                                        name="docs[joining][emp_contract][file]">
                                                                    @if ($existing && $existing->path)
                                                                        <input type="hidden"
                                                                            name="docs[joining][emp_contract][existing]"
                                                                            value="{{ $existing->path }}">
                                                                    @endif
                                                                </td>

                                                                <td class="">
                                                                    <input type="text" class="form-control"
                                                                        name="docs[joining][emp_contract][remarks]"
                                                                        placeholder="MOHRE / Free Zone contract"
                                                                        value="{{ old('docs.joining.emp_contract.remarks', optional($existing)->remarks ?? '') }}">
                                                                </td>
                                                            </tr>



                                                            {{-- Add your other rows (emp_contract, medical_fit, academic, etc.) the same way --}}
                                                        </tbody>
                                                    </table>
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



        {{-- Bank Modal --}}
        <div class="modal fade" data-bs-backdrop="false" tabindex="-1" aria-labelledby="DeviceSerialModalLabel"
            aria-hidden="true" id="bankModal" tabindex="-1">
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
                                    <label>Branch</label>
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
                                    <label>SWIFT / IFSC Code</label>
                                    <input type="text" class="form-control" id="swift_code" name="swift_code">
                                </div>

                                <div class="col-md-6">
                                    <label>Currency</label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="currency"
                                            id="currency">

                                            @foreach ($currencies as $value)
                                                <option value="{{ @$value->id }}">
                                                    {{ @$value->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>IBAN Letter (Attachment)</label>
                                    <input type="file" class="form-control" id="iban_letter" name="iban_letter"
                                        accept=".pdf,image/*">
                                    <small class="text-muted" id="existingIbanLetter">Allowed: PDF, images</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_bank">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Education Modal --}}
        <div class="modal fade" data-bs-backdrop="false" tabindex="-1" aria-labelledby="DeviceSerialModalLabel"
            aria-hidden="true" id="educationModal" tabindex="-1">
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
                                    <select class="form-control js-example-basic-single" name="qualification" required>
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
                                    <select class="form-control js-example-basic-single" name="mode">
                                        <option value="">-Select-</option>
                                        <option>Full-Time</option>
                                        <option>Part-Time</option>
                                        <option>Distance</option>
                                        <option>Online</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Country of Study</label>
                                    <select class="form-control js-example-basic-single" name="country"
                                        id="edu_country">
                                        <option value="">-Select-</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Duration (Years)</label>
                                    <input type="number" step="any" class="form-control" name="duration">
                                </div>

                                <div class="col-md-6">
                                    <label>Certificate Upload <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="education_certificate"
                                        name="certificate" accept=".pdf,image/*">
                                    <small class="text-muted" id="existingCertificate">Allowed: PDF, images</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_education">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Experience Modal --}}
        <div class="modal fade" data-bs-backdrop="false" tabindex="-1" aria-labelledby="DeviceSerialModalLabel"
            aria-hidden="true" id="experienceModal" tabindex="-1">
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
                            <div class="row g-3">

                                <!-- LEFT COLUMN -->
                                <div class="col-lg-12 col-md-12">
                                    <div class="row g-3">

                                        <div class="col-6">
                                            <label class="form-label">
                                                Previous Organization <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="organization" required>
                                        </div>


                                        <div class="col-3">
                                            <label class="form-label">Previous Designation</label>
                                            <input type="text" class="form-control" name="designation">
                                        </div>

                                        <div class="col-3">
                                            <label class="form-label">Employment Duration</label>

                                            <div class="input-group">
                                                <input type="number" min="0" class="form-control"
                                                    name="years" placeholder="Years">



                                                <input type="number" min="0" max="11"
                                                    class="form-control" name="months" placeholder="Months">


                                            </div>
                                        </div>




                                        <div class="col-6">
                                            <label class="form-label">Experience Certificate (Attachment)</label>
                                            <input type="file" class="form-control" id="exp_certificate"
                                                name="certificate" accept=".pdf,image/*">
                                            <small class="text-muted d-block mt-1" id="existingExpCertificate">Allowed:
                                                PDF, images</small>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label">Key Responsibilities</label>
                                            <textarea class="form-control" name="responsibilities" rows="4"></textarea>
                                        </div>

                                    </div>
                                </div>



                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_experience">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Department Add Modal --}}
        <div class="modal side-panel fade" id="departmentAddModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="departmentAddModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="departmentAddModalLabel">Add Department</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                                <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal PO --}}


        {{-- Religion Add Modal --}}
        <div class="modal side-panel fade" id="religionAddModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="religionAddModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="religionAddModalLabel">Add Religion</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Form -->
                    <form id="religionAddForm">
                        @csrf

                        <div class="modal-body pt-3">

                            <!-- Department Name -->
                            <label class="form-label">
                                Religion Name <span class="text-danger">*</span>
                            </label>

                            <input type="text" class="form-control" id="religion_name" name="title" required
                                autocomplete="off" style="padding: 2px 5px;">

                            <!-- Footer -->
                            <div class="modal-footer d-flex justify-content-center p-0 pt-3">

                                <button type="submit" id="saveReligionBtn"
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

    </div>

    
    <div class="modal fade" id="addShiftModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Add Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Document Form -->
                    <form id="documentForm">
                        <input type="hidden" id="documentEditIndex" value="-1">
                        <div class="row gy-2">
                            <div class="col-12">
                                <label for="document_name" class="form-label mb-1">Shift Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="add_shiftname"
                                    name="add_shiftname" placeholder="">
                            </div>
                            <div class="col-12 ">
                                <label for="shift_start_time" class="form-label mb-1">Start Time<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" id="add_shift_start_time"
                                    name="add_shift_start_time" placeholder="">
                            </div>
                            <div class="col-12">
                                <label for="shift_end_time" class="form-label mb-1">End Time<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-sm" id="add_shift_end_time"
                                    name="add_shift_end_time" placeholder="">
                            </div>
                        </div>
                    </form>

                    <!-- Add Document Button -->
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                            id="addShiftBtn">

                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>Save</span>
                        </button>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addWeeklyOffModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="addWeeklyOffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWeeklyOffModalLabel">Add Weekly Off</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="weeklyOffForm">
                        <div class="row gy-2">
                            <div class="col-12">
                                <label for="add_weeklyoffname" class="form-label mb-1">Weekly Off Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="add_weeklyoffname" name="add_weeklyoffname" placeholder="">
                            </div>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="addWeeklyOffBtn">
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>Save</span>
                        </button>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover" id="weeklyOffListTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="weeklyOffTableBody">
                                <tr class="no-weeklyoff-row">
                                    <td colspan="2" class="text-center text-muted">No weekly offs added yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <script>
        $(function() {
            function clearShiftErrors() {
                $('.shift-error').remove();
            }

            $('#addShiftBtn').on('click', function() {
                clearShiftErrors();
                var $btn = $(this);
                var name = $('#add_shiftname').val() ? $('#add_shiftname').val().trim() : '';
                var start = $('#add_shift_start_time').val();
                var end = $('#add_shift_end_time').val();
                var hasError = false;
                var timeRegex = /^([01]\d|2[0-3]):[0-5]\d$/;

                if (!name) {
                    $('#add_shiftname').after(
                        '<div class="text-danger mt-1 shift-error">Shift name is required</div>');
                    hasError = true;
                }
                if (!timeRegex.test(start)) {
                    $('#add_shift_start_time').after(
                        '<div class="text-danger mt-1 shift-error">Start time is invalid</div>');
                    hasError = true;
                }
                if (!timeRegex.test(end)) {
                    $('#add_shift_end_time').after(
                        '<div class="text-danger mt-1 shift-error">End time is invalid</div>');
                    hasError = true;
                }
                if (!hasError && start >= end) {
                    $('#add_shift_end_time').after(
                        '<div class="text-danger mt-1 shift-error">End time must be after start time</div>'
                    );
                    hasError = true;
                }
                if (hasError) return;

                // disable & spinner
                $btn.prop('disabled', true).append(
                    '<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>'
                );

                $.ajax({
                    url: '{{ url('/company/working-shifts/store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shift_name: name,
                        start_time: start,
                        end_time: end
                    },
                    success: function(res) {
                        if (res && res.ok) {
                            var s = res.shift;
                            var text = s.shift_name + ' (' + s.start_time + ' - ' + s.end_time +
                                ')';
                            var $select = $('[name="shift_id"]');
                            // append and select new option
                            $select.append(new Option(text, s.id, true, true));
                            $select.trigger('change');
                            // close modal and reset
                            $('#addShiftModal').modal('hide');
                            $('#add_shiftname,#add_shift_start_time,#add_shift_end_time').val(
                                '');

                            // small inline toast fallback
                            var $msg = $(
                                '<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Shift added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                            );
                            $('.modal-body').first().prepend($msg);
                            setTimeout(function() {
                                $msg.alert('close');
                            }, 3000);

                        } else if (res && res.errors) {
                            $.each(res.errors, function(k, v) {
                                var msg = Array.isArray(v) ? v[0] : v;
                                if (k === 'shift_name') $('#add_shiftname').after(
                                    '<div class="text-danger mt-1 shift-error">' +
                                    msg + '</div>');
                                if (k === 'start_time') $('#add_shift_start_time')
                                    .after(
                                        '<div class="text-danger mt-1 shift-error">' +
                                        msg + '</div>');
                                if (k === 'end_time') $('#add_shift_end_time').after(
                                    '<div class="text-danger mt-1 shift-error">' +
                                    msg + '</div>');
                            });
                        } else {
                            alert('Could not add shift. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                            .errors) {
                            var errs = xhr.responseJSON.errors;
                            $.each(errs, function(k, v) {
                                var msg = v[0];
                                if (k === 'shift_name') $('#add_shiftname').after(
                                    '<div class="text-danger mt-1 shift-error">' +
                                    msg + '</div>');
                                if (k === 'start_time') $('#add_shift_start_time')
                                    .after(
                                        '<div class="text-danger mt-1 shift-error">' +
                                        msg + '</div>');
                                if (k === 'end_time') $('#add_shift_end_time').after(
                                    '<div class="text-danger mt-1 shift-error">' +
                                    msg + '</div>');
                            });
                        } else {
                            alert('Server error. Please try again later.');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('.spinner-border').remove();
                    }
                });
            });

            // submit on Enter key
            $('#documentForm').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $('#addShiftBtn').click();
                }
            });
        });
    </script>

    <script>
        $(function() {
            function clearWeeklyOffErrors() {
                $('.weeklyoff-error').remove();
            }

            $('#addWeeklyOffBtn').on('click', function() {
                clearWeeklyOffErrors();

                var $btn = $(this);
                var name = $('#add_weeklyoffname').val() ? $('#add_weeklyoffname').val().trim() : '';

                if (!name) {
                    $('#add_weeklyoffname').after('<div class="text-danger mt-1 weeklyoff-error">Weekly off name is required</div>');
                    return;
                }

                $btn.prop('disabled', true).append('<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');

                $.ajax({
                    url: '{{ route('company.weeklyoff.store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function(res) {
                        if (res && res.ok) {
                            var wo = res.weekly_off;
                            var $select = $('#weeklyoff_select');
                            $select.append(new Option(wo.name, wo.id, true, true));
                            if ($select.hasClass('js-example-basic-single')) {
                                $select.trigger('change.select2');
                            } else {
                                $select.trigger('change');
                            }

                            var row = $('<tr class="weeklyoff-row" data-weeklyoff-id="' + wo.id + '"><td>' + wo.name + '</td><td class="text-center d-flex justify-content-center"><button type="button" class="btn btn-sm btn-light btn-delete-weeklyoff"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size:16px;"></i></button></td></tr>');
                            $('#weeklyOffTableBody .no-weeklyoff-row').remove();
                            $('#weeklyOffTableBody').append(row);

                            $('#addWeeklyOffModal').modal('hide');
                            $('#add_weeklyoffname').val('');
                            if (window.toastr) {
                                toastr.success('Weekly off "' + wo.name + '" added.');
                            }
                        } else if (res && res.errors) {
                            $.each(res.errors, function(k, v) {
                                var msg = Array.isArray(v) ? v[0] : v;
                                $('#add_weeklyoffname').after('<div class="text-danger mt-1 weeklyoff-error">' + msg + '</div>');
                            });
                        } else {
                            alert('Could not add weekly off. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                $('#add_weeklyoffname').after('<div class="text-danger mt-1 weeklyoff-error">' + v[0] + '</div>');
                            });
                        } else {
                            alert('Server error. Please try again later.');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('.spinner-border').remove();
                    }
                });
            });

            $('#weeklyOffTableBody').on('click', '.btn-delete-weeklyoff', function() {
                var $row = $(this).closest('tr.weeklyoff-row');
                var woId = $row.data('weeklyoff-id');
                var name = $row.find('td').first().text().trim();
                $row.remove();

                if ($('#weeklyOffTableBody .weeklyoff-row').length === 0) {
                    $('#weeklyOffTableBody').append('<tr class="no-weeklyoff-row"><td colspan="2" class="text-center text-muted">No weekly offs added yet.</td></tr>');
                }

                if (woId) {
                    $('#weeklyoff_select').find('option[value="' + woId + '"]').remove();
                } else {
                    $('#weeklyoff_select').find('option').filter(function() { return $(this).text() === name; }).remove();
                }
                $('#weeklyoff_select').trigger('change');
            });

            $('#add_weeklyoffname').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $('#addWeeklyOffBtn').click();
                }
            });
        });
    </script>


    <script>
        $(document).on('submit', '#religionAddForm', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ url('religion-store-ajax') }}",
                method: "POST",
                data: form.serialize(),
                dataType: "json",

                success: function(response) {
                    if (response.status === true) {

                        const option = new Option(
                            response.data.name,
                            response.data.id,
                            true,
                            true
                        );

                        $('#religion').append(option).trigger('change');

                        form[0].reset();
                        $('#religionAddModal').modal('hide');

                        toastr.success(response.message);
                    }
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },

                complete: function() {
                    submitBtn.prop('disabled', false).text('Save');
                }
            });
        });
    </script>


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
                        if ($('#department_id').hasClass('js-example-basic-single')) $('#department_id')
                            .trigger('change.select2');

                        if (typeof window.loadDesignationsForDepartment === 'function') {
                            window.loadDesignationsForDepartment(res.data.department_id, res.data.id);
                        } else {
                            // Fallback: set temporary marker so department change handler can preselect
                            $('#department_id').data('select-designation-after-load', res.data.id)
                                .trigger('change');
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


    <script>
        $(document).ready(function() {

            $(function() {
                var bankIndex = $('#bankTableBody').find('.bank-row').length || 0;
                var educationIndex = $('#educationTableBody').find('.education-row').length || 0;
                var experienceIndex = $('#experienceTableBody').find('.experience-row').length || 0;

                function createHidden(name, value) {
                    return $('<input>').attr({
                        type: 'hidden',
                        name: name,
                        value: value
                    });
                }

                // Add Bank row from modal into the bank table
                $('#btn_save_bank').on('click', function(e) {
                    e.preventDefault();

                    var bank_name = $('#bank_name').val() || '';
                    var branch_name = $('#branch_name').val() || '';
                    var account_holder = $('#account_holder').val() || '';
                    var account_number = $('#account_number').val() || '';
                    var iban_number = $('#iban_number').val() || '';
                    var swift_code = $('#swift_code').val() || '';
                    var currencyVal = $('#bankModal').find('select[name="currency"]').val() || '';
                    var currencyText = $('#bankModal').find(
                            'select[name="currency"] option:selected').text() ||
                        currencyVal;

                    if (!bank_name.trim()) {
                        toastr.error('Bank Name is required');
                        return;
                    }
                    // if (!account_holder.trim()) {
                    //     toastr.error('Account Holder is required');
                    //     return;
                    // }
                    // if (!iban_number.trim()) {
                    //     toastr.error('IBAN Number is required');
                    //     return;
                    // }

                    var fileInput = $('#iban_letter');
                    var fileName = '';
                    var movedFile = false;
                    if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                        var file = fileInput[0].files[0];
                        var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png',
                            'image/gif',
                            'image/webp'
                        ];
                        if ($.inArray(file.type, allowedTypes) === -1) {
                            toastr.error('Only PDF or image files are allowed for IBAN Letter.');
                            return;
                        }
                        fileName = file.name;
                        movedFile = true;
                    }

                    var row = $('<tr class="bank-row"></tr>');
                    var hidden = $('<div class="d-none"></div>');
                    // use explicit index so PHP receives structured arrays: banks[0][bank_name], banks[0][iban_letter], etc.
                    hidden.append(createHidden('banks[' + bankIndex + '][bank_name]', bank_name));
                    hidden.append(createHidden('banks[' + bankIndex + '][branch_name]',
                        branch_name));
                    hidden.append(createHidden('banks[' + bankIndex + '][account_holder]',
                        account_holder));
                    hidden.append(createHidden('banks[' + bankIndex + '][account_number]',
                        account_number));
                    hidden.append(createHidden('banks[' + bankIndex + '][iban_number]',
                        iban_number));
                    hidden.append(createHidden('banks[' + bankIndex + '][swift_code]', swift_code));
                    hidden.append(createHidden('banks[' + bankIndex + '][currency]', currencyVal));


                    // move the actual file input into the hidden container so the file is submitted
                    if (movedFile) {
                        fileInput.attr('name', 'banks[' + bankIndex + '][iban_letter]');
                        fileInput.attr('id', 'iban_letter_' + bankIndex);
                        fileInput.detach();
                        hidden.append(fileInput);

                        // Also create a hidden file input copy for docs[joining][iban_letter][file][] so all IBAN letters are available in the documents section
                        try {
                            var dtDocs = new DataTransfer();
                            dtDocs.items.add(file);
                            var docsInput = $("<input type='file' class='d-none'>");
                            var docsId = 'docs_iban_file_' + bankIndex;
                            docsInput.attr('name', 'docs[joining][iban_letter][file][]');
                            docsInput.attr('id', docsId);
                            docsInput[0].files = dtDocs.files;
                            $('#bankInputsContainer').append(docsInput);

                            // Show a visual entry in the IBAN docs container for user awareness and allow removal
                            var $label = $(
                                "<div class='iban-doc-item d-flex align-items-center mb-1' data-docs-id='" +
                                docsId + "'></div>");
                            $label.append($('<span>').text(fileName).css({
                                'margin-right': '8px'
                            }));
                            $label.append($(
                                '<button type="button" class="btn btn-sm btn-light remove-iban-doc" data-docs-id="' +
                                docsId +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            ));
                            $('#iban_letter_docs_container').append($label);

                            // Link the bank row with this docs id so deletion can clean up both
                            row.attr('data-docs-id', docsId);
                        } catch (err) {
                            console.error('Failed to duplicate IBAN letter for docs:', err);
                        }
                    }

                    row.append($('<td>').text(bank_name).append(hidden));
                    row.append($('<td>').text(branch_name));
                    row.append($('<td>').text(account_holder));
                    row.append($('<td>').text(account_number));
                    row.append($('<td>').text(iban_number));
                    row.append($('<td>').text(swift_code));
                    row.append($('<td>').text(currencyText));

                    row.append($('<td>').text(fileName));
                    row.append($('<td>').html(
                        '<button type="button" class="btn btn-sm btn-light btn-delete-bank"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                    ));

                    // Remove placeholder row if present
                    $('#bankTableBody').find('#no-bank-row').remove();

                    $('#bankTableBody').append(row);

                    // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                    $('#bankForm')[0].reset();
                    if (movedFile) {
                        var newFile = $(
                            '<input type="file" class="form-control" id="iban_letter" name="iban_letter" accept=".pdf,image/*">'
                        );
                        $('#existingIbanLetter').before(newFile);
                    }

                    bankIndex++;

                    $('#bankModal').modal('hide');
                });

                // Remove bank row (also clean any associated docs IBAN hidden inputs and UI)
                $('#bankTableBody').on('click', '.btn-delete-bank', function() {
                    var $tr = $(this).closest('tr');
                    var docsId = $tr.attr('data-docs-id');
                    if (docsId) {
                        // remove the hidden file input duplicated for docs
                        $('#' + docsId).remove();
                        // remove visible label in the docs container
                        $('#iban_letter_docs_container').find('[data-docs-id="' + docsId + '"]')
                            .remove();
                    }

                    $tr.remove();
                    // if no bank rows remain, show the placeholder
                    if ($('#bankTableBody').find('.bank-row').length === 0) {
                        $('#bankTableBody').append(
                            '<tr id="no-bank-row">\n                                                                            <td colspan="9" class="text-center text-muted">\n                                                                                No bank accounts added yet.\n                                                                            </td>\n                                                                        </tr>'
                        );
                    }
                });

                // Add Education row from modal into the education table
                $('#btn_save_education').on('click', function(e) {
                    e.preventDefault();

                    var $modal = $('#educationModal');
                    var qualification = $modal.find('select[name="qualification"]').val() || '';
                    var university = $modal.find('input[name="university"]').val() || '';
                    var specialization = $modal.find('input[name="specialization"]').val() || '';
                    var year = $modal.find('input[name="year"]').val() || '';
                    var result = $modal.find('input[name="result"]').val() || '';
                    var gpa = $modal.find('input[name="gpa"]').val() || '';
                    var mode = $modal.find('select[name="mode"]').val() || '';
                    var country = $modal.find('select[name="country"]').val() || '';
                    var duration = $modal.find('input[name="duration"]').val() || '';

                    if (!qualification.trim()) {
                        toastr.error('Qualification is required');
                        return;
                    }
                    if (!university.trim()) {
                        toastr.error('Board / University is required');
                        return;
                    }

                    var fileInput = $modal.find('#education_certificate');
                    var fileName = '';
                    var movedFile = false;
                    if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                        var file = fileInput[0].files[0];
                        var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png',
                            'image/gif',
                            'image/webp'
                        ];
                        if ($.inArray(file.type, allowedTypes) === -1) {
                            toastr.error('Only PDF or image files are allowed for Certificate.');
                            return;
                        }
                        fileName = file.name;
                        movedFile = true;
                    }

                    var row = $('<tr class="education-row"></tr>');
                    var hidden = $('<div class="d-none"></div>');

                    hidden.append(createHidden('educations[' + educationIndex + '][qualification]',
                        qualification));
                    hidden.append(createHidden('educations[' + educationIndex + '][university]',
                        university));
                    hidden.append(createHidden('educations[' + educationIndex + '][specialization]',
                        specialization));
                    hidden.append(createHidden('educations[' + educationIndex + '][year]', year));
                    hidden.append(createHidden('educations[' + educationIndex + '][result]',
                        result));
                    hidden.append(createHidden('educations[' + educationIndex + '][gpa]', gpa));
                    hidden.append(createHidden('educations[' + educationIndex + '][mode]', mode));
                    hidden.append(createHidden('educations[' + educationIndex + '][country]',
                        country));
                    hidden.append(createHidden('educations[' + educationIndex + '][duration]',
                        duration));

                    if (movedFile) {
                        fileInput.attr('name', 'educations[' + educationIndex + '][certificate]');
                        fileInput.attr('id', 'education_certificate_' + educationIndex);
                        fileInput.detach();
                        hidden.append(fileInput);

                        // Also create a hidden file input copy for docs[joining][academic][file][] so the certificate is available in the documents section
                        try {
                            var dtDocs = new DataTransfer();
                            dtDocs.items.add(file);
                            var docsInput = $("<input type='file' class='d-none'>");
                            var docsId = 'docs_academic_file_' + educationIndex;
                            docsInput.attr('name', 'docs[joining][academic][file][]');
                            docsInput.attr('id', docsId);
                            docsInput[0].files = dtDocs.files;
                            $('#bankInputsContainer').append(docsInput);

                            // Show a visual entry in the Academic docs container for user awareness and allow removal
                            var $label = $(
                                "<div class='academic-doc-item d-flex align-items-center mb-1' data-docs-id='" +
                                docsId + "'></div>");
                            $label.append($('<span>').text(fileName).css({
                                'margin-right': '8px'
                            }));
                            $label.append($(
                                '<button type="button" class="btn btn-sm btn-light remove-academic-doc" data-docs-id="' +
                                docsId +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            ));
                            $('#academic_docs_container').append($label);

                            // Link the education row with this docs id so deletion can clean up both
                            row.attr('data-docs-id', docsId);
                        } catch (err) {
                            console.error('Failed to duplicate academic certificate for docs:',
                                err);
                        }
                    }

                    row.append($('<td>').text(qualification).append(hidden));
                    row.append($('<td>').text(university));
                    row.append($('<td>').text(specialization));
                    row.append($('<td>').text(year));
                    row.append($('<td>').text(result));
                    row.append($('<td>').text(gpa));
                    row.append($('<td>').text(mode));
                    row.append($('<td>').text(country));
                    row.append($('<td>').text(duration));
                    row.append($('<td>').text(fileName));
                    row.append($('<td>').html(
                        '<button type="button" class="btn btn-sm btn-light btn-delete-education"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                    ));

                    // Remove placeholder row if present
                    $('#educationTableBody').find('.no-education-row').remove();

                    $('#educationTableBody').append(row);

                    // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                    $modal.find('form')[0].reset();
                    if (movedFile) {
                        var newFile = $(
                            '<input type="file" class="form-control" id="education_certificate" name="certificate" accept=".pdf,image/*">'
                        );
                        $modal.find('#existingCertificate').before(newFile);
                    }

                    educationIndex++;

                    $('#educationModal').modal('hide');
                });

                // Remove education row
                $('#educationTableBody').on('click', '.btn-delete-education', function() {
                    var $tr = $(this).closest('tr');
                    var docsId = $tr.attr('data-docs-id');
                    if (docsId) {
                        $('#' + docsId).remove();
                        $('#academic_docs_container').find('[data-docs-id="' + docsId + '"]')
                            .remove();
                    }
                    $tr.remove();
                    // if no education rows remain, show the placeholder
                    if ($('#educationTableBody').find('.education-row').length === 0) {
                        $('#educationTableBody').append(
                            '<tr class="no-education-row">\n                                                                            <td colspan="11" class="text-center text-muted">\n                                                                                No education records added yet.\n                                                                            </td>\n                                                                        </tr>'
                        );
                    }
                });

                // Add Experience row from modal into the experience table
                $('#btn_save_experience').on('click', function(e) {
                    e.preventDefault();

                    var $modal = $('#experienceModal');
                    var organization = $modal.find('input[name="organization"]').val() || '';
                    var designation = $modal.find('input[name="designation"]').val() || '';
                    var years = $modal.find('input[name="years"]').val() || '';
                    var months = $modal.find('input[name="months"]').val() || '';
                    var responsibilities = $modal.find('textarea[name="responsibilities"]').val() ||
                        '';

                    if (!organization.trim()) {
                        toastr.error('Previous Organization is required');
                        return;
                    }

                    // Basic numeric checks for duration
                    if (years !== '' && (!/^[0-9]+$/.test(years) || parseInt(years, 10) < 0)) {
                        toastr.error('Years must be a non-negative integer');
                        return;
                    }
                    if (months !== '' && (!/^[0-9]+$/.test(months) || parseInt(months, 10) < 0 ||
                            parseInt(
                                months, 10) > 12)) {
                        toastr.error('Months must be an integer between 1 and 12');
                        return;
                    }

                    var fileInput = $modal.find('#exp_certificate');
                    var fileName = '';
                    var movedFile = false;
                    if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                        var file = fileInput[0].files[0];
                        var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png',
                            'image/gif',
                            'image/webp'
                        ];
                        if ($.inArray(file.type, allowedTypes) === -1) {
                            toastr.error(
                                'Only PDF or image files are allowed for Experience Certificate.'
                            );
                            return;
                        }
                        fileName = file.name;
                        movedFile = true;
                    }

                    var row = $('<tr class="experience-row"></tr>');
                    var hidden = $('<div class="d-none"></div>');

                    hidden.append(createHidden('experiences[' + experienceIndex + '][organization]',
                        organization));
                    hidden.append(createHidden('experiences[' + experienceIndex + '][designation]',
                        designation));
                    hidden.append(createHidden('experiences[' + experienceIndex + '][years]',
                        years));
                    hidden.append(createHidden('experiences[' + experienceIndex + '][months]',
                        months));
                    hidden.append(createHidden('experiences[' + experienceIndex +
                        '][responsibilities]',
                        responsibilities));

                    if (movedFile) {
                        fileInput.attr('name', 'experiences[' + experienceIndex + '][certificate]');
                        fileInput.attr('id', 'exp_certificate_' + experienceIndex);
                        fileInput.detach();
                        hidden.append(fileInput);

                        // Also create a hidden file input copy for docs[joining][prof_certs][file][] so the certificate is available in the documents section
                        try {
                            var dtDocs = new DataTransfer();
                            dtDocs.items.add(file);
                            var docsInput = $("<input type='file' class='d-none'>");
                            var docsId = 'docs_prof_file_' + experienceIndex;
                            docsInput.attr('name', 'docs[joining][prof_certs][file][]');
                            docsInput.attr('id', docsId);
                            docsInput[0].files = dtDocs.files;
                            $('#bankInputsContainer').append(docsInput);

                            // Show a visual entry in the Professional Certs docs container for user awareness and allow removal
                            var $label = $(
                                "<div class='prof-doc-item d-flex align-items-center mb-1' data-docs-id='" +
                                docsId + "'></div>");
                            $label.append($('<span>').text(fileName).css({
                                'margin-right': '8px'
                            }));
                            $label.append($(
                                '<button type="button" class="btn btn-sm btn-light remove-prof-doc" data-docs-id="' +
                                docsId +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            ));
                            $('#prof_certs_docs_container').append($label);

                            // Link the experience row with this docs id so deletion can clean up both
                            row.attr('data-docs-id', docsId);
                        } catch (err) {
                            console.error('Failed to duplicate experience certificate for docs:',
                                err);
                        }
                    }

                    row.append($('<td>').text(organization).append(hidden));
                    row.append($('<td>').text(designation));
                    row.append($('<td>').text((years ? years : 0) + ' Y, ' + (months ? months : 0) +
                        ' M'));
                    row.append($('<td>').text(responsibilities));
                    row.append($('<td>').text(fileName));
                    row.append($('<td>').html(
                        '<button type="button" class="btn btn-sm btn-light btn-delete-experience"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                    ));

                    // Remove placeholder row if present
                    $('#experienceTableBody').find('.no-experience-row').remove();

                    $('#experienceTableBody').append(row);

                    // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                    $modal.find('form')[0].reset();
                    if (movedFile) {
                        var newFile = $(
                            '<input type="file" class="form-control" id="exp_certificate" name="certificate" accept=".pdf,image/*">'
                        );
                        $modal.find('#existingExpCertificate').before(newFile);
                    }

                    experienceIndex++;

                    $('#experienceModal').modal('hide');
                });

                // Remove experience row
                $('#experienceTableBody').on('click', '.btn-delete-experience', function() {
                    $(this).closest('tr').remove();
                    // if no experience rows remain, show the placeholder
                    if ($('#experienceTableBody').find('.experience-row').length === 0) {
                        $('#experienceTableBody').append(
                            '<tr class="no-experience-row">\n                                                                            <td colspan="6" class="text-center text-muted">\n                                                                                No experience records added yet.\n                                                                            </td>\n                                                                        </tr>'
                        );
                    }
                });

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Auto-add class 'capitalize-title' to inputs that should be title-cased
            $('input').each(function() {
                var $i = $(this);
                var type = ($i.attr('type') || '').toLowerCase();
                var name = ($i.attr('name') || '').toLowerCase();

                // Skip excluded input types
                var excludedTypes = ['email', 'tel', 'date', 'file', 'number', 'hidden', 'checkbox',
                    'radio', 'submit', 'button', 'password'
                ];
                if (type && excludedTypes.indexOf(type) !== -1) return;

                // Skip date-picker, selects and inputs that look like contact details
                if ($i.hasClass('date-picker')) return;
                if (name.match(
                        /email|mobile|phone|password|iban_number|swift_code|currency|docs\[joining\]\[passport_visa\]\[number\]|docs\[joining\]\[iban_letter\]\[number\]|docs\[joining\]\[prof_certs\]\[number\]|docs\[joining\]\[academic\]\[number\]/
                    )) return;


                $i.addClass('capitalize-title');
            });

            // Title-case inputs having class 'capitalize-title'
            $(document).on('input', '.capitalize-title', function() {
                var val = $(this).val() || '';
                // Make everything lowercase first, then uppercase word-start letters
                val = val.toLowerCase().replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
                $(this).val(val);
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            (function() {
                var $form = $('#staffAllForm, #staffAllFormEdit');
                var allowSubmit = false;

                // Move focus to the next logical input on Enter (for inputs/selects only). Textareas and select2 search are excluded.
                $form.on('keydown', 'input, select, textarea, .select2-search__field', function(e) {
                    if (e.key !== 'Enter') return;

                    var $target = $(e.target);

                    // Allow Enter inside textarea for newline
                    if ($target.is('textarea')) return;

                    // If typing in Select2 search, let Select2 handle selection (do not move focus here)
                    if ($target.hasClass('select2-search__field')) return;

                    // Prevent any default submit triggered by Enter
                    e.preventDefault();

                    // Build a list of focusable form controls (visible & enabled), exclude types we don't want to focus
                    var focusable = $form.find('input, select, textarea, button, [tabindex]').filter(
                        ':visible:enabled').filter(function() {
                        var $el = $(this);
                        var t = ($el.attr('type') || '').toLowerCase();
                        if (['hidden', 'submit', 'button', 'file', 'checkbox', 'radio'].indexOf(
                                t) !== -1)
                            return false;
                        return true;
                    });

                    var idx = focusable.index(this);

                    // Find next focusable control
                    var found = false;
                    for (var i = idx + 1; i < focusable.length; i++) {
                        var $next = focusable.eq(i);
                        if ($next.length) {
                            $next.focus();
                            // If it's a Select2 field, open the dropdown search to make selection smooth
                            if ($next.hasClass('js-example-basic-single') || $next.hasClass(
                                    'js-product-select') ||
                                $next.hasClass('js-account-select')) {
                                setTimeout(function($el) {
                                    return function() {
                                        try {
                                            $el.select2('open');
                                        } catch (e) {}
                                    };
                                }($next), 50);
                            }
                            found = true;
                            break;
                        }
                    }

                    // If there is no next field, focus the main Save button for this form
                    if (!found) {
                        var $formEl = $(this).closest('form');
                        var $saveButton = $formEl.find(
                            '#btnSaveOnboard, #btnSaveAll, #btnSaveAllBottom').first();
                        if ($saveButton.length) $saveButton.focus();
                    }

                });

                // Only allow form submission when Save buttons are clicked (not via Enter)
                $('#btnSaveAll, #btnSaveAllBottom, #btnSaveOnboard').on('click', function() {
                    allowSubmit = true;
                    var $btn = $(this);
                    // submit the parent form programmatically in case it's blocked elsewhere
                    setTimeout(function() {
                        $btn.closest('form').trigger('submit');
                    }, 0);
                });

                // Intercept any form submit and block unless allowed explicitly
                $form.on('submit', function(e) {
                    if (!allowSubmit) {
                        e.preventDefault();
                        return false;
                    }




                    // reset flag to prevent accidental resubmits
                    allowSubmit = false;
                    return true;
                });

                // Extra safety: prevent implicit submits from other sources
                // (for example, plugins that call form.submit()), by overriding native submit
                var nativeSubmit = HTMLFormElement.prototype.submit;
                HTMLFormElement.prototype.submit = function() {
                    if ((this.id === 'staffAllForm' || this.id === 'staffAllFormEdit') && !allowSubmit) {
                        // Ignore programmatic submit unless allowed
                        return;
                    }
                    nativeSubmit.apply(this, arguments);
                };
            })();
        });
    </script>

    <script>
        $(document).ready(function() {

            $("#perm_country").on('change', function() {
                console.log("sdsdjsdfjdjkf")
                $("#loading_bg").css("display", "block");
                var country_id = $('#perm_country').val();
                var ajaxUrl = "{{ url('get_state') }}";
                if (!country_id) {
                    // clear dependent selects and stop
                    $('#perm_state').find('option').not(':first').remove();
                    $('#perm_city').find('option').not(':first').remove();
                    $("#loading_bg").css("display", "none");
                    return;
                }
                $.ajax({
                    type: "GET",
                    data: {
                        country_id: country_id
                    },
                    dataType: 'json',
                    url: ajaxUrl,
                    success: function(data) {
                        console.log(data);
                        var a = '';
                        $.each(data, function(i, item) {
                            if (item.length) {

                                $('#perm_state').find('option').not(':first').remove();
                                $('#sectionStateDiv ul').find('li').not(':first')
                                    .remove();

                                $('#perm_city').find('option').not(':first').remove();
                                $('#sectionCityDiv ul').find('li').not(':first')
                                    .remove();

                                $.each(item, function(i, pin) {
                                    $('#perm_state').append($('<option>', {
                                        value: pin.id,
                                        text: pin.name
                                    }));

                                    $("#perm_state_div ul").append(
                                        "<li data-value='" +
                                        pin.id + "' value='" + pin.id +
                                        "' class='option'>" + pin.name +
                                        "</li>");
                                });
                                GLOBAL_STATE_CHANGE_TRIGGER =
                                    true; // Set the flag to true to indicate state change
                                if (window.SELECTED_STATE_ID) {
                                    $('#perm_state').val(window.SELECTED_STATE_ID)
                                        .trigger(
                                            'change');
                                }
                            } else {
                                $('#perm_state_div .current').html('');
                                $('#perm_state').find('option').not(':first').remove();
                                $('#sectionPermStateDiv ul').find('li').not(':first')
                                    .remove();
                                $('#perm_city').find('option').not(':first').remove();
                                $('#sectionPermCityDiv .current').html('');


                            }
                        });
                        console.log(a);
                        $("#loading_bg").css("display", "none");
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {

            $("#curr_country").on('change', function() {
                $("#loading_bg").css("display", "block");
                var country_id = $('#curr_country').val();
                var ajaxUrl = "{{ url('get_state') }}";
                if (!country_id) {
                    // clear dependent selects and stop
                    $('#curr_state').find('option').not(':first').remove();
                    $('#curr_city').find('option').not(':first').remove();
                    $("#loading_bg").css("display", "none");
                    return;
                }
                $.ajax({
                    type: "GET",
                    data: {
                        country_id: country_id
                    },
                    dataType: 'json',
                    url: ajaxUrl,
                    success: function(data) {
                        console.log(data);
                        var a = '';
                        $.each(data, function(i, item) {
                            if (item.length) {

                                $('#curr_state').find('option').not(':first').remove();
                                $('#sectionStateDiv ul').find('li').not(':first')
                                    .remove();

                                $('#curr_city').find('option').not(':first').remove();
                                $('#sectionCurrStateDiv ul').find('li').not(':first')
                                    .remove();

                                $.each(item, function(i, pin) {
                                    $('#curr_state').append($('<option>', {
                                        value: pin.id,
                                        text: pin.name
                                    }));

                                    $("#curr_state_div ul").append(
                                        "<li data-value='" +
                                        pin.id + "' value='" + pin.id +
                                        "' class='option'>" + pin.name +
                                        "</li>");
                                });
                                GLOBAL_STATE_CHANGE_TRIGGER =
                                    true; // Set the flag to true to indicate state change
                                if (window.SELECTED_STATE_ID) {
                                    $('#curr_state').val(window.SELECTED_STATE_ID)
                                        .trigger(
                                            'change');
                                }
                            } else {
                                $('#curr_state_div .current').html('');
                                $('#curr_state').find('option').not(':first').remove();
                                $('#sectionCurrStateDiv ul').find('li').not(':first')
                                    .remove();
                                $('#curr_city').find('option').not(':first').remove();
                                $('#sectionCurrCityDiv .current').html('');


                            }
                        });
                        console.log(a);
                        $("#loading_bg").css("display", "none");
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });

        });
    </script>

    
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

        // Ensure sales targets visibility and field state on initial load
        $(function() {
            try {
                checkRole();
                // sync target sections based on whether sales targets are enabled
                fn_role_id();
                // ensure correct revenue/gp inputs visibility based on selected type
                toggleTargetInputs();
            } catch (e) {
                // fail silently to avoid blocking page if functions are not available
                console.warn('Sales target init skipped:', e);
            }
        });

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

            // Handle focus to remove commas for easier editing
            $('.target-amount-input').on('focus', function() {
                var input = $(this);
                var val = input.val().replace(/,/g, ''); // Remove commas
                if (val === '0.00') {
                    input.val(''); // Optional: make it empty for new input
                } else {
                    input.val(val);
                }
            });

            // Format target amount inputs on blur to ensure .00 format
            $('.target-amount-input').on('blur', function() {
                var input = $(this);
                var val = input.val().replace(/,/g, ''); // Remove commas for parsing
                var numVal = parseFloat(val);

                if (!isNaN(numVal) && numVal !== 0) {
                    var formatted = numVal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    input.val(formatted);
                } else {
                    input.val('0.00'); // Default for empty or invalid input
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.js-example-basic-single').select2({
                width: '100%'
            });


            // When main company changes, copy the selected company name into #work_location input
            $('#main_company').on('change', function() {
                const txt = $(this).find('option:selected').text() || '';
                $('#work_location').val(txt.trim());
            });


               $('#main_company').on('change', function() {

                console.log('Main company changed, fetching shift & weekly off...');

                const companyId = $(this).val();
                const txt = $(this).find('option:selected').text() || '';
                var $weeklyOffSelect = $('select[name="hr_weekly_off[]"]');
                var prevWeekly = $weeklyOffSelect.val();

                // copy company name
                $('#work_location').val(txt.trim());

                if (!companyId) return;

                $.ajax({
                    url: "{{ route('company.getshift') }}",
                    type: "GET",
                    data: {
                        company_id: companyId
                    },
                    success: function(res) {

                        console.log('Received shift & weekly off data:', res);

                        /* ---------------------------
                           Set shift dropdown
                        --------------------------- */
                        if (Array.isArray(res.shifts) && res.shifts.length > 0) {
                            var $shiftSelect = $('#shift_id');
                            var prevShift = $shiftSelect.val();
                            $shiftSelect.empty();
                            res.shifts.forEach(function(shift) {
                                var option = $('<option>', {
                                    value: shift.id,
                                    text: shift.shift_name + ' (' + (shift.start_time || '') + ' - ' + (shift.end_time || '') + ')'
                                });
                                if (String(shift.id) === String(prevShift)) {
                                    option.prop('selected', true);
                                }
                                $shiftSelect.append(option);
                            });
                            if (!$shiftSelect.val()) {
                                $shiftSelect.val(res.shifts[0].id);
                            }
                            $shiftSelect.trigger('change').trigger('change.select2');
                        } else {
                            $('#shift_id').empty().trigger('change').trigger('change.select2');
                        }

                        /* ---------------------------
                           Weekly off logic
                        --------------------------- */
                        var $weeklyOffSelect = $('select[name="hr_weekly_off[]"]');

                        // If server provides weekly off options, update them but try to preserve any existing selections
                        if (Array.isArray(res.weekly_off_days) && res.weekly_off_days.length > 0) {

                            console.log('Setting weekly off days:', res.weekly_off_days);

                            // Build list of new option values for quick lookup
                            const newVals = res.weekly_off_days.map(d => String(d.id));

                            // Replace options
                            $weeklyOffSelect.empty();
                            res.weekly_off_days.forEach(function(day) {
                                const option = $('<option>', {
                                    value: day.id,
                                    text: day.name
                                });
                                $weeklyOffSelect.append(option);
                            });

                            // Restore intersection of previous selections and new options (handles single or multi-select)
                            const prevArray = Array.isArray(prevWeekly) ? prevWeekly : [prevWeekly];
                            const restore = prevArray.filter(Boolean).map(String).filter(v => newVals.includes(v));
                            if (restore.length) {
                                $weeklyOffSelect.val(restore);
                            }

                            // Trigger change event to update any dependent UI
                            $weeklyOffSelect.trigger('change');

                        } else {
                            console.log('No weekly off days received from server. Preserving existing weekly off selections.');
                            // Do not clear the select to avoid overwriting current user selection
                        }

                    },
                    error: function() {
                        console.error('Failed to fetch company shift & weekly off');
                    }
                });

               

            });

              /* -----------------------------------
               🔥 TRIGGER ON FIRST LOAD (ONLY ONCE)
            ----------------------------------- */
            if ($('#main_company').val()) {
                $('#main_company').trigger('change');
            }

            // Initialize work_location based on current selection (if any)
            (function initWorkLocation() {
                const sel = $('#main_company option:selected').text() || '';
                if (sel.trim()) $('#work_location').val(sel.trim());
            })();

            // Load designations for a department (dependency dropdown)
            window.loadDesignationsForDepartment = function loadDesignationsForDepartment(deptId, selectedId =
                null) {
                const $des = $('#designation_id');
                if (!$des.length) return;

                $des.prop('disabled', true).html('<option>Loading...</option>');

                if (!deptId) {
                    $des.html('<option value="">-Select-</option>').prop('disabled', false).trigger('change');
                    return;
                }

                $.get("{{ url('get-designations-by-department') }}/" + deptId)
                    .done(function(resp) {
                        $des.empty().append($('<option>', {
                            value: '',
                            text: '-Select-'
                        }));
                        if (resp && resp.success && resp.designations && resp.designations.length) {
                            resp.designations.forEach(function(d) {
                                $des.append($('<option>', {
                                    value: d.id,
                                    text: d.title
                                }));
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

            // Add/Remove rows for Others documents
            $('#addOtherDoc').on('click', function() {
                // Prevent adding a new empty row if the last row is completely empty
                var $last = $('#otherDocsTable tbody tr').last();
                if ($last.length) {
                    var hasValue = false;
                    $last.find('input[type="text"]').each(function() {
                        if ($(this).val().trim() !== '') hasValue = true;
                    });
                    $last.find('input[type="file"]').each(function() {
                        if (this.files && this.files.length > 0) hasValue = true;
                    });
                    if (!hasValue) return; // don't add another empty row
                }

                var idx = $('#otherDocsTable tbody tr').length;
                var row = '<tr>' +
                    '<td><input type="text" class="form-control" name="docs[others][' + idx +
                    '][name]"></td>' +
                    '<td><input type="text" class="form-control" name="docs[others][' + idx +
                    '][number]"></td>' +
                    '<td><input type="file" accept=".pdf,image/*" class="form-control" name="docs[others][' +
                    idx +
                    '][file]"></td>' +
                    '<td><input type="text" class="form-control" name="docs[others][' + idx +
                    '][remarks]"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-light text-dark btn-sm delOtherRow"><i class="ico icon-outline-trash-bin-minimalistic"></i></button></td>' +
                    '</tr>';
                $('#otherDocsTable tbody').append(row);
            });

            // Prevent double-submit on the main staff forms (legacy & edit)
            $('#staffAllForm, #staffAllFormEdit').on('submit', function() {
                var $btn = $(this).find('button[type="submit"]');
                if ($btn.length) {

                    $btn.data('orig-text', $btn.html());
                    $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                }
            });

            $(document).on('click', '.delOtherRow', function() {
                $(this).closest('tr').remove();
                // re-index others input names to keep sequential indexes
                $('#otherDocsTable tbody tr').each(function(i, tr) {
                    $(tr).find('input, select, textarea').each(function() {
                        var name = $(this).attr('name');
                        if (!name) return;
                        name = name.replace(/docs\[others\]\[\d+\]/, 'docs[others][' + i +
                            ']');
                        $(this).attr('name', name);
                    });
                });
            });

        });
    </script>



    <!-- Password validator styles + script -->
    <style>
        .password-validator {
            position: fixed;
            /* use fixed so it positions relative to viewport */
            z-index: 2500;
            min-width: 260px;
            max-width: 90vw;
            /* responsive */
            box-sizing: border-box;
            background: linear-gradient(180deg, #ffffff 0%, #fbfbfd 100%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            padding: 12px 14px;
            font-size: 13px;
            color: #111827;
            transition: transform 160ms ease, opacity 160ms ease;
        }

        .password-validator .pv-arrow {
            position: absolute;
            width: 12px;
            height: 12px;
            transform: rotate(45deg);
            background: #fff;
            left: 16px;
            top: -7px;
            border-left: 1px solid rgba(0, 0, 0, 0.06);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .password-validator .pv-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--bs-gray-900, #111827);
            font-size: 14px
        }

        .pv-progress {
            height: 6px;
            background: rgba(15, 23, 42, 0.06);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .pv-progress-bar {
            width: 0%;
            height: 100%;
            background: #ef4444;
            border-radius: 6px;
            transition: width 240ms ease, background-color 240ms ease
        }

        .password-validator .pv-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: block
        }

        .password-validator .pv-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            color: #6b7280;
            font-size: 13px
        }

        .pv-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            font-size: 11px;
            line-height: 1
        }

        .pv-item.valid .pv-dot {
            background: #16a34a;
            color: #fff
        }

        .pv-item.valid .pv-dot::after {
            content: '\2713';
            font-size: 11px
        }

        .pv-item.invalid .pv-dot {
            background: rgba(0, 0, 0, 0.06);
            color: transparent
        }

        .pv-text {
            color: #374151
        }

        .pv-tip {
            margin-top: 8px;
            font-size: 12px
        }

        .password-validator.d-none {
            display: none;
        }
    </style>

    <script>
        (function($) {
            var $win = $(window);

            function updatePosition($input, $box) {
                if (!$input.length || !$box.length) return;
                var rect = $input[0].getBoundingClientRect();
                var boxW = $box.outerWidth();
                var boxH = $box.outerHeight();
                var margin = 8;
                var top = rect.bottom + margin;
                var left = rect.left;
                var viewportW = window.innerWidth || document.documentElement.clientWidth;
                var viewportH = window.innerHeight || document.documentElement.clientHeight;

                // Shift left if overflowing right edge
                if (left + boxW > viewportW - margin) {
                    left = Math.max(margin, viewportW - boxW - margin);
                }

                // If bottom overflows, place above input if possible
                if (top + boxH > viewportH - margin) {
                    var alt = rect.top - boxH - margin;
                    if (alt > margin) {
                        top = alt;
                    } else {
                        // clamp inside viewport
                        top = Math.max(margin, viewportH - boxH - margin);
                    }
                }

                // Apply fixed positioning so it stays visible with scrolling
                $box.css({
                    position: 'fixed',
                    top: Math.round(top) + 'px',
                    left: Math.round(left) + 'px'
                });
            }

            function checkPassword(p) {
                return {
                    length: p.length >= 6,
                    lower: /[a-z]/.test(p),
                    upper: /[A-Z]/.test(p),
                    digit: /\d/.test(p),
                    // exclude whitespace so spaces are not considered special characters
                    special: /[^A-Za-z0-9\s]/.test(p)
                };
            }
            $(function() {
                var $input = $('#password');
                var $box = $('#password-validator');
                if (!$input.length || !$box.length) return;

                // show on focus / typing, hide on blur or when all valid (after short delay)
                $input.on('focus input', function(e) {
                    // show with subtle animation
                    $box.removeClass('d-none').css({
                        opacity: 0,
                        transform: 'translateY(6px)'
                    }).animate({
                        opacity: 1,
                        opacity: 1
                    }, 120, function() {
                        $box.css('transform', 'translateY(0)');
                    });
                    updatePosition($input, $box);
                    var p = $input.val() || '';
                    var state = checkPassword(p);
                    var total = 5;
                    var matched = 0;
                    $box.find('[data-criteria]').each(function() {
                        var c = $(this).data('criteria');
                        var ok = !!state[c];
                        $(this).toggleClass('valid', ok).toggleClass('invalid', !ok);
                        if (ok) matched++;
                    });
                    var pct = Math.round((matched / total) * 100);
                    $box.find('.pv-progress-bar').css({
                        width: pct + '%'
                    });
                    // set color and title by strength
                    if (pct === 100) {
                        $box.find('.pv-progress-bar').css('background', '#16a34a');
                        $box.find('.pv-title').text('Excellent password');
                    } else if (pct >= 75) {
                        $box.find('.pv-progress-bar').css('background', '#65a30d');
                        $box.find('.pv-title').text('Strong password');
                    } else if (pct >= 50) {
                        $box.find('.pv-progress-bar').css('background', '#f59e0b');
                        $box.find('.pv-title').text('Weak password');
                    } else {
                        $box.find('.pv-progress-bar').css('background', '#ef4444');
                        $box.find('.pv-title').text('Choose a stronger password');
                    }
                    if (pct === 100) {
                        setTimeout(function() {
                            $box.addClass('d-none');
                        }, 700);
                    }
                });

                $input.on('blur', function() {
                    setTimeout(function() {
                        $box.addClass('d-none');
                    }, 250);
                });

                // reposition on window resize / scroll while visible
                $win.on('resize scroll', function() {
                    if (!$box.hasClass('d-none')) updatePosition($input, $box);
                });
            });
        })(jQuery);
    </script>

    <script>
        function isValidPassword(password) {
            const minLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasDigit = /\d/.test(password);
            const hasSpecial = /[^A-Za-z0-9\s]/.test(password); // ❌ excludes space

            return minLength && hasUpper && hasLower && hasDigit && hasSpecial;
        }

        // 🚫 Prevent space from being entered
        $(document).on('keydown', '#password', function(e) {
            if (e.key === ' ') {
                e.preventDefault();
            }
        });

        // 🔄 Realtime validation (typing)
        $(document).on('input', '#password', function() {
            const val = $(this).val();
            const valid = isValidPassword(val);

            $(this).toggleClass('is-invalid', !valid);
            $(this).toggleClass('is-valid', valid);
        });

        // ⛔ Final submit guard (VERY IMPORTANT)
        $(document).on('submit', 'form', function(e) {
            const password = $('#password').val();

            if (password && !isValidPassword(password)) {
                e.preventDefault();

                toastr.error(
                    'Password must have at least 8 characters, 1 uppercase, 1 lowercase, 1 digit and 1 special character (no spaces).'
                );

                $('#password')
                    .addClass('is-invalid')
                    .focus();

                return false;
            }
        });
    </script>

    <script>
        $('#bankForm').on('keydown', 'input, select, textarea', function(e) {
            if (e.key !== 'Enter') return;
            var $el = $(this);
            // allow Enter inside textarea for newline
            if ($el.is('textarea')) return;
            e.preventDefault();

            var $focusables = $('#bankForm').find('input, select, textarea')
                .filter(':visible:enabled')
                .not(
                    '[type="file"], [type="hidden"], [type="checkbox"], [type="radio"], [type="submit"], [type="button"]'
                );

            var idx = $focusables.index(this);
            if (idx === -1) return;

            if (idx + 1 < $focusables.length) {
                $focusables.eq(idx + 1).focus();
            } else {
                // We're at the last field — click the Save button
                // $('#btn_save_bank').trigger('click');
            }
        });

        // Keyboard navigation inside Experience Modal: Enter jumps to next field and triggers Save on last field
        $('#experienceForm').on('keydown', 'input, select, textarea', function(e) {
            if (e.key !== 'Enter') return;
            var $el = $(this);
            // allow Enter inside textarea for newline
            if ($el.is('textarea')) return;
            e.preventDefault();

            var $focusables = $('#experienceForm').find('input, select, textarea')
                .filter(':visible:enabled')
                .not(
                    '[type="file"], [type="hidden"], [type="checkbox"], [type="radio"], [type="submit"], [type="button"]'
                );

            var idx = $focusables.index(this);
            if (idx === -1) return;

            if (idx + 1 < $focusables.length) {
                var $next = $focusables.eq(idx + 1);
                $next.focus();
                // if next is a Select2, open it
                if ($next.hasClass('js-example-basic-single')) {
                    setTimeout(function() {
                        $next.select2('open');
                    }, 60);
                }
            } else {
                // We're at the last field — click the Save button
                // $('#btn_save_experience').trigger('click');
            }
        });
    </script>

    <script>
        (function() {

            let dragging = false;
            let startX, startY, startLeft, startTop;
            let currentModal = null;

            // Bind drag start
            $(document).on('mousedown', '.modal-dialog .modal-header', function(e) {
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
            $(document).on('mousemove', function(e) {
                if (!dragging || !currentModal) return;

                let newLeft = startLeft + (e.clientX - startX);
                let newTop = startTop + (e.clientY - startY);

                currentModal.offset({
                    top: newTop,
                    left: newLeft
                });
            });

            // Stop drag
            $(document).on('mouseup', function() {
                dragging = false;
                $('body').removeClass('unselectable');
            });

            // Reset modal on open (production behavior)
            $(document).on('show.bs.modal', '.modal', function() {
                let dialog = $(this).find('.modal-dialog.draggable');
                dialog.css({
                    top: '10%',
                    // left: '65%',
                    transform: 'translateX(-50%)'
                });
            });

        })();
    </script>

    {{-- Auto-lowercase email inputs (live + on submit) --}}
    <script>
        (function() {
            // Lowercase as user types, preserve cursor position when possible
            $(document).on('input', 'input[type="email"]', function() {
                var el = this;
                var val = el.value || '';
                var lower = val.toLowerCase();
                if (val !== lower) {
                    try {
                        var start = el.selectionStart;
                        var end = el.selectionEnd;
                        el.value = lower;
                        el.setSelectionRange(start, end);
                    } catch (e) {
                        // fallback if selection not supported
                        el.value = lower;
                    }
                }
            });

            // Extra safety: ensure all email inputs are lowercased before any form submit
            $(document).on('submit', 'form', function() {
                $(this).find('input[type="email"]').each(function() {
                    this.value = (this.value || '').toLowerCase();
                });

                // Also uppercase IBAN/SWIFT fields before submit as a safety net
                $(this).find('#iban_number, #swift_code').each(function() {
                    this.value = (this.value || '').toUpperCase();
                });
            });

            // ---------- Uppercase IBAN and SWIFT on input (live) ----------
            $(document).on('input', '#iban_number, #swift_code', function() {
                var el = this;
                var val = el.value || '';
                var upper = val.toUpperCase();
                if (val !== upper) {
                    try {
                        var s = el.selectionStart;
                        var e = el.selectionEnd;
                        el.value = upper;
                        el.setSelectionRange(s, e);
                    } catch (err) {
                        el.value = upper;
                    }
                }
            });
        })();
    </script>

    <script>
        // Mirror staff photo into docs[joining][photo][file] in edit form and show preview (also update visible staff preview)
        $(document).ready(function() {
            var staffInput = document.querySelector('input[name="staff_photo"]');
            var joiningInput = document.querySelector('input[name="docs[joining][photo][file]"]');

            var joiningPreview = document.getElementById('joining_photo_preview_edit');
            var lastJoiningPreviewUrl = null;

            var staffPreview = document.getElementById('staff_photo_preview');
            var lastStaffPreviewUrl = null;

            function clearJoiningPreview() {
                if (lastJoiningPreviewUrl) {
                    try {
                        URL.revokeObjectURL(lastJoiningPreviewUrl);
                    } catch (e) {}
                    lastJoiningPreviewUrl = null;
                }
                if (joiningPreview) {
                    joiningPreview.src = '';
                    joiningPreview.style.display = 'none';
                }
            }

            function updateJoiningPreviewFromFile(file) {
                if (!joiningPreview) return;
                if (!file) {
                    clearJoiningPreview();
                    return;
                }
                if (file.type && file.type.indexOf('image/') === 0) {
                    var url = URL.createObjectURL(file);
                    clearJoiningPreview();
                    lastJoiningPreviewUrl = url;
                    joiningPreview.src = url;
                    joiningPreview.style.display = 'block';
                } else {
                    clearJoiningPreview();
                }
            }

            function clearStaffPreview() {
                if (lastStaffPreviewUrl) {
                    try {
                        URL.revokeObjectURL(lastStaffPreviewUrl);
                    } catch (e) {}
                    lastStaffPreviewUrl = null;
                }
                if (staffPreview) {
                    var orig = staffPreview.getAttribute('data-original') || '';
                    staffPreview.src = orig;
                    staffPreview.style.display = orig ? 'inline-block' : 'none';
                }
            }

            function updateStaffPreviewFromFile(file) {
                if (!staffPreview) return;
                if (!file) {
                    clearStaffPreview();
                    return;
                }
                if (file.type && file.type.indexOf('image/') === 0) {
                    var url = URL.createObjectURL(file);
                    clearStaffPreview();
                    lastStaffPreviewUrl = url;
                    staffPreview.src = url;
                    staffPreview.style.display = 'inline-block';
                } else {
                    clearStaffPreview();
                }
            }

            if (staffInput && joiningInput) {
                $(staffInput).on('change', function() {
                    if (staffInput.files && staffInput.files.length > 0) {
                        var file = staffInput.files[0];
                        try {
                            var dt = new DataTransfer();
                            dt.items.add(file);
                            joiningInput.files = dt.files;
                        } catch (err) {
                            console.error('Failed to duplicate staff photo into joining docs input:', err);
                        }

                        // remove existing hidden path so server knows this replaces it
                        $('input[name="docs[joining][photo][existing]"]').remove();

                        updateJoiningPreviewFromFile(file);
                        updateStaffPreviewFromFile(file);
                    } else {
                        try {
                            var dt = new DataTransfer();
                            joiningInput.files = dt.files;
                        } catch (e) {}
                        clearJoiningPreview();
                        clearStaffPreview();
                    }
                });

                $(joiningInput).on('change', function() {
                    if (joiningInput.files && joiningInput.files.length > 0) {
                        // if user manually selects, also remove existing hidden path
                        $('input[name="docs[joining][photo][existing]"]').remove();
                        updateJoiningPreviewFromFile(joiningInput.files[0]);
                        updateStaffPreviewFromFile(joiningInput.files[0]);
                    } else {
                        clearJoiningPreview();
                        clearStaffPreview();
                    }
                });

                // When user selects files directly in the visible IBAN docs input, list them and support per-file removal
                $(document).on('change', '#docs_joining_iban_file', function() {
                    var $container = $('#iban_letter_docs_container');
                    // remove only manual-selected items (preserve bank-added docs with data-docs-id)
                    $container.find('.iban-doc-item[data-manual-id]').remove();
                    var files = this.files || [];
                    for (var i = 0; i < files.length; i++) {
                        (function(idx, f) {
                            var id = 'manual_iban_' + idx + '_' + Date.now();
                            var $item = $(
                                "<div class='iban-doc-item d-flex align-items-center mb-1' data-manual-id='" +
                                id + "' data-index='" + idx + "'></div>");
                            $item.append($('<span>').text(f.name).css({
                                'margin-right': '8px'
                            }));
                            var $btn = $(
                                '<button type="button" class="btn btn-sm btn-light remove-manual-iban" data-index="' +
                                idx +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            );
                            $item.append($btn);
                            $container.append($item);
                        })(i, files[i]);
                    }
                });

                // Remove a manual IBAN doc selected in the visible input (rebuild FileList without that index)
                $(document).on('click', '.remove-manual-iban', function() {
                    var idx = parseInt($(this).data('index'));
                    var input = document.getElementById('docs_joining_iban_file');
                    if (!input) return;
                    var dt = new DataTransfer();
                    for (var i = 0; i < input.files.length; i++) {
                        if (i === idx) continue; // skip removed
                        dt.items.add(input.files[i]);
                    }
                    input.files = dt.files;
                    // trigger change to refresh the listing
                    $(input).trigger('change');
                });

                // Allow user to manually remove a duplicated IBAN doc entry (clear hidden input and UI)
                $(document).on('click', '.remove-iban-doc', function() {
                    var id = $(this).data('docs-id');
                    if (id) {
                        $('#' + id).remove();
                        $(this).closest('.iban-doc-item').remove();
                    }
                });

                // Academic docs: manual selection listing
                $(document).on('change', '#docs_joining_academic_file', function() {
                    var $container = $('#academic_docs_container');
                    // remove only manual-selected items (preserve education-added docs with data-docs-id)
                    $container.find('.academic-doc-item[data-manual-id]').remove();
                    var files = this.files || [];
                    for (var i = 0; i < files.length; i++) {
                        (function(idx, f) {
                            var id = 'manual_acad_' + idx + '_' + Date.now();
                            var $item = $(
                                "<div class='academic-doc-item d-flex align-items-center mb-1' data-manual-id='" +
                                id + "' data-index='" + idx + "'></div>");
                            $item.append($('<span>').text(f.name).css({
                                'margin-right': '8px'
                            }));
                            var $btn = $(
                                '<button type="button" class="btn btn-sm btn-light remove-manual-academic" data-index="' +
                                idx +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            );
                            $item.append($btn);
                            $container.append($item);
                        })(i, files[i]);
                    }
                });

                // Remove a manual Academic doc selected in the visible input (rebuild FileList without that index)
                $(document).on('click', '.remove-manual-academic', function() {
                    var idx = parseInt($(this).data('index'));
                    var input = document.getElementById('docs_joining_academic_file');
                    if (!input) return;
                    var dt = new DataTransfer();
                    for (var i = 0; i < input.files.length; i++) {
                        if (i === idx) continue; // skip removed
                        dt.items.add(input.files[i]);
                    }
                    input.files = dt.files;
                    // trigger change to refresh the listing
                    $(input).trigger('change');
                });

                // Allow user to manually remove a duplicated Academic doc entry (clear hidden input and UI)
                $(document).on('click', '.remove-academic-doc', function() {
                    var id = $(this).data('docs-id');
                    if (id) {
                        $('#' + id).remove();
                        $(this).closest('.academic-doc-item').remove();
                    }
                });

                $(document).on('reset', '#staffAllFormEdit', function() {
                    try {
                        var dt = new DataTransfer();
                        joiningInput.files = dt.files;
                    } catch (e) {}
                    clearPreview();

                    // remove any docs IBAN hidden inputs added when banks were added
                    $('#bankInputsContainer').empty();
                    $('#iban_letter_docs_container').empty();
                    $('#academic_docs_container').empty();
                    $('#prof_certs_docs_container').empty();
                    $('#police_noc_docs_container').empty();
                    $('#relieving_letter_docs_container').empty();

                    // also clear any manual-selected file input
                    var docsIban = document.getElementById('docs_joining_iban_file');
                    if (docsIban) docsIban.value = '';
                    var docsAcad = document.getElementById('docs_joining_academic_file');
                    if (docsAcad) docsAcad.value = '';
                    var docsProf = document.getElementById('docs_joining_prof_certs_file');
                    if (docsProf) docsProf.value = '';
                    var docsPolice = document.getElementById('docs_joining_police_noc_file');
                    if (docsPolice) docsPolice.value = '';
                    var docsRel = document.getElementById('docs_joining_relieving_letter_file');
                    if (docsRel) docsRel.value = '';
                });

                // Professional certs: manual selection listing
                $(document).on('change', '#docs_joining_prof_certs_file', function() {
                    var $container = $('#prof_certs_docs_container');
                    // remove only manual-selected items (preserve experience-added docs with data-docs-id)
                    $container.find('.prof-doc-item[data-manual-id]').remove();
                    var files = this.files || [];
                    for (var i = 0; i < files.length; i++) {
                        (function(idx, f) {
                            var id = 'manual_prof_' + idx + '_' + Date.now();
                            var $item = $(
                                "<div class='prof-doc-item d-flex align-items-center mb-1' data-manual-id='" +
                                id + "' data-index='" + idx + "'></div>");
                            $item.append($('<span>').text(f.name).css({
                                'margin-right': '8px'
                            }));
                            var $btn = $(
                                '<button type="button" class="btn btn-sm btn-light remove-manual-prof" data-index="' +
                                idx +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                            );
                            $item.append($btn);
                            $container.append($item);
                        })(i, files[i]);
                    }
                });

                // Remove a manual Professional cert selected in the visible input (rebuild FileList without that index)
                $(document).on('click', '.remove-manual-prof', function() {
                    var idx = parseInt($(this).data('index'));
                    var input = document.getElementById('docs_joining_prof_certs_file');
                    if (!input) return;
                    var dt = new DataTransfer();
                    for (var i = 0; i < input.files.length; i++) {
                        if (i === idx) continue; // skip removed
                        dt.items.add(input.files[i]);
                    }
                    input.files = dt.files;
                    // trigger change to refresh the listing
                    $(input).trigger('change');
                });

                // Allow user to manually remove a duplicated Professional cert doc entry (clear hidden input and UI)
                $(document).on('click', '.remove-prof-doc', function() {
                    var id = $(this).data('docs-id');
                    if (id) {
                        $('#' + id).remove();
                        $(this).closest('.prof-doc-item').remove();
                    }
                });

                // When user selects files directly in the visible Relieving docs input, list them and support per-file removal
                $(document).on('change', '#docs_joining_relieving_letter_file', function() {
                    var $container = $('#relieving_letter_docs_container');
                    // remove only manual-selected items (preserve existing server-saved docs)
                    $container.find('.relieving-doc-item[data-manual-id]').remove();
                    var files = this.files || [];
                    for (var i = 0; i < files.length; i++) {
                        (function(idx, f) {
                            var id = 'manual_relieve_' + idx + '_' + Date.now();
                            var $item = $(
                                "<div class='relieving-doc-item d-flex align-items-center mb-1' data-manual-id='" +
                                id + "' data-index='" + idx + "'></div>");
                            $item.append($('<span>').text(f.name).css({
                                'margin-right': '8px'
                            }));
                            var $btn = $(
                                '<button type="button" class="btn btn-sm btn-light remove-manual-relieving" data-index="' +
                                idx +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            $item.append($btn);
                            $container.append($item);
                        })(i, files[i]);
                    }
                });

                // Remove a manual Relieving doc selected in the visible input (rebuild FileList without that index)
                $(document).on('click', '.remove-manual-relieving', function() {
                    var idx = parseInt($(this).data('index'));
                    var input = document.getElementById('docs_joining_relieving_letter_file');
                    if (!input) return;
                    var dt = new DataTransfer();
                    for (var i = 0; i < input.files.length; i++) {
                        if (i === idx) continue; // skip removed
                        dt.items.add(input.files[i]);
                    }
                    input.files = dt.files;
                    // trigger change to refresh the listing
                    $(input).trigger('change');
                });

                // When user selects a Police NOC file in the visible input, list it (single file) and allow removal
                $(document).on('change', '#docs_joining_police_noc_file', function() {
                    var $container = $('#police_noc_docs_container');
                    $container.find('.police-doc-item[data-manual-id]').remove();
                    var files = this.files || [];
                    for (var i = 0; i < files.length; i++) {
                        (function(idx, f) {
                            var id = 'manual_police_' + idx + '_' + Date.now();
                            var $item = $(
                                "<div class='police-doc-item d-flex align-items-center mb-1' data-manual-id='" +
                                id + "' data-index='" + idx + "'></div>");
                            $item.append($('<span>').text(f.name).css({
                                'margin-right': '8px'
                            }));
                            var $btn = $(
                                '<button type="button" class="btn btn-sm btn-light remove-manual-police" data-index="' +
                                idx +
                                '"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            $item.append($btn);
                            $container.append($item);
                        })(i, files[i]);
                    }
                });

                // Remove a manual Police NOC file selected in the visible input (rebuild FileList without that index)
                $(document).on('click', '.remove-manual-police', function() {
                    var idx = parseInt($(this).data('index'));
                    var input = document.getElementById('docs_joining_police_noc_file');
                    if (!input) return;
                    var dt = new DataTransfer();
                    for (var i = 0; i < input.files.length; i++) {
                        if (i === idx) continue; // skip removed
                        dt.items.add(input.files[i]);
                    }
                    input.files = dt.files;
                    // trigger change to refresh the listing
                    $(input).trigger('change');
                });

            } // end if (staffInput && joiningInput)

            // If a file was already present in the docs input on load (unlikely in edit), preview it
            if (joiningInput && joiningInput.files && joiningInput.files.length > 0) {
                updatePreviewFromFile(joiningInput.files[0]);
            }
        });

         // Auto-set probation end date = joining date + 6 months
        (function() {
            var $join = $('input[name="date_of_joining_2"]');
            var $prob = $('input[name="probation_end_date"]');

            function parseDMY(str) {
                if (!str) return null;
                // support d/m/Y or Y-m-d (ISO)
                if (str.indexOf('/') !== -1) {
                    var parts = str.split('/');
                    if (parts.length !== 3) return null;
                    var d = parseInt(parts[0], 10);
                    var m = parseInt(parts[1], 10) - 1;
                    var y = parseInt(parts[2], 10);
                    var dt = new Date(y, m, d);
                    if (isNaN(dt.getTime())) return null;
                    return dt;
                }
                var dt2 = new Date(str);
                return isNaN(dt2.getTime()) ? null : dt2;
            }

            function formatDMY(d) {
                var dd = String(d.getDate()).padStart(2, '0');
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var yyyy = d.getFullYear();
                return dd + '/' + mm + '/' + yyyy;
            }

            function addMonths(date, months) {
                var d = new Date(date.getTime());
                var day = d.getDate();
                d.setMonth(d.getMonth() + months);
                // if month overflow occured (e.g., Jan 31 -> Mar 3), set to last day of previous month
                if (d.getDate() < day) {
                    d.setDate(0);
                }
                return d;
            }

            function updateProbation() {
                var val = $join.val() || '';
                var dt = parseDMY(val.trim());
                if (!dt) {
                    // clear probation if no joining date
                    if ($prob.length) {
                        if ($prob[0] && $prob[0]._flatpickr) {
                            $prob[0]._flatpickr.clear();
                        } else {
                            $prob.val('');
                        }
                    }
                    return;
                }
                var end = addMonths(dt, 6);
                var formatted = formatDMY(end);
                if ($prob.length) {
                    if ($prob[0] && $prob[0]._flatpickr) {
                        try {
                            $prob[0]._flatpickr.setDate(end, true, 'd/m/Y');
                        } catch (e) {
                            $prob.val(formatted);
                        }
                    } else {
                        $prob.val(formatted).trigger('change');
                    }
                }
            }

            // Bind events
            $join.on('change blur input', updateProbation);
            // If flatpickr instance exists, hook its onChange
            function hookFlatpickr(el) {
                if (!el || !el._flatpickr) return;
                var inst = el._flatpickr;
                if (inst && inst.config && Array.isArray(inst.config.onChange)) {
                    inst.config.onChange.push(function() {
                        updateProbation();
                    });
                }
            }
            hookFlatpickr($join[0]);
            // if flatpickr not ready yet, poll briefly
            var tries = 0;
            var timer = setInterval(function() {
                if ($join[0] && $join[0]._flatpickr) {
                    hookFlatpickr($join[0]);
                    clearInterval(timer);
                    return;
                }
                if (++tries > 20) clearInterval(timer);
            }, 200);

            // initialize once
            updateProbation();

        })();
    </script>
@endsection

<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        {{ $employee->staff_no }}
    </h4>
    <div class="purchase-order-content-header-right">

        <style>
            #stf-details label {
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #stf-details .form-control-plaintext {
                text-align: center !important;
            }
        </style>

        <a href="{{ url('hrms/staff/' . $employee->id . '/edit') }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <a href="{{ url('add-staff') }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>





        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">


                <li><a href="{{ url('onboarding-employee-list') }}"
                        class="dropdown-item d-flex align-items-center text-dark"><i
                            class="ico icon-outline-document-text text-success  title-15 me-2"></i> Onboard Employee
                        List</a>
                </li>

                <li><a data-copy-url="{{ url('onboard-employee/' . session('logged_session_data.company_id')) }}"
                        title="Click to copy link"
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i
                            class="ico icon-outline-user-plus text-success  title-15 me-2"></i> Onboard Employee
                        Link</a>
                </li>


            </ul>
        </div>


    </div>
</div>

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

<div class="card mb-3">
    <div class="card-body" style="padding: 0.5rem 0.5rem">

        <div class="row">
            <div class="col-2 d-flex align-items-center justify-content-center">
                <div class="staff-photo d-flex align-items-center justify-content-center rounded-circle overflow-hidden bg-light"
                    style="width:120px;max-width:100%;height:120px;">
                    @if (!empty($employee->staff_photo))
                        @php
                            $photoUrl = null;
                            // If path starts with 'public/' it's already accessible via asset()
if (strpos($employee->staff_photo, 'public/') === 0) {
    $photoUrl = asset($employee->staff_photo);
} elseif (Storage::disk('public')->exists($employee->staff_photo)) {
    $photoUrl = asset('storage/app/public/' . $employee->staff_photo);
} elseif (file_exists(public_path($employee->staff_photo))) {
    $photoUrl = asset($employee->staff_photo);
} else {
    $photoUrl = asset('public/uploads/staff/demo/staff.png');
                            }
                        @endphp
                        <img src="{{ $photoUrl }}" alt="Staff Photo"
                            style="width:100%;height:100%;object-fit:cover;display:block;">
                    @else
                        <span class="text-muted">No Photo</span>
                    @endif
                </div>
            </div>

            <div class="col-10">
                <div class="row row-cols-5">
                    <div class="col">
                        <label class="form-label">First Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                        {{ $employee->employee_salutation ? $employee->employee_salutation . '.' : '' }} {{ $employee->first_name_full ?? '' }}

                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Last Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $employee->last_name ?? '' }}
                        </div>
                    </div>





                    <div class="col">
                        <label class="form-label">Email</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $employee->email ?? '' }}
                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Mobile</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $employee->mobile ?? '' }}
                        </div>
                    </div>



                    <div class="col mb-3">
                        <label class="form-label">Gender</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            @php $genders = [1 => 'Male', 2 => 'Female', 3 => 'Other']; @endphp
                            {{ $genders[$employee->gender_id ?? null] ?? '' }}
                        </div>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label">Blood Group</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ ucfirst($employee->blood_group ?? '') }}
                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Marital Status</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ ucfirst($employee->marital_status ?? '') }}
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">DOB</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ @App\SysHelper::normalizeToDmy($employee->date_of_birth) }}
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Place of Birth</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ ucfirst($employee->place_of_birth ?? '') }}
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Religion</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ ucfirst($employee->religion ?? '') }}
                        </div>
                    </div>

                    {{-- <div class="col-2">
                        <label class="form-label">Current Address</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ trim(implode(', ', array_filter([
                                $employee->current_building_no,
                                $employee->current_area,
                                $employee->current_city,
                                optional(App\SysStates::find($employee->current_state))->name ?? $employee->current_state
                            ]))) }}

                        </div>

                    </div>

                    <div class="col">
                        <label class="form-label">Permanent Address</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ trim(implode(', ', array_filter([$employee->permanent_building_no, $employee->permanent_area, $employee->permanent_city, $employee->permanent_state]))) }}

                        </div>

                    </div> --}}



                </div>
            </div>
        </div>






    </div>
</div>






<div class="tab-wrap mb-3" id="internal-note">

    <style>
        /* Documents column sizing: keep Key and File columns consistent across family and other document tables */
        .documents-table .key-col {
            width: 20%;
        }

        .documents-table .file-col {
            width: 15%;
            text-align: center;
        }

        .documents-table .name-col {
            width: auto;
        }

        .documents-table th,
        .documents-table td {
            vertical-align: middle;
        }
    </style>
    <ul class="nav nav-tabs" id="employeeInfoTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-family-tab" data-bs-toggle="tab" data-bs-target="#tab-family"
                type="button" role="tab" aria-controls="tab-family" aria-selected="true">Family Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-job" type="button"
                role="tab" aria-controls="tab-job" aria-selected="true">Job Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-bank-tab" data-bs-toggle="tab" data-bs-target="#tab-bank" type="button"
                role="tab" aria-controls="tab-bank" aria-selected="false">Bank Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-edu-tab" data-bs-toggle="tab" data-bs-target="#tab-edu" type="button"
                role="tab" aria-controls="tab-edu" aria-selected="false">Educational Qualifications</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-exp-tab" data-bs-toggle="tab" data-bs-target="#tab-exp" type="button"
                role="tab" aria-controls="tab-exp" aria-selected="false">Professional Experience</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-docs-tab" data-bs-toggle="tab" data-bs-target="#tab-docs"
                type="button" role="tab" aria-controls="tab-docs" aria-selected="false">Documents</button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">

        <div class="tab-pane fade show active" id="tab-family" role="tabpanel" aria-labelledby="tab-family-tab">



            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Father Details</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label"> First Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->fathers_first_name ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Last Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->fathers_last_name ?? '' }}</div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Mobile</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->father_mobile ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->father_email ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Document</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php $fatherDoc = $employee->documents->firstWhere('key', 'father_attachment') ?? null; @endphp
                                @if ($fatherDoc)
                                    <a class="btn-sm btn-light text-dark"
                                        href="{{ asset('storage/app/public/' . $fatherDoc->path) }}"
                                        target="_blank"><i
                                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>












                    </div>
                </div>
            </div>





            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3">Mother Details</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label"> First Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->mothers_first_name ?? '' }}
                            </div>
                        </div>









                        <div class="col">
                            <label class="form-label"> Last Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->mothers_last_name ?? '' }}</div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Mobile</label>
                            <div class="form-control-plaintext  truncate-text-custom">
                                {{ $employee->mother_mobile ?? '' }}
                            </div>
                        </div>



                        <div class="col">
                            <label class="form-label"> Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->mother_email ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Document</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php $motherDoc = $employee->documents->firstWhere('key', 'mother_attachment') ?? null; @endphp
                                @if ($motherDoc)
                                    <a class="btn-sm btn-light"
                                        href="{{ asset('storage/app/public/' . $motherDoc->path) }}"
                                        target="_blank"><i
                                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @if ($employee->spouse_first_name)
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important">
                        <span class="font-weight-600 mb-2 mt-3">Spouse Details</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                            <div class="col">
                                <label class="form-label"> First Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $employee->spouse_first_name ?? '' }}
                                </div>
                            </div>









                            <div class="col">
                                <label class="form-label"> Last Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $employee->spouse_last_name ?? '' }}</div>
                            </div>

                            <div class="col">
                                <label class="form-label"> Mobile</label>
                                <div class="form-control-plaintext  truncate-text-custom">
                                    {{ $employee->spouse_mobile ?? '' }}
                                </div>
                            </div>



                            <div class="col">
                                <label class="form-label"> Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $employee->spouse_email ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Document</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    @php $spouseDoc = $employee->documents->firstWhere('key', 'spouse_attachment') ?? null; @endphp
                                    @if ($employee->spouse_attachment)
                                        <a class="btn-sm btn-light"
                                            href="{{ asset('storage/app/public/' . $employee->spouse_attachment) }}"
                                            target="_blank"><i
                                                class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif









            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3 text-dark">Emergency Contact 1</span>

                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label">Salutation</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->em1_salutation ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency_contact_name ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Mobile</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency_mobile ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency_email ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Relationship</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency_contact_relationship ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>






            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3">Emergency Contact 2</span>

                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label">Salutation</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->em2_salutation ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency2_contact_name ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Mobile:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency2_mobile ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Email:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency2_email ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Relationship:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->emergency2_contact_relationship ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>






            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3">Current Address</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label">Building No:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->current_flat_no . ', ' ?? '' }}
                                {{ $employee->current_building_no ?? '' }}</div>
                        </div>
                        <div class="col">
                            <label class="form-label">Area:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->current_area ?? '' }}</div>
                        </div>
                        <div class="col">
                            <label class="form-label">City:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->current_city ?? '' }}</div>
                        </div>
                        <div class="col">
                            <label class="form-label">State:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(App\SysStates::find($employee->current_state))->name ?? ($employee->current_state ?? '') }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Country:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(App\SysCountries::find($employee->current_country))->name ?? ($employee->current_country ?? '') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>




            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3">Permanent Address</span>

                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label">Building No:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->permanent_flat_no . ', ' ?? '' }}
                                {{ $employee->permanent_building_no ?? '' }}</div>
                        </div>
                        <div class="col">
                            <label class="form-label">Area:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->permanent_area ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">City:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $employee->permanent_city ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">State:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(App\SysStates::find($employee->permanent_state))->name ?? ($employee->permanent_state ?? '') }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Country:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(App\SysCountries::find($employee->permanent_country))->name ?? ($employee->permanent_country ?? '') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>








        </div>

        <div class="tab-pane fade" id="tab-job" role="tabpanel" aria-labelledby="tab-job-tab">

            @php
                $job = $employee->jobDetail ?? null;
                $docs = $employee->documents ?? collect();
                $docsGrouped = $docs->groupBy('group');
                $joiningDocs = $docsGrouped->get('joining', collect());
                // $employmentDocs = $docsGrouped->get('employment', collect());
            @endphp

            

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Job Details</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label">Date of Joining:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($job)->date_of_joining ? App\SysHelper::normalizeToDmy(optional($job)->date_of_joining) : (optional($employee)->date_of_joining ? App\SysHelper::normalizeToDmy($employee->date_of_joining) : '—') }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Probation End Date:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php
                                    $probationDate = optional($job)->probation_end_date;
                                    if (!$probationDate) {
                                        $doj = optional($job)->date_of_joining ?: optional($employee)->date_of_joining;
                                        if ($doj) {
                                            try {
                                                $probationDate = (new \Carbon\Carbon($doj))->addMonths(6)->toDateString();
                                            } catch (\Exception $e) {
                                                $probationDate = null;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $probationDate ? App\SysHelper::normalizeToDmy($probationDate) : '—' }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Department:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(optional($job)->departments)->name ?: optional($employee->departments)->name ?: '—' }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Designation:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(optional($job)->designations)->title ?: optional($employee->designations)->title ?: '—' }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Grade:</label>
                            @php
                                $gradeRaw = optional($job)->grade ?: $employee->grade ?: null;
                                $gradeLabel = null;
                                if ($gradeRaw) {
                                    if (preg_match('/^g(\d+)$/i', trim($gradeRaw), $m)) {
                                        $gradeLabel = 'Grade ' . $m[1];
                                    } else {
                                        // Fallback: prettify string (g1-like already handled)
                                        $gradeLabel = ucwords(str_replace(['_', '-'], ' ', $gradeRaw));
                                    }
                                }
                            @endphp
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $gradeLabel ?? '—' }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Reporting Manager(s):</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php
                                    $rmNames = [];
                                    if ($job && method_exists($job, 'getReportingManagerNamesAttribute')) {
                                        $rmNames = $job->reporting_manager_names ?: [];
                                    }
                                    if (empty($rmNames)) {
                                        $rmNames = optional($employee->reportingManager)->full_name
                                            ? [optional($employee->reportingManager)->full_name]
                                            : [];
                                    }
                                @endphp
                                {{ count($rmNames) ? implode(', ', $rmNames) : '—' }}
                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Employment Type:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ 
    ucwords(
        str_replace('_', ' ', optional($job)->employment_type 
        ?: $employee->employment_type 
        ?: '—')
    ) 
}}</div>
                        </div>

                    </div>
                </div>
            </div>

            @php
                   $user_role = @App\Role::where('id', optional($job)->role_id)->first()->name;
                @endphp

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Company Information</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Visa Company:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(optional($job)->visacompany)->company_name ?: optional($employee->company)->company_name ?: optional($employee)->company_name ?: '—' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Working Company:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional(optional($job)->company)->company_name ?: optional($employee->maincompany)->company_name ?: optional($employee)->company_name ?: '—' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Company Access:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php
                                    $ca = null;
                                    if ($job && method_exists($job, 'companyAccessCompanies')) {
                                        $companies = $job->companyAccessCompanies();
                                        if ($companies->isNotEmpty()) {
                                            $ca = $companies->pluck('company_name')->implode(', ');
                                        }
                                    }
                                    if (!$ca) {
                                        $raw = optional($job)->company_access ?: optional($employee)->company_access;
                                        if (is_array($raw)) {
                                            $ca = implode(', ', $raw);
                                        } elseif (is_string($raw) && trim($raw) !== '') {
                                            preg_match_all('/\d+/', $raw, $m);
                                            $ids = $m[0] ?? [];
                                            if (!empty($ids)) {
                                                $names = \App\SysCompany::whereIn('id', $ids)
                                                    ->pluck('company_name')
                                                    ->toArray();
                                                if (!empty($names)) {
                                                    $ca = implode(', ', $names);
                                                } else {
                                                    $ca = $raw;
                                                }
                                            } else {
                                                $ca = $raw;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $ca ?: '—' }}
                            </div>
                        </div>
                         <div class="col">
                            <label class="form-label">Role:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ @$user_role ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Work Details</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Work Location:</label>
                            <div class="form-control-plaintext truncate-text-custom">{{ optional($job)->work_location ?: '—' }}</div>
                        </div>

                        <div class="col">
                            <label class="form-label">Work Hours:</label>
                       <div class="form-control-plaintext truncate-text-custom">
    {{ optional(optional($job)->shift)->shift_name ?? '—' }}
{{
    (optional($job)->shift && optional($job->shift)->start_time)
        ? ' (' . \Carbon\Carbon::parse($job->shift->start_time)->format('h:i A')
        : ''
}}
{{
    (optional($job)->shift && optional($job->shift)->end_time)
        ? ' - ' . \Carbon\Carbon::parse($job->shift->end_time)->format('h:i A') . ')'
        : ''
}}
</div>

                        </div>

                        <div class="col">
                            <label class="form-label">Weekly Off:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                              @php

                                    $weeklyOffs =
                                        $job && method_exists($job, 'weeklyOffModels')
                                            ? $job->weeklyOffModels()
                                            : collect();

                                    $weeklyLabels = [];
                                    foreach ($weeklyOffs as $wo) {
                                        $weeklyLabels[] = $wo->name;
                                    }

                                @endphp
                                {{ count($weeklyLabels) ? implode(', ', $weeklyLabels) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Ext No:</label>
                            <div class="form-control-plaintext truncate-text-custom">{{ optional($job)->ext_no ?: '—' }}</div>
                        </div>
                    <div class="col">
                        <label class="form-label">Finger Print ID</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ ucfirst($employee->finger_print_id ?? '--') }}
                        </div>
                    </div>

                        <div class="col">
                            <label class="form-label">Company Email:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($job)->company_email ?: '—' }}
                            </div>
                        </div>
                         <div class="col">
                            <label class="form-label">Company  Mobile:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($job)->company_mobile ?: '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Salary</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Basic Salary:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $job && $job->salary_basic !== null ? number_format($job->salary_basic, 2) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Allowances:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $job && $job->salary_allowances !== null ? number_format($job->salary_allowances, 2) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Other Allowances:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $job && $job->salary_other_allowances !== null ? number_format($job->salary_other_allowances, 2) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Transport Allowance:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $job && $job->transport_allowance !== null ? number_format($job->transport_allowance, 2) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Gross:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $job && $job->salary_gross !== null ? number_format($job->salary_gross, 2) : '—' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Other Benefits:</label>
                            <div class="form-control-plaintext truncate-text-custom">{{ optional($job)->other_benefits ?: '—' }}</div>
                        </div>

                    </div>
                </div>
            </div>

            @if ($user_role == 'Sales')
                @php
                    $targetFrom = optional($job)->target_month_from ?? null;
                    $targetType = optional($job)->target_type ?? null;
                    $targetPeriod = optional($job)->target_period ?? null;
                    $revenueTarget = optional($job)->revenue_target ?? null;
                    $gpTarget = optional($job)->gp_target ?? null;
                    $segment = optional($job)->channel_distribution ?? null;

                    // Brands may be stored as array or CSV; normalize to array
                    $brandsRaw = optional($job)->brand_ids ?? null;
                    $brandsArr = [];
                    if ($brandsRaw) {
                        if (is_array($brandsRaw)) $brandsArr = $brandsRaw;
                        elseif (is_string($brandsRaw)) $brandsArr = array_filter(array_map('trim', explode(',', $brandsRaw)));
                    }

                    $brandsText = '—';
                    if (!empty($brandsArr)) {
                        if (in_array('all', $brandsArr)) {
                            $brandsText = 'All';
                        } else {
                            $ids = array_values(array_filter($brandsArr, function($v) { return is_numeric($v); }));
                            if (!empty($ids)) {
                                $names = \App\SysBrand::whereIn('id', $ids)->pluck('title')->toArray();
                                $brandsText = !empty($names) ? implode(', ', $names) : implode(', ', $brandsArr);
                            } else {
                                $brandsText = implode(', ', $brandsArr);
                            }
                        }
                    }
                @endphp
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Sales Targets</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                    
                           
                            @if (optional($job)->is_target)
                                <div class="col">
                                <label class="form-label">Target From:</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $targetFrom ? @App\SysHelper::normalizeToDmy($targetFrom) : '—' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Type:</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                {{ $targetType ? strtoupper(str_replace('_', ' ', $targetType)) : '—' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Target Period</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $targetPeriod ?: '—' }}
                                </div>
                            </div>

                               @if ($targetType == 'revenue' || $targetType == 'both')
                             <div class="col">
                                <label class="form-label">Revenue Target</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $revenueTarget !== null ? number_format($revenueTarget, 2) : '—' }}
                                </div>
                            </div>
                            @endif

                          

                            @if ($targetType == 'gp' || $targetType == 'both')
                                 <div class="col">
                                <label class="form-label">GP Target</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $gpTarget !== null ? number_format($gpTarget, 2) : '—' }}
                                </div>
                            </div>
                            @endif

                           

                           
                            @endif
                            

                            <div class="col">
                                <label class="form-label">Segment</label>
                                <div class="form-control-plaintext truncate-text-custom">{{ $segment ?: '—' }}</div>
                            </div>

                            <div class="col">
                                <label class="form-label">Brands</label>
                                <div class="form-control-plaintext truncate-text-custom">{{ $brandsText }}</div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
            

       

        

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Joining Documents</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Resume</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @if(optional($job)->att_resume)
                                    <a href="{{ asset('storage/app/public/' . $job->att_resume) }}" target="_blank">{{ basename(optional($job)->att_resume) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Offer Letter</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @if(optional($job)->att_offer_letter)
                                    <a href="{{ asset('storage/app/public/' . $job->att_offer_letter) }}" target="_blank">{{ basename(optional($job)->att_offer_letter) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Signed Contract</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @if(optional($job)->att_signed_contract)
                                    <a class="" href="{{ asset('storage/app/public/' . optional($job)->att_signed_contract) }}" target="_blank">{{ basename(optional($job)->att_signed_contract) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>


                



            </div>





        </div>








        <div class="tab-pane fade" id="tab-bank" role="tabpanel" aria-labelledby="tab-bank-tab">


            @if (!empty($employee->bankDetails) && $employee->bankDetails->count())
                <div class="table-responsive">
                    <table class="table table-hover" id="long-list">
                        <thead class="">
                            <tr>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Holder</th>
                                <th>Account No</th>
                                <th>IBAN</th>
                                <th>SWIFT</th>
                                <th>Currency</th>
                                <th>IBAN Letter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employee->bankDetails as $b)
                                <tr>
                                    <td>{{ $b->bank_name ?? '' }}</td>
                                    <td>{{ $b->bank_branch ?? '' }}</td>
                                    <td>{{ $b->bank_ac_holder ?? '' }}</td>
                                    <td>{{ $b->bank_ac_number ?? '' }}</td>
                                    <td>{{ $b->iban_number ?? '' }}</td>
                                    <td>{{ $b->swift_code ?? '' }}</td>
                                    <td>{{ optional($b->currency)->name ?? '' }}</td>
                                    <td>
                                        @if ($b->att_iban_letter)
                                            <a href="{{ asset('storage/app/public/' . $b->att_iban_letter) }}"
                                                target="_blank">Download</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">No bank accounts found.</div>
            @endif


        </div>

        <div class="tab-pane fade" id="tab-edu" role="tabpanel" aria-labelledby="tab-edu-tab">


            @if (!empty($employee->educations) && $employee->educations->count())
                <div class="table-responsive">
                    <table class="table table-hover" id="long-list">
                        <thead>
                            <tr>
                                <th>Qualification</th>
                                <th>University</th>
                                <th>Specialization</th>
                                <th>Year</th>
                                <th>Result</th>
                                <th>GPA</th>
                                <th>Mode</th>
                                <th>Country</th>
                                <th>Duration</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employee->educations as $ed)
                                <tr>
                                    <td>{{ $ed->qualification ?? '' }}</td>
                                    <td>{{ $ed->university ?? '' }}</td>
                                    <td>{{ $ed->specialization ?? '' }}</td>
                                    <td>{{ $ed->year ?? '' }}</td>
                                    <td>{{ $ed->result ?? '' }}</td>
                                    <td>{{ $ed->gpa ?? '' }}</td>
                                    <td>{{ $ed->mode ?? '' }}</td>
                                    <td>{{ optional(App\SysCountries::find($ed->country))->name ?? ($ed->country ?? '') }}
                                    </td>
                                    <td>{{ (int) $ed->duration_years }} Years</td>

                                    <td>
                                        @if ($ed->certificate_path)
                                            <a href="{{ asset('storage/app/public/' . $ed->certificate_path) }}"
                                                target="_blank">Download</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">No education records found.</div>
            @endif

        </div>

        <div class="tab-pane fade" id="tab-exp" role="tabpanel" aria-labelledby="tab-exp-tab">


            @if (!empty($employee->experiences) && $employee->experiences->count())
                <div class="table-responsive">
                    <table class="table table-hover" id="long-list">
                        <thead class="table-light">
                            <tr>
                                <th>Organization</th>
                                <th>Designation</th>
                                <th>Duration</th>
                                <th>Responsibilities</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employee->experiences as $ex)
                                <tr>
                                    <td>{{ $ex->organization ?? '' }}</td>
                                    <td>{{ $ex->designation ?? '' }}</td>
                                    <td>{{ ($ex->years ?? 0) . ' Y, ' . ($ex->months ?? 0) . ' M' }}
                                    </td>
                                    <td>{{ $ex->responsibilities ?? '' }}</td>
                                    <td>
                                        @if ($ex->certificate_path)
                                            <a href="{{ asset('storage/app/public/' . $ex->certificate_path) }}"
                                                target="_blank">Download</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">No experience records found.</div>
            @endif

        </div>

        <div class="tab-pane fade" id="tab-docs" role="tabpanel" aria-labelledby="tab-docs-tab">


            @if (!empty($employee->documents) && $employee->documents->count())
                @php $grouped = $employee->documents->groupBy('group'); @endphp
                @foreach ($grouped as $group => $docs)
                    <div class="mb-3">
                        <h6 class="mb-2">{{ ucfirst($group) }}</h6>
                        <div class="table-responsive">
                            @if (strtolower($group) === 'family')
                                <table class="table table-hover documents-table" id="long-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="key-col">Type</th>
                                            <th class="name-col">Name</th>
                                            <th class="text-center file-col">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($docs as $d)
                                            <tr>
                                                <td>{{ ucwords(str_replace(['_', '-'], [' ', ' '], $d->key ?? '')) }}
                                                </td>
                                                <td>{{ $d->name ?? '' }}</td>
                                                <td class="text-center">
                                                    @if ($d->path)
                                                        <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                            target="_blank">Download</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <table class="table table-hover documents-table" id="long-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="key-col">Type</th>
                                            <th class="document-number-col">Document Number</th>
                                            <th class="name-col">Name</th>
                                            <th class="remarks-col">Remarks</th>
                                            <th class="text-center expiry-col">Expiry</th>
                                            <th class="text-center file-col">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($docs as $d)
                                            <tr>
                                                <td>{{ ucwords(str_replace(['_', '-'], [' ', ' '], $d->key ?? '')) }}
                                                </td>
                                                <td>{{ $d->document_number ?? '' }}</td>
                                                <td>{{ $d->name ?? '' }}</td>
                                                <td>{{ $d->remarks ?? '' }}</td>
                                                <td class="text-center">
                                                    {{ $d->expiry_date ? App\SysHelper::normalizeToDmy($d->expiry_date) : '' }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($d->path)
                                                        <a href="{{ asset('storage/app/public/' . $d->path) }}"
                                                            target="_blank">Download</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-muted">No documents uploaded.</div>
            @endif

        </div>

    </div>
</div>

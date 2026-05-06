<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        <?php echo e($employee->staff_no); ?>

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

        <a href="<?php echo e(url('hrms/staff/' . $employee->id . '/edit')); ?>" class="btn btn-light text-dark">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <a href="<?php echo e(url('add-staff')); ?>" class="btn btn-light text-dark">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>





        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">


                <li><a href="<?php echo e(url('onboarding-employee-list')); ?>"
                        class="dropdown-item d-flex align-items-center text-dark"><i
                            class="ico icon-outline-document-text text-success  title-15 me-2"></i> Onboard Employee
                        List</a>
                </li>

                <li><a data-copy-url="<?php echo e(url('onboard-employee/' . session('logged_session_data.company_id'))); ?>"
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
                    <?php if(!empty($employee->staff_photo)): ?>
                        <?php
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
                        ?>
                        <img src="<?php echo e($photoUrl); ?>" alt="Staff Photo"
                            style="width:100%;height:100%;object-fit:cover;display:block;">
                    <?php else: ?>
                        <span class="text-muted">No Photo</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-10">
                <div class="row row-cols-5">
                    <div class="col">
                        <label class="form-label">First Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                        <?php echo e($employee->employee_salutation ? $employee->employee_salutation . '.' : ''); ?> <?php echo e($employee->first_name_full ?? ''); ?>


                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Last Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e($employee->last_name ?? ''); ?>

                        </div>
                    </div>





                    <div class="col">
                        <label class="form-label">Email</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e($employee->email ?? ''); ?>

                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Mobile</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e($employee->mobile ?? ''); ?>

                        </div>
                    </div>



                    <div class="col mb-3">
                        <label class="form-label">Gender</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php $genders = [1 => 'Male', 2 => 'Female', 3 => 'Other']; ?>
                            <?php echo e($genders[$employee->gender_id ?? null] ?? ''); ?>

                        </div>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label">Blood Group</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(ucfirst($employee->blood_group ?? '')); ?>

                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Marital Status</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(ucfirst($employee->marital_status ?? '')); ?>

                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">DOB</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(@App\SysHelper::normalizeToDmy($employee->date_of_birth)); ?>

                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Place of Birth</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(ucfirst($employee->place_of_birth ?? '')); ?>

                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Religion</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(ucfirst($employee->religion ?? '')); ?>

                        </div>
                    </div>

                    



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
                                <?php echo e($employee->fathers_first_name ?? ''); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Last Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->fathers_last_name ?? ''); ?></div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Mobile</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->father_mobile ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->father_email ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Document</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php $fatherDoc = $employee->documents->firstWhere('key', 'father_attachment') ?? null; ?>
                                <?php if($fatherDoc): ?>
                                    <a class="btn-sm btn-light text-dark"
                                        href="<?php echo e(asset('storage/app/public/' . $fatherDoc->path)); ?>"
                                        target="_blank"><i
                                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
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
                                <?php echo e($employee->mothers_first_name ?? ''); ?>

                            </div>
                        </div>









                        <div class="col">
                            <label class="form-label"> Last Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->mothers_last_name ?? ''); ?></div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Mobile</label>
                            <div class="form-control-plaintext  truncate-text-custom">
                                <?php echo e($employee->mother_mobile ?? ''); ?>

                            </div>
                        </div>



                        <div class="col">
                            <label class="form-label"> Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->mother_email ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Document</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php $motherDoc = $employee->documents->firstWhere('key', 'mother_attachment') ?? null; ?>
                                <?php if($motherDoc): ?>
                                    <a class="btn-sm btn-light"
                                        href="<?php echo e(asset('storage/app/public/' . $motherDoc->path)); ?>"
                                        target="_blank"><i
                                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <?php if($employee->spouse_first_name): ?>
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important">
                        <span class="font-weight-600 mb-2 mt-3">Spouse Details</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                            <div class="col">
                                <label class="form-label"> First Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($employee->spouse_first_name ?? ''); ?>

                                </div>
                            </div>









                            <div class="col">
                                <label class="form-label"> Last Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($employee->spouse_last_name ?? ''); ?></div>
                            </div>

                            <div class="col">
                                <label class="form-label"> Mobile</label>
                                <div class="form-control-plaintext  truncate-text-custom">
                                    <?php echo e($employee->spouse_mobile ?? ''); ?>

                                </div>
                            </div>



                            <div class="col">
                                <label class="form-label"> Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($employee->spouse_email ?? ''); ?>

                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Document</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php $spouseDoc = $employee->documents->firstWhere('key', 'spouse_attachment') ?? null; ?>
                                    <?php if($employee->spouse_attachment): ?>
                                        <a class="btn-sm btn-light"
                                            href="<?php echo e(asset('storage/app/public/' . $employee->spouse_attachment)); ?>"
                                            target="_blank"><i
                                                class="ico icon-bold-download-minimalistic text-success fw-bold title-15 me-2"></i>Download</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>









            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important">
                    <span class="font-weight-600 mb-2 mt-3 text-dark">Emergency Contact 1</span>

                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col">
                            <label class="form-label">Salutation</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->em1_salutation ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency_contact_name ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Mobile</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency_mobile ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency_email ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Relationship</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency_contact_relationship ?? ''); ?>

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
                                <?php echo e($employee->em2_salutation ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Name</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency2_contact_name ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Mobile:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency2_mobile ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Email:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency2_email ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Relationship:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->emergency2_contact_relationship ?? ''); ?>

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
                                <?php echo e($employee->current_flat_no . ', ' ?? ''); ?>

                                <?php echo e($employee->current_building_no ?? ''); ?></div>
                        </div>
                        <div class="col">
                            <label class="form-label">Area:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->current_area ?? ''); ?></div>
                        </div>
                        <div class="col">
                            <label class="form-label">City:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->current_city ?? ''); ?></div>
                        </div>
                        <div class="col">
                            <label class="form-label">State:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(App\SysStates::find($employee->current_state))->name ?? ($employee->current_state ?? '')); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Country:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(App\SysCountries::find($employee->current_country))->name ?? ($employee->current_country ?? '')); ?>

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
                                <?php echo e($employee->permanent_flat_no . ', ' ?? ''); ?>

                                <?php echo e($employee->permanent_building_no ?? ''); ?></div>
                        </div>
                        <div class="col">
                            <label class="form-label">Area:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->permanent_area ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">City:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($employee->permanent_city ?? ''); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">State:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(App\SysStates::find($employee->permanent_state))->name ?? ($employee->permanent_state ?? '')); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Country:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(App\SysCountries::find($employee->permanent_country))->name ?? ($employee->permanent_country ?? '')); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>








        </div>

        <div class="tab-pane fade" id="tab-job" role="tabpanel" aria-labelledby="tab-job-tab">

            <?php
                $job = $employee->jobDetail ?? null;
                $docs = $employee->documents ?? collect();
                $docsGrouped = $docs->groupBy('group');
                $joiningDocs = $docsGrouped->get('joining', collect());
                // $employmentDocs = $docsGrouped->get('employment', collect());
            ?>

            

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Job Details</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label">Date of Joining:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional($job)->date_of_joining ? App\SysHelper::normalizeToDmy(optional($job)->date_of_joining) : (optional($employee)->date_of_joining ? App\SysHelper::normalizeToDmy($employee->date_of_joining) : '—')); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Probation End Date:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php
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
                                ?>
                                <?php echo e($probationDate ? App\SysHelper::normalizeToDmy($probationDate) : '—'); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Department:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(optional($job)->departments)->name ?: optional($employee->departments)->name ?: '—'); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Designation:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(optional($job)->designations)->title ?: optional($employee->designations)->title ?: '—'); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Grade:</label>
                            <?php
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
                            ?>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($gradeLabel ?? '—'); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Reporting Manager(s):</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php
                                    $rmNames = [];
                                    if ($job && method_exists($job, 'getReportingManagerNamesAttribute')) {
                                        $rmNames = $job->reporting_manager_names ?: [];
                                    }
                                    if (empty($rmNames)) {
                                        $rmNames = optional($employee->reportingManager)->full_name
                                            ? [optional($employee->reportingManager)->full_name]
                                            : [];
                                    }
                                ?>
                                <?php echo e(count($rmNames) ? implode(', ', $rmNames) : '—'); ?>

                            </div>
                        </div>

                        <div class="col mb-3">
                            <label class="form-label">Employment Type:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(ucwords(
        str_replace('_', ' ', optional($job)->employment_type 
        ?: $employee->employment_type 
        ?: '—')
    )); ?></div>
                        </div>

                    </div>
                </div>
            </div>

            <?php
                   $user_role = @App\Role::where('id', optional($job)->role_id)->first()->name;
                ?>

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Company Information</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Visa Company:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(optional($job)->visacompany)->company_name ?: optional($employee->company)->company_name ?: optional($employee)->company_name ?: '—'); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Working Company:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional(optional($job)->company)->company_name ?: optional($employee->maincompany)->company_name ?: optional($employee)->company_name ?: '—'); ?>

                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Company Access:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php
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
                                ?>
                                <?php echo e($ca ?: '—'); ?>

                            </div>
                        </div>
                         <div class="col">
                            <label class="form-label">Role:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(@$user_role ?? '—'); ?>

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
                            <div class="form-control-plaintext truncate-text-custom"><?php echo e(optional($job)->work_location ?: '—'); ?></div>
                        </div>

                        <div class="col">
                            <label class="form-label">Work Hours:</label>
                       <div class="form-control-plaintext truncate-text-custom">
    <?php echo e(optional(optional($job)->shift)->shift_name ?? '—'); ?>

<?php echo e((optional($job)->shift && optional($job->shift)->start_time)
        ? ' (' . \Carbon\Carbon::parse($job->shift->start_time)->format('h:i A')
        : ''); ?>

<?php echo e((optional($job)->shift && optional($job->shift)->end_time)
        ? ' - ' . \Carbon\Carbon::parse($job->shift->end_time)->format('h:i A') . ')'
        : ''); ?>

</div>

                        </div>

                        <div class="col">
                            <label class="form-label">Weekly Off:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                              <?php

                                    $weeklyOffs =
                                        $job && method_exists($job, 'weeklyOffModels')
                                            ? $job->weeklyOffModels()
                                            : collect();

                                    $weeklyLabels = [];
                                    foreach ($weeklyOffs as $wo) {
                                        $weeklyLabels[] = $wo->name;
                                    }

                                ?>
                                <?php echo e(count($weeklyLabels) ? implode(', ', $weeklyLabels) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Ext No:</label>
                            <div class="form-control-plaintext truncate-text-custom"><?php echo e(optional($job)->ext_no ?: '—'); ?></div>
                        </div>
                    <div class="col">
                        <label class="form-label">Finger Print ID</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            <?php echo e(ucfirst($employee->finger_print_id ?? '--')); ?>

                        </div>
                    </div>

                        <div class="col">
                            <label class="form-label">Company Email:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional($job)->company_email ?: '—'); ?>

                            </div>
                        </div>
                         <div class="col">
                            <label class="form-label">Company  Mobile:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e(optional($job)->company_mobile ?: '—'); ?>

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
                                <?php echo e($job && $job->salary_basic !== null ? number_format($job->salary_basic, 2) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Allowances:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($job && $job->salary_allowances !== null ? number_format($job->salary_allowances, 2) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Other Allowances:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($job && $job->salary_other_allowances !== null ? number_format($job->salary_other_allowances, 2) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Transport Allowance:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($job && $job->transport_allowance !== null ? number_format($job->transport_allowance, 2) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Gross:</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($job && $job->salary_gross !== null ? number_format($job->salary_gross, 2) : '—'); ?>

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Other Benefits:</label>
                            <div class="form-control-plaintext truncate-text-custom"><?php echo e(optional($job)->other_benefits ?: '—'); ?></div>
                        </div>

                    </div>
                </div>
            </div>

            <?php if($user_role == 'Sales'): ?>
                <?php
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
                ?>
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Sales Targets</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                    
                           
                            <?php if(optional($job)->is_target): ?>
                                <div class="col">
                                <label class="form-label">Target From:</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($targetFrom ? @App\SysHelper::normalizeToDmy($targetFrom) : '—'); ?>

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Type:</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                <?php echo e($targetType ? strtoupper(str_replace('_', ' ', $targetType)) : '—'); ?>


                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Target Period</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($targetPeriod ?: '—'); ?>

                                </div>
                            </div>

                               <?php if($targetType == 'revenue' || $targetType == 'both'): ?>
                             <div class="col">
                                <label class="form-label">Revenue Target</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($revenueTarget !== null ? number_format($revenueTarget, 2) : '—'); ?>

                                </div>
                            </div>
                            <?php endif; ?>

                          

                            <?php if($targetType == 'gp' || $targetType == 'both'): ?>
                                 <div class="col">
                                <label class="form-label">GP Target</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    <?php echo e($gpTarget !== null ? number_format($gpTarget, 2) : '—'); ?>

                                </div>
                            </div>
                            <?php endif; ?>

                           

                           
                            <?php endif; ?>
                            

                            <div class="col">
                                <label class="form-label">Segment</label>
                                <div class="form-control-plaintext truncate-text-custom"><?php echo e($segment ?: '—'); ?></div>
                            </div>

                            <div class="col">
                                <label class="form-label">Brands</label>
                                <div class="form-control-plaintext truncate-text-custom"><?php echo e($brandsText); ?></div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
            

       

        

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Joining Documents</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">
                        <div class="col">
                            <label class="form-label">Resume</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php if(optional($job)->att_resume): ?>
                                    <a href="<?php echo e(asset('storage/app/public/' . $job->att_resume)); ?>" target="_blank"><?php echo e(basename(optional($job)->att_resume)); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Offer Letter</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php if(optional($job)->att_offer_letter): ?>
                                    <a href="<?php echo e(asset('storage/app/public/' . $job->att_offer_letter)); ?>" target="_blank"><?php echo e(basename(optional($job)->att_offer_letter)); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Signed Contract</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                <?php if(optional($job)->att_signed_contract): ?>
                                    <a class="" href="<?php echo e(asset('storage/app/public/' . optional($job)->att_signed_contract)); ?>" target="_blank"><?php echo e(basename(optional($job)->att_signed_contract)); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                </div>


                



            </div>





        </div>








        <div class="tab-pane fade" id="tab-bank" role="tabpanel" aria-labelledby="tab-bank-tab">


            <?php if(!empty($employee->bankDetails) && $employee->bankDetails->count()): ?>
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
                            <?php $__currentLoopData = $employee->bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($b->bank_name ?? ''); ?></td>
                                    <td><?php echo e($b->bank_branch ?? ''); ?></td>
                                    <td><?php echo e($b->bank_ac_holder ?? ''); ?></td>
                                    <td><?php echo e($b->bank_ac_number ?? ''); ?></td>
                                    <td><?php echo e($b->iban_number ?? ''); ?></td>
                                    <td><?php echo e($b->swift_code ?? ''); ?></td>
                                    <td><?php echo e(optional($b->currency)->name ?? ''); ?></td>
                                    <td>
                                        <?php if($b->att_iban_letter): ?>
                                            <a href="<?php echo e(asset('storage/app/public/' . $b->att_iban_letter)); ?>"
                                                target="_blank">Download</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">No bank accounts found.</div>
            <?php endif; ?>


        </div>

        <div class="tab-pane fade" id="tab-edu" role="tabpanel" aria-labelledby="tab-edu-tab">


            <?php if(!empty($employee->educations) && $employee->educations->count()): ?>
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
                            <?php $__currentLoopData = $employee->educations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($ed->qualification ?? ''); ?></td>
                                    <td><?php echo e($ed->university ?? ''); ?></td>
                                    <td><?php echo e($ed->specialization ?? ''); ?></td>
                                    <td><?php echo e($ed->year ?? ''); ?></td>
                                    <td><?php echo e($ed->result ?? ''); ?></td>
                                    <td><?php echo e($ed->gpa ?? ''); ?></td>
                                    <td><?php echo e($ed->mode ?? ''); ?></td>
                                    <td><?php echo e(optional(App\SysCountries::find($ed->country))->name ?? ($ed->country ?? '')); ?>

                                    </td>
                                    <td><?php echo e((int) $ed->duration_years); ?> Years</td>

                                    <td>
                                        <?php if($ed->certificate_path): ?>
                                            <a href="<?php echo e(asset('storage/app/public/' . $ed->certificate_path)); ?>"
                                                target="_blank">Download</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">No education records found.</div>
            <?php endif; ?>

        </div>

        <div class="tab-pane fade" id="tab-exp" role="tabpanel" aria-labelledby="tab-exp-tab">


            <?php if(!empty($employee->experiences) && $employee->experiences->count()): ?>
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
                            <?php $__currentLoopData = $employee->experiences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($ex->organization ?? ''); ?></td>
                                    <td><?php echo e($ex->designation ?? ''); ?></td>
                                    <td><?php echo e(($ex->years ?? 0) . ' Y, ' . ($ex->months ?? 0) . ' M'); ?>

                                    </td>
                                    <td><?php echo e($ex->responsibilities ?? ''); ?></td>
                                    <td>
                                        <?php if($ex->certificate_path): ?>
                                            <a href="<?php echo e(asset('storage/app/public/' . $ex->certificate_path)); ?>"
                                                target="_blank">Download</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">No experience records found.</div>
            <?php endif; ?>

        </div>

        <div class="tab-pane fade" id="tab-docs" role="tabpanel" aria-labelledby="tab-docs-tab">


            <?php if(!empty($employee->documents) && $employee->documents->count()): ?>
                <?php $grouped = $employee->documents->groupBy('group'); ?>
                <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $docs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3">
                        <h6 class="mb-2"><?php echo e(ucfirst($group)); ?></h6>
                        <div class="table-responsive">
                            <?php if(strtolower($group) === 'family'): ?>
                                <table class="table table-hover documents-table" id="long-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="key-col">Type</th>
                                            <th class="name-col">Name</th>
                                            <th class="text-center file-col">File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(ucwords(str_replace(['_', '-'], [' ', ' '], $d->key ?? ''))); ?>

                                                </td>
                                                <td><?php echo e($d->name ?? ''); ?></td>
                                                <td class="text-center">
                                                    <?php if($d->path): ?>
                                                        <a href="<?php echo e(asset('storage/app/public/' . $d->path)); ?>"
                                                            target="_blank">Download</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
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
                                        <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(ucwords(str_replace(['_', '-'], [' ', ' '], $d->key ?? ''))); ?>

                                                </td>
                                                <td><?php echo e($d->document_number ?? ''); ?></td>
                                                <td><?php echo e($d->name ?? ''); ?></td>
                                                <td><?php echo e($d->remarks ?? ''); ?></td>
                                                <td class="text-center">
                                                    <?php echo e($d->expiry_date ? App\SysHelper::normalizeToDmy($d->expiry_date) : ''); ?>

                                                </td>
                                                <td class="text-center">
                                                    <?php if($d->path): ?>
                                                        <a href="<?php echo e(asset('storage/app/public/' . $d->path)); ?>"
                                                            target="_blank">Download</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="text-muted">No documents uploaded.</div>
            <?php endif; ?>

        </div>

    </div>
</div>

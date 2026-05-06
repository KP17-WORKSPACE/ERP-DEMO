@php
    use Illuminate\Support\Str;

    $imgPath = isset($staff->staff_photo) ? asset($staff->staff_photo) : asset('public/uploads/staff/demo/staff.png');

    $statusText = $staff->active_status == 1 ? 'Active' : 'Inactive';
    $statusClass = $staff->active_status == 1 ? 'success' : 'secondary';

    // Shortcuts
    $job = $staff->jobDetail; // may be null
    $banks = $staff->bankDetails ?? collect(); // multiple bank accounts
    $bank = $staff->bankDetail; // single bank (for backward compatibility)
    $edus = $staff->educationQualifications ?? collect();
    $exps = $staff->professionalExperiences ?? collect();
    $docs = $staff->documents ?? collect();

    // helper: normalize json/array/csv to array
    $toArray = function ($v) {
        if (is_array($v)) {
            return $v;
        }
        if (is_string($v) && $v !== '') {
            $j = json_decode($v, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($j)) {
                return $j;
            }
            return array_filter(array_map('trim', explode(',', $v)));
        }
        return [];
    };
    $visaName = optional($staff->company)->company_name ?? '—';
    $mainName = optional($job->company)->company_name ?? '—';
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
        {{ $staff->staff_no ?? '—' }}
        <span class="badge bg-{{ $statusClass }} ms-2">{{ $statusText }}</span>
    </h4>
    <div class="purchase-order-content-header-right">
         <button data-copy-url="{{ url('onboard-employee/'.session('logged_session_data.company_id')) }}" title="Click to copy link" class="btn btn-light text-dark copy-onboard-url">
            <i class="ico icon-outline-user-plus text-success"></i> Onboard
        </button>
        <a href="{{ url('hrms/staff/' . $staff->id . '/edit') }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <a href="{{ url('add-staff') }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>
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

{{-- Header Card with Photo and Basic Info --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row g-3 align-items-start">
            {{-- Photo --}}
            <div class="col-md-2 text-center">
                <img src="{{ $staff->staff_photo_public_url ?? asset('public/uploads/staff/demo/staff.png') }}" 
                     alt="Staff Photo" 
                     class="img-fluid rounded mb-2"
                     style="max-width:120px; height:120px; object-fit:cover;" 
                     loading="lazy">
                <div class="badge bg-primary">{{ optional($staff->roles)->name ?? '—' }}</div>
            </div>

            {{-- Quick Info --}}
            <div class="col-md-10">
                <div class="row g-2">
                    <div class="col-md-2">
                        <div class="view-label">User No</div>
                        <div class="view-value fw-bold text-success">{{ $staff->staff_no ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">First Name</div>
                        <div class="view-value">{{ $staff->first_name ?? '—' }}</div>
                    </div>
                   
                    <div class="col-md-2">
                        <div class="view-label">Last Name</div>
                        <div class="view-value">{{ $staff->last_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Date of Birth</div>
                        <div class="view-value">{{ $staff->date_of_birth ? \Carbon\Carbon::parse($staff->date_of_birth)->format('d/m/Y') : '—' }}</div>
                    </div>
                   
                    <div class="col-md-2">
                        <div class="view-label">Gender</div>
                        <div class="view-value">  @php $genders = [1 => 'Male', 2 => 'Female', 3 => 'Other']; @endphp
                            {{ $genders[$staff->gender_id ?? null] ?? '' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Mobile</div>
                        <div class="view-value">{{ $staff->mobile ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="view-label">Email</div>
                        <div class="view-value text-truncate" title="{{ $staff->email }}">{{ $staff->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Ext No</div>
                        <div class="view-value">{{ $staff->ext_no ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Department</div>
                      
                        <div class="view-value">{{ optional($staff->departments)->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="view-label">Designation</div>
                        <div class="view-value">{{ optional($staff->designations)->title ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Staff Details Tabs --}}
<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="staffDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="job-detail-tab" data-bs-toggle="tab" data-bs-target="#job-details-view" type="button" role="tab">Job Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="bank-detail-tab" data-bs-toggle="tab" data-bs-target="#bank-details-view" type="button" role="tab">Bank Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edu-detail-tab" data-bs-toggle="tab" data-bs-target="#edu-details-view" type="button" role="tab">Educational Qualification</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="exp-detail-tab" data-bs-toggle="tab" data-bs-target="#exp-details-view" type="button" role="tab">Professional Experience</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="doc-detail-tab" data-bs-toggle="tab" data-bs-target="#doc-details-view" type="button" role="tab">Documentation</button>
        </li>
    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white" id="staffDetailTabsContent">
        
        {{-- JOB DETAILS TAB --}}
        <div class="tab-pane fade show active" id="job-details-view" role="tabpanel">
            <div class="accordion" id="jobDetailsViewAccordion">
                
                {{-- 1. Job Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#viewJobInfo" aria-expanded="true">
                            <span class="me-2">1</span> Job Information
                        </button>
                    </h2>
                    <div id="viewJobInfo" class="accordion-collapse collapse show" data-bs-parent="#jobDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="view-label">Date of Joining</div>
                                    <div class="view-value">{{ $job && $job->date_of_joining ? \Carbon\Carbon::parse($job->date_of_joining)->format('d/m/Y') : '—' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Department</div>
                                    <div class="view-value">{{ optional($staff->jobDetail->departments)->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Designation</div>
                                    <div class="view-value">{{ optional($staff->jobDetail->designations)->title ?? '—' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Reporting Manager</div>
                                    <div class="view-value">
                                        @php $names = $job ? ($job->reporting_manager_names ?? []) : []; @endphp
                                        @forelse($names as $nm)
                                            <span class="badge bg-info text-dark">{{ $nm }}</span>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Employment Type</div>
                                    <div class="view-value">{{ ucfirst(str_replace('_', ' ', $job->employment_type ?? '—')) }}</div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="view-label">Week Off</div>
                                    <div class="view-value">{{ ucfirst(str_replace('_', ' ', $job->week_off ?? '—')) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Company Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewCompanyInfo">
                            <span class="me-2">2</span> Company Information
                        </button>
                    </h2>
                    <div id="viewCompanyInfo" class="accordion-collapse collapse" data-bs-parent="#jobDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="view-label">Company (Visa)</div>
                                    <div class="view-value">{{ $visaName }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Company (Working)</div>
                                    <div class="view-value">{{ $mainName }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Company Access</div>
                                    <div class="view-value">
                                      
                                        @php $companies = $job->companyAccessCompanies(); @endphp
                                        @forelse ($companies as $co)
                                            <span class="badge bg-info text-dark me-1">{{ Str::limit($co->company_name ?? '—', 15) }}</span>
                                        @empty
                                            —
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Work Location</div>
                                    <div class="view-value">{{ $job->work_location ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Work Hours</div>
                                    <div class="view-value">{{ $job->work_hours ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Ext No</div>
                                    <div class="view-value">{{ $job->ext_no ?? $staff->ext_no ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Salary Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewSalaryInfo">
                            <span class="me-2">3</span> Salary Information
                        </button>
                    </h2>
                    <div id="viewSalaryInfo" class="accordion-collapse collapse" data-bs-parent="#jobDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="view-label">Basic Salary</div>
                                    <div class="view-value">{{ $job && $job->salary_basic ? number_format($job->salary_basic, 2) : '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Allowances</div>
                                    <div class="view-value">{{ $job && $job->salary_allowances ? number_format($job->salary_allowances, 2) : '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Other Allowances</div>
                                    <div class="view-value">{{ $job && $job->salary_other_allowances ? number_format($job->salary_other_allowances, 2) : '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Gross Salary</div>
                                    <div class="view-value fw-bold text-success">{{ $job && $job->salary_gross ? number_format($job->salary_gross, 2) : '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Target Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewTargetInfo">
                            <span class="me-2">4</span> Target Information
                        </button>
                    </h2>
                    <div id="viewTargetInfo" class="accordion-collapse collapse" data-bs-parent="#jobDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="view-label">Targets Enabled</div>
                                    <div class="view-value">
                                        @if($job && $job->is_target)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </div>
                                </div>
                                @if($job && $job->is_target)
                                <div class="col-md-3">
                                    <div class="view-label">Target Month From</div>
                                    <div class="view-value">{{ $job->target_month_from ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Revenue Target (Monthly)</div>
                                    <div class="view-value">{{ $job->revenue_target_monthly ? number_format($job->revenue_target_monthly, 2) : '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">GP Target (Monthly)</div>
                                    <div class="view-value">{{ $job->gp_target_monthly ? number_format($job->gp_target_monthly, 2) : '—' }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Basic Information --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewBasicInfo">
                            <span class="me-2">5</span> Personal Information
                        </button>
                    </h2>
                    <div id="viewBasicInfo" class="accordion-collapse collapse" data-bs-parent="#jobDetailsViewAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="view-label">Father's Name</div>
                                    <div class="view-value">{{ $staff->fathers_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Mother's Name</div>
                                    <div class="view-value">{{ $staff->mothers_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Religion</div>
                                    <div class="view-value">{{ ucfirst($staff->religion ?? '—') }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Marital Status</div>
                                    <div class="view-value">{{ ucfirst($staff->marital_status ?? '—') }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="view-label">Nationality</div>
                                    <div class="view-value">{{ optional($staff->nationalityCountry)->name ?? '—' }}</div>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            <h6 class="text-muted mb-3">Emergency Contact</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="view-label">Contact Name</div>
                                    <div class="view-value">{{ $staff->emergency_contact_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Relationship</div>
                                    <div class="view-value">{{ $staff->emergency_contact_relationship ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="view-label">Contact Number</div>
                                    <div class="view-value">{{ $staff->emergency_mobile ?? '—' }}</div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <h6 class="text-muted mb-3">Address Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="view-label">Permanent Address</div>
                                    <div class="view-value">
                                        {{ $staff->permanent_building_no ? 'Bldg: ' . $staff->permanent_building_no . ', ' : '' }}
                                        {{ $staff->permanent_flat_no ? 'Flat: ' . $staff->permanent_flat_no . ', ' : '' }}
                                        {{ $staff->permanent_area ?? '' }}
                                        {{ $staff->permanent_city ? ', ' . $staff->permanent_city : '' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="view-label">Current Address</div>
                                    <div class="view-value">
                                        {{ $staff->current_building_no ? 'Bldg: ' . $staff->current_building_no . ', ' : '' }}
                                        {{ $staff->current_flat_no ? 'Flat: ' . $staff->current_flat_no . ', ' : '' }}
                                        {{ $staff->current_area ?? '' }}
                                        {{ $staff->current_city ? ', ' . $staff->current_city : '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- BANK DETAILS TAB --}}
        <div class="tab-pane fade" id="bank-details-view" role="tabpanel">
            @if ($banks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-view mb-0">
                        <thead>
                            <tr>
                                <th>Bank Name</th>
                                <th>Branch</th>
                                <th>Account Holder</th>
                                <th>Account No</th>
                                <th>IBAN</th>
                                <th>SWIFT</th>
                                <th>Currency</th>
                                <th>IBAN Letter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banks as $b)
                            <tr>
                                <td>{{ $b->bank_name ?? '—' }}</td>
                                <td>{{ $b->bank_branch ?? '—' }}</td>
                                <td>{{ $b->bank_ac_holder ?? '—' }}</td>
                                <td class="font-monospace">{{ $b->bank_ac_number ?? '—' }}</td>
                                <td class="font-monospace text-truncate" style="max-width:150px" title="{{ $b->iban_number }}">{{ $b->iban_number ?? '—' }}</td>
                                <td class="font-monospace">{{ $b->swift_code ?? '—' }}</td>
                                <td>{{ $b->bank_currency ?? '—' }}</td>
                                <td>
                                    @if (!empty($b->att_iban_letter))
                                        <a href="{{ asset($b->att_iban_letter) }}" target="_blank" class="btn btn-xs btn-light">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($bank)
                {{-- Fallback for single bank detail --}}
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="view-label">Bank Name</div>
                        <div class="view-value">{{ $bank->bank_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Branch</div>
                        <div class="view-value">{{ $bank->bank_branch ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Account Holder</div>
                        <div class="view-value">{{ $bank->bank_ac_holder ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Account No</div>
                        <div class="view-value font-monospace">{{ $bank->bank_ac_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">IBAN</div>
                        <div class="view-value font-monospace">{{ $bank->iban_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">SWIFT Code</div>
                        <div class="view-value font-monospace">{{ $bank->swift_code ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">Currency</div>
                        <div class="view-value">{{ $bank->bank_currency ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="view-label">IBAN Letter</div>
                        <div class="view-value">
                            @if (!empty($bank->att_iban_letter))
                                <a href="{{ asset($bank->att_iban_letter) }}" target="_blank" class="btn btn-sm btn-light">View</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">No bank details available.</p>
            @endif
        </div>

        {{-- EDUCATIONAL QUALIFICATION TAB --}}
        <div class="tab-pane fade" id="edu-details-view" role="tabpanel">
            @php $edus = ($staff->educationQualifications ?? collect())->sortByDesc('year'); @endphp
            @if ($edus->count())
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-view mb-0">
                        <thead>
                            <tr>
                                <th>Qualification</th>
                                <th>University/Board</th>
                                <th>Specialization</th>
                                <th>Year</th>
                                <th>Result</th>
                                <th>GPA</th>
                                <th>Mode</th>
                                <th>Country</th>
                                <th>Duration</th>
                                <th class="text-center">Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($edus as $eq)
                            <tr>
                                <td class="text-truncate" style="max-width:150px" title="{{ $eq->qualification }}">{{ Str::limit($eq->qualification ?? '—', 25) }}</td>
                                <td class="text-truncate" style="max-width:180px" title="{{ $eq->university }}">{{ Str::limit($eq->university ?? '—', 28) }}</td>
                                <td class="text-truncate" style="max-width:150px" title="{{ $eq->specialization }}">{{ Str::limit($eq->specialization ?? '—', 20) }}</td>
                                <td>{{ $eq->year ?? '—' }}</td>
                                <td>{{ $eq->result ?? '—' }}</td>
                                <td class="font-monospace">{{ $eq->gpa ?? '—' }}</td>
                                <td>{{ $eq->mode ?? '—' }}</td>
                                <td>{{ $eq->country ?? '—' }}</td>
                                <td>{{ $eq->duration_years ? $eq->duration_years . ' yrs' : '—' }}</td>
                                <td class="text-center">
                                    @if (!empty($eq->certificate_path))
                                        <a href="{{ asset($eq->certificate_path) }}" target="_blank" class="btn btn-xs btn-light">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No educational qualifications available.</p>
            @endif
        </div>

        {{-- PROFESSIONAL EXPERIENCE TAB --}}
        <div class="tab-pane fade" id="exp-details-view" role="tabpanel">
            @if ($exps->count())
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-view mb-0">
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Designation</th>
                                <th>Duration</th>
                                <th>Responsibilities</th>
                                <th class="text-center">Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exps as $exp)
                            <tr>
                                <td class="text-truncate" style="max-width:200px" title="{{ $exp->organization }}">{{ Str::limit($exp->organization ?? '—', 30) }}</td>
                                <td>{{ $exp->designation ?? '—' }}</td>
                                <td>
                                    @if($exp->years || $exp->months)
                                        {{ $exp->years ? $exp->years . ' yr' . ($exp->years > 1 ? 's' : '') : '' }}
                                        {{ $exp->months ? $exp->months . ' mo' . ($exp->months > 1 ? 's' : '') : '' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width:250px" title="{{ $exp->responsibilities }}">{{ Str::limit($exp->responsibilities ?? '—', 40) }}</td>
                                <td class="text-center">
                                    @if (!empty($exp->certificate_path))
                                        <a href="{{ asset($exp->certificate_path) }}" target="_blank" class="btn btn-xs btn-light">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No professional experience records available.</p>
            @endif
        </div>

        {{-- DOCUMENTATION TAB --}}
        <div class="tab-pane fade" id="doc-details-view" role="tabpanel">
            @if ($docs->count())
                <div class="row g-3">
                    @php
                        $joiningDocs = $docs->where('group', 'joining');
                        $employmentDocs = $docs->where('group', 'employment');
                        $otherDocs = $docs->where('group', 'others');
                    @endphp

                    @if($joiningDocs->count())
                    <div class="col-12">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Joining Documents</h6>
                        <div class="row g-2">
                            @foreach ($joiningDocs as $doc)
                           
                            <div class="col-md-3">
                                <div class="view-card">
                                    <div class="view-label">{{ ucfirst(str_replace('_', ' ', $doc->key ?? $doc->name)) }}</div>
                                    <div class="view-value">
                                        @if (!empty($doc->path))
                                            <a href="{{ asset($doc->path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ico icon-outline-document"></i> View
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    @if($doc->expiry_date)
                                    <div class="text-muted xsmall mt-1">Expires: {{ \Carbon\Carbon::parse($doc->expiry_date)->format('d/m/Y') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($employmentDocs->count())
                    <div class="col-12">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Employment Documents</h6>
                        <div class="row g-2">
                            @foreach ($employmentDocs as $doc)
                            <div class="col-md-3">
                                <div class="view-card">
                                    <div class="view-label">{{ ucfirst(str_replace('_', ' ', $doc->key ?? $doc->name)) }}</div>
                                    <div class="view-value">
                                        @if (!empty($doc->path))
                                            <a href="{{ asset($doc->path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ico icon-outline-document"></i> View
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($otherDocs->count())
                    <div class="col-12">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Other Documents</h6>
                        <div class="row g-2">
                            @foreach ($otherDocs as $doc)
                            <div class="col-md-3">
                                <div class="view-card">
                                    <div class="view-label">{{ $doc->name ?? 'Document' }}</div>
                                    <div class="view-value">
                                        @if (!empty($doc->path))
                                            <a href="{{ asset($doc->path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ico icon-outline-document"></i> View
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-muted mb-0">No documents uploaded.</p>
            @endif
        </div>

    </div>
</div>

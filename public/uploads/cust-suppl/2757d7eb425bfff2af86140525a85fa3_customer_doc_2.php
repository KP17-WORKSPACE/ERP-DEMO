@php
use Illuminate\Support\Str;

// DEBUG: dump $company first to see if it exists
if (!isset($company) || !$company) {
    dd('Error: $company is null!');
}

// Safe assets
$logo = $company->company_logo ?: 'public/uploads/company/demo/company.png';
// Shortcuts
$comp     = optional($company->compliance);
$hr       = optional($company->hrPayroll);
$banks    = $company->banking ?? collect();
$policies = $company->hrPolicies ?? collect();
$docs = ($company->documents && $company->documents->count()) ? $company->documents->first() : collect();

$setting = ($company->settings && $company->settings->count()) ? $company->settings->first() : collect();

// Replace arrow function (not supported in PHP 7.1) with normal closure
$yn = function($v) {
    return $v ? 'Yes' : 'No';
};

$link = function($path, $label = 'View') {
    if (!$path) return '—';
    return '<a href="'.e(url('public/'.$path)).'" target="_blank" class="btn btn-xs btn-light">'.$label.'</a>';
};

@endphp


<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        Company Info (COM - {{ $comp->company_id ?? '—' }} )
    </h4>

    <div class="purchase-order-content-header-right">
        <a href="" class="btn btn-light">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>

        <a href="{{ url('company-add') }}" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>

            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ url('department') }}">Department</a></li>
                <li><a class="dropdown-item" href="{{ url('designation') }}">Designation</a></li>
            </ul>
        </div>
    </div>
</div>


<div class="card mb-2">
    <div class="card-body py-2">
        <div class="row g-2 align-items-start">

            {{-- LEFT LOGO --}}
            <div class="col-md-2 text-center">
                <img src="{{ asset('public/'.($logo)) }}" class="img-fluid rounded mb-1" alt="Company Logo">
                <div class="text-muted xsmall">{{ $company->industry ?: '—' }}</div>
            </div>

            {{-- RIGHT HEAD --}}
            <div class="col-md-10">

                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="small mb-0">Company ID:</label>
                        <div class="small">{{ $company->id ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <label class="small mb-0">Company Name:</label>
                        <div class="small">{{ $company->company_name ?? '—' }}</div>
                    </div>

                     <div class="col-md-2">
                        <label class="small mb-0">Trade Name:</label>
                        <div class="small">{{ $company->trade_name ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <label class="small mb-0">Business Entity:</label>
                        <div class="small">{{ optional($company->businessEntity)->name ?? '—' }}</div>
                    </div>

                    
                    <div class="col-md-2">
                        <label class="small mb-0">Industry</label>
                        <div class="small">{{ optional($company->businessIndustry)->name ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <label class="small mb-0">Sector</label>
                        <div class="small">{{ optional($company->businessSector)->name ?? '—' }}
                        </div>
                    </div>

                </div>

                <div class="row g-2 mt-2">
                   
                      <div class="col-md-2">
                    <label class="small mb-0">Country:</label>
                    <div class="small">
                    {{ optional($company->countryRelation)->name ?? ($company->country['name'] ?? '—') }}
                    </div>
                    </div>

                      <div class="col-md-2">
                    <label class="small mb-0">State:</label>
                    <div class="small">
                    {{ optional($company->stateRelation)->name ?? ($company->state ?? '—') }}
                    </div>
                    </div>

                     <div class="col-md-2">
                        <label class="small mb-0">City:</label>
                        <div class="small">{{ $company->city ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <label class="small mb-0">Company Type:</label>
                        <div class="small">{{ $company->company_type ?? '—' }}</div>
                    </div>

                    <div class="col-md-3">
                    <label class="small mb-0">
                    @if($company->company_type === 'parent')
                    Parent Company:
                    @else
                    Main Company:
                    @endif
                    </label>

                    <div class="small">
                    {{-- Parent company type --}}
                    @if($company->company_type === 'parent')
                    {{ $company->company_name }}

                    {{-- Subsidiary / Branch --}}
                    @elseif(in_array($company->company_type, ['subsidiary','branch']))
                    {{ optional($company->parentCompany)->company_name ?? '—' }}

                    @else
                    —
                    @endif
                    </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>


{{-- TABS --}}
<div class="tab-wrap mb-3 mt-2">
    <ul class="nav nav-tabs">

        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#contactTab">
                Contact Details
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#complianceTab">
                Company Registration
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bankingTab">
                Banking & Finance
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#policyTab">
                Policies
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docsTab">
                Documentation
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#settingsTab">
                Settings
            </button>
        </li>

    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white">

{{-- CONTACT TAB --}}
<div class="tab-pane fade show active" id="contactTab">

    <div class="row g-3">

    {{-- Company Email --}}
    <div class="col-md-3">
        <label class="small mb-0">Company Email:</label>
        <div class="small">{{ $company->email ?? '—' }}</div>
    </div>

    {{-- Website --}}
    <div class="col-md-3">
        <label class="small mb-0">Website:</label>
        <div class="small">
            @if($company->website)
                <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
            @else — @endif
        </div>
    </div>

    {{-- Office Phone --}}
    <div class="col-md-3">
        <label class="small mb-0">Office Phone:</label>
        <div class="small">{{ $company->telephone ?? '—' }}</div>
    </div>

    {{-- Mobile --}}
    <div class="col-md-3">
        <label class="small mb-0">Mobile:</label>
        <div class="small">{{ $company->mobile ?? '—' }}</div>
    </div>

    {{-- Date of Incorporation --}}
    <div class="col-md-3">
        <label class="small mb-0">Date of Incorporation:</label>
        <div class="small">{{ $company->date_of_incorporation ?? '—' }}</div>
    </div>

    {{-- Country --}}
    <div class="col-md-3">
        <label class="small mb-0">Country:</label>
        <div class="small">
            {{ optional($company->countryRelation)->name ?? ($company->country['name'] ?? '—') }}
        </div>
    </div>

    {{-- State --}}
    <div class="col-md-3">
        <label class="small mb-0">State:</label>
        <div class="small">
            {{ optional($company->stateRelation)->name ?? ($company->state ?? '—') }}
        </div>
    </div>

    {{-- Registered Address --}}
    <div class="col-md-3">
        <label class="small mb-0">Registered Address:</label>
        <div class="small">{{ $company->company_address ?? '—' }}</div>
    </div>

    {{-- Social Media Links --}}
    <div class="col-md-12">
    <label class="small mb-0">Social Media Links:</label>

    <div class="small d-flex flex-wrap gap-2">

    {{-- Facebook --}}
    <span>
        <strong>Facebook:</strong>
        @if(!empty($company->facebook))
            <a href="{{ $company->facebook }}" target="_blank">Link</a>
        @else
            —
        @endif
    </span>

    <span class="mx-2">|</span>

    {{-- Instagram --}}
    <span>
        <strong>Instagram:</strong>
        @if(!empty($company->instagram))
            <a href="{{ $company->instagram }}" target="_blank">Link</a>
        @else
            —
        @endif
    </span>

    <span class="mx-2">|</span>

    {{-- LinkedIn --}}
    <span>
        <strong>LinkedIn:</strong>
        @if(!empty($company->linkedin))
            <a href="{{ $company->linkedin }}" target="_blank">Link</a>
        @else
            —
        @endif
    </span>

    <span class="mx-2">|</span>

    {{-- Twitter --}}
    <span>
        <strong>Twitter:</strong>
        @if(!empty($company->twitter_x))
            <a href="{{ $company->twitter_x }}" target="_blank">Link</a>
        @else
            —
        @endif
    </span>

    </div>
    </div>


    <table class="table table-bordered table-sm mt-3">
    <thead class="table-light">
        <tr>
            <th style="width: 120px;">Type</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Designation</th>
            <th>Passport Copy</th>
            <th>Emirates ID</th>
            <th>Visa Copy</th>
        </tr>
    </thead>

    <tbody>
        @forelse($company->people as $p)
        <tr>
            <td class="text-capitalize">{{ $p->type }}</td>
            <td>{{ $p->name ?? '—' }}</td>
            <td>{{ $p->mobile ?? '—' }}</td>
            <td>{{ $p->email ?? '—' }}</td>

            {{-- Only contact person has designation --}}
            <td>
                @if($p->type == 'contact')
                    {{ $p->designation ?? '—' }}
                @else
                    —
                @endif
            </td>

            {{-- Passport Copy --}}
            <td>
                @if($p->passport_copy)
                    <a href="{{ asset('public/'.$p->passport_copy) }}" target="_blank" class="text-primary">View</a>
                @else
                    —
                @endif
            </td>

            {{-- Emirates ID --}}
            <td>
                @if($p->emirates_id)
                    <a href="{{ asset('public/'.$p->emirates_id) }}" target="_blank" class="text-primary">View</a>
                @else
                    —
                @endif
            </td>

            {{-- Visa Copy --}}
            <td>
                @if($p->visa_copy)
                    <a href="{{ asset('public/'.$p->visa_copy) }}" target="_blank" class="text-primary">View</a>
                @else
                    —
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">No person details available</td>
        </tr>
        @endforelse
    </tbody>
    </table>

    </div>

</div>

{{-- COMPLIANCE TAB --}}
<div class="tab-pane fade" id="complianceTab">

    @if($company && $company->compliance)
        <div class="row g-2">

            <div class="col-md-3">
                <label class="small mb-0">Trade License No:</label>
                <div class="small">{{ $company->compliance->trade_license_no  ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Issue Date:</label>
                <div class="small">{{ $company->compliance->license_issue_date ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Expiry Date:</label>
                <div class="small">{{ $company->compliance->license_expiry_date ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Issue Authority:</label>
                <div class="small">{{ $company->compliance->issuing_authority ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Vat Registration Number</label>
                <div class="small">{{ $company->compliance->vat_registration_number ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Vat Percentage</label>
                <div class="small">{{ $company->compliance->vat_percentage ?? '—' }}</div>
            </div>

             <div class="col-md-3">
                <label class="small mb-0"> Vat Certificate</label>
                <div class="small">
                @if(!empty($company->compliance->vat_certificate))
                    <a href="{{ asset('public/'.$company->compliance->vat_certificate) }}" target="_blank" class="text-primary">View</a>
                @else
                    —
                @endif
                </div>
            </div>

             <div class="col-md-3">
                <label class="small mb-0"> Vat Issuing Authority</label>
                <div class="small">{{ $company->compliance->vat_issuing_authority ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Corporate Tax Number</label>
                <div class="small">{{ $company->compliance->corporate_tax_number ?? '—' }}</div>
            </div>

             <div class="col-md-3">
                <label class="small mb-0">Corporate Tax Date</label>
                <div class="small">{{ $company->compliance->corporate_tax_date ?? '—' }}</div>
            </div>

            <div class="col-md-3">
                <label class="small mb-0">Corporate Tax Vat</label>
                <div class="small">{{ $company->compliance->corporate_tax_vat ?? '—' }}</div>
            </div>

            <div class="col-md-3">
            <label class="small mb-0">Corporate Tax Certificate</label>
            <div class="small">
            @if(!empty($company->compliance->corporate_tax_certificate))
                <a href="{{ asset('public/' . $company->compliance->corporate_tax_certificate) }}"
                target="_blank"
                class="text-primary">View</a>
            @else
                —
            @endif
            </div>
            </div>

              <div class="col-md-3">
                <label class="small mb-0">Corporate Issuing Authority</label>
                <div class="small">{{ $company->compliance->corporate_issuing_authority ?? '—' }}</div>
            </div>

        </div>

    @else
        <p class="small text-muted mb-0">No Compliance Found.</p>
    @endif

</div>

{{-- BANKING TAB --}}
<div class="tab-pane fade" id="bankingTab">

    @php $hasRows = $banks && $banks->count() > 0; @endphp

    @if($hasRows)

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bank</th>
                        <th>Branch</th>
                        <th>Account #</th>
                        <th>IBAN</th>
                        <th>SWIFT</th>
                        <th>Finance Code</th>
                        <th>Currency</th>
                        <th class="text-center">Letter</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($banks as $b)
                    <tr>
                        <td>{{ $b->bank_name ?? '—' }}</td>
                        <td>{{ $b->branch_name ?? $b->branch ?? '—' }}</td>
                        <td>{{ $b->account_number ?? '—' }}</td>
                        <td>{{ $b->iban_number ?? $b->iban ?? '—' }}</td>
                        <td>{{ $b->swift_code ?? '—' }}</td>
                        <td>{{ $b->finance_code ?? '—' }}</td>
                        <td>{{ optional($b->currency)->code ?? ($b->currency ?? '—') }}</td>
                        <td class="text-center">{!! $link($b->bank_letter ?? $b->finance_letter ?? null) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @else

        <div class="row g-2">
            <div class="col-md-3"><label class="small mb-0">Bank Name:</label> <div class="small">{{ $company->bank_name ?? '—' }}</div></div>
            <div class="col-md-3"><label class="small mb-0">Account #:</label> <div class="small">{{ $company->account_number ?? '—' }}</div></div>
            <div class="col-md-3"><label class="small mb-0">IBAN:</label> <div class="small">{{ $company->iban_no ?? '—' }}</div></div>
            <div class="col-md-3"><label class="small mb-0">SWIFT:</label> <div class="small">{{ $company->branch_swift_code ?? '—' }}</div></div>
            <div class="col-md-3"><label class="small mb-0">Finance Code:</label> <div class="small">{{ $company->finance_code ?? '—' }}</div></div>
            <div class="col-md-3"><label class="small mb-0">Bank Letter:</label> <div class="small">{!! $link($company->company_logo ?? null) !!}</div></div>
        </div>

    @endif

</div>

{{-- POLICIES TAB --}}
<div class="tab-pane fade" id="policyTab">

    @php
        $fmt = function($d) {
            if (!$d) return '—';
            try { return \Carbon\Carbon::parse($d)->format('d M Y'); }
            catch(\Exception $e) { return $d; }
        };
    @endphp

    @if($policies->count())

        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Details</th>
                        <th>Category</th>
                        <th>Valid Till</th>
                        <th>Visible</th>
                        <th>File</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($policies as $p)
                    <tr>
                        <td>{{ $fmt($p->policy_date) }}</td>
                        <td>{{ Str::limit($p->policy_name, 40) }}</td>
                        <td>{{ Str::limit($p->policy_details, 40) }}</td>
                        <td>{{ ucfirst($p->policy_category) }}</td>
                        <td>{{ $fmt($p->policy_valid) }}</td>
                        <td>
                            @if($p->view_to_employees)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>{!! $link($p->policy_file) !!}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    @else
        <p class="small text-muted">No policies saved.</p>
    @endif

</div>

{{-- DOCUMENTATION TAB --}}
<div class="tab-pane fade" id="docsTab">

    @php
        $rows = [
            ['Establishment Card', $docs->establishment_number ?? null, $docs->establishment_expiry ?? null, $docs->establishment_file ?? null],
            ['Immigration Card',   $docs->immigration_number ?? null,   $docs->immigration_expiry ?? null,   $docs->immigration_file ?? null],
            ['Labour Card',        $docs->labour_number ?? null,        $docs->labour_expiry ?? null,        $docs->labour_file ?? null],
            ['Chamber Certificate',$docs->chamber_number ?? null,       $docs->chamber_expiry ?? null,       $docs->chamber_file ?? null],
            ['Insurance Cert.',    $docs->insurance_certificate_number ?? null, $docs->insurance_certificate_expiry ?? null, $docs->insurance_file ?? null],
            ['MOA / AOA',          null, null, $docs->moa_aoa_file ?? null],
            ['Board Resolution',   null, null, $docs->board_resolution_file ?? null],
            ['Power of Attorney',  null, null, $docs->poa_file ?? null],
        ];
    @endphp

    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Number</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>File</th>
                </tr>
            </thead>

            <tbody>
            @foreach($rows as $row)
                @php list($label, $num, $exp, $file) = $row; @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $num ?: '—' }}</td>
                    <td>{{ $exp ?: '—' }}</td>
                    <td class="text-center">
                        @if($file)
                            <i class="ico icon-outline-check-read text-success"></i>
                        @else
                            <i class="ico icon-outline-close text-danger"></i>
                        @endif
                    </td>
                    <td class="text-center">{!! $link($file) !!}</td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

</div>

{{-- SETTINGS TAB --}}
<div class="tab-pane fade" id="settingsTab">

    <div class="row g-3 mt-2">

        <div class="col-md-2">
            <label class="small mb-0">Customer Code:</label>
            <div class="small">{{ ($setting && isset($setting->is_customer_code)) ? ($setting->is_customer_code ? 'Yes' : 'No') : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Supplier Code:</label>
            <div class="small">{{ ($setting && isset($setting->is_supplier_code)) ? ($setting->is_supplier_code ? 'Yes' : 'No') : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Account Code:</label>
            <div class="small">{{ ($setting && isset($setting->is_account_code)) ? ($setting->is_account_code ? 'Yes' : 'No') : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Sub-Account Code:</label>
            <div class="small">{{ ($setting && isset($setting->is_subaccount_code)) ? ($setting->is_subaccount_code ? 'Yes' : 'No') : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Currency:</label>
            <div class="small">{{ $setting->currency ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Currency Digit:</label>
            <div class="small">{{ $setting->currency_digit ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Book Closed:</label>
            <div class="small">
                @if(!empty($setting->book_closed))
                    {{ \Carbon\Carbon::parse($setting->book_closed)->format('d M Y') }}
                @else
                    —
                @endif
            </div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Sales Code:</label>
            <div class="small">{{ $setting->sales_code ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Other Code:</label>
            <div class="small">{{ $setting->other_code ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">WPS Establishment ID:</label>
            <div class="small">{{ $setting->hr_wps_establishment_id ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">WPS Bank:</label>
            <div class="small">{{ $setting->hr_wps_bank ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">WPS Salary File Code:</label>
            <div class="small">{{ $setting->hr_wps_salary_file_code ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Payroll Cycle:</label>
            <div class="small">{{ $setting->hr_payroll_cycle ? ucfirst($setting->hr_payroll_cycle) : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Payroll Start:</label>
            <div class="small">{{ $setting->hr_payroll_start ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Payroll End:</label>
            <div class="small">{{ $setting->hr_payroll_end ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Weekly Off:</label>
            <div class="small">{{ $setting->hr_weekly_off ? ucfirst($setting->hr_weekly_off) : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Gratuity Method:</label>
            <div class="small">{{ $setting->hr_gratuity_method ? ucfirst(str_replace('_', ' ', $setting->hr_gratuity_method)) : '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Insurance Provider:</label>
            <div class="small">{{ $setting->hr_insurance_provider ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Insurance Policy No.:</label>
            <div class="small">{{ $setting->hr_insurance_policy_number ?? '—' }}</div>
        </div>

        <div class="col-md-2">
            <label class="small mb-0">Insurance Expiry:</label>
            <div class="small">
                @if(!empty($setting->hr_insurance_policy_expiry))
                    {{ \Carbon\Carbon::parse($setting->hr_insurance_policy_expiry)->format('d M Y') }}
                @else
                    —
                @endif
            </div>
        </div>

    </div>

</div>


</div> {{-- tab-content --}}
</div> {{-- tab-wrap --}}

<style>
.small { font-size:.85rem; }
.xsmall { font-size:.75rem; }
.truncate { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.custom-green {
    width: 3.5em !important;
    height: 1.5em !important;
    cursor: pointer;
    border-color: #499258 !important;
}
.custom-green:checked {
    background-color:#499258 !important;
    border-color:#499258 !important;
}
</style>

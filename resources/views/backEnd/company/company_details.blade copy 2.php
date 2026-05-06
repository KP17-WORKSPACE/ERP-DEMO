@php

    // DEBUG: dump $company first to see if it exists
    if (!isset($company) || !$company) {
        // Non-fatal: render a friendly placeholder and stop processing this include.
        echo '<div class="p-3 text-muted">
No company selected or company details are not available.</div>';
        return;
    }

    // Safe assets
    $logo = $company->company_logo ?: 'public/uploads/company/demo/company.png';
    // Shortcuts
    $comp = optional($company->compliance);
    $hr = optional($company->hrPayroll);
    $banks = $company->banking ?? collect();
    $warehouses = $company->warehouses ?? collect();
    $policies = $company->hrPolicies ?? collect();
    $docs = null;
    if ($company->documents && $company->documents->count()) {
        $first = $company->documents->first();
        // If the relationship returns a nested collection, try to unwrap one level
        if ($first instanceof \Illuminate\Support\Collection) {
            $first = $first->first();
        }
        // If it's an array convert to object for property-style access later
    if (is_array($first)) {
        $docs = (object) $first;
    } else {
        $docs = $first;
    }
}

// Fix: Load settings specifically for current company instead of using potentially wrong relationship
$setting = \App\SysCompanySetting::where('company_id', $company->id)->first();
// Replace arrow function (not supported in PHP 7.1) with normal closure
$yn = function ($v) {
    return $v ? 'Yes' : 'No';
};

$link = function ($path, $label = 'View') {
    if (!$path) {
        return '—';
    }
    return '<a href="' .
        e(url('public/' . $path)) .
        '" target="_blank" class="btn btn-xs btn-light">' .
        $label .
        '</a>';
    };

@endphp


<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        Company Info (COM - {{ $comp->id ?? '—' }} )
    </h4>

    <div class="purchase-order-content-header-right">
        <a href="{{ url('company-edit/' . $company->id) }}" class="btn btn-light">
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
                <img src="{{ asset('public/' . $logo) }}" class="img-fluid rounded mb-1"
                    style="max-width: 100%; height: 120px; object-fit: cover;" alt="Company Logo">
            </div>

            {{-- RIGHT HEAD --}}
            <div class="col-md-10">

                <div class="row g-4">
                    {{-- <div class="col-md-2">
                        <label class="small mb-0">Company ID:</label>
                        <div class="small">{{ $company->id ?? '—' }}</div>
                    </div> --}}

                    <div class="col-md-4">
                        <label class="small mb-0">Company Name:</label>

                        <span
                            class="font-weight-600 title-15 me-3 text-success    form-control-plaintext fw-semibold truncate-text"
                            title="{{ $company->company_name }}">
                            {{ $company->company_name ?? '—' }}
                        </span>


                    </div>

                    <div class="col-md-4">
                        <label class="small mb-0">Trade Name:</label>
                        <div class="small">{{ $company->trade_name ?? '—' }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="small mb-0">Business Entity:</label>
                        <div class="small">{{ optional($company->businessEntity)->name ?? '—' }}</div>
                    </div>


                    <div class="col-md-4">
                        <label class="small mb-0">Industry</label>
                        <div class="small">{{ optional($company->businessIndustry)->name ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <label class="small mb-0">Sector</label>
                        <div class="small">{{ optional($company->businessSector)->name ?? '—' }}
                        </div>
                    </div>


                    <div class="col-md-2">
                        <label class="small mb-0">Company Type:</label>
                        <div class="small">{{ $company->company_type ?? '—' }}</div>
                    </div>

                    <div class="col-md-4">
                        <label class="small mb-0">
                            @if ($company->company_type === 'parent')
                                Parent Company:
                            @else
                                Main Company:
                            @endif
                        </label>

                        <div class="small">
                            {{-- Parent company type --}}
                            @if ($company->company_type === 'parent')
                                {{ $company->company_name }}

                                {{-- Subsidiary / Branch --}}
                            @elseif(in_array($company->company_type, ['subsidiary', 'branch']))
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
                Contact Information
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#settingsTab">
                Company Settings
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
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#warehouseTab">
                Warehouse Information
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#policyTab">
                Company Policies
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hrPayrollTab">
                HRMS Settings
            </button>
        </li>


        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docsTab">
                Documentation
            </button>
        </li>



    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white">
        {{-- CONTACT TAB --}}
        <div class="tab-pane fade show active" id="contactTab">
            <div class="row g-3">

                {{-- Company Email --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Company
                        Email</p>
                    <div class="small text-center">{{ $company->email ?? '—' }}</div>
                </div>

                {{-- Website --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Website
                    </p>
                    <div class="small text-center">
                        @if ($company->website)
                            <a href="{{ $company->website }}" target="_blank">View Site</a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                {{-- Office Phone --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Office
                        Phone</p>
                    <div class="small text-center">{{ $company->telephone ?? '—' }}</div>
                </div>

                {{-- Mobile --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Mobile
                    </p>
                    <div class="small text-center">{{ $company->mobile ?? '—' }}</div>
                </div>



                {{-- Date of Incorporation --}}
                <div class="col-md-3">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Date
                        of Incorporation</p>
                    <div class="small text-center">{{ $company->date_of_incorporation ?? '—' }}</div>
                </div>

                {{-- Country --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Country</p>
                    <div class="small text-center">
                        {{ optional($company->countryRelation)->name ?? ($company->country['name'] ?? '—') }}
                    </div>
                </div>

                {{-- State --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">State
                    </p>
                    <div class="small text-center">
                        {{ optional($company->stateRelation)->name ?? ($company->state ?? '—') }}
                    </div>
                </div>

                {{-- City --}}
                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">City
                    </p>
                    <div class="small text-center">{{ $company->city ?? '—' }}</div>
                </div>


                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Area
                    </p>
                    <div class="small text-center">{{ $company->area ?? '—' }}</div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Building Name</p>
                    <div class="small text-center">{{ $company->building_no ?? '—' }}</div>
                </div>

                <div class="col-lg-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Flat /
                        Office No</p>
                    <div class="small text-center">{{ $company->floor_shop_no ?? '—' }}</div>
                </div>
                {{-- Social Media Links --}}
                <div class="col-lg-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Facebook</p>
                    <div class="small text-center">
                        @if (!empty($company->facebook))
                            <a href="{{ $company->facebook }}" target="_blank">Link</a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="col-lg-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Instagram</p>
                    <div class="small text-center">
                        @if (!empty($company->instagram))
                            <a href="{{ $company->instagram }}" target="_blank">Link</a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="col-lg-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        LinkedIn</p>
                    <div class="small text-center">
                        @if (!empty($company->linkedin))
                            <a href="{{ $company->linkedin }}" target="_blank">Link</a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="col-lg-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Twitter</p>
                    <div class="small text-center">
                        @if (!empty($company->twitter_x))
                            <a href="{{ $company->twitter_x }}" target="_blank">Link</a>
                        @else
                            —
                        @endif
                    </div>
                </div>


                <div class="accordion mt-3" id="peopleAccordion" style="margin-left: -18px;">
                    @php
                        $owners = $company->people->where('type', 'owner');
                        $contacts = $company->people->where('type', 'contact');
                        $sponsors = $company->people->where('type', 'sponsor');
                        $others = $company->people->whereNotIn('type', ['owner', 'contact', 'sponsor']);
                    @endphp

                    {{-- OWNER DETAILS --}}
                    @if ($owners->count() > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOwners">
                                    <span class="text-capitalize me-2 badge bg-primary">Owner Details</span>
                                </button>
                            </h2>
                            <div id="collapseOwners" class="accordion-collapse collapse show"
                                data-bs-parent="#peopleAccordion">
                                <div class="accordion-body">
                                    @foreach ($owners as $index => $p)
                                        <div class="mb-3">
                                            <h6 class="mb-1 text-primary">Owner {{ $index + 1 }}</h6>
                                            <div class="row gy-1">

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Name</p>
                                                    <div class="small text-center">{{ $p->name ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                                                    <div class="small text-center">{{ $p->mobile ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                                    <div class="small text-center">{{ $p->email ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Share %</p>
                                                    <div class="small text-center">{{ $p->share_percentage ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">View
                                                        Documents</p>
                                                    <div class="small text-center">
                                                        @php
                                                            $companyDocs = $company->documentItems ?? collect();
                                                            $docCount = $companyDocs->count();
                                                        @endphp
                                                        @if($docCount > 0)
                                                            <button class="btn btn-sm btn-primary" onclick="showDocuments({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                                                View ({{ $docCount }})
                                                            </button>
                                                        @else
                                                            <span class="text-muted">No documents</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CONTACT DETAILS --}}
                    @if ($contacts->count() > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $owners->count() > 0 ? 'collapsed' : '' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseContacts">
                                    <span class="text-capitalize me-2 badge bg-success">Contact Details</span>
                                </button>
                            </h2>
                            <div id="collapseContacts"
                                class="accordion-collapse collapse {{ $owners->count() == 0 ? 'show' : '' }}"
                                data-bs-parent="#peopleAccordion">
                                <div class="accordion-body">
                                    @foreach ($contacts as $index => $p)
                                        <div class="mb-3">
                                            <div class="row gy-1">

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Name</p>
                                                    <div class="small text-center">{{ $p->name ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                                                    <div class="small text-center">{{ $p->mobile ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                                    <div class="small text-center">{{ $p->email ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">
                                                        Designation</p>
                                                    <div class="small text-center">{{ $p->designation ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">View Documents</p>
                                                    <div class="small text-center">
                                                        @php
                                                            $companyDocs = $company->documentItems ?? collect();
                                                            $docCount = $companyDocs->count();
                                                        @endphp
                                                        @if($docCount > 0)
                                                            <button class="btn btn-sm btn-primary" onclick="showDocuments({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                                                View ({{ $docCount }})
                                                            </button>
                                                        @else
                                                            <span class="text-muted">No documents</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SPONSOR DETAILS --}}
                    @if ($sponsors->count() > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseSponsors">
                                    <span class="text-capitalize me-2 badge bg-warning">Sponsor Details</span>
                                </button>
                            </h2>
                            <div id="collapseSponsors" class="accordion-collapse collapse"
                                data-bs-parent="#peopleAccordion">
                                <div class="accordion-body">
                                    @foreach ($sponsors as $index => $p)
                                        <div class="mb-3">
                                            <div class="row gy-1">

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Name</p>
                                                    <div class="small text-center">{{ $p->name ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                                                    <div class="small text-center">{{ $p->mobile ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                                    <div class="small text-center">{{ $p->email ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">View Documents</p>
                                                    <div class="small text-center">
                                                        @php
                                                            $companyDocs = $company->documentItems ?? collect();
                                                            $docCount = $companyDocs->count();
                                                        @endphp
                                                        @if($docCount > 0)
                                                            <button class="btn btn-sm btn-primary" onclick="showDocuments({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                                                View ({{ $docCount }})
                                                            </button>
                                                        @else
                                                            <span class="text-muted">No documents</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- OTHER TYPES --}}
                    @if ($others->count() > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOthers">
                                    <span class="text-capitalize me-2 badge bg-secondary">Other Details</span>
                                </button>
                            </h2>
                            <div id="collapseOthers" class="accordion-collapse collapse"
                                data-bs-parent="#peopleAccordion">
                                <div class="accordion-body">
                                    @foreach ($others as $index => $p)
                                        <div class="mb-3">
                                            <h6 class="mb-1 text-secondary">{{ ucfirst($p->type) }}
                                                {{ $index + 1 }}</h6>
                                            <div class="row gy-1">

                                                <div class="col-lg-3">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Name</p>
                                                    <div class="small text-center">{{ $p->name ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                                                    <div class="small text-center">{{ $p->mobile ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                                    <div class="small text-center">{{ $p->email ?? '—' }}</div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <p class="font-weight-600 text-center"
                                                        style="background-color: #deebe1;margin-bottom: 3px">View Documents</p>
                                                    <div class="small text-center">
                                                        @php
                                                            $companyDocs = $company->documentItems ?? collect();
                                                            $docCount = $companyDocs->count();
                                                        @endphp
                                                        @if($docCount > 0)
                                                            <button class="btn btn-sm btn-primary" onclick="showDocuments({{ $company->id }}, '{{ addslashes($company->company_name) }}')">
                                                                View ({{ $docCount }})
                                                            </button>
                                                        @else
                                                            <span class="text-muted">No documents</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($company->people->count() == 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No person details available
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- COMPLIANCE TAB --}}
        <div class="tab-pane fade" id="complianceTab">

            @if ($company && $company->compliance)
                @php $c = $company->compliance; @endphp
                <div class="row g-3">

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Trade License No</p>
                        <div class="small text-center">
                            {{ !empty(trim($c->trade_license_no ?? '')) ? $c->trade_license_no : '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            License Issue Date</p>
                        <div class="small text-center">
                            {{ $c->license_issue_date ? \Carbon\Carbon::parse($c->license_issue_date)->format('d/m/Y') : '—' }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            License Expiry Date</p>
                        <div class="small text-center">
                            {{ $c->license_expiry_date ? \Carbon\Carbon::parse($c->license_expiry_date)->format('d/m/Y') : '—' }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Issuing Authority</p>
                        <div class="small text-center">
                            {{ !empty(trim($c->issuing_authority ?? '')) ? $c->issuing_authority : '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Trade License Upload</p>
                        <div class="small text-center">
                            @php
                                // Check for both attachment fields (attachment for non-UAE, business_license_upload for UAE legacy)
                                $attachmentFile = $c->attachment ?: $c->business_license_upload;
                            @endphp
                            @if (!empty($attachmentFile))
                                <a href="{{ asset('storage/' . $attachmentFile) }}"
                                    target="_blank">View</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Tax Applicable</p>
                        <div class="small text-center">
                            @if ($c->tax_applicable == 'vat')
                                VAT
                            @elseif($c->tax_applicable == 'ct')
                                CT
                            @elseif($c->tax_applicable == 'both')
                                Both (CT/VAT)
                            @else
                                Not Applicable
                            @endif
                        </div>
                    </div>

                    {{-- VAT fields --}}
                    @if (in_array($c->tax_applicable, ['vat', 'both']))
                        <div class="col-md-2">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">VAT Registration No (TRN)</p>
                            <div class="small text-center">{{ $c->vat_registration_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-2">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">VAT %</p>
                            <div class="small text-center">{{ $c->vat_percentage ?? '—' }}</div>
                        </div>
                        <div class="col-md-2">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">VAT Registration Date</p>
                            <div class="small text-center">
                                {{ $c->vat_date ? \Carbon\Carbon::parse($c->vat_date)->format('d/m/Y') : '—' }}</div>
                        </div>
                        <div class="col-md-2">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">VAT Issuing Authority</p>
                            <div class="small text-center">{{ $c->vat_issuing_authority ?? '—' }}</div>
                        </div>
                        <div class="col-md-2">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">VAT Certificate</p>
                            <div class="small text-center">
                                @if (!empty($c->vat_certificate))
                                    <a href="{{ asset('storage/' . $c->vat_certificate) }}" target="_blank">View</a>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- CT fields --}}
                    @if (in_array($c->tax_applicable, ['ct', 'both']))
                        <div class="col-md-3">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">CT Registration No (CTN)</p>
                            <div class="small text-center">{{ $c->corporate_tax_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">CT %</p>
                            <div class="small text-center">{{ $c->corporate_tax_vat ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">CT Registration Date</p>
                            <div class="small text-center">
                                {{ $c->corporate_tax_date ? \Carbon\Carbon::parse($c->corporate_tax_date)->format('d/m/Y') : '—' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">CT Issuing Authority</p>
                            <div class="small text-center">
                                {{ $c->corporate_issuing_authority ?? ($c->ct_issuing_authority ?? '—') }}</div>
                        </div>
                        <div class="col-md-3">
                            <p class="font-weight-600 text-center"
                                style="background-color: #deebe1;margin-bottom: 3px">CT Certificate</p>
                            <div class="small text-center">
                                @if (!empty($c->corporate_tax_certificate))
                                    <a href="{{ asset('storage/' . $c->corporate_tax_certificate) }}"
                                        target="_blank">View</a>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            @else
                <p class="small text-muted mb-0">No Compliance Found.</p>
            @endif

        </div>

        {{-- BANKING TAB --}}
        <div class="tab-pane fade" id="bankingTab">

            @php $hasRows = $banks && $banks->count() > 0; @endphp

            @if ($hasRows)

                <div class="table-responsive">
                    <table class="table table-hover data-table" style="table-layout: fixed;width:100%">
                        <thead>
                            <tr style="text-align: center">
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Account No</th>
                                <th>IBAN</th>
                                <th>SWIFT</th>
                                <th>Finance Code</th>
                                <th>Currency</th>
                                <th class="text-center">Letter</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($banks as $b)
                                <tr style="text-align: center">
                                    <td>{{ $b->bank_name ?? '—' }}</td>
                                    <td>{{ $b->branch_name ?? ($b->branch ?? '—') }}</td>
                                    <td>{{ $b->account_number ?? '—' }}</td>
                                    <td>{{ $b->iban_number ?? ($b->iban ?? '—') }}</td>
                                    <td>{{ $b->swift_code ?? '—' }}</td>
                                    <td>{{ $b->finance_code ?? '—' }}</td>
                                    <td>{{ optional($b->currency)->code ?? ($b->currency ?? '—') }}</td>
                                    <td class="text-center">
                                        @if ($b->bank_letter ?? ($b->finance_letter ?? null))
                                            <a href="{{ asset('public/' . ($b->bank_letter ?? $b->finance_letter)) }}"
                                                target="_blank">View File</a>
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
                <div class="row g-2">
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Bank Name</p>
                        <div class="small text-center">{{ $company->bank_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Account No</p>
                        <div class="small text-center">{{ $company->account_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            IBAN</p>
                        <div class="small text-center">{{ $company->iban_no ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            SWIFT</p>
                        <div class="small text-center">{{ $company->branch_swift_code ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Finance Code</p>
                        <div class="small text-center">{{ $company->finance_code ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Bank Letter</p>
                        <div class="small text-center">{{ $company->bank_letter ? 'View File' : '—' }}</div>
                    </div>
                </div>

            @endif

        </div>

        {{-- WAREHOUSE TAB --}}
        <div class="tab-pane fade" id="warehouseTab">

            @php $hasWarehouses = $warehouses && $warehouses->count() > 0; @endphp

            @if ($hasWarehouses)

                @foreach ($warehouses as $warehouse)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $warehouse->warehouse_name ?? 'Warehouse' }} - {{ $warehouse->warehouse_code ?? 'N/A' }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Warehouse Code</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_code ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Warehouse Name</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Country</p>
                                    <div class="small text-center">{{ $warehouse->country->name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">State</p>
                                    <div class="small text-center">{{ $warehouse->state->name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">City</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_city ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Area</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_area ?? '—' }}</div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Building Name</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_building_name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Flat / Office No</p>
                                    <div class="small text-center">{{ $warehouse->warehouse_flat_office_no ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">First Name</p>
                                    <div class="small text-center">{{ $warehouse->contact_first_name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Last Name</p>
                                    <div class="small text-center">{{ $warehouse->contact_last_name ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                                    <div class="small text-center">{{ $warehouse->contact_mobile ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                    <div class="small text-center">{{ $warehouse->contact_email ?? '—' }}</div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Designation</p>
                                    <div class="small text-center">{{ $warehouse->contact_designation ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Fire Safety Status</p>
                                    <div class="small text-center">
                                        @if($warehouse->fire_safety_compliance_status)
                                            @php
                                                $status = $warehouse->fire_safety_compliance_status;
                                                $statusClass = 'bg-secondary';
                                                
                                                if ($status === 'compliant') {
                                                    $statusClass = 'bg-success';
                                                } elseif ($status === 'non_compliant') {
                                                    $statusClass = 'bg-danger';
                                                } elseif ($status === 'pending') {
                                                    $statusClass = 'bg-warning';
                                                }
                                                
                                                $statusText = ucwords(str_replace('_', ' ', $status));
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Safety Equipment</p>
                                    <div class="small text-center">
                                        @if($warehouse->safety_equipment_available)
                                            @php
                                                $equipment = $warehouse->safety_equipment_available;
                                                $equipmentClass = 'bg-secondary';
                                                $equipmentText = '';
                                                
                                                if ($equipment === 'yes') {
                                                    $equipmentClass = 'bg-success';
                                                    $equipmentText = 'Yes';
                                                } elseif ($equipment === 'no') {
                                                    $equipmentClass = 'bg-danger';
                                                    $equipmentText = 'No';
                                                } elseif ($equipment === 'partial') {
                                                    $equipmentClass = 'bg-warning';
                                                    $equipmentText = 'Partial';
                                                }
                                            @endphp
                                            <span class="badge {{ $equipmentClass }}">{{ $equipmentText }}</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">NOC Certificate</p>
                                    <div class="small text-center">{{ $warehouse->fire_noc_certificate_number ?? '—' }}</div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">NOC Expiry Date</p>
                                    <div class="small text-center">
                                        @if($warehouse->fire_noc_expiry_date)
                                            {{ \Carbon\Carbon::parse($warehouse->fire_noc_expiry_date)->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Last Safety Inspection</p>
                                    <div class="small text-center">
                                        @if($warehouse->last_safety_inspection_date)
                                            {{ \Carbon\Carbon::parse($warehouse->last_safety_inspection_date)->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-lg-2">
                                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Documents</p>
                                    <div class="small text-center">
                                        @if($warehouse->contact_documents)
                                            @php
                                                $docs = is_string($warehouse->contact_documents) ? json_decode($warehouse->contact_documents, true) : $warehouse->contact_documents;
                                                $docCount = is_array($docs) ? count(array_filter($docs)) : 0;
                                            @endphp
                                            @if($docCount > 0)
                                                <span class="badge bg-info">{{ $docCount }} Files</span>
                                            @else
                                                —
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @else

                {{-- Basic Information Row --}}
                <div class="row mb-3">
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Warehouse Code</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Warehouse Name</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Country</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">State</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">City</p>
                        <div class="small text-center">—</div>
                    </div>
                </div>

                {{-- Address Information Row --}}
                <div class="row mb-3">
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Area</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Building Name</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Flat / Office No</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">First Name</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Last Name</p>
                        <div class="small text-center">—</div>
                    </div>
                </div>

                {{-- Contact & Safety Information Row --}}
                <div class="row mb-3">
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Designation</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Fire Safety Status</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Safety Equipment Available</p>
                        <div class="small text-center">—</div>
                    </div>
                </div>

                {{-- Compliance & Dates Information Row --}}
                <div class="row mb-3">
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Fire NOC Certificate Number</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Fire NOC Expiry Date</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Last Safety Inspection Date</p>
                        <div class="small text-center">—</div>
                    </div>
                    <div class="col-lg-3">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Contact Documents</p>
                        <div class="small text-center">—</div>
                    </div>
                </div>

            @endif

        </div>

        {{-- POLICIES TAB --}}
        <div class="tab-pane fade" id="policyTab">

            @php
                $fmt = function ($d) {
                    if (!$d) {
                        return '—';
                    }
                    try {
                        return \Carbon\Carbon::parse($d)->format('d/m/Y');
                    } catch (\Exception $e) {
                        return $d;
                    }
                };
            @endphp

            @if ($policies->count())

                <div class="table-responsive">
                    <table class="table table-hover data-table" style="table-layout: fixed;width:100%">
                        <thead>
                            <tr style="text-align: center">
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
                            @foreach ($policies as $p)
                                <tr style="text-align: center">
                                    <td>{{ $fmt($p->policy_date) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($p->policy_name, 40) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($p->policy_details, 12) }}</td>

                                    <td>{{ ucfirst($p->policy_category) }}</td>
                                    <td>{{ $fmt($p->policy_valid) }}</td>
                                    <td>
                                        @if ($p->view_to_employees)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($p->policy_file)
                                            <a href="{{ asset('public/' . $p->policy_file) }}" target="_blank">View
                                                File</a>
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
                <p class="small text-muted">No policies saved.</p>
            @endif

        </div>

        {{-- DOCUMENTATION TAB --}}
        <div class="tab-pane fade" id="docsTab">

            @php
                $countryId = $company->country ?? null;
                $isUae = $countryId == '231';
                $nonUaeDocs = null;

                // Load non-UAE documents if applicable
                if (!$isUae && $company->id) {
                    $nonUaeDocs = \App\SysCompanyDocumentItem::where('company_id', $company->id)->get();
                }
            @endphp

            {{-- UAE DOCUMENTS --}}
            @if ($isUae)
                @php
                    $rows = [
                        [
                            'Establishment Card',
                            optional($docs)->establishment_number ?? null,
                            optional($docs)->establishment_expiry ?? null,
                            optional($docs)->establishment_file ?? null,
                        ],
                        [
                            'Immigration Card',
                            optional($docs)->immigration_number ?? null,
                            optional($docs)->immigration_expiry ?? null,
                            optional($docs)->immigration_file ?? null,
                        ],
                        [
                            'Labour Card',
                            optional($docs)->labour_number ?? null,
                            optional($docs)->labour_expiry ?? null,
                            optional($docs)->labour_file ?? null,
                        ],
                        [
                            'Chamber Certificate',
                            optional($docs)->chamber_number ?? null,
                            optional($docs)->chamber_expiry ?? null,
                            optional($docs)->chamber_file ?? null,
                        ],
                        [
                            'Insurance Cert.',
                            optional($docs)->insurance_certificate_number ?? null,
                            optional($docs)->insurance_certificate_expiry ?? null,
                            optional($docs)->insurance_file ?? null,
                        ],
                        ['MOA / AOA', null, null, optional($docs)->moa_aoa_file ?? null],
                        ['Board Resolution', null, null, optional($docs)->board_resolution_file ?? null],
                        ['Power of Attorney', null, null, optional($docs)->poa_file ?? null],
                    ];
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover data-table" style="table-layout: fixed;width:100%">

                        <thead>
                            <tr style="text-align: center">
                                <th style="text-align: left">Document</th>
                                <th>Number</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th>File</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rows as $row)
                                @php list($label, $num, $exp, $file) = $row; @endphp
                                <tr style="text-align: center">
                                    <td style="text-align: left">{{ $label }}</td>
                                    <td>{{ $num ?: '—' }}</td>
                                    <td>{{ $exp ? \Carbon\Carbon::parse($exp)->format('d/m/Y') : '—' }}</td>
                                    <td class="text-center">
                                        @if ($file)
                                            <i class="ico icon-outline-check-read text-success"></i>
                                        @else
                                            <i class="ico icon-outline-close text-danger"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($file)
                                            <a href="{{ asset('public/' . $file) }}" target="_blank">View File</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{-- NON-UAE DOCUMENTS --}}
            @else
                @if ($nonUaeDocs && $nonUaeDocs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr style="text-align: center">
                                    <th style="text-align: left">Document Name</th>
                                    <th>Document Number</th>
                                    <th>Document Date</th>
                                    <th>Expiry Date</th>
                                    {{-- <th>Status</th> --}}
                                    <th>File</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($nonUaeDocs as $doc)
                                    @php
                                        $fmt = function ($d) {
                                            if (!$d) {
                                                return '—';
                                            }
                                            try {
                                                return \Carbon\Carbon::parse($d)->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                return $d;
                                            }
                                        };
                                    @endphp
                                    <tr style="text-align: center">
                                        <td style="text-align: left">{{ $doc->document_name ?? '—' }}</td>
                                        <td>{{ $doc->document_number ?? '—' }}</td>
                                        <td>{{ $fmt($doc->document_date) }}</td>
                                        <td>{{ $fmt($doc->expiry_date) }}</td>
                                        {{-- <td class="text-center">
                                @if ($doc->attachment_file)
                                    <i class="ico icon-outline-check-read text-success"></i>
                                @else
                                    <i class="ico icon-outline-close text-danger"></i>
                                @endif
                            </td> --}}
                                        <td class="text-center">
                                            @if ($doc->attachment_file)
                                                <a href="{{ asset('public/' . $doc->attachment_file) }}"
                                                    target="_blank">View File</a>
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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No documents saved yet.
                    </div>
                @endif
            @endif

        </div>

        {{-- SETTINGS TAB --}}
        <div class="tab-pane fade" id="settingsTab">

            <div class="row g-3">

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Currency</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->currency) ? $setting->currency : '—' }}</div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Symbol
                    </p>
                    <div class="small text-center">{{ $setting && isset($setting->currency_symbol) ? $setting->currency_symbol : '—' }}
                    </div>
                </div>

                {{-- debug removed: dd($setting) --}}

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Currency Digit</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->currency_digit) ? $setting->currency_digit : '—' }}</div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">R Code
                    </p>
                    <div class="small text-center">{{ $setting && isset($setting->r_code) ? $setting->r_code : '—' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">P Code
                    </p>
                    <div class="small text-center">{{ $setting && isset($setting->p_code) ? $setting->p_code : '—' }}
                    </div>
                </div>



                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Book
                        Closed</p>
                    <div class="small text-center">
                        @if ($setting && !empty($setting->book_closed))
                            {{ \Carbon\Carbon::parse($setting->book_closed)->format('d/m/Y') }}
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Sales
                        Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->sales_code) ? $setting->sales_code : '—' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">Other
                        Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->other_code) ? $setting->other_code : '—' }}
                    </div>
                </div>

                 <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Customer Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->is_customer_code) ? ($setting->is_customer_code ? 'Yes' : 'No') : '—' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Supplier Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->is_supplier_code) ? ($setting->is_supplier_code ? 'Yes' : 'No') : '—' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Account Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->is_account_code) ? ($setting->is_account_code ? 'Yes' : 'No') : '—' }}
                    </div>
                </div>

                <div class="col-md-2">
                    <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                        Sub-Account Code</p>
                    <div class="small text-center">
                        {{ $setting && isset($setting->is_subaccount_code) ? ($setting->is_subaccount_code ? 'Yes' : 'No') : '—' }}
                    </div>
                </div>





            </div>

        </div>

        {{-- HR PAYROLL SETTINGS TAB --}}
        <div class="tab-pane fade" id="hrPayrollTab">

            @php
                $hrPayroll = \App\SysCompanyHrPayrollSetting::where('company_id', $company->id)->first();
            @endphp

            @if ($hrPayroll)
                {{-- ================================== LEAVES POLICY ================================== --}}
                <h6 class="mb-3 mt-3">
                    <i class="ico icon-outline-leaves text-primary me-1"></i>
                    Leave Policy Types
                </h6>
                
                <div class="row g-3">
                    {{-- Leave Policy Section --}}
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Leave Policy Type</p>
                        <div class="small text-center">{{ $hrPayroll->leave_policy_type ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Annual Leave</p>
                        <div class="small text-center">{{ $hrPayroll->annual_leave_cl_sl ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Sick Leave</p>
                        <div class="small text-center">{{ $hrPayroll->sick_leave_sl ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Casual Leave</p>
                        <div class="small text-center">{{ $hrPayroll->casual_leave_cl ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Comp Off Allowed</p>
                        <div class="small text-center">
                            {{ isset($hrPayroll->comp_off_allowed) ? ($hrPayroll->comp_off_allowed ? 'Yes' : 'No') : '—' }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Carry Forward</p>
                        <div class="small text-center">{{ $hrPayroll->carry_forward_unused_leaves ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Max Carry Forward</p>
                        <div class="small text-center">{{ $hrPayroll->max_carry_forward_days ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Encashable Leaves</p>
                        <div class="small text-center">
                            {{ isset($hrPayroll->encashable_leaves) ? ($hrPayroll->encashable_leaves ? 'Yes' : 'No') : '—' }}
                        </div>
                    </div>
                </div>

                {{-- ================================== ATTENDANCE POLICY ================================== --}}
                <h6 class="mb-3 mt-4">
                    <i class="ico icon-outline-attendance text-primary me-1"></i>
                    Attendance Policy
                </h6>
                
                <div class="row g-3">
                    {{-- Attendance Policy Section --}}
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Attendance Policy</p>
                        <div class="small text-center">{{ $hrPayroll->attendance_policy ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Min Working Hours</p>
                        <div class="small text-center">{{ $hrPayroll->minimum_working_hours ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Grace Period (min)</p>
                        <div class="small text-center">{{ $hrPayroll->grace_period_minutes ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Half Day After (hrs)</p>
                        <div class="small text-center">{{ $hrPayroll->half_day_after_hours ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Absent if Below (hrs)</p>
                        <div class="small text-center">{{ $hrPayroll->absent_if_hours_below ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Late Mark Count</p>
                        <div class="small text-center">{{ $hrPayroll->late_mark_count_allowed ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Late to Half Day</p>
                        <div class="small text-center">{{ $hrPayroll->consecutive_late_to_halfday ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Auto Absent After (days)</p>
                        <div class="small text-center">{{ $hrPayroll->auto_mark_absent_after_days ?? '—' }}</div>
                    </div>

                    @php
                        $shift = @App\WorkingShift::find($company->shift_id);
                       
                    @endphp
                    {{-- Shift & Time Section --}}
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Shift Start Time</p>
                        <div class="small text-center">{{ $shift && $shift->start_time
        ? \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A')
        : '—'
    }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Shift End Time</p>
                        <div class="small text-center">{{ $shift && $shift->end_time
        ? \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A')
        : '—'
    }}</div>
                    </div>

                    {{-- Weekly Off Days (JSON Array) --}}
                    <div class="col-md-4">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Weekly Off Days</p>
                        <div class="small text-center">
                            @php
                                $weeklyOffDays = $hrPayroll->weekly_off_days;
                                if (is_string($weeklyOffDays)) {
                                    $weeklyOffDays = json_decode($weeklyOffDays, true);
                                }
                                $dayNames = [
                                    'Sunday',
                                    'Monday',
                                    'Tuesday',
                                    'Wednesday',
                                    'Thursday',
                                    'Friday',
                                    'Saturday',
                                ];
                            @endphp

                            @if (is_array($weeklyOffDays) && !empty($weeklyOffDays))
                                @foreach ($weeklyOffDays as $day)
                                    <span class="badge bg-warning text-dark">{{ $dayNames[$day] ?? $day }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ================================== PAYROLL CONFIGURATION ================================== --}}
                <h6 class="mb-3 mt-4">
                    <i class="ico icon-outline-money text-primary me-1"></i>
                    Payroll Configuration
                </h6>
                
                <div class="row g-3">
                    {{-- WPS / Salary Section --}}
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            WPS Establishment ID</p>
                        <div class="small text-center">{{ $hrPayroll->wps_establishment_id ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            WPS Bank</p>
                        <div class="small text-center">{{ $hrPayroll->wps_bank ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            WPS Salary File Code</p>
                        <div class="small text-center">{{ $hrPayroll->wps_salary_file_code ?? '—' }}</div>
                    </div>

                    {{-- Payroll Cycle Section --}}
                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Payroll Cycle</p>
                        <div class="small text-center">{{ $hrPayroll->payroll_cycle ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Payroll Start Day</p>
                        <div class="small text-center">{{ $hrPayroll->payroll_start_day ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Payroll End Day</p>
                        <div class="small text-center">{{ $hrPayroll->payroll_end_day ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Weekly Off Day</p>
                        <div class="small text-center">{{ $hrPayroll->weekly_off_day ?? '—' }}</div>
                    </div>

                    <div class="col-md-2">
                        <p class="font-weight-600 text-center" style="background-color: #deebe1;margin-bottom: 3px">
                            Gratuity Method</p>
                        <div class="small text-center">{{ $hrPayroll->gratuity_calculation_method ?? '—' }}</div>
                    </div>
                </div>

                {{-- ================================== LOANS & ADVANCES ================================== --}}
                <h6 class="mb-3 mt-4">
                    <i class="ico icon-outline-loan text-primary me-1"></i>
                    Loans & Advances
                </h6>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No HR Payroll Settings saved yet.
                </div>
            @endif

        </div>


    </div> {{-- tab-content --}}
</div> {{-- tab-wrap --}}

<style>
    .small {
        font-size: .85rem;
    }

    .xsmall {
        font-size: .75rem;
    }

    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .custom-green {
        width: 3.5em !important;
        height: 1.5em !important;
        cursor: pointer;
        border-color: #499258 !important;
    }

    .custom-green:checked {
        background-color: #499258 !important;
        border-color: #499258 !important;
    }
</style>

<!-- Documents Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentsModalLabel">Company Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="documentsModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to show company documents
function showDocuments(companyId, companyName) {
    // Set modal title
    document.getElementById('documentsModalLabel').innerText = companyName + ' - Documents';
    
    // Clear previous content
    const modalBody = document.getElementById('documentsModalBody');
    modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('documentsModal'));
    modal.show();
    
    // Fetch documents via AJAX
    fetch(`/company/${companyId}/documents`)
        .then(response => response.json())
        .then(data => {
            let content = '';
            if (data.documents && data.documents.length > 0) {
                content = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Document Name</th><th>Document Number</th><th>Date</th><th>Expiry</th><th>File</th></tr></thead><tbody>';
                data.documents.forEach(doc => {
                    content += `<tr>
                        <td>${doc.document_name || '—'}</td>
                        <td>${doc.document_number || '—'}</td>
                        <td>${doc.document_date || '—'}</td>
                        <td>${doc.expiry_date || '—'}</td>
                        <td>${doc.attachment_file ? `<a href="${window.location.origin}/storage/${doc.attachment_file}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` : '—'}</td>
                    </tr>`;
                });
                content += '</tbody></table></div>';
            } else {
                content = '<div class="text-center text-muted">No documents found</div>';
            }
            modalBody.innerHTML = content;
        })
        .catch(error => {
            console.error('Error fetching documents:', error);
            modalBody.innerHTML = '<div class="text-center text-danger">Error loading documents</div>';
        });
}
</script>

@php
    use Carbon\Carbon;
    $isEdit = isset($companyRow) && !empty($companyRow->id);
    $fmt = function($d){
        if(!$d) return '';
        try { return Carbon::parse($d)->format('Y-m-d'); } catch(\Exception $e){ return $d; }
    };
    // multiselect (owner/sponsor/contact)
    $sections = collect(old('contact_sections', $companyRow->contact_sections ?? []))->toArray();
@endphp

<form id="companyAllForm" action="{{ route('company.basic.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- This hidden is what backend reads --}}
    <input type="hidden" id="saved_company_id" name="company_id" value="{{ $companyRow->id ?? $nextId ?? '' }}">

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        {{ $isEdit ? 'Edit Company' : 'Add Company' }}
                    </h4>
                    <span id="saveAllMsg" class="ms-2"></span>
                    <div class="purchase-order-content-header-right">
                        <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                                id="btnSaveAllCompany" data-busy-text="Saving...">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                            <span class="btn-text">Save</span>
                        </button>
                        <a class="btn btn-light" href="{{ url('staff-directory') }}">User List</a>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">

                                {{-- alerts --}}
                                @if (session()->has('message-success'))
                                    <div class="alert alert-success">{{ session('message-success') }}</div>
                                @elseif(session()->has('message-danger'))
                                    <div class="alert alert-danger">{{ session('message-danger') }}</div>
                                @endif

                                <div class="white-box">
                                    <div class="staff">
                                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                        <div class="row mb-30">
                                            <div class="col-lg-12 mb-4">
                                                <div class="row gy-2">

                                                    {{-- DISPLAY ONLY (no name) --}}
                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Company ID <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   value="{{ $companyRow->id ?? $nextId }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Company Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="company_name"
                                                                   value="{{ old('company_name', $companyRow->company_name ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Trade Name</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="trade_name"
                                                                   value="{{ old('trade_name', $companyRow->trade_name ?? '') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Legal Entity Type</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="legal_entity_type"
                                                                   value="{{ old('legal_entity_type', $companyRow->legal_entity_type ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Industry / Business Activity</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="industry"
                                                                   value="{{ old('industry', $companyRow->industry ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">Parent / Group Company</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="parent_company"
                                                                   value="{{ old('parent_company', $companyRow->parent_company ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row gy-2 mt-1">
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Date of Incorporation</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                               name="date_of_incorporation"
                                                               value="{{ old('date_of_incorporation', $fmt($companyRow->date_of_incorporation ?? '')) }}">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Country <span class="text-danger">*</span></label>
                                                        <select name="country" id="country" class="form-select form-select-sm">
                                                            <option value="">Select Country</option>
                                                            @foreach ($country as $syscountry)
                                                                @php $selCountry = old('country', $companyRow->country ?? ''); @endphp
                                                                <option value="{{ $syscountry->id }}" {{ (string)$selCountry===(string)$syscountry->id ? 'selected' : '' }}>
                                                                    {{ $syscountry->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <div class="input-effect">
                                                            <label class="form-label mb-1">City <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                   name="city" id="city"
                                                                   value="{{ old('city', $companyRow->city ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Registered Address <span class="text-danger">*</span></label>
                                                        <textarea class="form-control form-control-sm" name="company_address" rows="1" required>{{ old('company_address', $companyRow->company_address ?? '') }}</textarea>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Sales Code <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="sales_code"
                                                               value="{{ old('sales_code', $companyRow->sales_code ?? '') }}">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Other Code <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="other_code"
                                                               value="{{ old('other_code', $companyRow->other_code ?? '') }}">
                                                    </div>
                                                </div>

                                                <div class="row gy-2 mt-1">
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Currency</label>
                                                        <select name="currency" class="form-select form-select-sm">
                                                            <option value="">Select Currency</option>
                                                            @php $selCurrency = old('currency', $companyRow->currency ?? ''); @endphp
                                                            @foreach ($currency as $syscurrency)
                                                                <option value="{{ $syscurrency->code }}"
                                                                    {{ $selCurrency===$syscurrency->code ? 'selected' : '' }}>
                                                                    {{ $syscurrency->name }} ({{ $syscurrency->code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Currency digit</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="currency_digit"
                                                               value="{{ old('currency_digit', $companyRow->currency_digit ?? '') }}">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Book Closed</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                               name="book_closed"
                                                               value="{{ old('book_closed', $fmt($companyRow->book_closed ?? '')) }}">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Company Logo</label>
                                                        <input type="file" class="form-control form-control-sm" name="company_logo" accept="image/*">
                                                        @if(!empty($companyRow->company_logo))
                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->company_logo,'/')) }}">Current file</a>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Digital Stamp</label>
                                                        <input type="file" class="form-control form-control-sm" name="digital_stamp" accept="image/*">
                                                        @if(!empty($companyRow->digital_stamp))
                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->digital_stamp,'/')) }}">Current file</a>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1">Company Profile</label>
                                                        <input type="file" class="form-control form-control-sm" name="company_profile" accept=".pdf,.doc,.docx,.txt">
                                                        @if(!empty($companyRow->company_profile))
                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->company_profile,'/')) }}">Current file</a>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <span id="saveMsg" class="ms-2"></span>
                                                </div>
                                            </div>

                                            {{-- ======================= CONTACT INFO TAB (preselect) ======================= --}}
                                            {{-- Inside #contactinfo tab --}}
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="mb-3">Company Details</h6>
                                                    <div class="tab-wrap mb-3">
                                                        <ul class="nav nav-tabs" id="hrTabs" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="contact-info" data-bs-toggle="tab" data-bs-target="#contactinfo" type="button" role="tab">Contact Information</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="Compliance-Regulatory" data-bs-toggle="tab" data-bs-target="#compliance-regulatory" type="button" role="tab">Compliance & Regulatory</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#Banking-Finance" type="button" role="tab">Banking & Finance</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hr-payroll" type="button" role="tab">HR & Payroll Setup</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#Policies" type="button" role="tab">Policies</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#documentation" type="button" role="tab">Documentation</button>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content border p-3 bg-white" id="hrTabsContent">

                                                            <div class="tab-pane fade show active" id="contactinfo" role="tabpanel">
                                                                <div class="row gy-2">
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Company Email <span class="text-danger">*</span></label>
                                                                        <input type="email" class="form-control form-control-sm" name="email"
                                                                               value="{{ old('company_email', $companyRow->company_email ?? '') }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Company Website</label>
                                                                        <input type="url" class="form-control form-control-sm" name="website"
                                                                               value="{{ old('website', $companyRow->website ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Office Phone Number <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control form-control-sm" name="telephone"
                                                                               value="{{ old('telephone', $companyRow->telephone ?? '') }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Fax Number</label>
                                                                        <input type="text" class="form-control form-control-sm" name="fax"
                                                                               value="{{ old('fax', $companyRow->fax ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Mobile Number</label>
                                                                        <input type="text" class="form-control form-control-sm" name="mobile"
                                                                               value="{{ old('mobile', $companyRow->mobile ?? '') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <div class="input-effect">
                                                                            <label class="form-label mb-1">Contact Sections <span class="text-danger">*</span></label>
                                                                            <select class="form-select form-select-sm js-example-basic-single" name="contact_sections[]" id="contactSections" multiple data-placeholder="Select sections">
                                                                                <option value="owner"   {{ in_array('owner', $sections)   ? 'selected' : '' }}>Owner</option>
                                                                                <option value="sponsor" {{ in_array('sponsor', $sections) ? 'selected' : '' }}>Sponsor</option>
                                                                                <option value="contact" {{ in_array('contact', $sections) ? 'selected' : '' }}>Contact Person</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- ... (owner/sponsor/contact blocks unchanged, but use old(..., $companyRow->...) the same way) --}}
                                                            </div>

                                                            {{-- ======================= COMPLIANCE TAB (prefill) ======================= --}}
                                                            <div class="tab-pane fade" id="compliance-regulatory" role="tabpanel">
                                                                <div class="row gy-2">

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Trade License Number <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control form-control-sm" name="business_license_number"
                                                                               value="{{ old('business_license_number', $companyRow->business_license_number ?? '') }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">License Issue Date <span class="text-danger">*</span></label>
                                                                        <input type="date" class="form-control form-control-sm" name="license_issue_date"
                                                                               value="{{ old('license_issue_date', $fmt($companyRow->license_issue_date ?? '')) }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">License Expiry Date <span class="text-danger">*</span></label>
                                                                        <input type="date" class="form-control form-control-sm" name="license_expiry_date"
                                                                               value="{{ old('license_expiry_date', $fmt($companyRow->license_expiry_date ?? '')) }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Trade License Upload <span class="text-danger">*</span></label>
                                                                        <input type="file" class="form-control form-control-sm" name="business_license_upload">
                                                                        @if(!empty($companyRow->business_license_upload))
                                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->business_license_upload,'/')) }}">Current file</a>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Issuing Authority <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control form-control-sm" name="issuing_authority"
                                                                               value="{{ old('issuing_authority', $companyRow->issuing_authority ?? '') }}" required>
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Tax Applicable</label>
                                                                        @php $selTax = old('tax_applicable', $companyRow->tax_applicable ?? ''); @endphp
                                                                        <select class="form-control" name="tax_applicable" id="tax_applicable">
                                                                            <option value="">Select</option>
                                                                            <option value="vat"  {{ $selTax==='vat'  ? 'selected' : '' }}>VAT</option>
                                                                            <option value="ct"   {{ $selTax==='ct'   ? 'selected' : '' }}>CT</option>
                                                                            <option value="both" {{ $selTax==='both' ? 'selected' : '' }}>Both</option>
                                                                            <option value="none" {{ $selTax==='none' ? 'selected' : '' }}>Not Applicable</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- VAT fields --}}
                                                                @php
                                                                    $showVAT = in_array($selTax, ['vat','both']);
                                                                    $showCT  = in_array($selTax, ['ct','both']);
                                                                @endphp
                                                                <div class="row gy-2 mt-1 {{ $showVAT ? '' : 'd-none' }}" id="vatFields">
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">VAT Registration No. (TRN) <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control form-control-sm" name="vat_registration_number"
                                                                               value="{{ old('vat_registration_number', $companyRow->vat_registration_number ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">VAT %</label>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm" name="vat_percentage"
                                                                               value="{{ old('vat_percentage', $companyRow->vat_percentage ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">VAT Registration Date</label>
                                                                        <input type="date" class="form-control form-control-sm" name="vat_date"
                                                                               value="{{ old('vat_date', $fmt($companyRow->vat_date ?? '')) }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">VAT Certificate Upload</label>
                                                                        <input type="file" class="form-control form-control-sm" name="vat_certificate" accept="image/*,.pdf">
                                                                        @if(!empty($companyRow->vat_certificate))
                                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->vat_certificate,'/')) }}">Current file</a>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                {{-- CT fields --}}
                                                                <div class="row gy-2 mt-1 {{ $showCT ? '' : 'd-none' }}" id="ctFields">
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">CT Registration No. (CTN)</label>
                                                                        <input type="text" class="form-control form-control-sm" name="corporate_tax_number"
                                                                               value="{{ old('corporate_tax_number', $companyRow->corporate_tax_number ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">CT %</label>
                                                                        <input type="text" class="form-control form-control-sm" name="corporate_tax_vat"
                                                                               value="{{ old('corporate_tax_vat', $companyRow->corporate_tax_vat ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">CT Registration Date</label>
                                                                        <input type="date" class="form-control form-control-sm" name="corporate_tax_date"
                                                                               value="{{ old('corporate_tax_date', $fmt($companyRow->corporate_tax_date ?? '')) }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">CT Certificate Upload</label>
                                                                        <input type="file" class="form-control form-control-sm" name="corporate_tax_certificate" accept="image/*,.pdf">
                                                                        @if(!empty($companyRow->corporate_tax_certificate))
                                                                            <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('public/'.ltrim($companyRow->corporate_tax_certificate,'/')) }}">Current file</a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- rest of your tabs stay same; apply same old(..., $companyRow->...) pattern --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- /Company Details --}}

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
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <div class="form-scroll">
        <form id="companyAllForm" action="{{ route('company.basic.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_company_id" name="company_id" value="{{ $companyRow->id ?? '' }}">
            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                    <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                Add Company
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right">
                                <button type="button"
                                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                                    id="btnSaveAllCompany" data-busy-text="Saving...">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                                    <span class="btn-text">Save</span>
                                </button>
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
                                                        <div class="row gy-2">

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Company ID <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="company_id" value="{{ $nextId }}"
                                                                        required readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Company Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="company_name"
                                                                        value="{{ old('company_name') }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Trade Name</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="trade_name" value="{{ old('trade_name') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Legal Entity Type</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="legal_entity_type"
                                                                        value="{{ old('legal_entity_type') }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Industry / Business
                                                                        Activity</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm" name="industry"
                                                                        value="{{ old('industry') }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Parent / Group
                                                                        Company</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="parent_company"
                                                                        value="{{ old('parent_company') }}">
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row gy-2 mt-1">

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Date of Incorporation</label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm date-picker"
                                                                    name="date_of_incorporation"
                                                                    value="{{ old('date_of_incorporation') }}">
                                                            </div>


                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Country <span
                                                                        class="text-danger">*</span></label>
                                                                <select name="country" id="country"
                                                                    class="form-select form-select-sm">
                                                                    <option value="">Select Country</option>
                                                                    @foreach ($country as $syscountry)
                                                                        <option value="{{ $syscountry->id }}">
                                                                            {{ $syscountry->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">City <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        name="city" id="city"
                                                                        value="{{ old('city') }}"
                                                                        placeholder="Enter City" required>
                                                                </div>
                                                            </div>






                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Registered Address <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea class="form-control form-control-sm" name="company_address" rows="1" required>{{ old('company_address') }}</textarea>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Sales Code <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="sales_code" value="{{ old('sales_code') }}">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Other Code <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="other_code" value="{{ old('other_code') }}">
                                                            </div>

                                                        </div>

                                                        <div class="row gy-2 mt-1">

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Currency</label>
                                                                <select name="currency"
                                                                    class="form-select form-select-sm">
                                                                    <option value="">Select Currency</option>
                                                                    @foreach ($currency as $syscurrency)
                                                                        <option value="{{ $syscurrency->code }}"
                                                                            {{ old('currency') == $syscurrency->code ? 'selected' : '' }}>
                                                                            {{ $syscurrency->name }}
                                                                            ({{ $syscurrency->code }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>


                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Currency digit</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="currency_digit"
                                                                    value="{{ old('currency_digit') }}">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Book Closed</label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm date-picker"
                                                                    name="book_closed" value="{{ old('book_closed') }}">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Company Logo</label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="company_logo" accept="image/*">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Digital Stamp</label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="digital_stamp" accept="image/*">
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Company Profile</label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="company_profile" accept=".pdf,.doc,.docx,.txt">
                                                            </div>


                                                        </div>

                                                        <div class="mt-3">
                                                            <span id="saveMsg" class="ms-2"></span>
                                                        </div>
                                                    </div>

                                                    {{-- ======================= EMPLOYEE MASTER – TABS (Drop-in) ======================= --}}
                                                    <div class="row">
                                                        <div class="col-12">

                                                            <h6 class="mb-3">Company Details</h6>

                                                            <div class="tab-wrap mb-3">
                                                                <ul class="nav nav-tabs" id="hrTabs" role="tablist">
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link active" id="contact-info"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#contactinfo" type="button"
                                                                            role="tab" aria-controls="contact-info"
                                                                            aria-selected="true">
                                                                            Contact Information
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link"
                                                                            id="Compliance-Regulatory"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#compliance-regulatory"
                                                                            type="button" role="tab"
                                                                            aria-controls="compliance-regulatory"
                                                                            aria-selected="false">
                                                                            Compliance & Regulatory
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="edu-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#Banking-Finance"
                                                                            type="button" role="tab"
                                                                            aria-controls="Banking-Finance"
                                                                            aria-selected="false">
                                                                            Banking & Finance
                                                                        </button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="edu-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#hr-payroll" type="button"
                                                                            role="tab" aria-controls="hr-payroll"
                                                                            aria-selected="false">
                                                                            HR & Payroll Setup
                                                                        </button>
                                                                    </li>

                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="edu-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#Policies" type="button"
                                                                            role="tab" aria-controls="Policies"
                                                                            aria-selected="false">
                                                                            Policies
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
                                                                        id="contactinfo" role="tabpanel"
                                                                        aria-labelledby="contact-info">
                                                                        <div class="row gy-2">

                                                                            {{-- Company Contact (always visible) --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Company
                                                                                    Email <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="email"
                                                                                    class="form-control form-control-sm"
                                                                                    name="email"
                                                                                    value="{{ old('company_email') }}"
                                                                                    required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Company
                                                                                    Website</label>
                                                                                <input type="url"
                                                                                    class="form-control form-control-sm"
                                                                                    name="website"
                                                                                    value="{{ old('website') }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Office Phone
                                                                                    Number <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="telephone"
                                                                                    value="{{ old('telephone') }}"
                                                                                    required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Fax
                                                                                    Number</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="fax"
                                                                                    value="{{ old('fax') }}">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Mobile
                                                                                    Number</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="mobile"
                                                                                    value="{{ old('mobile') }}">
                                                                            </div>

                                                                            {{-- Role chooser --}}
                                                                            <div class="col-lg-2">
                                                                                <div class="input-effect">
                                                                                    <label class="form-label mb-1">Contact
                                                                                        Sections <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select
                                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                                        name="contact_sections[]"
                                                                                        id="contactSections" multiple
                                                                                        data-placeholder="Select sections">
                                                                                        <option value="owner"
                                                                                            {{ collect(old('contact_sections', []))->contains('owner') ? 'selected' : '' }}>
                                                                                            Owner</option>
                                                                                        <option value="sponsor"
                                                                                            {{ collect(old('contact_sections', []))->contains('sponsor') ? 'selected' : '' }}>
                                                                                            Sponsor</option>
                                                                                        <option value="contact"
                                                                                            {{ collect(old('contact_sections', []))->contains('contact') ? 'selected' : '' }}>
                                                                                            Contact Person</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>


                                                                        </div>

                                                                        {{-- OWNER block --}}
                                                                        <div class="row gy-2 contact-block d-none mt-1"
                                                                            data-block="owner">
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner Name
                                                                                </label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="owner_name"
                                                                                    value="{{ old('owner_name') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner Mobile
                                                                                    No. </label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="owner_mobile"
                                                                                    value="{{ old('owner_mobile') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner Email
                                                                                </label>
                                                                                <input type="email"
                                                                                    class="form-control form-control-sm"
                                                                                    name="owner_email"
                                                                                    value="{{ old('owner_email') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner
                                                                                    Passport Copy</label>
                                                                                <input type="file"
                                                                                    name="owner_passport_copy"
                                                                                    id="owner_passport_copy"
                                                                                    class="form-control form-control-sm"
                                                                                    accept="image/*,.pdf"
                                                                                    form="companyAllForm">
                                                                                <!-- important if it's outside the <form> -->

                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner
                                                                                    Emirates ID</label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="owner_emirates_id"
                                                                                    accept="image/*,.pdf">
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Owner Visa
                                                                                    Copy</label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="owner_visa_copy"
                                                                                    accept="image/*,.pdf">
                                                                            </div>
                                                                        </div>

                                                                        {{-- SPONSOR block --}}
                                                                        <div class="row gy-2 mt-1">
                                                                            <div class="row gy-2 contact-block d-none"
                                                                                data-block="sponsor">
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Name
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_name"
                                                                                        value="{{ old('sponsor_name') }}"
                                                                                        data-required>
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Mobile No. <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="text"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_mobile"
                                                                                        value="{{ old('sponsor_mobile') }}"
                                                                                        data-required>
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Email <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="email"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_email"
                                                                                        value="{{ old('sponsor_email') }}"
                                                                                        data-required>
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Passport Copy</label>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_passport_copy"
                                                                                        accept="image/*,.pdf">
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Emirates ID</label>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_emirates_id"
                                                                                        accept="image/*,.pdf">
                                                                                </div>
                                                                                <div class="col-lg-2">
                                                                                    <label class="form-label mb-1">Sponsor
                                                                                        Visa
                                                                                        Copy</label>
                                                                                    <input type="file"
                                                                                        class="form-control form-control-sm"
                                                                                        name="sponsor_visa_copy"
                                                                                        accept="image/*,.pdf">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- CONTACT PERSON block --}}
                                                                        <div class="row gy-2 contact-block d-none"
                                                                            data-block="contact">
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Contact
                                                                                    Person Name <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_person_name"
                                                                                    value="{{ old('contact_person_name') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Contact
                                                                                    Person Mobile No. <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_person_mobile"
                                                                                    value="{{ old('contact_person_mobile') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Contact
                                                                                    Person Email <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="email"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_person_email"
                                                                                    value="{{ old('contact_person_email') }}"
                                                                                    data-required>
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Contact
                                                                                    Person Designation</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_person_designation"
                                                                                    value="{{ old('contact_person_designation') }}">
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Passport
                                                                                    Copy</label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_passport_copy"
                                                                                    accept="image/*,.pdf">
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Emirates
                                                                                    ID</label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_emirates_id"
                                                                                    accept="image/*,.pdf">
                                                                            </div>
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Visa
                                                                                    Copy</label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="contact_visa_copy"
                                                                                    accept="image/*,.pdf">
                                                                            </div>
                                                                        </div>


                                                                    </div>


                                                                    {{-- ======================= TAB: compliance-regulatory ======================= --}}
                                                                    <div class="tab-pane fade" id="compliance-regulatory"
                                                                        role="tabpanel"
                                                                        aria-labelledby="compliance-regulatory">
                                                                        <div class="row gy-2">

                                                                            {{-- License Details --}}
                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">
                                                                                   Trade License Number <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="business_license_number"
                                                                                    value="{{ old('business_license_number') }}"
                                                                                    required>
                                                                            </div>


                                                                            

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">License
                                                                                    Issue Date <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="license_issue_date"
                                                                                    value="{{ old('license_issue_date') }}"
                                                                                    required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">License
                                                                                    Expiry Date <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="license_expiry_date"
                                                                                    value="{{ old('license_expiry_date') }}"
                                                                                    required>
                                                                            </div>

                                                                             <div class="col-lg-2">
                                                                                <label class="form-label mb-1">
                                                                                   Trade License Upload <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="business_license_upload"
                                                                                    required>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Issuing
                                                                                    Authority <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="issuing_authority"
                                                                                    value="{{ old('issuing_authority') }}"
                                                                                    required>
                                                                            </div>

                                                                                        {{-- Tax Details --}}
                                                         

                                                                                            <div class="col-lg-2">
                                                                                            <label class="form-label mb-1">Tax Applicable</label>
                                                                                            <select class="form-control form-control" name="tax_applicable" id="tax_applicable">
                                                                                            <option value="">Select</option>
                                                                                            <option value="vat"  {{ old('tax_applicable')=='vat' ? 'selected' : '' }}>VAT</option>
                                                                                            <option value="ct"   {{ old('tax_applicable')=='ct'  ? 'selected' : '' }}>CT</option>
                                                                                            <option value="both" {{ old('tax_applicable')=='both' ? 'selected' : '' }}>Both</option>
                                                                                            <option value="none" {{ old('tax_applicable')=='none' ? 'selected' : '' }}>Not Applicable</option>
                                                                                            </select>
                                                                                            </div>
                                                                                            </div>

                                                                                            {{-- VAT + Corporate Tax block (shown only if tax_applicable = yes) --}}
                                                                                            <div class="row gy-2 mt-1 d-none" id="vatFields">
                                                                                                            <div class="col-lg-2">
                                                                                                        <label class="form-label mb-1">VAT Registration No. (TRN) <span class="text-danger">*</span></label>
                                                                                                        <input type="text" class="form-control form-control-sm"
                                                                                                            name="vat_registration_number" id="vat_registration_number"
                                                                                                            value="{{ old('vat_registration_number') }}">
                                                                                                    </div>

                                                                                                    <div class="col-lg-2">
                                                                                                        <label class="form-label mb-1">VAT %</label>
                                                                                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                                                                                            name="vat_percentage" value="{{ old('vat_percentage') }}">
                                                                                                    </div>

                                                                                                    <div class="col-lg-2">
                                                                                                        <label class="form-label mb-1">VAT Registration Date</label>
                                                                                                        <input type="date" class="form-control form-control-sm date-picker"
                                                                                                            name="vat_date" value="{{ old('vat_date') }}">
                                                                                                    </div>

                                                                                                    <div class="col-lg-2">
                                                                                                        <label class="form-label mb-1">VAT Certificate Upload</label>
                                                                                                        <input type="file" class="form-control form-control-sm"
                                                                                                            name="vat_certificate" accept="image/*,.pdf">
                                                                                                    </div>
                                                                                                    </div>


                                                                                            <div class="row gy-2 mt-1 d-none" id="ctFields">
                                                                                            <div class="col-lg-2">
                                                                                                <label class="form-label mb-1">CT Registration No. (CTN)</label>
                                                                                                <input type="text" class="form-control form-control-sm"
                                                                                                    name="corporate_tax_number" value="{{ old('corporate_tax_number') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label class="form-label mb-1">CT %</label>
                                                                                                <input type="text" class="form-control form-control-sm"
                                                                                                    name="corporate_tax_vat" value="{{ old('corporate_tax_vat') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label class="form-label mb-1">CT Registration Date</label>
                                                                                                <input type="text" class="form-control form-control-sm date-picker"
                                                                                                    name="corporate_tax_date" value="{{ old('corporate_tax_date') }}">
                                                                                            </div>

                                                                                            <div class="col-lg-2">
                                                                                                <label class="form-label mb-1">CT Certificate Upload</label>
                                                                                                <input type="file" class="form-control form-control-sm"
                                                                                                    name="corporate_tax_certificate" accept="image/*,.pdf">
                                                                                            </div>
                                                                                            </div>




                                                                                        </div>

                                                                                        {{-- Banking-Finance --}}

                                                                                        <div class="tab-pane fade" id="Banking-Finance"
                                                                                            role="tabpanel" aria-labelledby="edu-tab">
                                                                                            <div class="mb-2 d-flex justify-content-end">
                                                                                                <button type="button"
                                                                                                    class="btn btn-light btn-sm"
                                                                                                    id="addBankRow">
                                                                                                    <i
                                                                                                        class="ico icon-outline-add-square text-success"></i>
                                                                                                    Add Row
                                                                                                </button>
                                                                                            </div>

                                                                                            <div class="table-responsive">
                                                                                                <table
                                                                                                    class="table table-bordered align-middle"
                                                                                                    id="bankTable">
                                                                                                    <thead class="table-light">
                                                                                                        <tr>
                                                                                                            <th style="width: 180px;">Bank Name
                                                                                                                <span
                                                                                                                    class="text-danger">*</span>
                                                                                                            </th>
                                                                                                            <th style="width: 160px;">Branch
                                                                                                                Name</th>
                                                                                                            <th style="width: 180px;">Account
                                                                                                                Number <span
                                                                                                                    class="text-danger">*</span>
                                                                                                            </th>
                                                                                                            <th style="width: 180px;">IBAN
                                                                                                                Number <span
                                                                                                                    class="text-danger">*</span>
                                                                                                            </th>
                                                                                                            <th style="width: 140px;">SWIFT
                                                                                                                Code</th>
                                                                                                            <th style="width: 140px;">Finance
                                                                                                                Code</th>
                                                                                                            <th style="width: 140px;">Currency
                                                                                                            </th>
                                                                                                            <th style="width: 220px;">Bank
                                                                                                                Letter Upload <span
                                                                                                                    class="text-danger">*</span>
                                                                                                            </th>
                                                                                                            <th style="width: 60px;">Action
                                                                                                            </th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][bank_name]"
                                                                                                                    required></td>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][branch_name]">
                                                                                                            </td>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][account_number]"
                                                                                                                    required></td>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][iban_number]"
                                                                                                                    required></td>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][swift_code]">
                                                                                                            </td>
                                                                                                            <td><input type="text"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][finance_code]">
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <select class="form-control"
                                                                                                                    name="banks[0][currency]">
                                                                                                                    <option value="">
                                                                                                                        -Select-</option>
                                                                                                                    <option value="AED">AED
                                                                                                                    </option>
                                                                                                                    <option value="USD">USD
                                                                                                                    </option>
                                                                                                                    <option value="INR">INR
                                                                                                                    </option>
                                                                                                                    <option value="EUR">EUR
                                                                                                                    </option>
                                                                                                                    <option value="GBP">GBP
                                                                                                                    </option>
                                                                                                                    <option value="SAR">SAR
                                                                                                                    </option>
                                                                                                                    <option value="QAR">QAR
                                                                                                                    </option>
                                                                                                                    <option value="OMR">OMR
                                                                                                                    </option>
                                                                                                                    <option value="KWD">KWD
                                                                                                                    </option>
                                                                                                                </select>
                                                                                                            </td>
                                                                                                            <td><input type="file"
                                                                                                                    class="form-control"
                                                                                                                    name="banks[0][bank_letter]"
                                                                                                                    accept="image/*,.pdf"
                                                                                                                    required></td>
                                                                                                            <td class="text-center">
                                                                                                                <button type="button"
                                                                                                                    class="btn btn-light text-dark btn-sm delBankRow">
                                                                                                                    <i
                                                                                                                        class="ico icon-bold-trash-bin-minimalistic-2"></i>
                                                                                                                </button>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>

                                                                                            {{-- jQuery required --}}
                                                                                            <script>
                                                                                                (function() {
                                                                                                    const $table = $('#bankTable');
                                                                                                    const $tbody = $table.find('tbody');

                                                                                                    $('#addBankRow').on('click', function() {
                                                                                                        const index = $tbody.find('tr').length; // next row index
                                                                                                        const rowHtml = `
                                                                                                <tr>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][bank_name]" required></td>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][branch_name]"></td>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][account_number]" required></td>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][iban_number]" required></td>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][swift_code]"></td>
                                                                                                    <td><input type="text" class="form-control"
                                                                                                            name="banks[${index}][finance_code]"></td>
                                                                                                    <td>
                                                                                                        <select class="form-control" name="banks[${index}][currency]">
                                                                                                            <option value="">-Select-</option>
                                                                                                            <option value="AED">AED</option>
                                                                                                            <option value="USD">USD</option>
                                                                                                            <option value="INR">INR</option>
                                                                                                            <option value="EUR">EUR</option>
                                                                                                            <option value="GBP">GBP</option>
                                                                                                            <option value="SAR">SAR</option>
                                                                                                            <option value="QAR">QAR</option>
                                                                                                            <option value="OMR">OMR</option>
                                                                                                            <option value="KWD">KWD</option>
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td><input type="file" class="form-control"
                                                                                                            name="banks[${index}][bank_letter]" accept="image/*,.pdf" required></td>
                                                                                                    <td class="text-center">
                                                                                                        <button type="button" class="btn btn-light text-dark btn-sm delBankRow">
                                                                                                            <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                                                                                                        </button>
                                                                                                    </td>
                                                                                                </tr>`;
                                                                                                        $tbody.append(rowHtml);
                                                                                                    });

                                                                                                    // remove row (event delegation)
                                                                                                    $tbody.on('click', '.delBankRow', function() {
                                                                                                        const $rows = $tbody.find('tr');
                                                                                                        if ($rows.length === 1) {
                                                                                                            // keep at least one row
                                                                                                            $rows.eq(0).find('input, select').val('');
                                                                                                            return;
                                                                                                        }
                                                                                                        $(this).closest('tr').remove();

                                                                                                        // optional: re-index names after removal to keep indices contiguous
                                                                                                        $tbody.find('tr').each(function(i, tr) {
                                                                                                            $(tr).find('input, select').each(function(_, el) {
                                                                                                                const name = $(el).attr('name');
                                                                                                                if (!name) return;
                                                                                                                $(el).attr('name', name.replace(/banks\[\d+\]/, `banks[${i}]`));
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                })();
                                                                                            </script>

                                                                                        </div>

                                                                                        {{-- Banking-Finance end --}}

                                                                                        {{-- professional experience --}}
                                                                                        <div class="tab-pane fade" id="hr-payroll"
                                                                                            role="tabpanel" aria-labelledby="hr-payroll">
                                                                                            <div class="row gy-2">

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">WPS
                                                                                                        Establishment ID <span
                                                                                                            class="text-danger">*</span></label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="wps_establishment_id"
                                                                                                        value="{{ old('wps_establishment_id') }}"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">WPS Bank
                                                                                                        <span
                                                                                                            class="text-danger">*</span></label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="wps_bank"
                                                                                                        value="{{ old('wps_bank') }}"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">WPS Salary
                                                                                                        File Code</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="wps_salary_file_code"
                                                                                                        value="{{ old('wps_salary_file_code') }}">
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Payroll
                                                                                                        Cycle <span
                                                                                                            class="text-danger">*</span></label>
                                                                                                    <select
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="payroll_cycle" required>
                                                                                                        <option value="">Select</option>
                                                                                                        <option value="monthly"
                                                                                                            {{ old('payroll_cycle') == 'monthly' ? 'selected' : '' }}>
                                                                                                            Monthly</option>
                                                                                                        <option value="bi-weekly"
                                                                                                            {{ old('payroll_cycle') == 'bi-weekly' ? 'selected' : '' }}>
                                                                                                            Bi-Weekly</option>
                                                                                                        <option value="weekly"
                                                                                                            {{ old('payroll_cycle') == 'weekly' ? 'selected' : '' }}>
                                                                                                            Weekly</option>
                                                                                                    </select>
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Weekly
                                                                                                        Off</label>
                                                                                                    <select
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="weekly_off">
                                                                                                        <option value="">Select</option>
                                                                                                        <option value="sunday"
                                                                                                            {{ old('weekly_off') == 'sunday' ? 'selected' : '' }}>
                                                                                                            Sunday</option>
                                                                                                        <option value="monday"
                                                                                                            {{ old('weekly_off') == 'monday' ? 'selected' : '' }}>
                                                                                                            Monday</option>
                                                                                                        <option value="tuesday"
                                                                                                            {{ old('weekly_off') == 'tuesday' ? 'selected' : '' }}>
                                                                                                            Tuesday</option>
                                                                                                        <option value="wednesday"
                                                                                                            {{ old('weekly_off') == 'wednesday' ? 'selected' : '' }}>
                                                                                                            Wednesday</option>
                                                                                                        <option value="thursday"
                                                                                                            {{ old('weekly_off') == 'thursday' ? 'selected' : '' }}>
                                                                                                            Thursday</option>
                                                                                                        <option value="friday"
                                                                                                            {{ old('weekly_off') == 'friday' ? 'selected' : '' }}>
                                                                                                            Friday</option>
                                                                                                        <option value="saturday"
                                                                                                            {{ old('weekly_off') == 'saturday' ? 'selected' : '' }}>
                                                                                                            Saturday</option>
                                                                                                    </select>
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Gratuity
                                                                                                        Calculation Method</label>
                                                                                                    <select
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="gratuity_method">
                                                                                                        <option value="">Select</option>
                                                                                                        <option value="basic_salary"
                                                                                                            {{ old('gratuity_method') == 'basic_salary' ? 'selected' : '' }}>
                                                                                                            Basic Salary</option>
                                                                                                        <option value="gross_salary"
                                                                                                            {{ old('gratuity_method') == 'gross_salary' ? 'selected' : '' }}>
                                                                                                            Gross Salary</option>
                                                                                                    </select>
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Insurance
                                                                                                        Provider</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="insurance_provider"
                                                                                                        value="{{ old('insurance_provider') }}">
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Insurance
                                                                                                        Policy Number</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm"
                                                                                                        name="insurance_policy_number"
                                                                                                        value="{{ old('insurance_policy_number') }}">
                                                                                                </div>

                                                                                                <div class="col-lg-2">
                                                                                                    <label class="form-label mb-1">Insurance
                                                                                                        Policy Expiry Date</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control form-control-sm date-picker"
                                                                                                        name="insurance_policy_expiry"
                                                                                                        value="{{ old('insurance_policy_expiry') }}">
                                                                                                </div>

                                                                                            </div>


                                                                                        </div>
                                                                                        {{-- Policies  --}}
                                                                                        <div class="tab-pane fade" id="Policies"
                                                                                            role="tabpanel" aria-labelledby="docs-tab">
                                                                                            <div class="mb-2 d-flex justify-content-between align-items-right">
                                                                                            <button type="button" class="btn btn-light btn-sm" id="addPolicyRow">
                                                                                            <i class="ico icon-outline-add-square text-success"></i> Add Policy
                                                                                            </button>
                                                                                            </div>

                    <div id="policyList"></div>
                                                                                    <script type="text/template" id="policyRowTemplate">
                    <div class="policy-item" data-index="__INDEX__">
                        <div class="row gy-2 align-items-end">
                        <div class="col-lg-2">
                            <label class="form-label mb-1">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm"
                                name="policies[__INDEX__][policy_date]" required>
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label mb-1">Policy Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm"
                                name="policies[__INDEX__][policy_name]" required>
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label mb-1">Policy Category</label>
                            <select class="form-select form-select-sm"
                                    name="policies[__INDEX__][policy_category]">
                            <option value="">Select</option>
                            <option value="health">Health</option>
                            <option value="life">Life</option>
                            <option value="vehicle">Vehicle</option>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label mb-1">Valid</label>
                            <input type="date" class="form-control form-control-sm"
                                name="policies[__INDEX__][policy_valid]">
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label mb-1">View to Employees <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm"
                                    name="policies[__INDEX__][view_to_employees]" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label mb-1">File Upload <span class="text-danger">*</span></label>
                            <input type="file" class="form-control form-control-sm"
                                name="policies[__INDEX__][policy_file]" required>
                        </div>
                        </div>

                        <div class="row gy-2 mt-2">
                        <div class="col-lg-10">
                            <label class="form-label mb-1">Details</label>
                            <textarea class="form-control form-control-sm policy-editor"
                                    name="policies[__INDEX__][policy_details]" rows="6"
                                    id="policy_details___INDEX__"></textarea>
                        </div>
                        <div class="col-lg-2 d-flex justify-content-end align-items-start">
                            <button type="button" class="btn btn-light text-danger btn-sm removePolicyRow" title="Remove">
                            <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                            </button>
                        </div>
                        </div>
                    </div>
                    </script>


                                                                                        </div>

                                                                                        {{-- documents tab --}}
                                                                                        <div class="tab-pane fade" id="documentation"
                                                                                            role="tabpanel" aria-labelledby="docs-tab">

                                                                                            <div class="table-responsive">
                                                                                                <table
                                                                                                    class="table table-bordered align-middle">
                                                                                                    <thead class="table-light">
                                                                                                        <tr>
                                                                                                            <th style="width: 250px;">Document
                                                                                                                Name</th>
                                                                                                            <th style="width: 400px;">File</th>
                                                                                                            <th style="width: 120px;">Action
                                                                                                            </th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td>Trade License</td>
                                                                                                            <td><i
                                                                                                                    class="bi bi-file-earmark-pdf"></i>
                                                                                                                trade_license.pdf</td>
                                                                                                            <td class="text-center">
                                                                                                                <a href="#"
                                                                                                                    target="_blank"
                                                                                                                    class="btn btn-sm btn-light text-dark">
                                                                                                                    <i
                                                                                                                        class="ico icon-outline-eye"></i>
                                                                                                                    View
                                                                                                                </a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>VAT Certificate</td>
                                                                                                            <td><i
                                                                                                                    class="bi bi-file-earmark-pdf"></i>
                                                                                                                vat_certificate.pdf</td>
                                                                                                            <td class="text-center">
                                                                                                                <a href="#"
                                                                                                                    target="_blank"
                                                                                                                    class="btn btn-sm btn-light text-dark">
                                                                                                                    <i
                                                                                                                        class="ico icon-outline-eye"></i>
                                                                                                                    View
                                                                                                                </a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>Insurance Policy</td>
                                                                                                            <td><i
                                                                                                                    class="bi bi-file-earmark-text"></i>
                                                                                                                insurance_policy.docx</td>
                                                                                                            <td class="text-center">
                                                                                                                <a href="#"
                                                                                                                    target="_blank"
                                                                                                                    class="btn btn-sm btn-light text-dark">
                                                                                                                    <i
                                                                                                                        class="ico icon-outline-eye"></i>
                                                                                                                    View
                                                                                                                </a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>

                                                                                        </div>
                                                                                        {{-- documents tab end --}}



        </form>



    </div> {{-- /.tab-content --}}
    </div> {{-- /.tab-wrap --}}


    </div>
    </div>
    {{-- ======================= / EMPLOYEE MASTER – TABS ======================= --}}

    {{-- Minimal JS: add/remove rows for Education, Experience, Other Docs --}}



    </div>

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
            // -------- Client-side required checks --------
            function isEmptyVal(el) {
                if (!el) return true;
                if (el.type === 'file') return !el.value;
                if (el.tagName === 'SELECT' && el.multiple) return !($(el).val() && $(el).val().length);
                return String($(el).val() || '').trim() === '';
            }

            function countMissingRequired($scope) {
                // visible + required only
                const $els = $scope.find(':input[required]').filter(function() {
                    const $el = $(this);
                    // skip disabled or hidden (including display:none and hidden ancestors)
                    if ($el.is(':disabled') || !$el.is(':visible')) return false;
                    // skip inside .d-none blocks
                    if ($el.closest('.d-none').length) return false;
                    return true;
                });

                let missing = 0;
                $els.each(function() {
                    if (isEmptyVal(this)) missing++;
                });
                return missing;
            }

            // Run-and-badge for a given tab pane selector
            function badgeTabRequired(tabSel) {
                const $pane = $(tabSel);
                if (!$pane.length) return;
                const count = countMissingRequired($pane);
                setTabBadge(tabSel, count);
                return count;
            }

            // Validate Contact tab (client-side). Returns true/false.
            function validateContactClient() {
                const TAB = '#contact-info';
                const $pane = $(TAB);
                // Clear only contact tab errors (keep your global clear before AJAX too)
                $pane.find('.is-invalid').removeClass('is-invalid');
                $pane.find('.invalid-feedback').remove();
                $pane.find('.select2 .select2-selection').removeClass('is-invalid');

                let invalid = 0;

                // Always required in contact tab:
                const always = ['[name="company_email"]', '[name="office_phone"]'];
                always.forEach(sel => {
                    const $f = $pane.find(sel);
                    if ($f.length && isEmptyVal($f[0])) {
                        markInvalid($f, 'This field is required');
                        invalid++;
                    }
                });

                // Requireds inside visible selected sections (we already toggle [required] in your second script)
                // So we can simply rely on [required] within this TAB:
                $pane.find(':input[required]').each(function() {
                    const $f = $(this);
                    if ($f.closest('.d-none').length) return; // skip hidden sections
                    if (isEmptyVal(this)) {
                        // avoid double-marking always[] twice
                        if (!$f.hasClass('is-invalid')) {
                            markInvalid($f, 'This field is required');
                            invalid++;
                        }
                    }
                });

                // Badge update for the tab
                badgeTabRequired(TAB);
                return invalid === 0;
            }


            // 0) Never let the browser submit the form (Enter key, stray submit btn)
            $(document).on('submit', '#companyAllForm', function(e) {
                e.preventDefault();
            });

            // 1) Make sure all inputs across tabs belong to the single form
            $('#company-details   [name]').attr({
                'form': 'companyAllForm'
            });
            $('#contact-info      [name]').attr({
                'form': 'companyAllForm'
            });
            $('#compliance        [name]').attr({
                'form': 'companyAllForm'
            });
            $('#banking-finance   [name]').attr({
                'form': 'companyAllForm'
            });
            $('#hr-payroll        [name]').attr({
                'form': 'companyAllForm'
            });
            $('#documentation     [name]').attr({
                'form': 'companyAllForm'
            }); // viewing-only tab (no inputs now)

            // ---------- Helpers ----------
            const ALL_TABS = [
                '#company-details', '#contact-info', '#compliance',
                '#banking-finance', '#hr-payroll', '#documentation'
            ];

            function getForm() {
                const $f = $('#companyAllForm');
                if (!$f.length) throw new Error('#companyAllForm not found');
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

                    // A) dot notation -> bracket notation (banks.0.iban_number etc.)
                    if (!$f.length && name.includes('.')) {
                        const bracket = dotToBracket(name); // banks[0][iban_number]
                        $f = $root.find('[name="' + bracket + '"]');

                        // If still not found, map to root array control (banks[] etc.)
                        if (!$f.length) {
                            const base = name.split('.')[0]; // banks
                            $f = $root.find('[name="' + base + '[]"]');
                        }
                    }

                    // B) Server key without [] but DOM has [] (e.g., tags vs tags[])
                    if (!$f.length && !name.includes('.')) {
                        $f = $root.find('[name="' + name + '[]"]');
                    }

                    if (!$f.length) return;

                    markInvalid($f, errs[name][0]);
                    if (!firstField) firstField = $f.get(0);

                    let tabId = '#company-details';
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
                fd.set('company_id', $('#saved_company_id').val() || '');
                appendDisabledFields(fd);
                return fd;
            }

            // ---------- AJAX calls (adjust routes to your controllers) ----------
            function saveCompanyBasic() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: $form.attr('action') || "{{ route('company.basic.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            if (resp && resp.ok) {
                                $('#saved_company_id').val(resp.company_id);
                                resolve(resp.company_id);
                            } else reject({
                                generic: 'Could not save Company Basic info.'
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

            function saveContact() {
                const $form = getForm();
                clearErrors($form);

                // ✅ client-side validation for contact tab
                if (!validateContactClient()) {
                    // Open the contact tab and stop early
                    openTab('#contact-info');
                    return Promise.reject({
                        generic: 'Please fill required Contact Information fields.'
                    });
                }

                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('company.contact.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Contact Information.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }


            function saveCompliance() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('company.compliance.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Compliance & Regulatory.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            function saveBankingFinance() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('company.banking.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save Banking & Finance.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }
            function saveHrPolicy() {
  const $form = getForm();
  clearErrors($form);

  // client-side validation for HR Policy
  if (!validateHrPolicyClient()){
    return Promise.reject({ generic: 'Please complete required HR Policy fields.' });
  }

  const fd = buildFD(); // includes policies[*] and their files
  return new Promise(function(resolve, reject){
    $.ajax({
      url: "{{ route('company.hrpolicy.store') }}",
      method: 'POST',
      data: fd,
      processData:false,
      contentType:false,
      headers:{ 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: resp => resp && resp.ok ? resolve(resp)
                                       : reject({ generic:'Could not save HR Policy.' }),
      error:   xhr => { if (xhr.status===422 && xhr.responseJSON?.errors) showErrors($form, xhr.responseJSON.errors);
                        reject(xhr); }
    });
  });
}



            function saveHRPayroll() {
                const $form = getForm();
                clearErrors($form);
                const fd = buildFD();
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('company.hrpayroll.store') }}",
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: resp => resp && resp.ok ? resolve(resp) : reject({
                            generic: 'Could not save HR & Payroll.'
                        }),
                        error: xhr => {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) showErrors(
                                $form, xhr.responseJSON.errors);
                            reject(xhr);
                        }
                    });
                });
            }

            

            // Documentation tab: view-only (no saveDocs here)
            // If later you add uploads in this tab, create saveDocs() similarly.

            // ---------- One-button flow ----------
            let savingAll = false;

            $('#btnSaveAllCompany').off('click.saveAll').on('click.saveAll', async function() {
                if (savingAll) return;
                savingAll = true;

                const $btn = $(this);
                if ($btn.data('busy')) {
                    savingAll = false;
                    return;
                }
                $btn.data('busy', true);

                // spinner + busy text (matches your staff button behavior)
                const $spinner = $btn.find('.spinner-border');
                const $text = $btn.find('.btn-text');
                const origText = $text.text();
                const busyText = $btn.data('busy-text') || 'Saving...';

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $text.text(busyText);
                $('#saveAllMsgCompany').text('');

                try {
                    let companyId = await saveCompanyBasic(); // from earlier script
                    await saveContact();
                    await saveCompliance();
                    await saveHrPolicy();       
                    await saveBankingFinance();
                    await saveHRPayroll();
                    $('#saveAllMsgCompany').text('All saved ✓ (Company ID: ' + companyId + ')');
                } catch (e) {
                    $('#saveAllMsgCompany').text(e && e.generic ? e.generic :
                        'Error saving. Please check highlighted fields.');
                    console.error('Company SaveAll failed:', e);
                } finally {
                    $spinner.addClass('d-none');
                    $text.text(origText);
                    $btn.prop('disabled', false).data('busy', false);
                    savingAll = false;
                }
            });

        });
        
    </script>
    <script>
        $(function() {
            // Init Select2 if you use it
            if ($.fn.select2) {
                $('#contactSections').select2({
                    placeholder: $('#contactSections').data('placeholder') || 'Select sections',
                    width: '100%'
                });
            }

            function toggleSectionBlocks(values) {
                // values: array like ['owner','contact']
                const set = new Set(values || []);
                $('.contact-block').each(function() {
                    const $blk = $(this);
                    const key = $blk.data('block'); // owner | sponsor | contact
                    const show = set.has(String(key));

                    $blk.toggleClass('d-none', !show);

                    // toggle requireds only inside this block
                    $blk.find('[data-required]').each(function() {
                        $(this).prop('required', show);
                        if (!show) {
                            $(this).removeClass('is-invalid');
                            // remove any inline feedback next to it
                            const $fb = $(this).next('.invalid-feedback');
                            if ($fb.length) $fb.remove();
                        }
                    });
                });
            }

            // initial (from old form state)
            toggleSectionBlocks($('#contactSections').val());

            // on change
            $('#contactSections').on('change', function() {
                const selected = $(this).val() || [];
                toggleSectionBlocks(selected);
            });
        });
        const fd = buildFD();

        // DEBUG: list what’s being sent
        for (const [k, v] of fd.entries()) {
            console.log(k, v instanceof File ? `(File: ${v.name}, size=${v.size})` : v);
        }
    </script>

    <!-- CKEditor 5 Classic build (free) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('textarea.editor'), {
                toolbar: [
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'undo', 'redo'
                ]
            })
            .catch(error => {
                console.error(error);
            });
    </script>

<script>
$(function(){
  function toggleTaxFields(val){
    // hide all first
    $('#vatFields, #ctFields').addClass('d-none');
    $('#vat_registration_number').prop('required', false);

    if(val === 'vat'){
      $('#vatFields').removeClass('d-none');
      $('#vat_registration_number').prop('required', true);
    }
    else if(val === 'ct'){
      $('#ctFields').removeClass('d-none');
    }
    else if(val === 'both'){
      $('#vatFields, #ctFields').removeClass('d-none');
      $('#vat_registration_number').prop('required', true);
    }
    // if "none" -> keep hidden
  }

  // init (respect old value)
  toggleTaxFields($('#tax_applicable').val() || '');

  // on change
  $('#tax_applicable').on('change', function(){
    toggleTaxFields(this.value);
  });
});
</script>

<script>
    <!-- CKEditor 5 (Classic) — include once on the page -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<!-- Header + Add button -->
<div class="mb-2 d-flex justify-content-between align-items-center">
  <h6 class="mb-0">HR Policies</h6>
  <button type="button" class="btn btn-light btn-sm" id="addPolicyRow">
    <i class="ico icon-outline-add-square text-success"></i> Add Policy
  </button>
</div>

<!-- Policies container -->
<div id="policyList"></div>

<!-- Template (not rendered) -->
<script type="text/template" id="policyRowTemplate">
  <div class="policy-item border rounded p-2 mb-3" data-index="__INDEX__">
    <div class="row gy-2 align-items-end">
      <div class="col-lg-2">
        <label class="form-label mb-1">Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control form-control-sm"
               name="policies[__INDEX__][policy_date]" required>
      </div>

      <div class="col-lg-2">
        <label class="form-label mb-1">Policy Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm"
               name="policies[__INDEX__][policy_name]" required>
      </div>

      <div class="col-lg-2">
        <label class="form-label mb-1">Policy Category</label>
        <select class="form-select form-select-sm"
                name="policies[__INDEX__][policy_category]">
          <option value="">Select</option>
          <option value="health">Health</option>
          <option value="life">Life</option>
          <option value="vehicle">Vehicle</option>
        </select>
      </div>

      <div class="col-lg-2">
        <label class="form-label mb-1">Valid</label>
        <input type="date" class="form-control form-control-sm"
               name="policies[__INDEX__][policy_valid]">
      </div>

      <div class="col-lg-2">
        <label class="form-label mb-1">View to Employees <span class="text-danger">*</span></label>
        <select class="form-select form-select-sm"
                name="policies[__INDEX__][view_to_employees]" required>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
      </div>

      <div class="col-lg-2">
        <label class="form-label mb-1">File Upload <span class="text-danger">*</span></label>
        <input type="file" class="form-control form-control-sm"
               name="policies[__INDEX__][policy_file]" required>
      </div>
    </div>

    <div class="row gy-2 mt-2">
      <div class="col-lg-10">
        <label class="form-label mb-1">Details</label>
        <textarea class="form-control form-control-sm policy-editor"
                  name="policies[__INDEX__][policy_details]" rows="6"
                  id="policy_details___INDEX__"></textarea>
      </div>
      <div class="col-lg-2 d-flex justify-content-end align-items-start">
        <button type="button" class="btn btn-light text-danger btn-sm removePolicyRow" title="Remove">
          <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
        </button>
      </div>
    </div>
  </div>
</script>

<script>
$(function () {
  // Ensure these inputs belong to your main form (if using multi-tab setup)
  // $('#hr-policy [name]').attr({ form: 'companyAllForm' });

  let policyIdx = 0;
  const editors = {}; // { index: CKEditorInstance }

  function initCkEditor(textarea) {
    if (!window.ClassicEditor) return;
    ClassicEditor.create(textarea).then(instance => {
      const idx = $(textarea).closest('.policy-item').data('index');
      editors[idx] = instance;
    }).catch(console.error);
  }

  function destroyEditorFor(index) {
    if (editors[index]) {
      editors[index].destroy().catch(()=>{});
      delete editors[index];
    }
  }

  function addPolicyRow(prefill = {}) {
    const html = $('#policyRowTemplate').html().replace(/__INDEX__/g, policyIdx);
    const $row = $(html);
    // Optional: prefill values if passed
    Object.entries(prefill).forEach(([k, v]) => {
      $row.find('[name="policies['+policyIdx+']['+k+']"]').val(v);
    });
    $('#policyList').append($row);

    // Init CKEditor on this row's textarea
    const ta = $row.find('textarea.policy-editor').get(0);
    initCkEditor(ta);

    policyIdx++;
  }

  // Initial one row (or call addPolicyRow() multiple times if you want more)
  addPolicyRow();

  // Add new row
  $('#addPolicyRow').on('click', function () {
    addPolicyRow();
  });

  // Remove row
  $(document).on('click', '.removePolicyRow', function () {
    const $item = $(this).closest('.policy-item');
    const idx = $item.data('index');
    destroyEditorFor(idx);
    $item.remove();
  });

  // If you need to reindex (usually not necessary), you could implement it here.

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

@include('backEnd.company.partials._modals')


@endsection

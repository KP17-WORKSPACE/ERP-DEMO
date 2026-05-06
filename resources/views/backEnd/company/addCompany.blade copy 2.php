@extends('backEnd.newmasterpage')

@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp



    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <form id="companyAllForm" novalidate action="{{ route('company.basic.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <!-- DATA DETAILS -->
                <div role="tabpanel" aria-labelledby="data-tab" id="data-details" class="tab-pane show active">
                    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
                        <h4 class="purchase-order-content-header-left">New (COM - {{ $nextId }})

                        </h4>
                        <div class="purchase-order-content-header-right">



                            <button type="submit" class="btn btn-light" id="btnSaveAll"><i
                                    class="ico icon-outline-bookmark-opened text-success"></i>Save</button>


                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu"
                                    style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(-124px, 30px);"
                                    data-popper-placement="bottom-end">
                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/department') }}">
                                                <i class="ico icon-outline-folder text-primary"></i>
                                                Company Policy
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/department') }}">
                                                <i class="ico icon-outline-folder text-primary"></i>
                                                Department
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/designation') }}">
                                                <i class="ico icon-outline-document text-warning"></i>
                                                Designation
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/legal-entity') }}">
                                                <i class="ico icon-outline-document text-warning"></i>
                                                Business Entity
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/industry') }}">
                                                <i class="ico icon-outline-layers text-secondary"></i>
                                                Industry Type
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ url('/business-activity') }}">
                                                <i class="ico icon-outline-layers text-secondary"></i>
                                                Business Sector
                                            </a>
                                        </li>

                                    </ul>


                                </ul>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row gy-3">

                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">Company Name</label>
                                        <input type="text" class="form-control form-control-sm" name="company_name"
                                            id="company_name" value="{{ old('company_name') }}">

                                        @if ($errors->has('company_name'))
                                            <small class="text-danger">{{ $errors->first('company_name') }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="form-label mb-1">Trade Name</label>
                                        <input type="text" class="form-control form-control-sm" name="trade_name"
                                            value="{{ old('trade_name') }}">

                                        @if ($errors->has('trade_name'))
                                            <small class="text-danger">{{ $errors->first('trade_name') }}</small>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-lg-2 mt-n1">
                                    <div class="input-effect">
                                        <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                            <span>Business Entity Type</span>
                                            <button type="button" class="btn btn-sm p-0 ms-2"
                                                style="border:none;background:none;" data-bs-toggle="modal"
                                                data-bs-target="#entityTypeAddModal">
                                                <i class="ico icon-outline-add-square text-success"
                                                    style="font-size:18px;"></i>
                                            </button>
                                        </label>

                                        <select name="business_entity_type_id"
                                            class="form-control form-control-sm js-example-basic-single">
                                            <option value="">Select Business Entity Type</option>
                                            @foreach ($entities as $ent)
                                                <option value="{{ $ent->id }}"
                                                    {{ old('business_entity_type_id') == $ent->id ? 'selected' : '' }}>
                                                    {{ $ent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                {{-- Industry Type --}}
                                <div class="col-lg-2 mt-n1">
                                    <div class="input-effect">
                                        <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                            <span>Industry Type</span>
                                            <button type="button" class="btn btn-sm p-0 ms-2" data-bs-toggle="modal"
                                                data-bs-target="#industryTypeAddModal">
                                                <i class="ico icon-outline-add-square text-success"
                                                    style="font-size:18px;"></i>
                                            </button>
                                        </label>

                                        <select name="industry_type_id" id="industry_type_id"
                                            class="form-control form-control-sm js-example-basic-single">
                                            <option value="">Select Industry</option>
                                            @foreach ($industries as $ind)
                                                <option value="{{ $ind->id }}"
                                                    {{ old('industry_type_id') == $ind->id ? 'selected' : '' }}>
                                                    {{ $ind->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Business Sector --}}
                                <div class="col-lg-2 mt-n1">
                                    <div class="input-effect">
                                        <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                            <span>Business Sector</span>
                                            <button type="button" class="btn btn-sm p-0" data-bs-toggle="modal"
                                                data-bs-target="#addBusinessSector">
                                                <i class="ico icon-outline-add-square text-success"
                                                    style="font-size:17px;"></i>
                                            </button>
                                        </label>

                                        <select name="business_sector_id" id="business_sector_id"
                                            class="form-control form-control-sm js-example-basic-single">
                                            <option value="">Select Sector</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-1-5">
                                    <label class="form-label mb-1">Date of Incorporation</label>
                                    <input type="text" name="date_of_incorporation"
                                        class="form-control form-control-sm date-picker"
                                        value="{{ old('date_of_incorporation') }}">
                                </div>

                                <div class="col-1-5">
                                    <label class="form-label mb-1">Company Type</label>

                                    <select name="company_type" id="company_type"
                                        class="form-control form-control-sm js-example-basic-single">

                                        <option value="parent" {{ old('company_type') == 'parent' ? 'selected' : '' }}>
                                            Parent</option>
                                        <option value="subsidiary"
                                            {{ old('company_type') == 'subsidiary' ? 'selected' : '' }}>Group
                                        </option>
                                        <option value="branch" {{ old('company_type') == 'branch' ? 'selected' : '' }}>
                                            Branch</option>
                                        <option value="sub_branch"
                                            {{ old('company_type') == 'sub_branch' ? 'selected' : '' }}>Sub Branch
                                        </option>
                                    </select>

                                    @if ($errors->has('company_type'))
                                        <small class="text-danger">{{ $errors->first('company_type') }}</small>
                                    @endif
                                </div>


                                {{-- Parent Name (ONLY FOR PARENT) --}}
                                <div class="col-lg-3 d-none" id="parentNameBox">
                                    <label class="form-label mb-1">Parent Company Name</label>
                                    <input type="text" name="parent_company" id="parent_company_name"
                                        class="form-control form-control-sm" value="{{ old('parent_company') }}">
                                </div>


                                {{-- Parent Company Dropdown (Subsidiary/Branch/Sub Branch) --}}
                                <div class="col-lg-3 d-none" id="parentDropdownBox">
                                    <label class="form-label mb-1">Select Parent Company</label>

                                    <select name="parent_company_id" id="parent_company_id"
                                        class="form-select form-select-sm js-example-basic-single">
                                        <option value="">Select Company</option>
                                        @foreach ($parentCompanies as $comp)
                                            <option value="{{ $comp->id }}"
                                                {{ old('parent_company_id') == $comp->id ? 'selected' : '' }}>
                                                {{ $comp->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Company Logo --}}
                                <div class="col-lg-2">
                                    <label class="form-label mb-1">Company Logo</label>

                                    <input type="file" id="company_logo_input" class="form-control form-control-sm"
                                        name="company_logo" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                </div>

                                {{-- Digital Stamp --}}
                                <div class="col-lg-2">
                                    <label class="form-label mb-1">Digital Stamp</label>

                                    <input type="file" id="digital_stamp_input" class="form-control form-control-sm"
                                        name="digital_stamp" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                </div>

                                {{-- Company Profile --}}
                                <div class="col-lg-2 mb-4">
                                    <label class="form-label mb-1">Company Profile</label>

                                    <input type="file" id="company_profile_input" class="form-control form-control-sm"
                                        name="company_profile" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                </div>



                            </div>





                            <div class="row mt-4">
                                <div class="col-12">

                                    <h6 class="mb-3"></h6>

                                    <div class="tab-wrap mb-3">
                                        <ul class="nav nav-tabs" id="hrTabs" role="tablist">


                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="contact-tab" data-bs-toggle="tab"
                                                    data-bs-target="#contact-details" type="button" role="tab"
                                                    aria-controls="contact-details" aria-selected="false">
                                                    Contact Information
                                                </button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="company-tab" data-bs-toggle="tab"
                                                    data-bs-target="#company-details" type="button" role="tab"
                                                    aria-controls="company-details" aria-selected="false">
                                                    Company Settings
                                                </button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="company-registration-tab"
                                                    data-bs-toggle="tab" data-bs-target="#company-registration"
                                                    type="button" role="tab" aria-controls="company-registration"
                                                    aria-selected="false">
                                                    Company Registration
                                                </button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="banking-finance-tab" data-bs-toggle="tab"
                                                    data-bs-target="#banking-finance" type="button" role="tab"
                                                    aria-controls="banking-finance" aria-selected="false">
                                                    Banking & Finance
                                                </button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="warehouse-info-tab" data-bs-toggle="tab"
                                                    data-bs-target="#warehouse-info" type="button" role="tab"
                                                    aria-controls="warehouse-info" aria-selected="false">
                                                    Warehouse Info
                                                </button>
                                            </li>

                                            <li class="nav-item">
                                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                                    data-bs-target="#policy-info" data-tab-key="policy">
                                                    Company Policies

                                                </button>
                                            </li>

                                            <li class="nav-item">
                                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                                    data-bs-target="#hrms-info" data-tab-key="hrms">
                                                    HRMS Settings

                                                </button>
                                            </li>

                                            <li class="nav-item">
                                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                                    data-bs-target="#documentation-info" data-tab-key="documentation">
                                                    Documentation

                                                </button>
                                            </li>



                                        </ul>

                                        <div class="tab-content border  bg-white" id="hrTabsContent">



                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade show active" id="contact-details" role="tabpanel"
                                                aria-labelledby="contact-tab">
                                                <div class="accordion" id="contactInfoAccordion">

                                                    <!-- 1️⃣ ADDRESS INFORMATION -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseAddress">
                                                                1. Address
                                                            </button>
                                                        </h2>
                                                        <div id="collapseAddress" class="accordion-collapse collapse show"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row gy-2">

                                                                    <div class="col-lg-2">
                                                                        <label>Country</label>
                                                                        <select name="country" id="country_company"
                                                                            class="form-select form-select-sm js-example-basic-single">
                                                                            <option value="">Select</option>
                                                                            @foreach ($country as $c)
                                                                                <option value="{{ $c->id }}"
                                                                                    data-iso2="{{ strtolower($c->iso2 ?? '') }}"
                                                                                    data-dial-code="{{ $c->dial_code ?? '' }}"
                                                                                    {{ old('country') == $c->id ? 'selected' : '' }}>
                                                                                    {{ $c->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>


                                                                        <!-- External JS for country codes -->
                                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
                                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>


                                                                        <script>
                                                                            $(document).ready(function() {

                                                                                /* ----------------------------------------
                                                                                   Build ISO2 → Dial code map
                                                                                ---------------------------------------- */
                                                                                var countryCodes = {};
                                                                                if (window.intlTelInputGlobals && typeof window.intlTelInputGlobals.getCountryData === 'function') {
                                                                                    $.each(window.intlTelInputGlobals.getCountryData(), function(index, country) {
                                                                                        countryCodes[(country.iso2 || '').toLowerCase()] = country.dialCode;
                                                                                    });
                                                                                }

                                                                                /* ----------------------------------------
                                                                                   Remove existing country code & spaces
                                                                                ---------------------------------------- */
                                                                                function stripLeadingDialCode(value) {
                                                                                    if (!value) return '';
                                                                                    return value
                                                                                        .replace(/^\+\d+\s*/, '')
                                                                                        .replace(/\s+/g, '')
                                                                                        .trim();
                                                                                }

                                                                                /* ----------------------------------------
                                                                                   Apply dial code → ALWAYS "+CODE␠"
                                                                                ---------------------------------------- */
                                                                                function applyDialCodeToMobile(dialCode) {

                                                                                    // Hidden field for backend
                                                                                    $('#mobile_code').val(dialCode ? ('+' + dialCode) : '');

                                                                                    var formatWithDial = function($el) {
                                                                                        if (!$el || !$el.length) return;

                                                                                        var current = stripLeadingDialCode($el.val());

                                                                                        if (dialCode) {
                                                                                            // 👇 FORCE +CODE + SPACE even if number is empty
                                                                                            $el.val('+' + dialCode + ' ' + current);
                                                                                        } else {
                                                                                            $el.val(current);
                                                                                        }
                                                                                    };

                                                                                    // Main inputs
                                                                                    formatWithDial($('#company_mobile'));
                                                                                    formatWithDial($('#company_mobile_phone'));
                                                                                    formatWithDial($('#office_telephone'));

                                                                                    // Dynamic & legacy inputs
                                                                                    $('input[name$="[mobile]"], input[name="mobile"], input[name^="e_work_phone"], input[name^="e_mobile"]')
                                                                                        .each(function() {
                                                                                            formatWithDial($(this));
                                                                                        });
                                                                                }

                                                                                /* ----------------------------------------
                                                                                   Get dial code from option
                                                                                ---------------------------------------- */
                                                                                function getCountryDialCode($opt) {
                                                                                    if (!$opt || !$opt.length) return '';
                                                                                    var iso2 = ($opt.data('iso2') || '').toLowerCase();
                                                                                    return $opt.data('dial-code') || countryCodes[iso2] || '';
                                                                                }

                                                                                window.applyDialCodeToMobile = applyDialCodeToMobile;
                                                                                window.getCountryDialCode = getCountryDialCode;

                                                                                /* ----------------------------------------
                                                                                   Country change
                                                                                ---------------------------------------- */
                                                                                $('#country_company').on('change', function() {
                                                                                    var $opt = $(this).find('option:selected');
                                                                                    var dial = getCountryDialCode($opt);

                                                                                    // Sync hidden country
                                                                                    var $hiddenCountry = $('#country');
                                                                                    if ($hiddenCountry.length) {
                                                                                        $hiddenCountry.val($(this).val()).trigger('change');
                                                                                    }

                                                                                    applyDialCodeToMobile(dial);
                                                                                });

                                                                                /* ----------------------------------------
                                                                                   Init on load
                                                                                ---------------------------------------- */
                                                                                (function initCountryOnLoad() {
                                                                                    var $selected = $('#country_company option:selected');
                                                                                    if ($selected.length && $selected.val()) {
                                                                                        $('#country_company').trigger('change');
                                                                                    }
                                                                                })();

                                                                            });
                                                                        </script>


                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>State</label>
                                                                        <select name="state" id="state"
                                                                            class="form-select form-select-sm js-example-basic-single">
                                                                            <option value="">Select</option>
                                                                            @foreach ($states ?? [] as $s)
                                                                                <option value="{{ $s->id }}"
                                                                                    {{ old('state') == $s->id ? 'selected' : '' }}>
                                                                                    {{ $s->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>


                                                                    <div class="col-lg-2">
                                                                        <label>City</label>
                                                                        <input type="text" name="city"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('city') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label>Area</label>
                                                                        <input type="text" name="area"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('area') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label>Building Name</label>
                                                                        <input type="text" name="building_no"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('building_no') }}">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                        <label>Flat / Office No</label>
                                                                        <input type="text" name="floor_shop_no"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('floor_shop_no') }}">
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 2️⃣ CONTACT INFORMATION -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseBasicInfo">
                                                                2. Contact Details
                                                            </button>
                                                        </h2>

                                                        <div id="collapseBasicInfo" class="accordion-collapse collapse"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row gy-2">

                                                                    <div class="col-lg-2">
                                                                        <label>Company Email</label>
                                                                        <input type="email" name="email"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('email') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Website</label>
                                                                        <input type="text" name="website"
                                                                            class="form-control form-control-sm text-lowercase"
                                                                            value="{{ old('website') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Office Phone</label>
                                                                        <input type="text" name="telephone"
                                                                            class="form-control form-control-sm"
                                                                            id="office_telephone"
                                                                            value="{{ old('telephone') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Mobile Number</label>
                                                                        <input type="hidden" id="mobile_code"
                                                                            name="mobile_code"
                                                                            value="{{ old('mobile_code', '') }}">
                                                                        <input type="text" id="company_mobile"
                                                                            name="mobile"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('mobile') }}">
                                                                    </div>
                                                                    {{-- <div class="col-lg-2">
                            <label>Fax No *</label>
                            <input type="text" name="fax" class="form-control form-control-sm"
                                value="{{ old('fax', $company->fax ?? '') }}">
                        </div> --}}

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 3️⃣ OWNER DETAILS -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseOwners">
                                                                3. Owner Details
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOwners" class="accordion-collapse collapse"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div id="ownerWrapper">
                                                                    {{-- Owner shares summary and validation messages --}}
                                                                    <div
                                                                        class="mb-2 d-flex justify-content-end align-items-center">
                                                                        <div>
                                                                            <small id="ownersShareSummary"
                                                                                class="text-muted">Total shares: 0% —
                                                                                Remaining: 100%</small>
                                                                            <small id="ownersShareError"
                                                                                class="text-danger d-none ms-2">Total share
                                                                                cannot exceed 100%.</small>
                                                                        </div>

                                                                    </div>

                                                                    @php

                                                                        $all_designations = @App\SmDesignation::where(
                                                                            'active_status',
                                                                            '=',
                                                                            '1',
                                                                        )
                                                                            ->orderBy('title', 'asc')
                                                                            ->get();
                                                                    @endphp




                                                                    <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
                                                                        <div class="col-lg-1">
                                                                            <label>Salutation</label>
                                                                            <select name="owners[][salutation]"
                                                                                class="form-select form-select-sm js-example-basic-single">
                                                                                <option value="">Select</option>
                                                                                <option value="Mr">Mr</option>
                                                                                <option value="Mrs">Mrs</option>
                                                                                <option value="Miss">Miss</option>
                                                                                <option value="Ms">Ms</option>
                                                                                <option value="Dr">Dr</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>First Name</label>
                                                                            <input type="text"
                                                                                name="owners[][first_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>Last Name</label>
                                                                            <input type="text"
                                                                                name="owners[][last_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>Mobile</label>
                                                                            <input type="text" name="owners[][mobile]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>Email</label>
                                                                            <input type="email" name="owners[][email]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <div class="input-effect">
                                                                                <label class="form-label">
                                                                                    <span>@lang('Designation')
                                                                                    </span>

                                                                                </label>
                                                                                @php
                                                                                    $designations =
                                                                                        $all_designations ??
                                                                                        @App\SmDesignation::where(
                                                                                            'active_status',
                                                                                            '=',
                                                                                            '1',
                                                                                        )
                                                                                            ->orderBy('title', 'asc')
                                                                                            ->get();

                                                                                @endphp
                                                                                <select
                                                                                    class="form-select form-select-sm js-example-basic-single"
                                                                                    name="owners[][designation_id]"
                                                                                    id="designation_id_">
                                                                                    <option value="">
                                                                                    </option>
                                                                                    @foreach ($designations as $key => $value)
                                                                                        <option
                                                                                            value="{{ $value->id }}"
                                                                                            {{ empty($owner['designation_id'] ?? '') &&
                                                                                            (stripos($value->title, 'owner') !== false ||
                                                                                                stripos($value->title, 'share') !== false ||
                                                                                                stripos($value->title, 'shareholder') !== false ||
                                                                                                stripos($value->title, 'owners') !== false)
                                                                                                ? 'selected'
                                                                                                : '' }}>
                                                                                            {{ $value->title }}
                                                                                        </option>
                                                                                    @endforeach

                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-1">
                                                                            <label>Share %</label>
                                                                            <input type="number"
                                                                                name="owners[][share_percentage]"
                                                                                class="form-control form-control-sm"
                                                                                min="0" max="100"
                                                                                value="" placeholder="0">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <div class="d-flex gap-1 mt-4">
                                                                                <button type="button"
                                                                                    class="btn btn-light d-inline-flex align-items-center gap-2 owner-doc-btn"
                                                                                    onclick="ownerdocumentModal(this)">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                    Documents
                                                                                </button>

                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm addOwner">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                </button>

                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm removeOwner">
                                                                                    <i
                                                                                        class="ico icon-outline-minus-square text-danger"></i>
                                                                                </button>

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <div id="owner-documents-"
                                                                                class="mt-2 d-none owner-documents-container"
                                                                                style="display: none;">
                                                                                <small class="text-muted">Added
                                                                                    Documents:</small>
                                                                                <div
                                                                                    class="owner-doc-list- owner-doc-list">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 4️⃣ SPONSOR DETAILS -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseSponsors">
                                                                4. Sponsor Details
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSponsors" class="accordion-collapse collapse"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div id="sponsorWrapper">

                                                                    <div
                                                                        class="sponsorRow row gy-2 p-2 mb-2 border rounded">
                                                                        <div class="col-lg-1">
                                                                            <label>Salutation</label>
                                                                            <select name="sponsors[0][salutation]"
                                                                                class="form-select form-select-sm js-example-basic-single">
                                                                                <option value="">Select</option>
                                                                                <option value="Mr">
                                                                                    Mr</option>
                                                                                <option value="Mrs">
                                                                                    Mrs</option>
                                                                                <option value="Miss">
                                                                                    Miss</option>
                                                                                <option value="Ms">
                                                                                    Ms</option>
                                                                                <option value="Dr">
                                                                                    Dr</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>First Name</label>
                                                                            <input type="text"
                                                                                name="sponsors[0][first_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Last Name</label>
                                                                            <input type="text"
                                                                                name="sponsors[0][last_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Mobile</label>
                                                                            <input type="text"
                                                                                name="sponsors[0][mobile]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Email</label>
                                                                            <input type="email"
                                                                                name="sponsors[0][email]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <div class="d-flex gap-1 mt-4">
                                                                                <button type="button"
                                                                                    class="btn btn-light d-inline-flex align-items-center gap-2 sponsor-doc-btn"
                                                                                    onclick="sponsordocumentModal(this)">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                    Documents
                                                                                </button>

                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm addSponsor">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                </button>

                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm removeSponsor">
                                                                                    <i
                                                                                        class="ico icon-outline-minus-square text-danger"></i>
                                                                                </button>

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <div id="sponsor-documents-"
                                                                                class="mt-2 d-none sponsor-documents-container"
                                                                                style="display: none;">
                                                                                <small class="text-muted d-none">Added
                                                                                    Documents:</small>
                                                                                <div
                                                                                    class="sponsor-doc-list- sponsor-doc-list d-none">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 5️⃣ CONTACT PERSON DETAILS -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseContacts">
                                                                5. Contact Person Details
                                                            </button>
                                                        </h2>
                                                        <div id="collapseContacts" class="accordion-collapse collapse"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div id="contactWrapper">

                                                                    <div
                                                                        class="contactRow row gy-2 p-2 mb-2 border rounded">
                                                                        <div class="col-lg-1">
                                                                            <label>Salutation</label>
                                                                            <select name="contacts[0][salutation]"
                                                                                class="form-select form-select-sm js-example-basic-single">
                                                                                <option value="">Select</option>
                                                                                <option value="Mr">
                                                                                    Mr</option>
                                                                                <option value="Mrs">
                                                                                    Mrs</option>
                                                                                <option value="Miss">
                                                                                    Miss</option>
                                                                                <option value="Ms">
                                                                                    Ms</option>
                                                                                <option value="Dr">
                                                                                    Dr</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>First Name</label>
                                                                            <input type="text"
                                                                                name="contacts[0][first_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-1-5">
                                                                            <label>Last Name</label>
                                                                            <input type="text"
                                                                                name="contacts[0][last_name]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Mobile</label>
                                                                            <input type="text"
                                                                                name="contacts[0][mobile]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Email</label>
                                                                            <input type="email"
                                                                                name="contacts[0][email]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <label>Designation</label>
                                                                            <input type="text"
                                                                                name="contacts[0][designation]"
                                                                                class="form-control form-control-sm"
                                                                                value="">
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            <div class="d-flex gap-1 mt-4">
                                                                                <button type="button"
                                                                                    class="btn btn-light d-inline-flex align-items-center gap-2"
                                                                                    onclick="contactdocumentModal(this)">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                    Documents
                                                                                </button>

                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm addContact">
                                                                                    <i
                                                                                        class="ico icon-outline-add-square"></i>
                                                                                </button>


                                                                                <button type="button"
                                                                                    class="btn btn-light btn-sm removeContact">
                                                                                    <i
                                                                                        class="ico icon-outline-minus-square text-danger"></i>
                                                                                </button>

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <div id="contact-documents-"
                                                                                class="mt-2 d-none contact-documents-container"
                                                                                style="display:none;">
                                                                                <small class="text-muted d-none">Added
                                                                                    Documents:</small>
                                                                                <div
                                                                                    class="contact-doc-list- contact-doc-list d-none">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- 6️⃣ SOCIAL MEDIA LINKS -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseSocial">
                                                                6. Social Media Links
                                                            </button>
                                                        </h2>
                                                        <div id="collapseSocial" class="accordion-collapse collapse"
                                                            data-bs-parent="#contactInfoAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row gy-2">
                                                                    <div class="col-lg-2">
                                                                        <label>LinkedIn</label>
                                                                        <input type="text" name="linkedin"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('linkedin', $company->linkedin ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Facebook</label>
                                                                        <input type="text" name="facebook"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('facebook', $company->facebook ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Instagram</label>
                                                                        <input type="text" name="instagram"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('instagram', $company->instagram ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Twitter (X)</label>
                                                                        <input type="text" name="twitter_x"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('twitter_x', $company->twitter_x ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>YouTube</label>
                                                                        <input type="text" name="youtube"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('youtube', $company->youtube ?? '') }}">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <label>Other Social</label>
                                                                        <input type="text" name="other_social"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ old('other_social', $company->other_social ?? '') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>



                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="company-details" role="tabpanel"
                                                aria-labelledby="company-tab">
                                                <div class="accordion" id="settingsAccordion">

                                                    {{-- ======================= 1. COMPANY SETTINGS ======================= --}}
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseCompanySetting">
                                                                1. Account Setting
                                                            </button>
                                                        </h2>
                                                        <div id="collapseCompanySetting"
                                                            class="accordion-collapse collapse show"
                                                            data-bs-parent="#settingsAccordion">
                                                            <div class="accordion-body">
                                                                <div class="row gy-2">

                                                                    {{-- Currency --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Currency</label>
                                                                        <select name="currency" id="settingCurrency"
                                                                            class="form-select form-select-sm setting-input js-example-basic-single">
                                                                            <option value="" data-symbol=""
                                                                                data-rate="" data-rcode=""
                                                                                data-pcode="">Select Currency</option>
                                                                            @foreach ($currency as $c)
                                                                                <option value="{{ $c->code }}"
                                                                                    data-symbol="{{ $c->symbol }}"
                                                                                    data-rate="{{ $c->rate }}"
                                                                                    data-rcode="{{ $c->r_code }}"
                                                                                    data-pcode="{{ $c->p_code }}">
                                                                                    {{ $c->name }}
                                                                                    ({{ $c->code }})
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    {{-- Currency Symbol (Auto-filled) --}}
                                                                    <div class="col-lg-1">
                                                                        <label class="form-label mb-1">Symbol</label>
                                                                        <input type="text" name="currency_symbol"
                                                                            id="currencySymbol"
                                                                            class="form-control form-control-sm setting-input"
                                                                            readonly value="">
                                                                    </div>

                                                                    {{-- Currency Digit --}}
                                                                    <div class="col-lg-1">
                                                                        <label class="form-label mb-1">Currency
                                                                            Digit</label>
                                                                        <input type="number" name="currency_digit"
                                                                            min="0" max="4"
                                                                            class="form-control form-control-sm setting-input"
                                                                            value="2">
                                                                    </div>

                                                                    {{-- R Code --}}
                                                                    <div class="col-lg-1">
                                                                        <label class="form-label mb-1">R Code</label>
                                                                        <input type="text" name="r_code"
                                                                            id="currencyRCode"
                                                                            class="form-control form-control-sm setting-input"
                                                                            readonly value="">
                                                                    </div>

                                                                    {{-- P Code --}}
                                                                    <div class="col-lg-1">
                                                                        <label class="form-label mb-1">P Code</label>
                                                                        <input type="text" name="p_code"
                                                                            id="currencyPCode"
                                                                            class="form-control form-control-sm setting-input"
                                                                            readonly value="">
                                                                    </div>

                                                                    {{-- Book Closed --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Book Closed</label>
                                                                        <input type="text" name="book_closed"
                                                                            class="form-control form-control-sm date-picker setting-input"
                                                                            value="">
                                                                    </div>

                                                                    {{-- Sales Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">Sales Code <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" name="sales_code"
                                                                            class="form-control form-control-sm setting-input"
                                                                            value="">
                                                                    </div>

                                                                    {{-- Other Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1">All Other Code <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" name="other_code"
                                                                            class="form-control form-control-sm setting-input"
                                                                            value="">
                                                                    </div>

                                                                    {{-- Customer Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1 d-block">Customer
                                                                            Code</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input setting-input"
                                                                                type="checkbox" name="is_customer_code"
                                                                                value="1"
                                                                                {{ old('is_customer_code') ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Supplier Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1 d-block">Supplier
                                                                            Code</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input setting-input"
                                                                                type="checkbox" name="is_supplier_code"
                                                                                value="1"
                                                                                {{ old('is_supplier_code') ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Account Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1 d-block">Account
                                                                            Code</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input setting-input"
                                                                                type="checkbox" name="is_account_code"
                                                                                value="1"
                                                                                {{ old('is_account_code') ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Sub Account Code --}}
                                                                    <div class="col-lg-2">
                                                                        <label class="form-label mb-1 d-block">Sub Account
                                                                            Code</label>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input setting-input"
                                                                                type="checkbox" name="is_subaccount_code"
                                                                                value="1"
                                                                                {{ old('is_subaccount_code') ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>

                                                                    <script>
                                                                        $(document).ready(function() {
                                                                            // Currency selection handler - auto-fill related fields
                                                                            $('#settingCurrency').on('change', function() {
                                                                                var $selected = $(this).find('option:selected');

                                                                                $('#currencySymbol').val($selected.data('symbol') || '');
                                                                                $('#currencyRCode').val($selected.data('rcode') || '');
                                                                                $('#currencyPCode').val($selected.data('pcode') || '');
                                                                            });

                                                                            // Trigger on page load if currency is already selected
                                                                            if ($('#settingCurrency').val()) {
                                                                                $('#settingCurrency').trigger('change');
                                                                            }
                                                                        });
                                                                    </script>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Account code settings moved into Company Setting section -->

                                                </div>

                                            </div>



                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="company-registration" role="tabpanel"
                                                aria-labelledby="company-registration-tab">


                                                <!-- UAE Compliance Section - Show only if UAE is selected -->
                                                <div id="uae-compliance-section" class="">
                                                    <div class="row gy-2">

                                                        <!-- Trade License Number -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">Trade License Number <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="trade_license_no" value="">
                                                            <small class="text-danger error"
                                                                data-error="trade_license_no"></small>
                                                        </div>

                                                        <!-- License Issue Date -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">License Issue Date <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm date-picker"
                                                                name="license_issue_date" id="license_issue_date"
                                                                value="">
                                                            <small class="text-danger error"
                                                                data-error="license_issue_date"></small>
                                                        </div>

                                                        <!-- License Expiry Date -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">License Expiry Date <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control form-control-sm date-picker"
                                                                name="license_expiry_date" id="license_expiry_date"
                                                                value="">
                                                            <small class="text-danger error"
                                                                data-error="license_expiry_date"></small>
                                                        </div>

                                                        <!-- Issuing Authority -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">Issuing Authority <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="issuing_authority" value="">
                                                            <small class="text-danger error"
                                                                data-error="issuing_authority"></small>
                                                        </div>

                                                        <!-- License Upload -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">Trade License Upload <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="file" class="form-control form-control-sm"
                                                                name="business_license_upload">

                                                            <small class="text-danger error"
                                                                data-error="business_license_upload"></small>
                                                        </div>

                                                        <!-- TAX APPLICABLE -->
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">Tax Applicable</label>
                                                            <select
                                                                class="form-control form-control-sm js-example-basic-single"
                                                                name="tax_applicable" id="tax_applicable">
                                                                <option value="">Select</option>
                                                                <option value="vat">VAT</option>
                                                                <option value="ct">CT</option>
                                                                <option value="both">Both (CT/VAT)</option>
                                                                <option value="none">Not Applicable</option>
                                                            </select>
                                                            <small class="text-danger error"
                                                                data-error="tax_applicable"></small>
                                                        </div>

                                                    </div>

                                                    <!-- VAT SECTION -->
                                                    <div class="row gy-2 mt-2 vat-section ">
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">VAT Registration No
                                                                (TRN)</label>
                                                            <input type="text" name="vat_registration_number"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">VAT %</label>
                                                            <input type="number" step="0.01" name="vat_percentage"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">VAT Registration Date</label>
                                                            <input type="text" name="vat_date"
                                                                class="form-control form-control-sm date-picker"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">VAT Issuing Authority</label>
                                                            <input type="text" name="vat_issuing_authority"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">VAT Certificate Upload</label>
                                                            <input type="file" name="vat_certificate"
                                                                class="form-control form-control-sm">

                                                        </div>
                                                    </div>

                                                    <!-- CT SECTION -->
                                                    <div class="row gy-2 mt-2 ct-section">
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">CT Registration No (CTN)</label>
                                                            <input type="text" name="corporate_tax_number"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">CT %</label>
                                                            <input type="text" name="corporate_tax_vat"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">CT Registration Date</label>
                                                            <input type="text" name="corporate_tax_date"
                                                                class="form-control form-control-sm date-picker"
                                                                value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">CT Issuing Authority</label>
                                                            <input type="text" name="ct_issuing_authority"
                                                                class="form-control form-control-sm" value="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <label class="form-label mb-1">CT Certificate Upload</label>
                                                            <input type="file" name="corporate_tax_certificate"
                                                                class="form-control form-control-sm">

                                                        </div>
                                                    </div>

                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            // Date validation: License expiry date should be after license issue date
                                                            const licenseIssueDate = document.getElementById('license_issue_date');
                                                            const licenseExpiryDate = document.getElementById('license_expiry_date');

                                                            function validateLicenseDates() {
                                                                if (licenseIssueDate.value && licenseExpiryDate.value) {
                                                                    const issueDate = new Date(licenseIssueDate.value);
                                                                    const expiryDate = new Date(licenseExpiryDate.value);

                                                                    if (expiryDate <= issueDate) {
                                                                        licenseExpiryDate.classList.add('is-invalid');
                                                                        let errorElement = licenseExpiryDate.parentNode.querySelector('.date-validation-error');
                                                                        if (!errorElement) {
                                                                            errorElement = document.createElement('small');
                                                                            errorElement.classList.add('text-danger', 'date-validation-error');
                                                                            licenseExpiryDate.parentNode.appendChild(errorElement);
                                                                        }
                                                                        errorElement.textContent = 'License expiry date must be after issue date';
                                                                        return false;
                                                                    } else {
                                                                        licenseExpiryDate.classList.remove('is-invalid');
                                                                        const errorElement = licenseExpiryDate.parentNode.querySelector('.date-validation-error');
                                                                        if (errorElement) {
                                                                            errorElement.remove();
                                                                        }
                                                                        return true;
                                                                    }
                                                                }
                                                                return true;
                                                            }

                                                            if (licenseIssueDate && licenseExpiryDate) {
                                                                licenseIssueDate.addEventListener('change', validateLicenseDates);
                                                                licenseExpiryDate.addEventListener('change', validateLicenseDates);

                                                                // Initial validation on page load
                                                                validateLicenseDates();
                                                            }
                                                        });
                                                    </script>
                                                </div>


                                                <!-- Non-UAE Countries Document Management -->
                                                <div id="non-uae-compliance-section" class="">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div
                                                                class="d-flex justify-content-end align-items-center mb-3">
                                                                <button type="button" class="btn btn-light gap-2"
                                                                    onclick="openComplianceDocumentModal()">
                                                                    <i
                                                                        class="ico icon-outline-add-square text-success"></i>
                                                                    Add
                                                                    Document
                                                                </button>
                                                            </div>

                                                            <!-- Documents List -->
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="">Document Number</th>
                                                                            <th class="text-center">Issue Date</th>
                                                                            <th class="text-center">Expiry Date</th>
                                                                            <th>Issuing Authority</th>
                                                                            <th class="">Attachment</th>
                                                                            <th class="text-center">Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="complianceDocumentsList">
                                                                        <tr>
                                                                            <td colspan="6"
                                                                                class="text-muted text-center">No
                                                                                compliance documents added yet.</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="banking-finance" role="tabpanel"
                                                aria-labelledby="banking-finance-tab">


                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" id="addBankBtn" class="btn btn-light gap-2">
                                                        <i class="ico icon-outline-add-square text-success"></i> Add Bank
                                                    </button>
                                                </div>




                                                <div class="table-responsive">
                                                    <table class="table table-hover data-table" id="long-list"
                                                        style="table-layout: fixed;width:100%">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Bank Name</th>
                                                                <th>Branch</th>
                                                                <th>Account No</th>
                                                                <th>IBAN</th>
                                                                <th>SWIFT</th>
                                                                <th class="text-center">Finance Code</th>
                                                                <th class="text-center">Currency</th>
                                                                <th>Letter</th>
                                                                <th style="width:120px;" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bankTableBody">

                                                            <tr class="no-bank-row">
                                                                <td colspan="9" class="text-center text-muted">No
                                                                    banks added yet.</td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>


                                            </div>


                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="warehouse-info" role="tabpanel"
                                                aria-labelledby="warehouse-info-tab">


                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" class="btn btn-light gap-2"
                                                        id="addWarehouseBtn">
                                                        <i class="ico icon-outline-add-square text-success"></i> Add
                                                        Warehouse
                                                    </button>
                                                </div>




                                                <div class="table-responsive">
                                                    {{-- WAREHOUSE LIST --}}
                                                    <div id="warehouseList" class="table-responsive">
                                                        <table class="table table-hover data-table" id="long-list"
                                                            style="table-layout: fixed;width:100%">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    {{-- <th>Warehouse Code</th> --}}
                                                                    <th>Warehouse Name</th>
                                                                    <th>Building / Area</th>
                                                                    <th>City</th>
                                                                    <th>Country</th>
                                                                    <th>State</th>
                                                                    <th>Contact Person</th>
                                                                    <th>Fire Safety Status</th>
                                                                    <th>Fire NOC No</th>
                                                                    <th>Fire NOC Expiry</th>
                                                                    <th>Safety Equipment</th>
                                                                    <th>Last Safety Insp.</th>
                                                                    <th>Documents</th>
                                                                    <th width="100">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="warehouseTableBody">
                                                                <tr class="no-warehouse-row">
                                                                    <td colspan="12" class="text-center text-muted">No
                                                                        warehouses added yet.</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>


                                            </div>



                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="policy-info" role="tabpanel"
                                                aria-labelledby="policy-info-tab">


                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" class="btn btn-light gap-2" id="addPolicyBtn">
                                                        <i class="ico icon-outline-add-square text-success"></i> Add Policy
                                                    </button>
                                                </div>





                                                <div class="table-responsive">
                                                    <table class="table table-hover data-table"
                                                        style="table-layout: fixed;width:100%">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Policy Name</th>

                                                                <th>Valid Till</th>
                                                                <th>View to Employees</th>
                                                                <th>File</th>
                                                                <th width="100">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="policyTableBody">
                                                            <tr class="no-policy-row">
                                                                <td colspan="6" class="text-center text-muted">No
                                                                    policies added yet.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>


                                            </div>


                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="hrms-info" role="tabpanel"
                                                aria-labelledby="hrms-info-tab">


                                                {{-- ================================== LEAVES POLICY ================================== --}}
                                                <h6 class="mb-3">

                                                    Leave Policy Types
                                                </h6>

                                                <div class="row gy-2 mb-4">
                                                    <!-- Leave Policy -->
                                                    {{-- <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Leave Policy
                                                            Type</label>
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="leave_policy_type">
                                                            <option value="">Select</option>
                                                            <option value="default">Default</option>
                                                            <option value="custom">Custom</option>
                                                        </select>
                                                    </div> --}}

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Annual Leave
                                                            (AL)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="annual_leave" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Sick Leave
                                                            (SL)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="sick_leave" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Casual Leave
                                                            (CL)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="casual_leave" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Comp-Off
                                                            Allowed</label>
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="comp_off_allowed">
                                                            <option value="">Select</option>
                                                            <option value="yes">Yes</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                    </div>

                                                    <!-- Leave Carry Forward -->
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Carry Forward
                                                            Unused Leaves</label>
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="carry_forward" id="carry_forward">
                                                            <option value="">Select</option>
                                                            <option value="yes">Yes</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-2 d-none" id="maxCarryWrap">
                                                        <label class="form-label mb-1 hr-payroll-labels">Max Carry Forward
                                                            (Days)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="max_carry_forward" value="">
                                                    </div>

                                  <script>
$(document).ready(function () {
    $('#carry_forward').on('change', function () {
        $('#maxCarryWrap').toggleClass('d-none', $(this).val() !== 'yes');
    });

    // for edit page / preselected value
    $('#carry_forward').trigger('change');
});
</script>


                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Encashable
                                                            Leaves</label>
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="leave_encashment">
                                                            <option value="">Select</option>
                                                            <option value="yes">Yes</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr class="text-muted">
                                                {{-- ================================== ATTENDANCE POLICY ================================== --}}
                                                <h6 class="mb-3">

                                                    Attendance Policy
                                                </h6>

                                                <div class="row gy-2 mb-4">
                                                    {{-- Attendance Policy --}}
                                                    {{-- <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Attendance
                                                            Policy</label>
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="attendance_policy">
                                                            <option value="">Select</option>
                                                            <option value="standard">Standard</option>
                                                            <option value="flexible">Flexible</option>
                                                            <option value="strict">Strict</option>
                                                        </select>
                                                    </div> --}}

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Minimum Working
                                                            Hours</label>
                                                        <input type="number" step="0.1"
                                                            class="form-control form-control-sm setting-input"
                                                            name="min_working_hours" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Grace Period
                                                            (Minutes)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="grace_period" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Half Day After
                                                            (Hours)</label>
                                                        <input type="number" step="0.1"
                                                            class="form-control form-control-sm setting-input"
                                                            name="half_day_after" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Mark Absent If
                                                            Hours Below</label>
                                                        <input type="number" step="0.1"
                                                            class="form-control form-control-sm setting-input"
                                                            name="absent_below_hours" value="">
                                                    </div>

                                                    {{-- Late Mark Rules --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Late Mark Count
                                                            Allowed (per month)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="late_mark_allowed" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Consecutive Late
                                                            Mark → Half Day</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="late_mark_halfday" value="">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Auto Mark Absent
                                                            After (Days)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm setting-input"
                                                            name="auto_absent_after" value="">
                                                    </div>

                                                    <div class="col-lg-2">

                                                        <label
                                                            class="form-label mb-0 d-flex justify-content-between align-items-center">
                                                            <span>Working Shifts</span>
                                                            <button type="button" class="btn btn-sm p-0 ms-2"
                                                                style="border:none;background:none;"
                                                                data-bs-toggle="modal" data-bs-target="#addShiftModal">
                                                                <i class="ico icon-outline-add-square text-success"
                                                                    style="font-size:18px;"></i>
                                                            </button>
                                                        </label>
                                                        @php
                                                            $working_shifts = @App\WorkingShift::all();

                                                        @endphp
                                                        <select
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            name="shift_id">
                                                            <option value="">Select</option>

                                                            @foreach ($working_shifts as $shift)
                                                                <option value="{{ $shift->id }}">
                                                                    {{ $shift->shift_name }}
                                                                    ({{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }})
                                                                </option>
                                                            @endforeach


                                                        </select>
                                                    </div>



                                                    <div class="col-lg-4">
                                                        <label class="form-label mb-1 hr-payroll-labels">Weekly
                                                            Off</label>
                                                        <select name="hr_weekly_off[]"
                                                            class="form-select form-select-sm setting-input js-example-basic-single"
                                                            multiple>
                                                            <option value="monday_all">
                                                                Monday (All)
                                                            </option>
                                                            <option value="1_3_monday">
                                                                1 & 3 Monday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_monday">
                                                                2 & 4 Monday (Only 2 & 4)
                                                            </option>
                                                            <option value="tuesday_all">
                                                                Tuesday (All)
                                                            </option>
                                                            <option value="1_3_tuesday">
                                                                1 & 3 Tuesday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_tuesday">
                                                                2 & 4 Tuesday (Only 2 & 4)
                                                            </option>
                                                            <option value="wednesday_all">
                                                                Wednesday (All)
                                                            </option>
                                                            <option value="1_3_wednesday">
                                                                1 & 3 Wednesday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_wednesday">
                                                                2 & 4 Wednesday (Only 2 & 4)
                                                            </option>
                                                            <option value="thursday_all">
                                                                Thursday (All)
                                                            </option>
                                                            <option value="1_3_thursday">
                                                                1 & 3 Thursday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_thursday">
                                                                2 & 4 Thursday (Only 2 & 4)
                                                            </option>
                                                            <option value="friday_all">
                                                                Friday (All)
                                                            </option>
                                                            <option value="1_3_friday">
                                                                1 & 3 Friday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_friday">
                                                                2 & 4 Friday (Only 2 & 4)
                                                            </option>
                                                            <option value="saturday_all">
                                                                Saturday (All)
                                                            </option>
                                                            <option value="1_3_saturday">
                                                                1 & 3 Saturday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_saturday">
                                                                2 & 4 Saturday (Only 2 & 4)
                                                            </option>
                                                            <option value="sunday_all">
                                                                Sunday (All)
                                                            </option>
                                                            <option value="1_3_sunday">
                                                                1 & 3 Sunday (Only 1 & 3)
                                                            </option>
                                                            <option value="2_4_sunday">
                                                                2 & 4 Sunday (Only 2 & 4)
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr class="text-muted">

                                                {{-- ================================== PAYROLL CONFIGURATION ================================== --}}
                                                <h6 class="mb-3">

                                                    Payroll Configuration
                                                </h6>

                                                <div class="row gy-2 mb-4">

                                                    {{-- WPS Establishment ID --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">WPS Establishment
                                                            ID <span class="text-danger">*</span></label>
                                                        <input type="text" name="hr_wps_establishment_id"
                                                            class="form-control form-control-sm setting-input"
                                                            value="">
                                                    </div>

                                                    {{-- WPS Bank --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">WPS Bank <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="hr_wps_bank"
                                                            class="form-control form-control-sm setting-input"
                                                            value="">
                                                    </div>

                                                    {{-- Salary File Code --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">WPS Salary File
                                                            Code</label>
                                                        <input type="text" name="hr_wps_salary_file_code"
                                                            class="form-control form-control-sm setting-input"
                                                            value="">
                                                    </div>

                                                    {{-- Payroll Cycle --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Payroll Cycle
                                                            <span class="text-danger">*</span></label>
                                                        <select name="hr_payroll_cycle"
                                                            class="form-select form-select-sm setting-input js-example-basic-single">
                                                            <option value="">Select</option>
                                                            <option value="monthly">
                                                                Monthly
                                                            </option>
                                                            <option value="bi-weekly">
                                                                Bi-Weekly
                                                            </option>
                                                            <option value="weekly">
                                                                Weekly
                                                            </option>
                                                        </select>
                                                    </div>

                                                    {{-- Payroll Start --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Payroll Start
                                                            Date</label>
                                                        <select name="hr_payroll_start"
                                                            class="form-select form-select-sm setting-input js-example-basic-single">
                                                            <option value="">Select</option>
                                                            @for ($i = 1; $i <= 30; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    {{-- Payroll End --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Payroll End
                                                            Date</label>
                                                        <select name="hr_payroll_end"
                                                            class="form-select form-select-sm setting-input js-example-basic-single">
                                                            <option value="">Select</option>
                                                            @for ($i = 1; $i <= 30; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>



                                                    {{-- Gratuity --}}
                                                    <div class="col-lg-2">
                                                        <label class="form-label mb-1 hr-payroll-labels">Gratuity
                                                            Calculation Method</label>
                                                        <select name="hr_gratuity_method"
                                                            class="form-select form-select-sm setting-input js-example-basic-single">
                                                            <option value="">Select</option>
                                                            <option value="basic_salary">
                                                                Basic Salary
                                                            </option>
                                                            <option value="gross_salary">
                                                                Gross Salary
                                                            </option>
                                                        </select>
                                                    </div>

                                                    {{-- Insurance Provider --}}


                                                </div>

                                                <hr class="text-muted">


                                                <h6 class="mb-3">

                                                    Loans & Advances
                                                </h6>

                                            </div>


                                            {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                            <div class="tab-pane fade" id="documentation-info" role="tabpanel"
                                                aria-labelledby="documentation-tab">

                                                <!-- UAE Documents Section -->
                                                <div id="uae-documents-section" class="">
                                                    <div class="d-flex justify-content-end gap-2 mb-2">
                                                        <button type="button" class="btn btn-light btn-sm"
                                                            id="addDocumentRowTop">
                                                            <i class="ico icon-outline-add-square text-success me-1"></i>
                                                            Add Rows
                                                        </button>

                                                    </div>

                                                    <div class="table-responsive">
                                                        <table id="documentationTable" class="table table-hover"
                                                            style="table-layout: fixed; width: 100%;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 18%;">Document Name</th>
                                                                    <th class="text-center" style="width: 14%;">Document
                                                                        Number</th>
                                                                    <th class="text-center" style="width: 12%;">Date
                                                                    </th>
                                                                    <th class="text-center" style="width: 12%;">Expire
                                                                        Date</th>
                                                                    <th class="text-start" style="width: 20%;">
                                                                        Attachment</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {{-- Establishment Card --}}
                                                                <tr>
                                                                    <td class="fw-bold">Establishment Card</td>

                                                                    <td>
                                                                        <input type="text"
                                                                            name="establishment_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('establishment_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="establishment_date"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('establishment_date') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="establishment_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('establishment_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="establishment_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Immigration Card --}}
                                                                <tr>
                                                                    <td class="fw-bold">Immigration Card</td>
                                                                    <td>
                                                                        <input type="text" name="immigration_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('immigration_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="immigration_date"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('immigration_date') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="immigration_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('immigration_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="immigration_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Labour Establishment Card --}}
                                                                <tr>
                                                                    <td class="fw-bold">Labour Establishment Card</td>
                                                                    <td>
                                                                        <input type="text" name="labour_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('labour_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="labour_date"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('labour_date') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="labour_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('labour_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="labour_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Chamber of Commerce --}}
                                                                <tr>
                                                                    <td class="fw-bold">Chamber of Commerce</td>
                                                                    <td>
                                                                        <input type="text" name="chamber_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('chamber_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="chamber_date"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('chamber_date') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="chamber_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('chamber_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="chamber_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Insurance Certificate --}}
                                                                <tr>
                                                                    <td class="fw-bold">Medical Insurance </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="insurance_certificate_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('insurance_certificate_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="insurance_certificate_date"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('insurance_certificate_date') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input name="insurance_certificate_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('insurance_certificate_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="insurance_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- MOA / AOA --}}
                                                                <tr>
                                                                    <td class="fw-bold">MOA / AOA</td>
                                                                    <td>
                                                                        <input type="text" name="moa_aoa_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('moa_aoa_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        {{-- Not used, but keep for consistency --}}
                                                                    </td>
                                                                    <td>
                                                                        <input name="moa_aoa_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('moa_aoa_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="moa_aoa_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Board Resolution --}}
                                                                <tr>
                                                                    <td class="fw-bold">Board Resolution</td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="board_resolution_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('board_resolution_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        {{-- Not used, but keep for consistency --}}
                                                                    </td>
                                                                    <td>
                                                                        <input name="board_resolution_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('board_resolution_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file"
                                                                            name="board_resolution_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Power of Attorney --}}
                                                                <tr>
                                                                    <td class="fw-bold">Power of Attorney</td>
                                                                    <td>
                                                                        <input type="text" name="poa_number"
                                                                            class="form-control form-control-sm doc-input text-center"
                                                                            placeholder=""
                                                                            value="{{ old('poa_number') }}">
                                                                    </td>
                                                                    <td>
                                                                        {{-- Not used, but keep for consistency --}}
                                                                    </td>
                                                                    <td>
                                                                        <input name="poa_expiry"
                                                                            class="form-control form-control-sm date-picker doc-input"
                                                                            value="{{ old('poa_expiry') }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" name="poa_file"
                                                                            class="form-control form-control-sm doc-input">

                                                                    </td>
                                                                </tr>

                                                                {{-- Dynamic Document Rows will be inserted here --}}

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Non-UAE Documents Section -->
                                                <div id="non-uae-documents-section" class="">
                                                    <div class="d-flex justify-content-end gap-2 mb-2">
                                                        <button type="button" class="btn btn-light btn-sm"
                                                            id="addDocumentRowTopNonUae">
                                                            <i class="ico icon-outline-add-square me-1 text-success"></i>
                                                            Add Rows
                                                        </button>

                                                    </div>

                                                    <div class="table-responsive">
                                                        <table id="nonUaeDocumentationTable" class="table table-hover"
                                                            style="table-layout: fixed; width: 100%;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 18%;">Document Name</th>
                                                                    <th class="text-center" style="width: 14%;">Document
                                                                        Number</th>
                                                                    <th class="text-center" style="width: 12%;">Date
                                                                    </th>
                                                                    <th class="text-center" style="width: 12%;">Expire
                                                                        Date</th>
                                                                    <th class="text-center" style="width: 20%;">
                                                                        Attachment</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {{-- Dynamic Document Rows for Non-UAE countries --}}
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
                </div>
            </form>

        </div>





        <div class="modal side-panel  fade" id="entityTypeAddModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Business Entity Type</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">



                        <label class="form-label">Entity Type Name <span class="text-danger">*</span></label>
                        <input type="text" id="entity_type_name" name="name" class="form-control"
                            required="" autocomplete="off" style="    padding: 2px 5px;">

                        <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                            <button type="button" id="saveEntityType" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '#saveEntityType', function() {
                var $btn = $(this);
                var $input = $('#entity_type_name');
                var val = ($input.val() || '').trim();

                // clear previous validation
                $input.removeClass('is-invalid');
                if ($input.next('.invalid-feedback').length === 0) {
                    $input.after('<div class="invalid-feedback d-block" style="display:none"></div>');
                }
                $input.next('.invalid-feedback').hide().text('');

                if (!val) {
                    $input.addClass('is-invalid');
                    $input.next('.invalid-feedback').show().text('Entity Type name is required');
                    return;
                }

                $.ajax({
                    url: "{{ url('entity-type-store-ajax') }}",
                    type: 'POST',
                    data: {
                        name: val,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                        $btn.append(
                            '<span class="spinner-border spinner-border-sm ms-2" id="entityTypeLoader" role="status" aria-hidden="true"></span>'
                        );
                    },
                    success: function(res) {
                        if (res && res.status) {
                            // add new option to business entity type select and select it
                            var $sel = $('select[name="business_entity_type_id"]');
                            if ($sel.length) {
                                var option = $('<option>').val(res.data.id).text(res.data.name).prop(
                                    'selected', true);
                                $sel.append(option);
                                if ($sel.hasClass('js-example-basic-single')) {
                                    $sel.trigger('change.select2');
                                } else {
                                    $sel.trigger('change');
                                }
                            }

                            // Close modal and clear input
                            $('#entityTypeAddModal').modal('hide');
                            $input.val('');

                            toastr.success(res.message || 'Added', 'Success');
                        } else {
                            toastr.error(res.message || 'Operation failed', 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            if (errors.name) {
                                $input.addClass('is-invalid');
                                $input.next('.invalid-feedback').show().text(errors.name[0]);
                            }
                        } else {
                            toastr.error('Something went wrong', 'Error');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('#entityTypeLoader').remove();
                    }
                });
            });
        </script>

        {{-- Modal PO --}}




        <!-- Document Modal -->
        <div class="modal fade" id="ownerdocumentModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="documentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ownerdocumentModalLabel">Add Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Document Form -->
                        <form id="documentForm">

                            <div class="row gy-2">
                                <div class="col-md-3">
                                    <label for="owner_document_name" class="form-label mb-1">Document Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="owner_document_name" name="document_name" placeholder="">
                                </div>
                                <div class="col-md-3">
                                    <label for="owner_document_number" class="form-label mb-1">Document No <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="owner_document_number" name="document_number" placeholder="">
                                </div>
                                <div class="col-md-2">
                                    <label for="owner_document_date" class="form-label mb-1">Issue Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="owner_document_date" name="document_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="owner_expiry_date" class="form-label mb-1">Expiry Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="owner_expiry_date" name="expiry_date">
                                </div>
                                <div class="col-md-2" id="owner_document_attachment_wrap">
                                    <label for="owner_document_attachment" class="form-label mb-1">Attachment</label>
                                    <input type="file" class="form-control form-control-sm"
                                        id="owner_document_attachment" name="attachment"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                            </div>
                        </form>

                        <!-- Add Document Button -->
                        <div class="mt-3 mb-4 text-center">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                                id="saveDocumentBtnOwner" onclick="saveDocumentOwner()">
                                <span class="spinner-border spinner-border-sm d-none" id="documentLoader"></span>
                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                <span>Save</span>
                            </button>
                        </div>

                        <!-- Document List -->
                        <div class="table-responsive">
                            <table class="table table-hover data-table">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th class="text-center">Document Number</th>
                                        <th class="text-center">Issue Date</th>
                                        <th class="text-center">Expiry Date</th>
                                        <th class="text-center">Attachment</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="ownerDocumentList">
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No documents added yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sponsor Document Modal (separate from Owner modal) -->
        <div class="modal fade" id="sponsordocumentModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="sponsordocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sponsordocumentModalLabel">Add Document (Sponsor)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Sponsor Document Form -->
                        <form id="sponsorDocumentForm">
                            <div class="row gy-2">
                                <div class="col-md-3">
                                    <label for="sponsor_document_name" class="form-label mb-1">Document Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="sponsor_document_name" name="document_name" placeholder="">
                                </div>
                                <div class="col-md-3">
                                    <label for="sponsor_document_number" class="form-label mb-1">Document No <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="sponsor_document_number" name="document_number" placeholder="">
                                </div>
                                <div class="col-md-2">
                                    <label for="sponsor_document_date" class="form-label mb-1">Issue Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="sponsor_document_date" name="document_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="sponsor_expiry_date" class="form-label mb-1">Expiry Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="sponsor_expiry_date" name="expiry_date">
                                </div>
                                <div class="col-md-2" id="sponsor_document_attachment_wrap">
                                    <label for="sponsor_document_attachment" class="form-label mb-1">Attachment</label>
                                    <input type="file" class="form-control form-control-sm"
                                        id="sponsor_document_attachment" name="attachment"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                            </div>
                        </form>

                        <!-- Add Document Button -->
                        <div class="mt-3 mb-4 text-center">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                                id="saveDocumentBtnSponsor" onclick="saveDocumentSponsor()">
                                <span class="spinner-border spinner-border-sm d-none" id="sponsorDocumentLoader"></span>
                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                <span>Save</span>
                            </button>
                        </div>

                        <!-- Sponsor Document List -->
                        <div class="table-responsive">
                            <table class="table table-hover data-table">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th class="text-center">Document Number</th>
                                        <th class="text-center">Issue Date</th>
                                        <th class="text-center">Expiry Date</th>
                                        <th class="text-center">Attachment</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sponsorDocumentList">
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No documents added yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Document Modal (separate from Owner/Sponsor modals) -->
        <div class="modal fade" id="contactdocumentModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="contactdocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactdocumentModalLabel">Add Document (Contact)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Contact Document Form -->
                        <form id="contactDocumentForm">
                            <div class="row gy-2">
                                <div class="col-md-3">
                                    <label for="contact_document_name" class="form-label mb-1">Document Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="contact_document_name" name="document_name" placeholder="">
                                </div>
                                <div class="col-md-3">
                                    <label for="contact_document_number" class="form-label mb-1">Document No <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="contact_document_number" name="document_number" placeholder="">
                                </div>
                                <div class="col-md-2">
                                    <label for="contact_document_date" class="form-label mb-1">Issue Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="contact_document_date" name="document_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="contact_expiry_date" class="form-label mb-1">Expiry Date</label>
                                    <input type="date" class="form-control form-control-sm date-picker"
                                        id="contact_expiry_date" name="expiry_date">
                                </div>
                                <div class="col-md-2" id="contact_document_attachment_wrap">
                                    <label for="contact_document_attachment" class="form-label mb-1">Attachment</label>
                                    <input type="file" class="form-control form-control-sm"
                                        id="contact_document_attachment" name="attachment"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                            </div>
                        </form>

                        <!-- Add Document Button -->
                        <div class="mt-3 mb-4 text-center">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                                id="saveDocumentBtnContact" onclick="saveDocumentContact()">
                                <span class="spinner-border spinner-border-sm d-none" id="contactDocumentLoader"></span>
                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                <span>Save</span>
                            </button>
                        </div>

                        <!-- Contact Document List -->
                        <div class="table-responsive">
                            <table class="table table-hover data-table">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th class="text-center">Document Number</th>
                                        <th class="text-center">Issue Date</th>
                                        <th class="text-center">Expiry Date</th>
                                        <th class="text-center">Attachment</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="contactDocumentList">
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No documents added yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Compliance Document Modal for Non-UAE Countries -->
        <div class="modal fade" id="complianceDocumentModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="contactdocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="complianceDocumentModalLabel">Add Compliance Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="complianceDocumentForm">
                            <div class="row gy-3">
                                <div class="col-md-3">
                                    <label for="compliance_document_number" class="form-label">Document Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="compliance_document_number" name="compliance_document_number">
                                </div>
                                <div class="col-md-3">
                                    <label for="compliance_issue_date" class="form-label">Issue Date</label>
                                    <input type="text" class="form-control form-control-sm date-picker"
                                        id="compliance_issue_date" name="compliance_issue_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="compliance_expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control form-control-sm date-picker"
                                        id="compliance_expiry_date" name="compliance_expiry_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="compliance_issuing_authority" class="form-label">Issuing Authority <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="compliance_issuing_authority" name="compliance_issuing_authority">
                                </div>
                                <div class="col-md-2" id="compliance_attachment_wrap">
                                    <label for="compliance_attachment" class="form-label">Attachment</label>
                                    <input type="file" class="form-control form-control-sm"
                                        id="compliance_attachment" name="compliance_attachment"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                            id="saveComplianceDocumentBtn" onclick="saveComplianceDocument()">
                            <span class="spinner-border spinner-border-sm d-none" id="complianceLoader"></span>
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- =================== MODAL (NO FORM TAG) =================== -->
        <div class="modal fade" data-bs-backdrop="false" id="bankModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Add Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div id="bankForm"> <!-- ✔ REPLACED FORM -->
                        @csrf

                        <div class="modal-body">

                               <div class="row gy-2">


                            <input type="hidden" name="company_id" id="bank_company_id">
                            <input type="hidden" name="bank_id" id="bank_id">

                            <div class="col-6 mb-2">
                                <label>Bank Name *</label>
                                <input type="text" id="bank_name" name="bank_name"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>Branch</label>
                                <input type="text" id="branch_name" name="branch_name"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>Account Number *</label>
                                <input type="text" id="account_number" name="account_number"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>IBAN *</label>
                                <input type="text" id="iban_number" name="iban_number"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>SWIFT Code</label>
                                <input type="text" id="swift_code" name="swift_code"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>Finance Code</label>
                                <input type="text" id="finance_code" name="finance_code"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-6 mb-2">
                                <label>Currency</label>
                                {{-- <input type="text" id="currency" name="currency"
                                    class="form-control form-control-sm"> --}}
                                <select name="currency" id="currency"
                                                                            class="form-select form-select-sm  js-example-basic-single">
                                                                            
                                                                            @foreach ($currency as $c)
                                                                                <option value="{{ $c->id }}">
                                                                                  
                                                                                    {{ $c->code }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                            </div>

                            <div class="col-6 mb-2">
                                <label>Bank Letter</label>
                                <div id="bank_letter_wrap">
                                    <input type="file" id="bank_letter" name="bank_letter"
                                        class="form-control form-control-sm"  accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                                {{-- <small class="form-text text-muted" id="bank_letter_hint"></small> --}}
                            </div>
                               </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" id="bankSaveBtn"
                                class="btn btn-light d-inline-flex align-items-center gap-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                Save
                            </button>
                        </div>

                    </div> <!-- END #bankForm -->
                </div>
            </div>
        </div>





        <!-- Compliance Document Edit Modal (separate) -->
        <div class="modal fade" id="complianceDocumentEditModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="complianceDocumentEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="complianceDocumentEditModalLabel">Edit Compliance Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="complianceDocumentEditForm">
                            <div class="row gy-3">
                                <div class="col-md-3">
                                    <label for="compliance_document_number_edit" class="form-label">Document Number
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="compliance_document_number_edit" name="compliance_document_number_edit">
                                </div>
                                <div class="col-md-3">
                                    <label for="compliance_issue_date_edit" class="form-label">Issue Date</label>
                                    <input type="text" class="form-control form-control-sm date-picker"
                                        id="compliance_issue_date_edit" name="compliance_issue_date_edit">
                                </div>
                                <div class="col-md-2">
                                    <label for="compliance_expiry_date_edit" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control form-control-sm date-picker"
                                        id="compliance_expiry_date_edit" name="compliance_expiry_date_edit">
                                </div>
                                <div class="col-md-2">
                                    <label for="compliance_issuing_authority_edit" class="form-label">Issuing Authority
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm"
                                        id="compliance_issuing_authority_edit" name="compliance_issuing_authority_edit">
                                </div>
                                <div class="col-md-2" id="compliance_attachment_edit_wrap">
                                    <label for="compliance_attachment_edit" class="form-label">Attachment</label>
                                    <input type="file" class="form-control form-control-sm"
                                        id="compliance_attachment_edit" name="compliance_attachment_edit"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                            id="updateComplianceDocumentBtn" onclick="updateComplianceDocument()">
                            <span class="spinner-border spinner-border-sm d-none" id="complianceEditLoader"></span>
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        {{-- WAREHOUSE MODAL --}}
        <div class="modal fade" data-bs-backdrop="false" id="warehouseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form id="warehouseForm" enctype="multipart/form-data" novalidate>
                    @csrf

                    <input type="hidden" name="warehouse_id" id="warehouse_id">
                    <input type="hidden" name="company_id" id="warehouse_company_id">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Warehouse</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body row gy-2">

                            {{-- BASIC WAREHOUSE INFO --}}
                            {{-- <div class="col-lg-4">
                                <label>Warehouse Code</label>
                                <input type="text" name="warehouse_code" id="warehouse_code"
                                    class="form-control form-control-sm warehouse-input">
                            </div> --}}

                            <div class="col-lg-4">
                                <label>Warehouse Name</label>
                                <input type="text" name="warehouse_name" id="warehouse_name"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-2">
                                @php
                                     $staffs_list = @App\SmStaff::where('active_status', 1)->get();
                                @endphp
                                  <label>Contact Person</label>
                                <select name="contact_person_name" id="contact_person_name"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    @foreach ($staffs_list as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->first_name }} {{ $staff->last_name }}</option>
                                    @endforeach
                                    
                                    
                                </select>
                            </div>


                            <div class="col-lg-2">
                                <label>Country</label>
                                <select name="warehouse_country" id="warehouse_country"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    <option value="">Select Country</option>
                                    @foreach ($country ?? [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>State</label>
                                <select name="warehouse_state" id="warehouse_state"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    <option value="">Select State</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>City</label>
                                <input type="text" name="warehouse_city" id="warehouse_city"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Area</label>
                                <input type="text" name="warehouse_area" id="warehouse_area"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Building Name</label>
                                <input type="text" name="warehouse_building_name" id="warehouse_building_name"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Flat / Office No</label>
                                <input type="text" name="warehouse_flat_office_no" id="warehouse_flat_office_no"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            {{-- CONTACT PERSON DETAILS --}}
                            {{-- <div class="col-lg-2">
                                <label>Salutation</label>
                                <select name="contact_salutation" id="contact_salutation"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    <option value="">Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Dr">Dr</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>First Name</label>
                                <input type="text" name="contact_first_name" id="contact_first_name"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Last Name</label>
                                <input type="text" name="contact_last_name" id="contact_last_name"
                                    class="form-control form-control-sm warehouse-input">
                            </div> --}}

                            

                            <div class="col-lg-2">
                                <label>Mobile</label>
                                <input type="tel" name="contact_mobile" id="contact_mobile" placeholder="+"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Email</label>
                                <input type="email" name="contact_email" id="contact_email"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Designation</label>
                                <input type="text" name="contact_designation" id="contact_designation"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            {{-- SAFETY & COMPLIANCE CONFIRMATION --}}
                            <div class="col-lg-2">
                                <label style="font-size:12px">Fire/Safety Status</label>
                                <select name="fire_safety_compliance_status" id="fire_safety_compliance_status"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    <option value="">Select Status</option>
                                    <option value="compliant">Compliant</option>
                                    <option value="non_compliant">Non-Compliant</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label style="font-size:12px">Fire NOC / Cert No</label>
                                <input type="text" name="fire_noc_certificate_number"
                                    id="fire_noc_certificate_number"
                                    class="form-control form-control-sm warehouse-input">
                            </div>

                            <div class="col-lg-2">
                                <label>Safety Equipment</label>
                                <select name="safety_equipment_available" id="safety_equipment_available"
                                    class="form-select form-select-sm warehouse-input js-example-basic-single">
                                    <option value="">Select Status</option>
                                    <option value="yes">Yes Available</option>
                                    <option value="no">Not Available</option>
                                    <option value="partial">Partial Available</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>Fire NOC Expiry</label>
                                <input type="text" name="fire_noc_expiry_date" id="fire_noc_expiry_date"
                                    class="form-control form-control-sm warehouse-input date-picker">
                            </div>

                            <div class="col-lg-2">
                                <label>Last Safety Insp</label>
                                <input type="text" name="last_safety_inspection_date"
                                    id="last_safety_inspection_date"
                                    class="form-control form-control-sm warehouse-input date-picker">
                            </div>

                            <div class="col-lg-2">
                                <label>Documents</label>
                                <div id="contact_documents_wrap">
                                    <input type="file" name="contact_documents[0]" id="contact_documents"
                                        class="form-control form-control-sm warehouse-input" multiple>
                                </div>
                                {{-- <small class="form-text text-muted" id="warehouse_docs_hint"></small> --}}
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm"
                                id="saveWarehouseBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="warehouseLoader"></span>
                                Save Warehouse
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>







        {{-- =================== MODAL =================== --}}
        <div class="modal fade" data-bs-backdrop="false" id="policyModal">
            <div class="modal-dialog modal-lg">
                <form id="policyForm" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="policy_id" id="policy_id">
                    <input type="hidden" name="company_id" id="policy_company_id">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="policyModalTitle" class="modal-title">Add Policy</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body row gy-2">

                            <div class="col-lg-2">
                                <label>Date *</label>
                                <input type="text" name="policy_date"
                                    class="form-control form-control-sm policy-input date-picker">
                            </div>

                            <div class="col-lg-4">
                                <label>Policy Name *</label>
                                <input type="text" name="policy_name"
                                    class="form-control form-control-sm policy-input">
                            </div>

                            {{-- <div class="col-lg-3">
                        <label>Category</label>
                        <select name="policy_category" class="form-select form-select-sm policy-input">
                            <option value="">Select</option>
                            <option value="health">Health</option>
                            <option value="life">Life</option>
                            <option value="vehicle">Vehicle</option>
                        </select>
                    </div> --}}

                            <div class="col-lg-2">
                                <label>Valid Till</label>
                                <input type="text" name="policy_valid"
                                    class="form-control form-control-sm policy-input date-picker">
                            </div>

                            <div class="col-lg-2">
                                <label>View to All *</label>
                                <select name="view_to_employees"
                                    class="form-select form-select-sm policy-input js-example-basic-single">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>Policy File *</label>
                                <div id="policy_file_wrap">
                                    <input type="file" id="policy_file" name="policy_file"
                                        class="form-control form-control-sm policy-input"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                {{-- <div id="policy_file_hint" class="mt-1 text-muted"></div> --}}
                            </div>

                            <div class="col-lg-12">
                                <label>Details</label>
                                <!-- Simple Text Editor Toolbar -->
                                <div class="btn-toolbar mb-1" id="policyEditorToolbar">
                                    <div class="btn-group btn-group-sm me-1">
                                        <button type="button" class="btn btn-light" data-cmd="bold"
                                            title="Bold"><b>B</b></button>
                                        <button type="button" class="btn btn-light" data-cmd="italic"
                                            title="Italic"><i>I</i></button>
                                        <button type="button" class="btn btn-light" data-cmd="underline"
                                            title="Underline"><u>U</u></button>
                                    </div>
                                    <div class="btn-group btn-group-sm me-1">
                                        <button type="button" class="btn btn-light" data-cmd="insertUnorderedList"
                                            title="Bullet List">• List</button>
                                        <button type="button" class="btn btn-light" data-cmd="insertOrderedList"
                                            title="Numbered List">1. List</button>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-light" data-cmd="undo"
                                            title="Undo">↩</button>
                                        <button type="button" class="btn btn-light" data-cmd="redo"
                                            title="Redo">↪</button>
                                    </div>
                                </div>
                                <!-- Editable Content Area -->
                                <div id="policyDetailsEditor" contenteditable="true" class="form-control"
                                    style="min-height: 120px; max-height: 200px; overflow-y: auto;"></div>
                                <!-- Hidden textarea for form submission -->
                                <textarea name="policy_details" id="policyDetailsHidden" class="d-none"></textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm"
                                id="savePolicyBtn">Save</button>
                        </div>
                    </div>
                </form>
            </div>













            <script>
                (function() {
                    var currentOwnerForDoc = null; // index of owner for which modal is open

                    function updateCompanyType() {
                        var val = $('#company_type').val();

                        if (val === 'parent') {
                            $('#parentNameBox').removeClass('d-none').show();
                            $('#parentDropdownBox').addClass('d-none').hide();
                            $('#parent_company_name').prop('required', true);
                            $('#parent_company_id').prop('required', false);
                            // mirror company name into parent company name in UPPERCASE
                            try {
                                $('#parent_company_name').val(($('#company_name').val() || '').toUpperCase());
                            } catch (e) {}
                        } else if (val === 'subsidiary' || val === 'branch' || val === 'sub_branch') {
                            $('#parentNameBox').addClass('d-none').hide();
                            $('#parentDropdownBox').removeClass('d-none').show();
                            $('#parent_company_name').prop('required', false);
                            $('#parent_company_id').prop('required', true);
                        } else {
                            $('#parentNameBox').addClass('d-none').hide();
                            $('#parentDropdownBox').addClass('d-none').hide();
                            $('#parent_company_name').prop('required', false);
                            $('#parent_company_id').prop('required', false);
                        }
                    }

                    function refreshOwnerButtons() {
                        var $rows = $('#ownerWrapper .ownerRow');
                        $rows.each(function(index) {
                            var $row = $(this);
                            var $btnContainer = $row.find('.d-flex.gap-1.mt-4');

                            // Remove existing add/remove buttons and re-add in the desired order: Add (+) then Remove (−)
                            $btnContainer.find('.addOwner, .removeOwner').remove();

                            // Add button goes first
                            if (index === $rows.length - 1) {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm addOwner" title="Add" aria-label="Add owner"><i class="ico icon-outline-add-square"></i></button>'
                                );
                            }

                            // Remove button comes after Add, but hide if only one row
                            if ($rows.length === 1) {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm removeOwner d-none" title="Remove" aria-label="Remove owner"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            } else {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm removeOwner" title="Remove" aria-label="Remove owner"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            }
                        });
                    }

                    function normalizeClone($clone, index) {
                        // Remove any select2-generated DOM that got cloned
                        $clone.find('.select2-container, .select2').remove();

                        // Clear inputs (text/number/email/date/etc.) but do NOT preserve file inputs
                        $clone.find('input').each(function() {
                            // skip file inputs here - they'll be handled separately
                            if ($(this).attr('type') === 'file') return;
                            $(this).val('');
                        });

                        // Remove any cloned owner document containers (so documents are not copied)
                        $clone.find('.owner-documents-container, .owner-doc-entry, .owner-doc-list, .doc-row, .file-holder')
                            .remove();
                        // remove any leftover data-file-id attributes
                        $clone.find('[data-file-id]').removeAttr('data-file-id');

                        // Replace any file inputs with a fresh empty file input (prevents cloning selected FileList)
                        $clone.find('input[type="file"]').each(function() {
                            var accept = $(this).attr('accept');
                            var $new = $('<input type="file" class="form-control form-control-sm">');
                            if (accept) $new.attr('accept', accept);
                            $(this).replaceWith($new);
                        });

                        // Reset selects and sanitize attributes from select2 cloning
                        $clone.find('select').each(function() {
                            $(this).val('');
                            $(this).removeClass('select2-hidden-accessible');
                            $(this).removeAttr('aria-hidden tabindex data-select2-id');

                            // ensure unique ids to avoid collisions
                            if ($(this).attr('id')) {
                                $(this).attr('id', $(this).attr('id') + '_' + index);
                            }
                        });

                        // Re-init select2 on cloned selects cleanly
                        if ($.fn.select2) {
                            $clone.find('select.js-example-basic-single').each(function() {
                                // in case there's a leftover select2 instance, try destroying safely
                                try {
                                    if ($(this).hasClass('select2-hidden-accessible')) {
                                        $(this).select2('destroy');
                                    }
                                } catch (e) {
                                    // ignore
                                }

                                $(this).select2({
                                    width: '100%'
                                });
                            });
                        }
                    }

                    // ---------- Owner share helpers (client-side only) ----------
                    function getOwnerInputs() {
                        return $('#ownerWrapper input[name$="[share_percentage]"]');
                    }

                    function calculateOwnerShares() {
                        var total = 0;
                        getOwnerInputs().each(function() {
                            var v = parseFloat($(this).val());
                            if (!isNaN(v)) total += v;
                        });
                        total = Math.round(total * 100) / 100;
                        var remaining = Math.round((100 - total) * 100) / 100;

                        $('#ownersShareSummary').text('Total shares: ' + total + '% — Remaining: ' + (remaining < 0 ? 0 :
                            remaining) + '%');

                        if (total > 100) {
                            $('#ownersShareError').removeClass('d-none');
                            $('#btnSaveAll').prop('disabled', true);
                            getOwnerInputs().addClass('is-invalid');
                            $('.addOwner').prop('disabled', true);
                            return false;
                        } else {
                            $('#ownersShareError').addClass('d-none');
                            $('#btnSaveAll').prop('disabled', false);
                            getOwnerInputs().removeClass('is-invalid');
                            $('.addOwner').prop('disabled', false);
                        }

                        // update per-input max so a user cannot increase an input beyond the remaining amount
                        getOwnerInputs().each(function() {
                            var $this = $(this);
                            var cur = parseFloat($this.val()) || 0;
                            var maxAllowed = Math.round((100 - (total - cur)) * 100) / 100;
                            if (maxAllowed < 0) maxAllowed = 0;
                            $this.attr('max', maxAllowed);
                            $this.attr('placeholder', 'Max: ' + maxAllowed);
                        });

                        return true;
                    }

                    // Enforce per-input constraints while typing
                    $(document).off('input change', 'input[name$="[share_percentage]"]').on('input change',
                        'input[name$="[share_percentage]"]',
                        function(e) {
                            var $this = $(this);
                            var val = parseFloat($this.val());
                            if (isNaN(val) || val < 0) {
                                $this.val('');
                                calculateOwnerShares();
                                return;
                            }
                            var max = parseFloat($this.attr('max'));
                            if (!isNaN(max) && val > max) {
                                $this.val(max);
                            }
                            calculateOwnerShares();
                        });

                    // ---------- end helpers ----------

                    $(document).off('click', '.addOwner').on('click', '.addOwner', function(e) {
                        // Prevent adding if shares already full
                        var $inputs = getOwnerInputs();
                        var total = 0;
                        $inputs.each(function() {
                            var v = parseFloat($(this).val());
                            if (!isNaN(v)) total += v;
                        });
                        var remaining = Math.round((100 - total) * 100) / 100;
                        if (remaining <= 0) {
                            $('#ownersShareError').removeClass('d-none').text(
                                'Cannot add owner — total shares already 100%');
                            return;
                        }

                        var $last = $('#ownerWrapper .ownerRow').last();
                        var $clone = $last.clone(false);
                        var newIndex = $('#ownerWrapper .ownerRow').length;

                        normalizeClone($clone, newIndex);

                        // Ensure share field in cloned row respects remaining availability
                        $clone.find('input[name$="[share_percentage]"]').each(function() {
                            $(this).val('');
                            $(this).attr('max', remaining);
                            $(this).attr('placeholder', 'Max: ' + remaining);
                        });

                        // Apply country dial code to the mobile input in cloned row if available
                        var dial = '';
                        if (typeof window.getCountryDialCode === 'function') {
                            dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
                        }
                        $clone.find('input[name$="[mobile]"]').each(function() {

                            var cur = ($(this).val() || '').replace(/^\+\d+\s*/, '');

                            if (dial) {
                                // ALWAYS "+CODE␠"
                                $(this).val('+' + dial + ' ' + cur);
                            } else {
                                $(this).val(cur);
                            }

                        });


                        $('#ownerWrapper').append($clone);
                        refreshOwnerButtons();
                        reindexOwnerRows();
                        calculateOwnerShares();
                        // focus first input of new row
                        $('#ownerWrapper .ownerRow').last().find('input, select').filter(':visible').first().focus();
                    });

                    $(document).off('click', '.removeOwner').on('click', '.removeOwner', function(e) {
                        var $row = $(this).closest('.ownerRow');
                        var total = $('#ownerWrapper .ownerRow').length;

                        if (total === 1) {
                            // If only one row remains, just clear it instead of removing
                            $row.find('input').val('');
                            // clear selects and update select2 UI if present
                            $row.find('select').each(function() {
                                $(this).val('');
                                if ($(this).hasClass('select2-hidden-accessible')) {
                                    try {
                                        $(this).trigger('change');
                                    } catch (e) {}
                                }
                            });
                            // remove any owner documents that may have existed on this row
                            $row.find('.owner-documents-container').remove();
                            reindexOwnerRows();
                            calculateOwnerShares();
                            return;
                        }

                        $row.remove();
                        refreshOwnerButtons();
                        reindexOwnerRows();
                        calculateOwnerShares();
                    });

                    $(document).ready(function() {
                        updateCompanyType();
                        $('#company_type').on('change', updateCompanyType);

                        // Initialize select2 on any existing selects
                        if ($.fn.select2) {
                            $('#ownerWrapper select.js-example-basic-single').each(function() {
                                if (!$(this).hasClass('select2-hidden-accessible')) {
                                    $(this).select2({
                                        width: '100%'
                                    });
                                }
                            });
                        }

                        // Run initial buttons setup
                        refreshOwnerButtons();
                        // Ensure sponsor rows are indexed and buttons set
                        reindexSponsorRows();
                        refreshSponsorButtons();
                        // Ensure contact rows are indexed and buttons set
                        if (typeof reindexContactRows === 'function') reindexContactRows();
                        if (typeof refreshContactButtonsContact === 'function') refreshContactButtonsContact();

                        // Auto-uppercase company name and sync trade name & parent company name
                        $(document).on('input keyup', '#company_name', function() {
                            var name = ($(this).val() || '').toUpperCase();
                            $(this).val(name);
                            // update trade name
                            $("input[name='trade_name']").val(name);
                            // if parent type selected, keep parent_company_name in sync
                            if ($('#company_type').val() === 'parent') {
                                $('#parent_company_name').val(name);
                            }
                        });

                        // Keep trade_name uppercase if typed manually
                        $(document).on('input keyup', "input[name='trade_name']", function() {
                            $(this).val(($(this).val() || '').toUpperCase());
                        });

                        // UAE / Non-UAE compliance toggle: show UAE section when country_company == 231
                        function updateUaeComplianceSections() {
                            var cid = $('#country_company').val();
                            if (String(cid) === '231') {
                                $('#uae-compliance-section').removeClass('d-none').show();
                                $('#non-uae-compliance-section').addClass('d-none').hide();

                                $('#uae-documents-section').removeClass('d-none').show();
                                $('#non-uae-documents-section').addClass('d-none').hide();

                            } else {
                                $('#uae-compliance-section').addClass('d-none').hide();
                                $('#non-uae-compliance-section').removeClass('d-none').show();

                                $('#uae-documents-section').addClass('d-none').hide();
                                $('#non-uae-documents-section').removeClass('d-none').show();
                            }
                        }

                        // wire up change handler and run once on load
                        $(document).on('change', '#country_company', updateUaeComplianceSections);
                        updateUaeComplianceSections();

                        // Tax applicable toggle: show/hide VAT and CT sections
                        function updateTaxSections() {
                            var v = $('#tax_applicable').val();
                            if (v === 'vat') {
                                $('.vat-section').removeClass('d-none').show();
                                $('.ct-section').addClass('d-none').hide();
                            } else if (v === 'ct') {
                                $('.vat-section').addClass('d-none').hide();
                                $('.ct-section').removeClass('d-none').show();
                            } else if (v === 'both') {
                                $('.vat-section').removeClass('d-none').show();
                                $('.ct-section').removeClass('d-none').show();
                            } else {
                                $('.vat-section').addClass('d-none').hide();
                                $('.ct-section').addClass('d-none').hide();
                            }
                        }
                        $(document).on('change', '#tax_applicable', updateTaxSections);
                        updateTaxSections();

                        // Initialize share totals and constraints
                        calculateOwnerShares();
                    });

                    // ---------- Owner document modal helpers ----------
                    function reindexOwnerRows() {
                        $('#ownerWrapper .ownerRow').each(function(i) {
                            var $row = $(this);
                            $row.attr('data-owner-index', i);

                            // rename owner field names and ids (owners[][field] or owners[<num>][field])
                            $row.find('input, select, textarea').each(function() {
                                var $el = $(this);
                                var name = $el.attr('name');
                                if (name) {
                                    // owners[][field] OR owners[123][field]
                                    var newName = name.replace(/^owners\[\d*\]\[([^\]]+)\]/, 'owners[' + i +
                                        '][$1]');
                                    newName = newName.replace(/^owners\[\]\[([^\]]+)\]/, 'owners[' + i +
                                        '][$1]');
                                    $el.attr('name', newName);
                                }

                                var id = $el.attr('id');
                                if (id) {
                                    var base = id.replace(/_\d+$/, '');
                                    $el.attr('id', base + '_' + i);
                                }
                            });

                            // update owner documents container ids and lists
                            var $docContainer = $row.find('.owner-documents-container');
                            if ($docContainer.length) {
                                $docContainer.attr('id', 'owner-documents-' + i);
                                $docContainer.find('.owner-doc-list').each(function(j, el) {
                                    $(el).removeClass().addClass('owner-doc-list-' + i + ' owner-doc-list');
                                });

                                // rename hidden inputs for documents
                                $docContainer.find('input, select, textarea, button, .file-holder').each(function() {
                                    var $el = $(this);
                                    var name = $el.attr('name');
                                    if (!name) return;
                                    // replace owners[<old>][documents] or owners[][documents]
                                    var newName = name.replace(/^owners\[\d+\]\[documents\]/, 'owners[' + i +
                                        '][documents]');
                                    newName = newName.replace(/^owners\[\]\[documents\]/, 'owners[' + i +
                                        '][documents]');
                                    $el.attr('name', newName);
                                });
                            }

                            // Re-init select2 on selects inside this row
                            if ($.fn.select2) {
                                $row.find('select.js-example-basic-single').each(function() {
                                    try {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).select2('destroy');
                                        }
                                    } catch (e) {
                                        // ignore destroy errors
                                    }
                                    $(this).select2({
                                        width: '100%'
                                    });
                                });
                            }
                        });
                    }

                    function ownerdocumentModal(btn) {
                        var $btn = $(btn);
                        var $row = $btn.closest('.ownerRow');
                        var ownerIndex = $row.index('#ownerWrapper .ownerRow');

                        currentOwnerForDoc = ownerIndex;

                        // set modal title
                        $('#ownerdocumentModalLabel').text('Add Document — Owner #' + (ownerIndex + 1));

                        // clear modal inputs
                        $('#owner_document_name').val('');
                        $('#owner_document_number').val('');
                        $('#owner_document_date').val('');
                        $('#owner_expiry_date').val('');

                        // ensure modal file input is fresh
                        resetModalFileInput();

                        // populate modal list with existing owner documents
                        renderOwnerDocumentsInModal(ownerIndex);

                        $('#ownerdocumentModal').modal('show');
                    }

                    function resetModalFileInput() {
                        var $fileWrap = $('#owner_document_attachment_wrap');
                        $fileWrap.html(
                            '<label for="owner_document_attachment" class="form-label mb-1">Attachment</label><input type="file" class="form-control form-control-sm" id="owner_document_attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">'
                        );
                    }

                    function resetSponsorFileInput() {
                        var $fileWrap = $('#sponsor_document_attachment_wrap');
                        $fileWrap.html(
                            '<label for="sponsor_document_attachment" class="form-label mb-1">Attachment</label><input type="file" class="form-control form-control-sm" id="sponsor_document_attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">'
                        );
                    }

                    function renderOwnerDocumentsInModal(ownerIndex) {
                        var $list = $('#ownerDocumentList');
                        $list.empty();

                        var $row = $('#ownerWrapper .ownerRow').eq(ownerIndex);
                        var $docContainer = $row.find('.owner-documents-container');
                        if (!$docContainer.length) {
                            $list.append(
                                '<tr><td colspan="6" class="text-muted text-center">No documents added yet.</td></tr>');
                            return;
                        }

                        $docContainer.find('.owner-doc-entry').each(function(j) {
                            var $entry = $(this);
                            var name = $entry.find('input[name$="[name]"]').val();
                            var number = $entry.find('input[name$="[number]"]').val();
                            var issue = $entry.find('input[name$="[issue_date]"]').val();
                            var expiry = $entry.find('input[name$="[expiry_date]"]').val();
                            var fid = $entry.data('file-id');

                            // show the original filename of the attachment (if present)
                            var attachCell = '';
                            var fileEl = $entry.find('input[type="file"]')[0];
                            if (fileEl && fileEl.files && fileEl.files.length > 0) {
                                var filename = fileEl.files[0].name || '';
                                attachCell = '<span>' + $('<div/>').text(filename).html() + '</span>';
                            }

                            var row = '<tr data-doc-id="' + fid + '" data-doc-index="' + j + '">' +
                                '<td>' + (name || '') + '</td>' +
                                '<td class="text-center">' + (number || '') + '</td>' +
                                '<td class="text-center">' + (issue || '') + '</td>' +
                                '<td class="text-center">' + (expiry || '') + '</td>' +
                                '<td class="text-center">' + attachCell + '</td>' +
                                '<td class="text-center d-flex justify-content-center"><button type="button" class="btn btn-sm btn-light" onclick="removeOwnerDocumentByFileId(' +
                                fid +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button></td>' +
                                '</tr>';
                            $list.append(row);
                        });
                    }

                    function previewOwnerAttachmentByFileId(fileId) {
                        var $entry = $('.owner-doc-entry[data-file-id="' + fileId + '"]');
                        if (!$entry.length) {
                            alert('No attachment available');
                            return;
                        }
                        var fileEl = $entry.find('input[type="file"]')[0];
                        if (!fileEl || !fileEl.files || !fileEl.files.length) {
                            alert('No attachment available');
                            return;
                        }
                        var file = fileEl.files[0];
                        var url = URL.createObjectURL(file);
                        window.open(url, '_blank');
                    }

                    function saveDocumentOwner() {
                        var ownerIndex = currentOwnerForDoc;
                        if (ownerIndex === null) return;

                        var name = $('#owner_document_name').val().trim();
                        var number = $('#owner_document_number').val().trim();
                        var issue = $('#owner_document_date').val();
                        var expiry = $('#owner_expiry_date').val();
                        var $fileInput = $('#owner_document_attachment');

                        if (!name) {
                            alert('Document name is required');
                            return;
                        }

                        var $ownerRow = $('#ownerWrapper .ownerRow').eq(ownerIndex);
                        var $docContainer = $ownerRow.find('.owner-documents-container');
                        if (!$docContainer.length) {
                            // create hidden container (documents stored but not visible on the page)
                            $ownerRow.append('<div id="owner-documents-' + ownerIndex +
                                '" class="mt-2 d-none owner-documents-container" style="display:none;"><small class="text-muted d-none">Added Documents:</small><div class="owner-doc-list-' +
                                ownerIndex + ' owner-doc-list d-none"></div></div>');
                            $docContainer = $ownerRow.find('.owner-documents-container');
                        } else {
                            // keep container hidden — documents are shown only in the modal
                        }

                        // determine doc index
                        var docIndex = $docContainer.find('.owner-doc-entry').length;

                        // move file input from modal into the owner container (so chosen file is submitted with the form)
                        var $fileHolder = $('<div class="file-holder d-none"></div>');
                        // If a file was selected, detach the real input so the FileList is preserved.
                        if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                            $fileInput.detach();
                            $fileInput.attr('id', 'owners_' + ownerIndex + '_documents_' + docIndex + '_attachment');
                            $fileInput.attr('name', 'owners[' + ownerIndex + '][documents][' + docIndex + '][attachment]');
                            $fileHolder.append($fileInput);
                        } else {
                            // No file selected - insert a hidden placeholder to keep form structure predictable
                            $fileHolder.append('<input type="file" id="owners_' + ownerIndex + '_documents_' + docIndex +
                                '_attachment" name="owners[' + ownerIndex + '][documents][' + docIndex +
                                '][attachment]" class="form-control d-none">');
                        }

                        // create hidden inputs for meta
                        var $entry = $('<div class="owner-doc-entry d-none" data-file-id="' + Date.now() + '"></div>');
                        $entry.append('<input type="hidden" name="owners[' + ownerIndex + '][documents][' + docIndex +
                            '][name]" value="' + $('<div/>').text(name).html() + '">');
                        $entry.append('<input type="hidden" name="owners[' + ownerIndex + '][documents][' + docIndex +
                            '][number]" value="' + $('<div/>').text(number).html() + '">');
                        $entry.append('<input type="hidden" name="owners[' + ownerIndex + '][documents][' + docIndex +
                            '][issue_date]" value="' + $('<div/>').text(issue).html() + '">');
                        $entry.append('<input type="hidden" name="owners[' + ownerIndex + '][documents][' + docIndex +
                            '][expiry_date]" value="' + $('<div/>').text(expiry).html() + '">');

                        $entry.append($fileHolder);

                        $docContainer.find('.owner-doc-list').append('<div class="doc-row p-2 border rounded mb-1 d-none">' +
                            '<div><strong>' + $('<div/>').text(name).html() + '</strong></div>' +
                            '<div class="text-muted">' + $('<div/>').text(number).html() + '</div>' +
                            '</div>');

                        $docContainer.append($entry);

                        // Ensure modal's file input is replaced so user can add another file
                        resetModalFileInput();

                        // refresh modal list
                        renderOwnerDocumentsInModal(ownerIndex);

                        // show container
                        $docContainer.show();

                        // hide modal if user prefers (keep open for multiple adds) — we'll keep open and clear form
                        $('#owner_document_name').val('');
                        $('#owner_document_number').val('');
                        $('#owner_document_date').val('');
                        $('#owner_expiry_date').val('');

                        // reindex to make sure names and indices are consistent
                        reindexOwnerRows();
                    }

                    function removeOwnerDocumentByFileId(fileId) {
                        var $entry = $('.owner-doc-entry[data-file-id="' + fileId + '"]');
                        if (!$entry.length) return;

                        var $docContainer = $entry.closest('.owner-documents-container');
                        var ownerIndex = $('#ownerWrapper .ownerRow').index($entry.closest('.ownerRow'));

                        // determine doc index within its container
                        var docIdx = $docContainer.find('.owner-doc-entry').index($entry);

                        // remove entry and visible list row
                        $entry.remove();
                        $docContainer.find('.owner-doc-list').children().eq(docIdx).remove();

                        // reindex remaining docs for that owner
                        $docContainer.find('.owner-doc-entry').each(function(j) {
                            var $e = $(this);
                            // update names
                            $e.find('input').each(function() {
                                var name = $(this).attr('name');
                                if (!name) return;
                                var newName = name.replace(/owners\[\d+\]\[documents\]\[\d+\]/, 'owners[' +
                                    ownerIndex + '][documents][' + j + ']');
                                $(this).attr('name', newName);
                            });

                            // update file input name if exists
                            $e.find('input[type="file"]').each(function() {
                                $(this).attr('name', 'owners[' + ownerIndex + '][documents][' + j +
                                    '][attachment]');
                            });
                        });

                        // refresh modal list if open
                        if ($('#ownerdocumentModal').is(':visible')) {
                            renderOwnerDocumentsInModal(ownerIndex);
                        }

                        // hide container if now empty
                        if ($docContainer.find('.owner-doc-entry').length === 0) {
                            $docContainer.hide();
                        }

                        // reindex owner rows globally (names/ids)
                        reindexOwnerRows();
                    }

                    function removeOwnerDocumentFromModal(ownerIndex, docIdx) {
                        // backward compatible wrapper that finds the fileId then delegates
                        var $ownerRow = $('#ownerWrapper .ownerRow').eq(ownerIndex);
                        var $docContainer = $ownerRow.find('.owner-documents-container');
                        if (!$docContainer.length) return;
                        var $entry = $docContainer.find('.owner-doc-entry').eq(docIdx);
                        if (!$entry.length) return;
                        var fid = $entry.data('file-id');
                        removeOwnerDocumentByFileId(fid);
                    }

                    // ensure owner rows are reindexed after add/remove
                    var origAddOwner = $(document).on; // no-op reference
                    // hook into add/remove flows by calling reindexOwnerRows inside existing handlers

                    // ---------- Sponsor row / document helpers (mirrors owner behavior) ----------
                    var currentSponsorForDoc = null; // index of sponsor for which modal is open

                    function reindexSponsorRows() {
                        $('#sponsorWrapper .sponsorRow').each(function(i) {
                            var $row = $(this);
                            $row.attr('data-sponsor-index', i);

                            // rename sponsor field names and ids
                            $row.find('input, select, textarea').each(function() {
                                var $el = $(this);
                                var name = $el.attr('name');
                                if (name) {
                                    var newName = name.replace(/^sponsors\[\d*\]\[([^\]]+)\]/, 'sponsors[' + i +
                                        '][$1]');
                                    newName = newName.replace(/^sponsors\[\]\[([^\]]+)\]/, 'sponsors[' + i +
                                        '][$1]');
                                    $el.attr('name', newName);
                                }
                                var id = $el.attr('id');
                                if (id) {
                                    var base = id.replace(/_\d+$/, '');
                                    $el.attr('id', base + '_' + i);
                                }
                            });

                            // update sponsor documents container ids and lists
                            var $docContainer = $row.find('.sponsor-documents-container');
                            if ($docContainer.length) {
                                $docContainer.attr('id', 'sponsor-documents-' + i);
                                $docContainer.find('.sponsor-doc-list').each(function(j, el) {
                                    $(el).removeClass().addClass('sponsor-doc-list-' + i + ' sponsor-doc-list');
                                });

                                // rename hidden inputs for documents
                                $docContainer.find('input, select, textarea, button, .file-holder').each(function() {
                                    var $el = $(this);
                                    var name = $el.attr('name');
                                    if (!name) return;
                                    var newName = name.replace(/^sponsors\[\d+\]\[documents\]/, 'sponsors[' +
                                        i + '][documents]');
                                    newName = newName.replace(/^sponsors\[\]\[documents\]/, 'sponsors[' + i +
                                        '][documents]');
                                    $el.attr('name', newName);
                                });
                            }

                            // Re-init select2 on selects inside this row
                            if ($.fn.select2) {
                                $row.find('select.js-example-basic-single').each(function() {
                                    try {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).select2('destroy');
                                        }
                                    } catch (e) {
                                        // ignore destroy errors
                                    }
                                    $(this).select2({
                                        width: '100%'
                                    });
                                });
                            }
                        });
                    }

                    function sponsordocumentModal(btn) {
                        var $btn = $(btn);
                        var $row = $btn.closest('.sponsorRow');
                        var sponsorIndex = $row.index('#sponsorWrapper .sponsorRow');

                        currentSponsorForDoc = sponsorIndex;

                        // set modal title
                        $('#sponsordocumentModalLabel').text('Add Document — Sponsor');

                        // clear sponsor modal inputs
                        $('#sponsor_document_name').val('');
                        $('#sponsor_document_number').val('');
                        $('#sponsor_document_date').val('');
                        $('#sponsor_expiry_date').val('');

                        // ensure sponsor modal file input is fresh
                        resetSponsorFileInput();

                        // populate modal list with existing sponsor documents
                        renderSponsorDocumentsInModal(sponsorIndex);

                        $('#sponsordocumentModal').modal('show');
                    }

                    function renderSponsorDocumentsInModal(sponsorIndex) {
                        var $list = $('#sponsorDocumentList');
                        $list.empty();

                        var $row = $('#sponsorWrapper .sponsorRow').eq(sponsorIndex);
                        var $docContainer = $row.find('.sponsor-documents-container');
                        if (!$docContainer.length) {
                            $list.append(
                                '<tr><td colspan="6" class="text-muted text-center">No documents added yet.</td></tr>');
                            return;
                        }

                        $docContainer.find('.sponsor-doc-entry').each(function(j) {
                            var $entry = $(this);
                            var name = $entry.find('input[name$="[name]"]').val();
                            var number = $entry.find('input[name$="[number]"]').val();
                            var issue = $entry.find('input[name$="[issue_date]"]').val();
                            var expiry = $entry.find('input[name$="[expiry_date]"]').val();
                            var fid = $entry.data('file-id');

                            var attachCell = '';
                            var fileEl = $entry.find('input[type="file"]')[0];
                            if (fileEl && fileEl.files && fileEl.files.length > 0) {
                                var filename = fileEl.files[0].name || '';
                                attachCell = '<span>' + $('<div/>').text(filename).html() + '</span>';
                            }

                            var row = '<tr data-doc-id="' + fid + '" data-doc-index="' + j + '">' +
                                '<td>' + (name || '') + '</td>' +
                                '<td class="text-center">' + (number || '') + '</td>' +
                                '<td class="text-center">' + (issue || '') + '</td>' +
                                '<td class="text-center">' + (expiry || '') + '</td>' +
                                '<td class="text-center">' + attachCell + '</td>' +
                                '<td class="text-center d-flex justify-content-center"><button type="button" class="btn btn-sm btn-light" onclick="removeSponsorDocumentByFileId(' +
                                fid +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button></td>' +
                                '</tr>';
                            $list.append(row);
                        });
                    }

                    function saveDocumentSponsor() {
                        var sponsorIndex = currentSponsorForDoc;
                        if (sponsorIndex === null) return;

                        var name = $('#sponsor_document_name').val().trim();
                        var number = $('#sponsor_document_number').val().trim();
                        var issue = $('#sponsor_document_date').val();
                        var expiry = $('#sponsor_expiry_date').val();
                        var $fileInput = $('#sponsor_document_attachment');

                        if (!name) {
                            alert('Document name is required');
                            return;
                        }

                        var $sponsorRow = $('#sponsorWrapper .sponsorRow').eq(sponsorIndex);
                        var $docContainer = $sponsorRow.find('.sponsor-documents-container');
                        if (!$docContainer.length) {
                            $sponsorRow.append('<div id="sponsor-documents-' + sponsorIndex +
                                '" class="mt-2 d-none sponsor-documents-container" style="display:none;"><small class="text-muted d-none">Added Documents:</small><div class="sponsor-doc-list-' +
                                sponsorIndex + ' sponsor-doc-list d-none"></div></div>');
                            $docContainer = $sponsorRow.find('.sponsor-documents-container');
                        }

                        var docIndex = $docContainer.find('.sponsor-doc-entry').length;

                        var $fileHolder = $('<div class="file-holder d-none"></div>');
                        if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                            $fileInput.detach();
                            $fileInput.attr('id', 'sponsors_' + sponsorIndex + '_documents_' + docIndex + '_attachment');
                            $fileInput.attr('name', 'sponsors[' + sponsorIndex + '][documents][' + docIndex + '][attachment]');
                            $fileHolder.append($fileInput);
                        } else {
                            $fileHolder.append('<input type="file" id="sponsors_' + sponsorIndex + '_documents_' + docIndex +
                                '_attachment" name="sponsors[' + sponsorIndex + '][documents][' + docIndex +
                                '][attachment]" class="form-control d-none">');
                        }

                        var $entry = $('<div class="sponsor-doc-entry d-none" data-file-id="' + Date.now() + '"></div>');
                        $entry.append('<input type="hidden" name="sponsors[' + sponsorIndex + '][documents][' + docIndex +
                            '][name]" value="' + $('<div/>').text(name).html() + '">');
                        $entry.append('<input type="hidden" name="sponsors[' + sponsorIndex + '][documents][' + docIndex +
                            '][number]" value="' + $('<div/>').text(number).html() + '">');
                        $entry.append('<input type="hidden" name="sponsors[' + sponsorIndex + '][documents][' + docIndex +
                            '][issue_date]" value="' + $('<div/>').text(issue).html() + '">');
                        $entry.append('<input type="hidden" name="sponsors[' + sponsorIndex + '][documents][' + docIndex +
                            '][expiry_date]" value="' + $('<div/>').text(expiry).html() + '">');
                        $entry.append($fileHolder);

                        $docContainer.find('.sponsor-doc-list').append('<div class="doc-row p-2 border rounded mb-1 d-none">' +
                            '<div><strong>' + $('<div/>').text(name).html() + '</strong></div>' +
                            '<div class="text-muted">' + $('<div/>').text(number).html() + '</div>' +
                            '</div>');

                        $docContainer.append($entry);

                        resetSponsorFileInput();

                        renderSponsorDocumentsInModal(sponsorIndex);

                        $('#sponsor_document_name').val('');
                        $('#sponsor_document_number').val('');
                        $('#sponsor_document_date').val('');
                        $('#sponsor_expiry_date').val('');

                        reindexSponsorRows();
                    }

                    function removeSponsorDocumentByFileId(fileId) {
                        var $entry = $('.sponsor-doc-entry[data-file-id="' + fileId + '"]');
                        if (!$entry.length) return;

                        var $docContainer = $entry.closest('.sponsor-documents-container');
                        var sponsorIndex = $('#sponsorWrapper .sponsorRow').index($entry.closest('.sponsorRow'));

                        var docIdx = $docContainer.find('.sponsor-doc-entry').index($entry);

                        $entry.remove();
                        $docContainer.find('.sponsor-doc-list').children().eq(docIdx).remove();

                        $docContainer.find('.sponsor-doc-entry').each(function(j) {
                            var $e = $(this);
                            $e.find('input').each(function() {
                                var name = $(this).attr('name');
                                if (!name) return;
                                var newName = name.replace(/sponsors\[\d+\]\[documents\]\[\d+\]/, 'sponsors[' +
                                    sponsorIndex + '][documents][' + j + ']');
                                $(this).attr('name', newName);
                            });
                            $e.find('input[type="file"]').each(function() {
                                $(this).attr('name', 'sponsors[' + sponsorIndex + '][documents][' + j +
                                    '][attachment]');
                            });
                        });

                        if ($('#sponsordocumentModal').is(':visible')) {
                            renderSponsorDocumentsInModal(sponsorIndex);
                        }

                        if ($docContainer.find('.sponsor-doc-entry').length === 0) {
                            $docContainer.hide();
                        }

                        reindexSponsorRows();
                    }

                    // Add/remove sponsor rows (simple clone-based implementation)
                    function refreshSponsorButtons() {
                        var $rows = $('#sponsorWrapper .sponsorRow');
                        $rows.each(function(idx) {
                            var $row = $(this);
                            var $btnContainer = $row.find('.d-flex.gap-1.mt-4');
                            $btnContainer.find('.addSponsor, .removeSponsor').remove();

                            if (idx === $rows.length - 1) {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm addSponsor"><i class="ico icon-outline-add-square"></i></button>'
                                );
                            }
                            if ($rows.length === 1) {
                                // hide remove on only row
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm removeSponsor d-none"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            } else {
                                if (idx !== 0) {
                                    $btnContainer.append(
                                        '<button type="button" class="btn btn-light btn-sm removeSponsor"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                    );
                                } else {
                                    // first row: do not show remove
                                }
                            }
                        });
                    }

                    $(document).off('click', '.addSponsor').on('click', '.addSponsor', function(e) {
                        var $last = $('#sponsorWrapper .sponsorRow').last();
                        var $clone = $last.clone(false);
                        var newIndex = $('#sponsorWrapper .sponsorRow').length;

                        // cleanup clone: remove select2 rendered DOM, clear values, remove doc bits
                        $clone.find('.select2-container, .select2').remove();
                        $clone.find('input').each(function() {
                            if ($(this).attr('type') !== 'file') $(this).val('');
                        });
                        $clone.find('select').each(function() {
                            $(this).val('');
                            $(this).removeClass('select2-hidden-accessible');
                            $(this).removeAttr('aria-hidden tabindex data-select2-id');
                        });
                        $clone.find(
                            '.sponsor-documents-container, .sponsor-doc-entry, .sponsor-doc-list, .doc-row, .file-holder'
                        ).remove();
                        $clone.find('[data-file-id]').removeAttr('data-file-id');
                        $clone.find('input[type="file"]').each(function() {
                            var accept = $(this).attr('accept');
                            var $new = $('<input type="file" class="form-control form-control-sm">');
                            if (accept) $new.attr('accept', accept);
                            $(this).replaceWith($new);
                        });

                        // re-init select2 on cloned selects
                        if ($.fn.select2) {
                            $clone.find('select.js-example-basic-single').each(function() {
                                try {
                                    if ($(this).hasClass('select2-hidden-accessible')) {
                                        $(this).select2('destroy');
                                    }
                                } catch (e) {}
                                $(this).select2({
                                    width: '100%'
                                });
                            });
                        }

                        // Apply country dial code to the mobile input in cloned sponsor row if available
                        var dial = '';
                        if (typeof window.getCountryDialCode === 'function') {
                            dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
                        }
                        $clone.find('input[name$="[mobile]"]').each(function() {

                            var cur = ($(this).val() || '').replace(/^\+\d+\s*/, '');

                            if (dial) {
                                // ALWAYS "+CODE␠"
                                $(this).val('+' + dial + ' ' + cur);
                            } else {
                                $(this).val(cur);
                            }

                        });


                        $('#sponsorWrapper').append($clone);
                        reindexSponsorRows();
                        refreshSponsorButtons();
                    });

                    $(document).off('click', '.removeSponsor').on('click', '.removeSponsor', function(e) {
                        var $row = $(this).closest('.sponsorRow');
                        var total = $('#sponsorWrapper .sponsorRow').length;
                        if (total === 1) {
                            $row.find('input').val('');
                            $row.find('select').val('');
                            $row.find('.sponsor-documents-container').remove();
                            reindexSponsorRows();
                            refreshSponsorButtons();
                            return;
                        }
                        $row.remove();
                        reindexSponsorRows();
                        refreshSponsorButtons();
                    });

                    // initialize select2 on sponsor selects on ready
                    if ($.fn.select2) {
                        $('#sponsorWrapper select.js-example-basic-single').each(function() {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2({
                                    width: '100%'
                                });
                            }
                        });
                    }

                    // ---------- Contact row / document helpers (mirrors owner/sponsor behavior) ----------
                    var currentContactForDoc = null;

                    function reindexContactRows() {
                        $('#contactWrapper .contactRow').each(function(i) {
                            var $row = $(this);
                            $row.attr('data-contact-index', i);

                            $row.find('input, select, textarea').each(function() {
                                var $el = $(this);
                                var name = $el.attr('name');
                                if (name) {
                                    var newName = name.replace(/^contacts\[\d*\]\[([^\]]+)\]/, 'contacts[' + i +
                                        '][$1]');
                                    newName = newName.replace(/^contacts\[\]\[([^\]]+)\]/, 'contacts[' + i +
                                        '][$1]');
                                    $el.attr('name', newName);
                                }
                                var id = $el.attr('id');
                                if (id) {
                                    var base = id.replace(/_\d+$/, '');
                                    $el.attr('id', base + '_' + i);
                                }
                            });

                            // update contact documents container ids and lists
                            var $docContainer = $row.find('.contact-documents-container');
                            if ($docContainer.length) {
                                $docContainer.attr('id', 'contact-documents-' + i);
                                $docContainer.find('.contact-doc-list').each(function(j, el) {
                                    $(el).removeClass().addClass('contact-doc-list-' + i + ' contact-doc-list');
                                });

                                $docContainer.find('input, select, textarea, button, .file-holder').each(function() {
                                    var $el = $(this);
                                    var name = $el.attr('name');
                                    if (!name) return;
                                    var newName = name.replace(/^contacts\[\d+\]\[documents\]/, 'contacts[' +
                                        i + '][documents]');
                                    newName = newName.replace(/^contacts\[\]\[documents\]/, 'contacts[' + i +
                                        '][documents]');
                                    $el.attr('name', newName);
                                });
                            }

                            // Re-init select2 on selects inside this row
                            if ($.fn.select2) {
                                $row.find('select.js-example-basic-single').each(function() {
                                    try {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).select2('destroy');
                                        }
                                    } catch (e) {
                                        // ignore destroy errors
                                    }
                                    $(this).select2({
                                        width: '100%'
                                    });
                                });
                            }
                        });
                    }

                    function contactdocumentModal(btn) {
                        var $btn = $(btn);
                        var $row = $btn.closest('.contactRow');
                        var contactIndex = $row.index('#contactWrapper .contactRow');

                        currentContactForDoc = contactIndex;

                        $('#contactdocumentModalLabel').text('Add Document — Contact');

                        $('#contact_document_name').val('');
                        $('#contact_document_number').val('');
                        $('#contact_document_date').val('');
                        $('#contact_expiry_date').val('');

                        resetContactFileInput();

                        renderContactDocumentsInModal(contactIndex);

                        $('#contactdocumentModal').modal('show');
                    }

                    function renderContactDocumentsInModal(contactIndex) {
                        var $list = $('#contactDocumentList');
                        $list.empty();

                        var $row = $('#contactWrapper .contactRow').eq(contactIndex);
                        var $docContainer = $row.find('.contact-documents-container');
                        if (!$docContainer.length) {
                            $list.append(
                                '<tr><td colspan="6" class="text-muted text-center">No documents added yet.</td></tr>');
                            return;
                        }

                        $docContainer.find('.contact-doc-entry').each(function(j) {
                            var $entry = $(this);
                            var name = $entry.find('input[name$="[name]"]').val();
                            var number = $entry.find('input[name$="[number]"]').val();
                            var issue = $entry.find('input[name$="[issue_date]"]').val();
                            var expiry = $entry.find('input[name$="[expiry_date]"]').val();
                            var fid = $entry.data('file-id');

                            var attachCell = '';
                            var fileEl = $entry.find('input[type="file"]')[0];
                            if (fileEl && fileEl.files && fileEl.files.length > 0) {
                                var filename = fileEl.files[0].name || '';
                                attachCell = '<span>' + $('<div/>').text(filename).html() + '</span>';
                            }

                            var row = '<tr data-doc-id="' + fid + '" data-doc-index="' + j + '">' +
                                '<td>' + (name || '') + '</td>' +
                                '<td class="text-center">' + (number || '') + '</td>' +
                                '<td class="text-center">' + (issue || '') + '</td>' +
                                '<td class="text-center">' + (expiry || '') + '</td>' +
                                '<td class="text-center">' + attachCell + '</td>' +
                                '<td class="text-center d-flex justify-content-center"><button type="button" class="btn btn-sm btn-light" onclick="removeContactDocumentByFileId(' +
                                fid +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button></td>' +
                                '</tr>';
                            $list.append(row);
                        });
                    }

                    function saveDocumentContact() {
                        var contactIndex = currentContactForDoc;
                        if (contactIndex === null) return;

                        var name = $('#contact_document_name').val().trim();
                        var number = $('#contact_document_number').val().trim();
                        var issue = $('#contact_document_date').val();
                        var expiry = $('#contact_expiry_date').val();
                        var $fileInput = $('#contact_document_attachment');

                        if (!name) {
                            alert('Document name is required');
                            return;
                        }

                        var $contactRow = $('#contactWrapper .contactRow').eq(contactIndex);
                        var $docContainer = $contactRow.find('.contact-documents-container');
                        if (!$docContainer.length) {
                            $contactRow.append('<div id="contact-documents-' + contactIndex +
                                '" class="mt-2 d-none contact-documents-container" style="display:none;"><small class="text-muted d-none">Added Documents:</small><div class="contact-doc-list-' +
                                contactIndex + ' contact-doc-list d-none"></div></div>');
                            $docContainer = $contactRow.find('.contact-documents-container');
                        }

                        var docIndex = $docContainer.find('.contact-doc-entry').length;

                        var $fileHolder = $('<div class="file-holder d-none"></div>');
                        if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                            $fileInput.detach();
                            $fileInput.attr('id', 'contacts_' + contactIndex + '_documents_' + docIndex + '_attachment');
                            $fileInput.attr('name', 'contacts[' + contactIndex + '][documents][' + docIndex + '][attachment]');
                            $fileHolder.append($fileInput);
                        } else {
                            $fileHolder.append('<input type="file" id="contacts_' + contactIndex + '_documents_' + docIndex +
                                '_attachment" name="contacts[' + contactIndex + '][documents][' + docIndex +
                                '][attachment]" class="form-control d-none">');
                        }

                        var $entry = $('<div class="contact-doc-entry d-none" data-file-id="' + Date.now() + '"></div>');
                        $entry.append('<input type="hidden" name="contacts[' + contactIndex + '][documents][' + docIndex +
                            '][name]" value="' + $('<div/>').text(name).html() + '">');
                        $entry.append('<input type="hidden" name="contacts[' + contactIndex + '][documents][' + docIndex +
                            '][number]" value="' + $('<div/>').text(number).html() + '">');
                        $entry.append('<input type="hidden" name="contacts[' + contactIndex + '][documents][' + docIndex +
                            '][issue_date]" value="' + $('<div/>').text(issue).html() + '">');
                        $entry.append('<input type="hidden" name="contacts[' + contactIndex + '][documents][' + docIndex +
                            '][expiry_date]" value="' + $('<div/>').text(expiry).html() + '">');
                        $entry.append($fileHolder);

                        // $docContainer.find('.contact-doc-list').append('<div class="doc-row p-2 border rounded mb-1 d-none'><div><strong>' + $('<div/>').text(name).html() + '</strong></div><div class="text-muted">' + $('<div/>').text(number).html() + '</div></div>');
                        $docContainer.find('.contact-doc-list').append(
                            '<div class="doc-row p-2 border rounded mb-1 d-none">' +
                            '<div><strong>' + $('<div/>').text(name).html() + '</strong></div>' +
                            '<div class="text-muted">' + $('<div/>').text(number).html() + '</div>' +
                            '</div>'
                        );

                        $docContainer.append($entry);

                        // show container and refresh modal list
                        $docContainer.show();
                        resetContactFileInput();
                        renderContactDocumentsInModal(contactIndex);

                        $('#contact_document_name').val('');
                        $('#contact_document_number').val('');
                        $('#contact_document_date').val('');
                        $('#contact_expiry_date').val('');

                        reindexContactRows();
                    }

                    function removeContactDocumentByFileId(fileId) {
                        var $entry = $('.contact-doc-entry[data-file-id="' + fileId + '"]');
                        if (!$entry.length) return;

                        var $docContainer = $entry.closest('.contact-documents-container');
                        var contactIndex = $('#contactWrapper .contactRow').index($entry.closest('.contactRow'));

                        var docIdx = $docContainer.find('.contact-doc-entry').index($entry);

                        $entry.remove();
                        $docContainer.find('.contact-doc-list').children().eq(docIdx).remove();

                        $docContainer.find('.contact-doc-entry').each(function(j) {
                            var $e = $(this);
                            $e.find('input').each(function() {
                                var name = $(this).attr('name');
                                if (!name) return;
                                var newName = name.replace(/contacts\[\d+\]\[documents\]\[\d+\]/, 'contacts[' +
                                    contactIndex + '][documents][' + j + ']');
                                $(this).attr('name', newName);
                            });
                            $e.find('input[type="file"]').each(function() {
                                $(this).attr('name', 'contacts[' + contactIndex + '][documents][' + j +
                                    '][attachment]');
                            });
                        });

                        if ($('#contactdocumentModal').is(':visible')) {
                            renderContactDocumentsInModal(contactIndex);
                        }

                        if ($docContainer.find('.contact-doc-entry').length === 0) {
                            $docContainer.hide();
                        }

                        reindexContactRows();
                    }

                    function resetContactFileInput() {
                        var $fileWrap = $('#contact_document_attachment_wrap');
                        $fileWrap.html(
                            '<label for="contact_document_attachment" class="form-label mb-1">Attachment</label><input type="file" class="form-control form-control-sm" id="contact_document_attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">'
                        );
                    }

                    // Add/remove contact rows (clone-based)
                    function refreshContactButtonsContact() {
                        var $rows = $('#contactWrapper .contactRow');
                        $rows.each(function(idx) {
                            var $row = $(this);
                            var $btnContainer = $row.find('.d-flex.gap-1.mt-4');
                            $btnContainer.find('.addContact, .removeContact').remove();

                            if (idx === $rows.length - 1) {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm addContact"><i class="ico icon-outline-add-square"></i></button>'
                                );
                            }
                            if ($rows.length === 1) {
                                $btnContainer.append(
                                    '<button type="button" class="btn btn-light btn-sm removeContact d-none"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                );
                            } else {
                                if (idx !== 0) {
                                    $btnContainer.append(
                                        '<button type="button" class="btn btn-light btn-sm removeContact"><i class="ico icon-outline-minus-square text-danger"></i></button>'
                                    );
                                } else {
                                    // first row: do not show remove
                                }
                            }
                        });
                    }

                    $(document).off('click', '.addContact').on('click', '.addContact', function(e) {
                        var $last = $('#contactWrapper .contactRow').last();
                        var $clone = $last.clone(false);

                        // cleanup clone
                        $clone.find('.select2-container, .select2').remove();
                        $clone.find('input').each(function() {
                            if ($(this).attr('type') !== 'file') $(this).val('');
                        });
                        $clone.find('select').each(function() {
                            $(this).val('');
                            $(this).removeClass('select2-hidden-accessible');
                            $(this).removeAttr('aria-hidden tabindex data-select2-id');
                        });
                        $clone.find(
                            '.contact-documents-container, .contact-doc-entry, .contact-doc-list, .doc-row, .file-holder'
                        ).remove();
                        $clone.find('[data-file-id]').removeAttr('data-file-id');
                        $clone.find('input[type="file"]').each(function() {
                            var accept = $(this).attr('accept');
                            var $new = $('<input type="file" class="form-control form-control-sm">');
                            if (accept) $new.attr('accept', accept);
                            $(this).replaceWith($new);
                        });

                        // re-init select2 on cloned selects
                        if ($.fn.select2) {
                            $clone.find('select.js-example-basic-single').each(function() {
                                try {
                                    if ($(this).hasClass('select2-hidden-accessible')) {
                                        $(this).select2('destroy');
                                    }
                                } catch (e) {}
                                $(this).select2({
                                    width: '100%'
                                });
                            });
                        }

                        // Apply country dial code to the mobile input in cloned contact row if available
                        var dial = '';
                        if (typeof window.getCountryDialCode === 'function') {
                            dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
                        }
                        $clone.find('input[name$="[mobile]"]').each(function() {

                            var cur = ($(this).val() || '').replace(/^\+\d+\s*/, '');

                            if (dial) {
                                // ALWAYS "+CODE␠"
                                $(this).val('+' + dial + ' ' + cur);
                            } else {
                                $(this).val(cur);
                            }

                        });


                        $('#contactWrapper').append($clone);
                        reindexContactRows();
                        refreshContactButtonsContact();
                    });

                    $(document).off('click', '.removeContact').on('click', '.removeContact', function(e) {
                        var $row = $(this).closest('.contactRow');
                        var total = $('#contactWrapper .contactRow').length;
                        if (total === 1) {
                            $row.find('input').val('');
                            $row.find('select').val('');
                            $row.find('.contact-documents-container').remove();
                            reindexContactRows();
                            refreshContactButtonsContact();
                            return;
                        }
                        $row.remove();
                        reindexContactRows();
                        refreshContactButtonsContact();
                    });

                    // initialize select2 on contact selects on ready
                    if ($.fn.select2) {
                        $('#contactWrapper select.js-example-basic-single').each(function() {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2({
                                    width: '100%'
                                });
                            }
                        });
                    }



                    // ---------- Compliance Documents (Non-UAE) ----------
                    var complianceDocuments = [];
                    var editingComplianceIndex = -1;

                    function openComplianceDocumentModal(editIndex = -1) {
                        editingComplianceIndex = (typeof editIndex === 'number' && editIndex >= 0) ? editIndex : -1;
                        // reset form
                        $('#complianceDocumentForm')[0].reset();
                        // ensure file input exists (may have been detached earlier)
                        resetComplianceFileInput();
                        if (editingComplianceIndex >= 0 && complianceDocuments[editingComplianceIndex]) {
                            var d = complianceDocuments[editingComplianceIndex];
                            $('#compliance_document_number').val(d.document_number);
                            $('#compliance_issue_date').val(d.issue_date);
                            $('#compliance_expiry_date').val(d.expiry_date);
                            $('#compliance_issuing_authority').val(d.issuing_authority);
                        }
                        // show modal
                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('complianceDocumentModal'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#complianceDocumentModal').modal('show');
                        }
                    }

                    function resetComplianceFileInput() {
                        var $wrap = $('#compliance_attachment_wrap');
                        if ($wrap.length) {
                            $wrap.html(
                                '<label for="compliance_attachment" class="form-label">Attachment</label><input type="file" class="form-control form-control-sm" id="compliance_attachment" name="compliance_attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">'
                            );
                        }
                    }

                    function saveComplianceDocument() {
                        var number = $('#compliance_document_number').val().trim();
                        var issue = $('#compliance_issue_date').val();
                        var expiry = $('#compliance_expiry_date').val();
                        var issuing = $('#compliance_issuing_authority').val().trim();
                        var $fileInput = $('#compliance_attachment');

                        if (!number || !issuing) {
                            alert('Document Number and Issuing Authority are required');
                            return;
                        }

                        // create hidden container if missing
                        if ($('#compliance-documents-container').length === 0) {
                            $('#companyAllForm').append('<div id="compliance-documents-container" class="d-none"></div>');
                        }
                        var $container = $('#compliance-documents-container');

                        var fileId = Date.now() + Math.floor(Math.random() * 1000);
                        var attachmentName = '';

                        if (editingComplianceIndex >= 0) {
                            // update existing
                            var doc = complianceDocuments[editingComplianceIndex];
                            doc.document_number = number;
                            doc.issue_date = issue;
                            doc.expiry_date = expiry;
                            doc.issuing_authority = issuing;

                            var $entry = $container.find('.compliance-doc-entry').eq(editingComplianceIndex);
                            if ($entry.length) {
                                // update hidden inputs
                                $entry.find('input[name$="[document_number]"]').val(number);
                                $entry.find('input[name$="[issue_date]"]').val(issue);
                                $entry.find('input[name$="[expiry_date]"]').val(expiry);
                                $entry.find('input[name$="[issuing_authority]"]').val(issuing);

                                // if new file selected, replace
                                if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                    var file = $fileInput.detach();
                                    attachmentName = (file[0].files && file[0].files[0]) ? file[0].files[0].name : '';
                                    file.attr('id', 'compliance_documents_' + editingComplianceIndex + '_attachment');
                                    file.attr('name', 'compliance_documents[' + editingComplianceIndex + '][attachment]');
                                    $entry.find('.file-holder').remove();
                                    $entry.append('<div class="file-holder d-none"></div>');
                                    $entry.find('.file-holder').append(file);
                                    doc.attachment_name = attachmentName;
                                    // ensure modal file input exists for subsequent adds
                                    resetComplianceFileInput();
                                }
                            }
                        } else {
                            // add new entry
                            var docIndex = $container.find('.compliance-doc-entry').length;

                            var $fileHolder = $('<div class="file-holder d-none"></div>');
                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var file = $fileInput.detach();
                                attachmentName = (file[0].files && file[0].files[0]) ? file[0].files[0].name : '';
                                file.attr('id', 'compliance_documents_' + docIndex + '_attachment');
                                file.attr('name', 'compliance_documents[' + docIndex + '][attachment]');
                                $fileHolder.append(file);
                                // ensure modal file input is available for next adds
                                resetComplianceFileInput();
                            } else {
                                $fileHolder.append('<input type="file" name="compliance_documents[' + docIndex +
                                    '][attachment]" class="form-control d-none">');
                            }

                            var $entry = $('<div class="compliance-doc-entry d-none" data-file-id="' + fileId + '"></div>');
                            $entry.append('<input type="hidden" name="compliance_documents[' + docIndex +
                                '][document_number]" value="' + $('<div/>').text(number).html() + '">');
                            $entry.append('<input type="hidden" name="compliance_documents[' + docIndex +
                                '][issue_date]" value="' + $('<div/>').text(issue).html() + '">');
                            $entry.append('<input type="hidden" name="compliance_documents[' + docIndex +
                                '][expiry_date]" value="' + $('<div/>').text(expiry).html() + '">');
                            $entry.append('<input type="hidden" name="compliance_documents[' + docIndex +
                                '][issuing_authority]" value="' + $('<div/>').text(issuing).html() + '">');
                            $entry.append($fileHolder);

                            $container.append($entry);

                            complianceDocuments.push({
                                fileId: fileId,
                                document_number: number,
                                issue_date: issue,
                                expiry_date: expiry,
                                issuing_authority: issuing,
                                attachment_name: attachmentName
                            });
                        }

                        // reset modal
                        resetComplianceFileInput();
                        $('#compliance_document_number').val('');
                        $('#compliance_issue_date').val('');
                        $('#compliance_expiry_date').val('');
                        $('#compliance_issuing_authority').val('');

                        // refresh list
                        updateComplianceDocumentsList();

                        // close modal
                        if (typeof bootstrap !== 'undefined') {
                            var bs = bootstrap.Modal.getInstance(document.getElementById('complianceDocumentModal'));
                            if (bs) bs.hide();
                        } else if (typeof $ !== 'undefined') {
                            $('#complianceDocumentModal').modal('hide');
                        }

                        // reset editing index
                        editingComplianceIndex = -1;
                    }

                    function openComplianceDocumentEditModal(index) {
                        editingComplianceIndex = (typeof index === 'number' && index >= 0) ? index : -1;
                        $('#complianceDocumentEditForm')[0].reset();
                        var doc = complianceDocuments[editingComplianceIndex] || {};
                        $('#compliance_document_number_edit').val(doc.document_number || '');
                        $('#compliance_issue_date_edit').val(doc.issue_date || '');
                        $('#compliance_expiry_date_edit').val(doc.expiry_date || '');
                        $('#compliance_issuing_authority_edit').val(doc.issuing_authority || '');
                        resetComplianceEditFileInput();
                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('complianceDocumentEditModal'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#complianceDocumentEditModal').modal('show');
                        }
                    }

                    function resetComplianceEditFileInput() {
                        var $wrap = $('#compliance_attachment_edit_wrap');
                        if ($wrap.length) {
                            $wrap.html(
                                '<label for="compliance_attachment_edit" class="form-label">Attachment</label><input type="file" class="form-control form-control-sm" id="compliance_attachment_edit" name="compliance_attachment_edit" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">'
                            );
                        }
                    }

                    function updateComplianceDocument() {
                        var number = $('#compliance_document_number_edit').val().trim();
                        var issue = $('#compliance_issue_date_edit').val();
                        var expiry = $('#compliance_expiry_date_edit').val();
                        var issuing = $('#compliance_issuing_authority_edit').val().trim();
                        var $fileInput = $('#compliance_attachment_edit');

                        if (!number || !issuing) {
                            alert('Document Number and Issuing Authority are required');
                            return;
                        }
                        if (editingComplianceIndex < 0) {
                            alert('Invalid document selected');
                            return;
                        }

                        var $container = $('#compliance-documents-container');
                        var docIndex = editingComplianceIndex;
                        var doc = complianceDocuments[docIndex];

                        doc.document_number = number;
                        doc.issue_date = issue;
                        doc.expiry_date = expiry;
                        doc.issuing_authority = issuing;

                        var $entry = $container.find('.compliance-doc-entry').eq(docIndex);
                        if ($entry.length) {
                            $entry.find('input[name$="[document_number]"]').val(number);
                            $entry.find('input[name$="[issue_date]"]').val(issue);
                            $entry.find('input[name$="[expiry_date]"]').val(expiry);
                            $entry.find('input[name$="[issuing_authority]"]').val(issuing);

                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var file = $fileInput.detach();
                                var filename = (file[0].files && file[0].files[0]) ? file[0].files[0].name : '';
                                file.attr('id', 'compliance_documents_' + docIndex + '_attachment');
                                file.attr('name', 'compliance_documents[' + docIndex + '][attachment]');
                                $entry.find('.file-holder').remove();
                                $entry.append('<div class="file-holder d-none"></div>');
                                $entry.find('.file-holder').append(file);
                                doc.attachment_name = filename;
                                // ensure modal edit input is available for subsequent edits
                                resetComplianceEditFileInput();
                            }
                        }

                        updateComplianceDocumentsList();

                        if (typeof bootstrap !== 'undefined') {
                            var bs = bootstrap.Modal.getInstance(document.getElementById('complianceDocumentEditModal'));
                            if (bs) bs.hide();
                        } else if (typeof $ !== 'undefined') {
                            $('#complianceDocumentEditModal').modal('hide');
                        }

                        editingComplianceIndex = -1;
                    }

                    function updateComplianceDocumentsList() {
                        var $tbody = $('#complianceDocumentsList');
                        $tbody.empty();
                        if (complianceDocuments.length === 0) {
                            $tbody.append(
                                '<tr><td colspan="6" class="text-muted text-center">No compliance documents added yet.</td></tr>'
                            );
                            return;
                        }
                        complianceDocuments.forEach(function(d, idx) {
                            var attachCell = d.attachment_name ? '<span>' + $('<div/>').text(d.attachment_name).html() +
                                '</span>' : '<span class="text-muted">No file</span>';
                            var row = '<tr data-file-id="' + d.fileId + '">' +
                                '<td class="">' + $('<div/>').text(d.document_number).html() + '</td>' +
                                '<td class="text-center">' + (d.issue_date || '-') + '</td>' +
                                '<td class="text-center">' + (d.expiry_date || '-') + '</td>' +
                                '<td class="">' + $('<div/>').text(d.issuing_authority).html() + '</td>' +
                                '<td class="">' + attachCell + '</td>' +
                                '<td class="text-center d-flex justify-content-center">' +
                                '<button type="button" class="btn btn-sm btn-light d-inline-flex gap-2" onclick="openComplianceDocumentEditModal(' +
                                idx + ')"><i class="ico icon-outline-pen-2" style="font-size:16px"></i></button>' +
                                '<button type="button" class="btn btn-sm btn-light d-inline-flex gap-2" onclick="removeComplianceDocumentByFileId(\'' +
                                d.fileId +
                                '\')"><i class="ico icon-outline-trash-bin-minimalistic" style="font-size:16px"></i></button>' +
                                '</td>' +
                                '</tr>';
                            $tbody.append(row);
                        });
                    }

                    function removeComplianceDocumentByFileId(fileId) {
                        // find index
                        var idx = -1;
                        for (var i = 0; i < complianceDocuments.length; i++) {
                            if (String(complianceDocuments[i].fileId) === String(fileId)) {
                                idx = i;
                                break;
                            }
                        }
                        if (idx === -1) return;

                        // remove hidden entry
                        $('#compliance-documents-container').find('.compliance-doc-entry').eq(idx).remove();
                        complianceDocuments.splice(idx, 1);

                        // reindex hidden inputs names
                        $('#compliance-documents-container').find('.compliance-doc-entry').each(function(i) {
                            $(this).find('input').each(function() {
                                var name = $(this).attr('name');
                                if (!name) return;
                                var newName = name.replace(/compliance_documents\[\d+\]/,
                                    'compliance_documents[' + i + ']');
                                $(this).attr('name', newName);
                            });
                            $(this).find('input[type="file"]').each(function() {
                                $(this).attr('name', 'compliance_documents[' + i + '][attachment]');
                                $(this).attr('id', 'compliance_documents_' + i + '_attachment');
                            });
                        });

                        updateComplianceDocumentsList();
                    }

                    function previewComplianceAttachmentByFileId(fileId) {
                        var $entry = $('#compliance-documents-container .compliance-doc-entry').filter(function() {
                            return String($(this).data('file-id')) === String(fileId);
                        }).first();
                        if (!$entry.length) {
                            alert('No attachment available');
                            return;
                        }
                        var fileEl = $entry.find('input[type="file"]')[0];
                        if (!fileEl || !fileEl.files || !fileEl.files.length) {
                            alert('No attachment available');
                            return;
                        }
                        var file = fileEl.files[0];
                        var url = URL.createObjectURL(file);
                        window.open(url, '_blank');
                    }

                    // expose compliance globals
                    window.openComplianceDocumentModal = openComplianceDocumentModal;
                    window.saveComplianceDocument = saveComplianceDocument;
                    window.removeComplianceDocumentByFileId = removeComplianceDocumentByFileId;
                    window.previewComplianceAttachmentByFileId = previewComplianceAttachmentByFileId;
                    // edit modal globals
                    window.openComplianceDocumentEditModal = openComplianceDocumentEditModal;
                    window.updateComplianceDocument = updateComplianceDocument;

                    // If any compliance entries were server-rendered into the hidden container, load them into the client array
                    (function loadExistingComplianceDocs() {
                        var $container = $('#compliance-documents-container');
                        if (!$container.length) return;
                        $container.find('.compliance-doc-entry').each(function(i) {
                            var $e = $(this);
                            var fid = $e.data('file-id') || (Date.now() + i);
                            $e.attr('data-file-id', fid);
                            var docNumber = $e.find('input[name$="[document_number]"]').val() || '';
                            var issue = $e.find('input[name$="[issue_date]"]').val() || '';
                            var expiry = $e.find('input[name$="[expiry_date]"]').val() || '';
                            var issuing = $e.find('input[name$="[issuing_authority]"]').val() || '';
                            var attachName = '';
                            var fileEl = $e.find('input[type="file"]')[0];
                            if (fileEl && fileEl.files && fileEl.files.length > 0) {
                                attachName = fileEl.files[0].name || '';
                            }
                            complianceDocuments.push({
                                fileId: fid,
                                document_number: docNumber,
                                issue_date: issue,
                                expiry_date: expiry,
                                issuing_authority: issuing,
                                attachment_name: attachName
                            });
                        });
                        if (complianceDocuments.length) updateComplianceDocumentsList();
                    })();

                    // attach saveDocumentOwner and other helpers to global scope so inline onclick works
                    window.saveDocumentOwner = saveDocumentOwner;
                    window.ownerdocumentModal = ownerdocumentModal;
                    window.removeOwnerDocumentFromModal = removeOwnerDocumentFromModal;
                    // Expose file-id based helpers used by inline onclick attributes
                    window.removeOwnerDocumentByFileId = removeOwnerDocumentByFileId;
                    window.previewOwnerAttachmentByFileId = previewOwnerAttachmentByFileId;

                    // sponsor globals
                    window.saveDocumentSponsor = saveDocumentSponsor;
                    window.sponsordocumentModal = sponsordocumentModal;
                    window.removeSponsorDocumentByFileId = removeSponsorDocumentByFileId;

                    // contact globals
                    window.saveDocumentContact = saveDocumentContact;
                    window.contactdocumentModal = contactdocumentModal;
                    window.removeContactDocumentByFileId = removeContactDocumentByFileId;

                    // ---------- Banking (Add/Edit/Delete) ----------
                    var banks = [];
                    var editingBankIndex = -1;

                    function resetBankFileInput() {
                        var $wrap = $('#bank_letter_wrap');
                        if ($wrap.length) {
                            $wrap.html(
                                '<input type="file" id="bank_letter" name="bank_letter" class="form-control form-control-sm">'
                            );
                            $('#bank_letter_hint').text('');
                        }
                    }

                    function clearBankForm() {
                        $('#bankForm').find('input[type="text"]').val('');
                        $('#bankForm').find('input[type="hidden"]').val('');
                        $('#bank_id').val('');
                        resetBankFileInput();
                        $('#bank_letter_hint').text('');
                    }

                    function openBankModal(editIndex = -1) {
                        editingBankIndex = (typeof editIndex === 'number' && editIndex >= 0) ? editIndex : -1;
                        // reset modal fields
                        clearBankForm();

                        if (editingBankIndex >= 0 && typeof banks[editingBankIndex] !== 'undefined') {
                            var b = banks[editingBankIndex];
                            $('#bank_name').val(b.bank_name || '');
                            $('#branch_name').val(b.branch_name || '');
                            $('#account_number').val(b.account_number || '');
                            $('#iban_number').val(b.iban_number || '');
                            $('#swift_code').val(b.swift_code || '');
                            $('#finance_code').val(b.finance_code || '');
                            $('#currency').val(b.currency || '');

                            var $entry = $('#banks-container .bank-entry').eq(editingBankIndex);
                            if ($entry.length) {
                                var fileInput = $entry.find('input[type="file"]').first();
                                var existingName = $entry.data('existing-file-name') || '';
                                if ((fileInput && fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) ||
                                    existingName) {
                                    $('#bank_letter_hint').text(existingName ? ('Existing file: ' + existingName +
                                            ' — leave empty to keep it') :
                                        'Existing file attached — leave empty to keep or choose new to replace');
                                }
                                var bid = $entry.data('bank-id');
                                if (bid) $('#bank_id').val(bid);
                            }
                        }

                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('bankModal'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#bankModal').modal('show');
                        }
                    }

                    function createHidden(name, value) {
                        return $('<input type="hidden">').attr('name', name).val(value);
                    }

                    $(document).on('click', '#bankSaveBtn', function(e) {
                        e.preventDefault();
                        var bank_name = $('#bank_name').val() || '';
                        if (!bank_name.trim()) {
                            alert('Please enter Bank Name');
                            return;
                        }
                        var branch_name = $('#branch_name').val() || '';
                        var account_number = $('#account_number').val() || '';
                        var iban_number = $('#iban_number').val() || '';
                        var swift_code = $('#swift_code').val() || '';
                        var finance_code = $('#finance_code').val() || '';
                        var currency = $('#currency').val() || '';
                        var $fileInput = $('#bank_letter');

                        var $container = $('#banks-container');
                        if ($container.length === 0) {
                            $container = $('<div id="banks-container" class="d-none"></div>');
                            $('form#companyAllForm').append($container);
                        }

                        if (editingBankIndex >= 0 && typeof banks[editingBankIndex] !== 'undefined') {
                            // update existing
                            var idx = editingBankIndex;
                            var $oldEntry = $container.find('.bank-entry').eq(idx);
                            var fileId = $oldEntry.data('file-id') || Date.now();

                            var $newEntry = $('<div class="bank-entry d-none" data-bank-index="' + idx +
                                '" data-file-id="' + fileId + '"></div>');
                            $newEntry.append(createHidden('banks[' + idx + '][bank_name]', bank_name));
                            $newEntry.append(createHidden('banks[' + idx + '][branch_name]', branch_name));
                            $newEntry.append(createHidden('banks[' + idx + '][account_number]', account_number));
                            $newEntry.append(createHidden('banks[' + idx + '][iban_number]', iban_number));
                            $newEntry.append(createHidden('banks[' + idx + '][swift_code]', swift_code));
                            $newEntry.append(createHidden('banks[' + idx + '][finance_code]', finance_code));
                            $newEntry.append(createHidden('banks[' + idx + '][currency]', currency));

                            // If a new file was chosen, detach and rename it; otherwise keep existing file-holder
                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                // detach to preserve FileList reliably
                                var $f = $fileInput.detach();
                                $f.attr('name', 'banks[' + idx + '][bank_letter]');
                                $f.attr('id', 'banks_' + idx + '_bank_letter');
                                var $fh = $('<div class="file-holder d-none"></div>').append($f);
                                $newEntry.append($fh);
                                var fname = ($f[0].files && $f[0].files[0]) ? $f[0].files[0].name : '';
                                $newEntry.data('existing-file-name', fname);
                                // Immediately recreate file input in modal for next use
                                resetBankFileInput();
                            } else {
                                var $existingHolder = $oldEntry.find('.file-holder').first();
                                if ($existingHolder.length) $newEntry.append($existingHolder);
                                // preserve any existing filename data
                                var existingName = $oldEntry.data('existing-file-name');
                                if (existingName) $newEntry.data('existing-file-name', existingName);
                            }

                            $oldEntry.replaceWith($newEntry);

                            // update banks array and table row
                            banks[idx] = {
                                bank_name: bank_name,
                                branch_name: branch_name,
                                account_number: account_number,
                                iban_number: iban_number,
                                swift_code: swift_code,
                                finance_code: finance_code,
                                currency: currency,
                                file_id: fileId
                            };

                            var $row = $('#bankTableBody').find('.bank-row').eq(idx);
                            if ($row.length) {
                                $row.find('td').eq(0).text(bank_name);
                                $row.find('td').eq(1).text(branch_name);
                                $row.find('td').eq(2).text(account_number);
                                $row.find('td').eq(3).text(iban_number);
                                $row.find('td').eq(4).text(swift_code);
                                $row.find('td').eq(5).text(finance_code);
$row.find('td').eq(6).text(getCurrencyTextForValue(currency));

                                var newLetter = $newEntry.data('existing-file-name') || '—';
                                $row.find('td').eq(7).text(newLetter);
                            }

                        } else {
                            // add new
                            var bankIndex = $('#bankTableBody').find('.bank-row').length || 0;
                            var fileId = Date.now() + Math.floor(Math.random() * 1000);

                            var $entry = $('<div class="bank-entry d-none" data-bank-index="' + bankIndex +
                                '" data-file-id="' + fileId + '"></div>');
                            $entry.append(createHidden('banks[' + bankIndex + '][bank_name]', bank_name));
                            $entry.append(createHidden('banks[' + bankIndex + '][branch_name]', branch_name));
                            $entry.append(createHidden('banks[' + bankIndex + '][account_number]', account_number));
                            $entry.append(createHidden('banks[' + bankIndex + '][iban_number]', iban_number));
                            $entry.append(createHidden('banks[' + bankIndex + '][swift_code]', swift_code));
                            $entry.append(createHidden('banks[' + bankIndex + '][finance_code]', finance_code));
                            $entry.append(createHidden('banks[' + bankIndex + '][currency]', currency));

                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                // detach so FileList is preserved and place in hidden holder named for submission
                                var $f = $fileInput.detach();
                                $f.attr('name', 'banks[' + bankIndex + '][bank_letter]');
                                $f.attr('id', 'banks_' + bankIndex + '_bank_letter');
                                $entry.append($('<div class="file-holder d-none"></div>').append($f));
                                $entry.data('existing-file-name', ($f[0].files && $f[0].files[0] && $f[0].files[0]
                                    .name) ? $f[0].files[0].name : '');
                                // Immediately recreate file input in modal for next use
                                resetBankFileInput();
                            }

                            $container.append($entry);

                            var row = $('<tr class="bank-row" data-bank-index="' + bankIndex + '"></tr>');
                            row.append($('<td>').text(bank_name));
                            row.append($('<td>').text(branch_name));
                            row.append($('<td>').text(account_number));
                            row.append($('<td>').text(iban_number));
                            row.append($('<td>').text(swift_code));
                            row.append($('<td class="text-center">').text(finance_code));
                            row.append($('<td class="text-center">').text(getCurrencyTextForValue(currency)));

                            var letterName = ($entry && $entry.data('existing-file-name')) ? $entry.data(
                                'existing-file-name') : '—';
                            row.append($('<td>').text(letterName));

                            var actions = $(
                                '<td class="text-center d-flex justify-content-center align-items-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openBankModal(' +
                                bankIndex +
                                ')"><i class="ico ico icon-outline-pen-2 text-dark" style="font-size:16px"></i></button>'
                            );
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-bank" onclick="removeBankByIndex(' +
                                bankIndex +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );

                            row.append(actions);

                            $('#bankTableBody').find('.no-bank-row').remove();
                            $('#bankTableBody').append(row);

                            banks.push({
                                bank_name: bank_name,
                                branch_name: branch_name,
                                account_number: account_number,
                                iban_number: iban_number,
                                swift_code: swift_code,
                                finance_code: finance_code,
                                currency: currency,
                                file_id: fileId
                            });
                        }

                        // reset modal form
                        clearBankForm();
                        // Ensure file input is fresh (clearBankForm already calls resetBankFileInput, but call again for safety)
                        if ($('#bank_letter').length === 0) {
                            resetBankFileInput();
                        }
                        if (typeof bootstrap !== 'undefined') {
                            var bs = bootstrap.Modal.getInstance(document.getElementById('bankModal'));
                            if (bs) bs.hide();
                        } else if (typeof $ !== 'undefined') {
                            $('#bankModal').modal('hide');
                        }

                        editingBankIndex = -1;
                    });

                    function removeBankByIndex(index) {
                        var idx = parseInt(index, 10);
                        $('#bankTableBody').find('.bank-row').eq(idx).remove();
                        var $container = $('#banks-container');
                        $container.find('.bank-entry').eq(idx).remove();

                        // keep client array in sync
                        if (typeof banks.splice === 'function') banks.splice(idx, 1);

                        // reindex hidden inputs and rows
                        $container.find('.bank-entry').each(function(i) {
                            $(this).attr('data-bank-index', i);
                            $(this).find('input[type="hidden"]').each(function() {
                                var name = $(this).attr('name') || '';
                                var newName = name.replace(/banks\[\d+\]/, 'banks[' + i + ']');
                                $(this).attr('name', newName);
                            });

                            // Reindex any detached file inputs as well
                            $(this).find('input[type="file"]').each(function() {
                                var fname = $(this).attr('name') || '';
                                if (fname.indexOf('[bank_letter]') !== -1) {
                                    var newFname = fname.replace(/banks\[\d+\]\[bank_letter\]/, 'banks[' + i +
                                        '][bank_letter]');
                                    $(this).attr('name', newFname);
                                    $(this).attr('id', 'banks_' + i + '_bank_letter');
                                }
                            });
                        });

                        $('#bankTableBody').find('.bank-row').each(function(i) {
                            $(this).attr('data-bank-index', i);
                            $(this).find('button').each(function() {
                                var on = $(this).attr('onclick');
                                if (!on) return;
                                on = on.replace(/\(\d+\)/, '(' + i + ')');
                                $(this).attr('onclick', on);
                            });
                        });

                        if ($('#bankTableBody').find('.bank-row').length === 0) {
                            $('#bankTableBody').append(
                                '<tr class="no-bank-row"><td colspan="9" class="text-center text-muted">No banks added yet.</td></tr>'
                            );
                        }
                    }



                    // expose
                    window.openBankModal = openBankModal;
                    window.removeBankByIndex = removeBankByIndex;


                    // wire add button
                    $(document).on('click', '#addBankBtn', function() {
                        openBankModal();
                    });

                    // wire add policy button — ensure Add opens "Add" modal (reset form) instead of reusing edit state
                    $(document).on('click', '#addPolicyBtn', function(e) {
                        e.preventDefault();
                        openPolicyModal();
                    });

                    // Helper to resolve a display text for currency values
                    function getCurrencyTextForValue(value) {
                        var v = (value || '').toString();
                        var opt = $('#currency option[value="' + v + '"]');
                        if (!opt.length) opt = $('#currency option[data-id="' + v + '"]');
                        if (!opt.length) {
                            opt = $('#currency option').filter(function() {
                                var t = $(this).text().trim();
                                return t === v || t === String(v);
                            });
                        }
                        if (!opt.length) {
                            opt = $('#currency option').filter(function() {
                                return $(this).text().trim().indexOf(v) !== -1;
                            });
                        }
                        if (opt.length) return opt.first().text().trim();
                        return v;
                    }

                    // If server rendered banks exist in hidden container, load them into client array and table
                    function loadExistingBanks() {
                        var $container = $('#banks-container');
                        if (!$container.length) return;
                        $container.find('.bank-entry').each(function(i) {
                            var $e = $(this);
                            var getVal = function(key) {
                                var inp = $e.find('input[name$="[' + key + ']"]').first();
                                return inp.length ? inp.val() : '';
                            };
                            var bank_name = getVal('bank_name');
                            var branch_name = getVal('branch_name');
                            var account_number = getVal('account_number');
                            var iban_number = getVal('iban_number');
                            var swift_code = getVal('swift_code');
                            var finance_code = getVal('finance_code');
                            var currency = getVal('currency');
                            var currencyText = ($e.data('currency-text') || '').toString().trim();
                            if (!currencyText) {
                                var opt = $('#currency option[value="' + currency + '"]');
                                currencyText = opt.length ? opt.text().trim() : (currency || '');
                            }

                            var fileId = $e.data('file-id') || Date.now() + i;
                            var existingName = $e.data('existing-file-name') || $e.find('input[name$="[bank_letter]"]')
                                .attr('data-file-name') || '';

                            banks.push({
                                bank_name: bank_name,
                                branch_name: branch_name,
                                account_number: account_number,
                                iban_number: iban_number,
                                swift_code: swift_code,
                                finance_code: finance_code,
                                currency: currency, // keep original value for submission
                                file_id: fileId,
                                currency_text: currencyText // store for convenience
                            });

                            var row = $('<tr class="bank-row" data-bank-index="' + i + '"></tr>');
                            row.append($('<td>').text(bank_name));
                            row.append($('<td>').text(branch_name));
                            row.append($('<td>').text(account_number));
                            row.append($('<td>').text(iban_number));
                            row.append($('<td>').text(swift_code));
                            row.append($('<td>').text(finance_code));
                            row.append($('<td>').text(currencyText));
                            row.append($('<td>').text(existingName || '—'));

                            var actions = $('<td class="text-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openBankModal(' +
                                i + ')"><i class="ico icon-outline-pen-2" style="font-size:16px"></i></button>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-bank" onclick="removeBankByIndex(' +
                                i +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );
                            row.append(actions);

                            $('#bankTableBody').find('.no-bank-row').remove();
                            $('#bankTableBody').append(row);
                        });
                    }

                    // run loader on ready
                    $(document).ready(function() {
                        loadExistingBanks();
                        // load policies if server-rendered
                        loadExistingPolicies();
                    });

                    // ---------- Dynamic Document Rows (UAE / Non-UAE) ----------
                    function initDocDatePickers($context) {
                        $context = $context || $(document);
                        if (typeof flatpickr !== 'undefined') {
                            $context.find('.date-picker').each(function() {
                                if (!this._flatpickr) flatpickr(this, {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true
                                });
                            });
                        } else if ($.fn.datepicker) {
                            $context.find('.date-picker').not('.hasDatepicker').datepicker({
                                dateFormat: 'dd/mm/yy'
                            });
                        }
                    }

                    function reindexDocRows($tbody, prefix) {
                        $tbody.find('.dynamic-doc-row').each(function(i) {
                            var $tr = $(this);
                            $tr.attr('data-doc-index', i);
                            $tr.find('input, select, textarea').each(function() {
                                var name = $(this).attr('name') || '';
                                if (!name) return;
                                // replace first numeric index occurrence
                                var newName = name.replace(/\[\d+\]/, '[' + i + ']');
                                $(this).attr('name', newName);
                            });
                        });
                    }

                    function addDocumentRowTo(tableSelector, prefix) {
                        var $tbody = $(tableSelector + ' tbody');
                        if (!$tbody.length) return;
                        var idx = $tbody.find('.dynamic-doc-row').length;
                        var row = '<tr class="dynamic-doc-row" data-doc-index="' + idx + '">' +
                            '<td><input type="text" name="' + prefix + '[' + idx +
                            '][name]" class="form-control form-control-sm doc-input"></td>' +
                            '<td><input type="text" name="' + prefix + '[' + idx +
                            '][number]" class="form-control form-control-sm doc-input text-center"></td>' +
                            '<td><input type="text" name="' + prefix + '[' + idx +
                            '][date]" class="form-control form-control-sm date-picker doc-input"></td>' +
                            '<td><input type="text" name="' + prefix + '[' + idx +
                            '][expiry]" class="form-control form-control-sm date-picker doc-input"></td>' +
                            '<td>' +
                            '<div class="d-flex align-items-center gap-2">' +
                            '<input type="file" name="' + prefix + '[' + idx +
                            '][file]" class="form-control form-control-sm doc-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">' +
                            '<button type="button" class="btn btn-sm btn-light btn-delete-doc" title="Remove row">' +
                            '<i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size:16px"></i>' +
                            '</button>' +
                            '</div>' +
                            '</td>' +
                            '</tr>';

                        $tbody.append(row);
                        var $newRow = $tbody.find('.dynamic-doc-row').last();
                        initDocDatePickers($newRow);
                    }

                    // Add row handlers
                    $(document).on('click', '#addDocumentRowTop', function() {
                        addDocumentRowTo('#documentationTable', 'uae_documents');
                    });

                    $(document).on('click', '#addDocumentRowTopNonUae', function() {
                        addDocumentRowTo('#nonUaeDocumentationTable', 'non_uae_documents');
                    });

                    // Delete row handler (works for both tables)
                    $(document).on('click', '.btn-delete-doc', function() {
                        var $tr = $(this).closest('tr');
                        var $tbody = $tr.closest('tbody');
                        $tr.remove();
                        // determine prefix based on which table we are in
                        var tableId = $tbody.closest('table').attr('id');
                        var prefix = tableId === 'documentationTable' ? 'uae_documents' : 'non_uae_documents';
                        reindexDocRows($tbody, prefix);
                    });

                    // initialize datepickers for any existing dynamic rows on load
                    initDocDatePickers();

                    // Policies state
                    var policies = [];
                    var editingPolicyIndex = -1;

                    function resetPolicyFileInput() {
                        var $wrap = $('#policy_file_wrap');
                        if ($wrap.length) {
                            $wrap.html(
                                '<input type="file" id="policy_file" name="policy_file" class="form-control form-control-sm policy-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">'
                            );
                            $('#policy_file_hint').text('');
                        }
                    }

                    function clearPolicyForm() {
                        $('#policyForm')[0].reset();
                        $('#policy_id').val('');
                        $('#policyDetailsEditor').html('');
                        $('#policyDetailsHidden').val('');
                        resetPolicyFileInput();
                        $('#policyLoader').addClass('d-none');
                    }

                    function openPolicyModal(editIndex = -1) {
                        editingPolicyIndex = (typeof editIndex === 'number' && editIndex >= 0) ? editIndex : -1;
                        clearPolicyForm();
                        if (editingPolicyIndex >= 0 && typeof policies[editingPolicyIndex] !== 'undefined') {
                            var p = policies[editingPolicyIndex];
                            $('input[name="policy_date"]').val(p.policy_date || '');
                            $('input[name="policy_name"]').val(p.policy_name || '');
                            $('input[name="policy_valid"]').val(p.policy_valid || '');
                            $('select[name="view_to_employees"]').val(p.view_to_employees || '1');
                            $('#policyDetailsEditor').html(p.policy_details || '');
                            var $entry = $('#policies-container .policy-entry').eq(editingPolicyIndex);
                            if ($entry.length) {
                                var fname = $entry.data('existing-file-name') || '';
                                if (fname) $('#policy_file_hint').text('Existing: ' + fname + ' — leave empty to keep');
                            }
                            $('#policyModalTitle').text('Edit Policy');
                        } else {
                            $('#policyModalTitle').text('Add Policy');
                        }
                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('policyModal'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#policyModal').modal('show');
                        }
                    }

                    $(document).on('click', '#savePolicyBtn', function(e) {
                        e.preventDefault();
                        $('#policyLoader').removeClass('d-none');
                        var policy_date = $('input[name="policy_date"]').val() || '';
                        var policy_name = $('input[name="policy_name"]').val() || '';
                        if (!policy_name.trim()) {
                            alert('Please enter Policy Name');
                            $('#policyLoader').addClass('d-none');
                            return;
                        }
                        var policy_valid = $('input[name="policy_valid"]').val() || '';
                        var view_to_employees = $('select[name="view_to_employees"]').val() || '1';
                        var policy_details = $('#policyDetailsEditor').html() || '';
                        // set hidden textarea for submission
                        $('#policyDetailsHidden').val(policy_details);
                        var $fileInput = $('#policy_file');

                        var $container = $('#policies-container');
                        if ($container.length === 0) {
                            $container = $('<div id="policies-container" class="d-none"></div>');
                            $('form#companyAllForm').append($container);
                        }

                        if (editingPolicyIndex >= 0 && typeof policies[editingPolicyIndex] !== 'undefined') {
                            var idx = editingPolicyIndex;
                            var $oldEntry = $container.find('.policy-entry').eq(idx);
                            var fileId = $oldEntry.data('file-id') || Date.now();

                            var $newEntry = $('<div class="policy-entry d-none" data-policy-index="' + idx +
                                '" data-file-id="' + fileId + '"></div>');
                            $newEntry.append(createHidden('policies[' + idx + '][policy_date]', policy_date));
                            $newEntry.append(createHidden('policies[' + idx + '][policy_name]', policy_name));
                            $newEntry.append(createHidden('policies[' + idx + '][policy_valid]', policy_valid));
                            $newEntry.append(createHidden('policies[' + idx + '][view_to_employees]',
                                view_to_employees));
                            $newEntry.append('<input type="hidden" name="policies[' + idx +
                                '][policy_details]" value="' + $('<div/>').text(policy_details).html() + '">');

                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var $f = $fileInput.detach();
                                $f.removeAttr('id');
                                $f.attr('name', 'policies[' + idx + '][policy_file]');
                                $newEntry.append($('<div class="file-holder d-none"></div>').append($f));

                                var fname = ($f[0].files && $f[0].files[0]) ? $f[0].files[0].name : '';
                                $newEntry.data('existing-file-name', fname);
                            } else {
                                var $existingHolder = $oldEntry.find('.file-holder').first();
                                if ($existingHolder.length) $newEntry.append($existingHolder);
                                var existingName = $oldEntry.data('existing-file-name');
                                if (existingName) $newEntry.data('existing-file-name', existingName);
                            }

                            $oldEntry.replaceWith($newEntry);

                            policies[idx] = {
                                policy_date: policy_date,
                                policy_name: policy_name,
                                policy_valid: policy_valid,
                                view_to_employees: view_to_employees,
                                policy_details: policy_details,
                                file_id: fileId
                            };

                            var $row = $('#policyTableBody').find('.policy-row').eq(idx);
                            if ($row.length) {
                                $row.find('td').eq(0).text(policy_date || '—');
                                $row.find('td').eq(1).text(policy_name);

                                $row.find('td').eq(2).text(policy_valid || '—');
                                $row.find('td').eq(3).text(view_to_employees == '1' ? 'Yes' : 'No');
                                $row.find('td').eq(4).text($newEntry.data('existing-file-name') || '—');
                            }
                        } else {
                            var pIndex = $('#policyTableBody').find('.policy-row').length || 0;
                            var fileId = Date.now() + Math.floor(Math.random() * 1000);

                            var $entry = $('<div class="policy-entry d-none" data-policy-index="' + pIndex +
                                '" data-file-id="' + fileId + '"></div>');
                            $entry.append(createHidden('policies[' + pIndex + '][policy_date]', policy_date));
                            $entry.append(createHidden('policies[' + pIndex + '][policy_name]', policy_name));
                            $entry.append(createHidden('policies[' + pIndex + '][policy_valid]', policy_valid));
                            $entry.append(createHidden('policies[' + pIndex + '][view_to_employees]',
                                view_to_employees));
                            $entry.append('<input type="hidden" name="policies[' + pIndex +
                                '][policy_details]" value="' + $('<div/>').text(policy_details).html() + '">');

                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var $f = $fileInput.detach();
                                $f.removeAttr('id');
                                $f.attr('name', 'policies[' + pIndex + '][policy_file]');
                                $entry.append($('<div class="file-holder d-none"></div>').append($f));
                                $entry.data('existing-file-name', ($f[0].files && $f[0].files[0] && $f[0].files[0]
                                    .name) ? $f[0].files[0].name : '');
                            }

                            $container.append($entry);

                            var row = $('<tr class="policy-row" data-policy-index="' + pIndex + '"></tr>');
                            row.append($('<td>').text(policy_date || '—'));
                            row.append($('<td>').text(policy_name));

                            row.append($('<td>').text(policy_valid || '—'));
                            row.append($('<td>').text(view_to_employees == '1' ? 'Yes' : 'No'));
                            row.append($('<td>').text($entry.data('existing-file-name') || '—'));
                            var actions = $(
                                '<td class="text-center d-flex justify-content-center align-items-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openPolicyModal(' +
                                pIndex +
                                ')"><i class="ico icon-outline-pen-2 text-dark" style="font-size:16px"></i></button>'
                            );
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-policy" onclick="removePolicyByIndex(' +
                                pIndex +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );
                            row.append(actions);
                            $('#policyTableBody').find('.no-policy-row').remove();
                            $('#policyTableBody').append(row);

                            policies.push({
                                policy_date: policy_date,
                                policy_name: policy_name,
                                policy_valid: policy_valid,
                                view_to_employees: view_to_employees,
                                policy_details: policy_details,
                                file_id: fileId
                            });
                        }

                        // reset modal
                        resetPolicyFileInput();
                        clearPolicyForm();
                        if (typeof bootstrap !== 'undefined') {
                            var bs = bootstrap.Modal.getInstance(document.getElementById('policyModal'));
                            if (bs) bs.hide();
                        } else if (typeof $ !== 'undefined') {
                            $('#policyModal').modal('hide');
                        }

                        editingPolicyIndex = -1;
                    });

                    function removePolicyByIndex(index) {
                        var idx = parseInt(index, 10);
                        $('#policyTableBody').find('.policy-row').eq(idx).remove();
                        var $container = $('#policies-container');
                        $container.find('.policy-entry').eq(idx).remove();

                        if (typeof policies.splice === 'function') policies.splice(idx, 1);

                        $container.find('.policy-entry').each(function(i) {
                            $(this).attr('data-policy-index', i);
                            $(this).find('input, textarea').each(function() {
                                var name = $(this).attr('name') || '';
                                var newName = name.replace(/policies\[\d+\]/, 'policies[' + i + ']');
                                $(this).attr('name', newName);
                            });
                            $(this).find('input[type="file"]').each(function() {
                                $(this).attr('name', 'policies[' + i + '][policy_file]');
                            });
                        });

                        $('#policyTableBody').find('.policy-row').each(function(i) {
                            $(this).attr('data-policy-index', i);
                            $(this).find('button').each(function() {
                                var $btn = $(this);
                                $btn.attr('onclick', $btn.attr('onclick').replace(/\(\d+\)/, '(' + i + ')'));
                            });
                        });

                        if ($('#policyTableBody').find('.policy-row').length === 0) {
                            $('#policyTableBody').append(
                                '<tr class="no-policy-row"><td colspan="6" class="text-center text-muted">No policies added yet.</td></tr>'
                            );
                        }
                    }

                    function loadExistingPolicies() {
                        var $container = $('#policies-container');
                        if (!$container.length) return;
                        $container.find('.policy-entry').each(function(i) {
                            var $e = $(this);
                            var getVal = function(key) {
                                var inp = $e.find('input[name$="[' + key + ']"]').first();
                                return inp.length ? inp.val() : '';
                            };
                            var policy_date = getVal('policy_date');
                            var policy_name = getVal('policy_name');
                            var policy_valid = getVal('policy_valid');
                            var view_to_employees = getVal('view_to_employees') || '1';
                            var policy_details = $e.find('input[name$="[policy_details]"]').val() || '';
                            var fileId = $e.data('file-id') || Date.now() + i;
                            var existingName = $e.data('existing-file-name') || '';

                            policies.push({
                                policy_date: policy_date,
                                policy_name: policy_name,
                                policy_valid: policy_valid,
                                view_to_employees: view_to_employees,
                                policy_details: policy_details,
                                file_id: fileId
                            });

                            var row = $('<tr class="policy-row" data-policy-index="' + i + '"></tr>');
                            row.append($('<td>').text(policy_date || '—'));
                            row.append($('<td>').text(policy_name));
                            row.append($('<td>').text(''));
                            row.append($('<td>').text(policy_valid || '—'));
                            row.append($('<td>').text(view_to_employees == '1' ? 'Yes' : 'No'));
                            row.append($('<td>').text(existingName || '—'));
                            var actions = $('<td class="text-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openPolicyModal(' +
                                i +
                                ')"><i class="ico icon-outline-pen-2 text-dark" style="font-size:16px"></i></button>'
                            );
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-policy" onclick="removePolicyByIndex(' +
                                i +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );
                            row.append(actions);
                            $('#policyTableBody').find('.no-policy-row').remove();
                            $('#policyTableBody').append(row);
                        });
                    }

                    window.openPolicyModal = openPolicyModal;
                    window.removePolicyByIndex = removePolicyByIndex;

                    // ---------- Warehouses (Add/Edit/Delete) ----------
                    var warehouses = [];
                    var editingWarehouseIndex = -1;

                    function resetContactDocumentsInput() {
                        var $wrap = $('#contact_documents_wrap');
                        if ($wrap.length) {
                            $wrap.html(
                                '<input type="file" name="contact_documents[0]" id="contact_documents" class="form-control form-control-sm warehouse-input" multiple>'
                            );
                            $('#warehouse_docs_hint').text('');
                        }
                    }

                    function clearWarehouseForm() {
                        $('#warehouseForm')[0].reset();
                        $('#warehouse_id').val('');
                        resetContactDocumentsInput();
                        $('#warehouseLoader').addClass('d-none');
                    }

                    function openWarehouseModal(editIndex = -1) {
                        editingWarehouseIndex = (typeof editIndex === 'number' && editIndex >= 0) ? editIndex : -1;
                        clearWarehouseForm();

                        if (editingWarehouseIndex >= 0 && typeof warehouses[editingWarehouseIndex] !== 'undefined') {
                            var w = warehouses[editingWarehouseIndex];
                            // $('#warehouse_code').val(w.warehouse_code || '');
                            $('#warehouse_name').val(w.warehouse_name || '');
                            // set warehouse country: prefer id; if only name available, try to match option text
                            var countryVal = w.warehouse_country_id || '';
                            if (!countryVal && w.warehouse_country) {
                                var $match = $('#warehouse_country option').filter(function() {
                                    return $(this).text().trim() === (w.warehouse_country || '').trim();
                                }).first();
                                if ($match.length) countryVal = $match.val();
                            }
                            $('#warehouse_country').val(countryVal || '').trigger('change');
                            // prefer id (warehouse_state_id / warehouse_city_id) when setting selects; fallback to existing value
                            var prefState = w.warehouse_state_id || w.warehouse_state || '';
                            var prefCity = w.warehouse_city_id || w.warehouse_city || '';

                            // Prefill city immediately (city is a text input in this UI)
                            if (w.warehouse_city) {
                                $('#warehouse_city').val(w.warehouse_city);
                            }

                            if (prefState) {
                                var tries = 0;
                                var stateSetter = setInterval(function() {
                                    if ($('#warehouse_state option[value="' + prefState + '"]').length || tries > 10) {
                                        $('#warehouse_state').val(prefState).trigger('change');
                                        clearInterval(stateSetter);
                                        setTimeout(function() {
                                            if (prefCity) $('#warehouse_city').val(prefCity);
                                        }, 200);
                                    }
                                    tries++;
                                }, 200);
                            } else {
                                $('#warehouse_state').val('');
                                // if state not present, preserve or set city if we have it
                                if (prefCity) {
                                    $('#warehouse_city').val(prefCity);
                                } else if (!w.warehouse_city) {
                                    $('#warehouse_city').val('');
                                }
                            }
                            $('#warehouse_area').val(w.warehouse_area || '');
                            $('#warehouse_building_name').val(w.warehouse_building_name || '');
                            $('#warehouse_flat_office_no').val(w.warehouse_flat_office_no || '');
                            $('#contact_salutation').val(w.contact_salutation || '');
                            $('#contact_first_name').val(w.contact_first_name || '');
                            $('#contact_last_name').val(w.contact_last_name || '');
                            $('#contact_mobile').val(w.contact_mobile || '');
                            $('#contact_email').val(w.contact_email || '');
                            $('#contact_designation').val(w.contact_designation || '');

                            // prefill selected contact person (id) when editing
                            try {
                                $('#contact_person_name').val(w.contact_person_id || w.contact_person_name || '').trigger('change');
                            } catch (e) {}
                            $('#fire_safety_compliance_status').val(w.fire_safety_compliance_status || '');
                            $('#fire_noc_certificate_number').val(w.fire_noc_certificate_number || '');
                            $('#safety_equipment_available').val(w.safety_equipment_available || '');
                            $('#fire_noc_expiry_date').val(w.fire_noc_expiry_date || '');
                            $('#last_safety_inspection_date').val(w.last_safety_inspection_date || '');

                            var $entry = $('#warehouses-container .warehouse-entry').eq(editingWarehouseIndex);
                            if ($entry.length) {
                                var names = $entry.data('existing-file-names') || '';
                                if (names) $('#warehouse_docs_hint').text('Existing: ' + names + ' — leave empty to keep');
                            }

                            $('#warehouseModal .modal-title').text('Edit Warehouse');
                        } else {
                            $('#warehouseModal .modal-title').text('Add Warehouse');
                        }

                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('warehouseModal'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#warehouseModal').modal('show');
                        }

                        // ensure date-picker instances are initialized for modal fields
                        try {
                            if (typeof flatpickr !== 'undefined') {
                                if (document.getElementById('fire_noc_expiry_date') && !document.getElementById(
                                        'fire_noc_expiry_date')._flatpickr) {
                                    flatpickr('#fire_noc_expiry_date', {
                                        dateFormat: 'd/m/Y',
                                        allowInput: true
                                    });
                                }
                                if (document.getElementById('last_safety_inspection_date') && !document.getElementById(
                                        'last_safety_inspection_date')._flatpickr) {
                                    flatpickr('#last_safety_inspection_date', {
                                        dateFormat: 'd/m/Y',
                                        allowInput: true
                                    });
                                }
                            } else if ($.fn.datepicker) {
                                $('#fire_noc_expiry_date').not('.hasDatepicker').datepicker({
                                    dateFormat: 'dd/mm/yy'
                                });
                                $('#last_safety_inspection_date').not('.hasDatepicker').datepicker({
                                    dateFormat: 'dd/mm/yy'
                                });
                            }
                        } catch (e) {}

                    }

                    function createHidden(name, value) {
                        return $('<input type="hidden">').attr('name', name).val(value);
                    }

                    $(document).on('click', '#saveWarehouseBtn', function(e) {
                        e.preventDefault();
                        $('#warehouseLoader').removeClass('d-none');
                        // var warehouse_code = $('#warehouse_code').val() || '';
                        var warehouse_name = $('#warehouse_name').val() || '';
                        if ( !warehouse_name.trim()) {
                            alert('Please enter Warehouse Code and Name');
                            $('#warehouseLoader').addClass('d-none');
                            return;
                        }

                        // capture country id and display name
                        var warehouse_country_id = $('#warehouse_country').val() || '';
                        var warehouse_country = ($('#warehouse_country option:selected').text() || '').trim() || '';
                        // capture both id and display text for state and city
                        var warehouse_state_id = $('#warehouse_state').val() || '';
                        var warehouse_state = ($('#warehouse_state option:selected').text() || '').trim() || '';
                        // City is a free-text input (not a select). Use its value directly.
                        var warehouse_city = ($('#warehouse_city').val() || '').trim() || '';
                        var warehouse_city_id = '';
                        var warehouse_area = $('#warehouse_area').val() || '';
                        var warehouse_building_name = $('#warehouse_building_name').val() || '';
                        var warehouse_flat_office_no = $('#warehouse_flat_office_no').val() || '';
                        var contact_salutation = $('#contact_salutation').val() || '';
                        var contact_first_name = $('#contact_first_name').val() || '';
                        var contact_last_name = $('#contact_last_name').val() || '';
                        var contact_mobile = $('#contact_mobile').val() || '';
                        var contact_email = $('#contact_email').val() || '';
                        var contact_designation = $('#contact_designation').val() || '';
                        // New: selected contact person (id) and display text
                        var contact_person_id = $('#contact_person_name').val() || '';
                        var contact_person_text = ($('#contact_person_name option:selected').text() || '').trim() || '';
                        var fire_safety_compliance_status = $('#fire_safety_compliance_status').val() || '';
                        var fire_noc_certificate_number = $('#fire_noc_certificate_number').val() || '';
                        var safety_equipment_available = $('#safety_equipment_available').val() || '';
                        var fire_noc_expiry_date = $('#fire_noc_expiry_date').val() || '';
                        var last_safety_inspection_date = $('#last_safety_inspection_date').val() || '';
                        var $fileInput = $('#contact_documents');

                        var $container = $('#warehouses-container');
                        if ($container.length === 0) {
                            $container = $('<div id="warehouses-container" class="d-none"></div>');
                            $('form#companyAllForm').append($container);
                        }

                        if (editingWarehouseIndex >= 0 && typeof warehouses[editingWarehouseIndex] !== 'undefined') {
                            var idx = editingWarehouseIndex;
                            var $oldEntry = $container.find('.warehouse-entry').eq(idx);
                            var fileId = $oldEntry.data('file-id') || Date.now();

                            var $newEntry = $('<div class="warehouse-entry d-none" data-warehouse-index="' + idx +
                                '" data-file-id="' + fileId + '"></div>');
                            // $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_code]', warehouse_code));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_name]', warehouse_name));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_country_id]',
                                warehouse_country_id));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_country]',
                                warehouse_country));
                            // store both id and display name for state/city
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_state_id]',
                                warehouse_state_id));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_state]', warehouse_state));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_city_id]',
                                warehouse_city_id));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_city]', warehouse_city));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_area]', warehouse_area));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_building_name]',
                                warehouse_building_name));
                            $newEntry.append(createHidden('warehouses[' + idx + '][warehouse_flat_office_no]',
                                warehouse_flat_office_no));

                            // New: contact person id + text
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_person_name]', contact_person_id));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_person_name_text]', contact_person_text));

                            // If the user did not change the select (no text provided), preserve any previously stored text
                            if (!contact_person_text) {
                                var existingPersonText = $oldEntry.find('input[name$="[contact_person_name_text]"]').val() || $oldEntry.data('contact_person_text') || '';
                                if (existingPersonText) {
                                    // set hidden input value and a data attr on the new entry so display logic can use it
                                    $newEntry.find('input[name$="[contact_person_name_text]"]').val(existingPersonText);
                                    $newEntry.data('contact_person_text', existingPersonText);
                                }
                            } else {
                                // store data attr when user selected a contact
                                $newEntry.data('contact_person_text', contact_person_text);
                            }

                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_salutation]',
                                contact_salutation));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_first_name]',
                                contact_first_name));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_last_name]',
                                contact_last_name));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_mobile]', contact_mobile));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_email]', contact_email));
                            $newEntry.append(createHidden('warehouses[' + idx + '][contact_designation]',
                                contact_designation));
                            $newEntry.append(createHidden('warehouses[' + idx + '][fire_safety_compliance_status]',
                                fire_safety_compliance_status));
                            $newEntry.append(createHidden('warehouses[' + idx + '][fire_noc_certificate_number]',
                                fire_noc_certificate_number));
                            $newEntry.append(createHidden('warehouses[' + idx + '][safety_equipment_available]',
                                safety_equipment_available));
                            $newEntry.append(createHidden('warehouses[' + idx + '][fire_noc_expiry_date]',
                                fire_noc_expiry_date));
                            $newEntry.append(createHidden('warehouses[' + idx + '][last_safety_inspection_date]',
                                last_safety_inspection_date));

                            // handle files (if any selected, detach and rename; otherwise preserve existing)
                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var $f = $fileInput.detach();
                                // remove id to avoid collisions with modal input selector
                                $f.removeAttr('id');
                                $f.attr('name', 'warehouses[' + idx + '][contact_documents][]');
                                $newEntry.append($('<div class="file-holder d-none"></div>').append($f));

                                var names = [];
                                if ($f[0].files && $f[0].files.length) {
                                    for (var k = 0; k < $f[0].files.length; k++) names.push($f[0].files[k].name);
                                }
                                $newEntry.data('existing-file-names', names.join(', '));
                            } else {
                                var $existingHolder = $oldEntry.find('.file-holder').first();
                                if ($existingHolder.length) $newEntry.append($existingHolder);
                                var existingNames = $oldEntry.data('existing-file-names');
                                if (existingNames) $newEntry.data('existing-file-names', existingNames);
                            }

                            $oldEntry.replaceWith($newEntry);

                            // Determine canonical contact person text to store in warehouses array
                            var cps = contact_person_text || $newEntry.data('contact_person_text') || $oldEntry.data('contact_person_text') || '';

                            warehouses[idx] = {
                                // warehouse_code: warehouse_code,
                                warehouse_name: warehouse_name,
                                warehouse_country: warehouse_country,
                                warehouse_state_id: warehouse_state_id,
                                warehouse_state: warehouse_state,
                                warehouse_city_id: warehouse_city_id,
                                warehouse_city: warehouse_city,
                                warehouse_area: warehouse_area,
                                warehouse_building_name: warehouse_building_name,
                                warehouse_flat_office_no: warehouse_flat_office_no,
                                contact_salutation: contact_salutation,
                                contact_first_name: contact_first_name,
                                contact_last_name: contact_last_name,
                                contact_mobile: contact_mobile,
                                contact_email: contact_email,
                                contact_designation: contact_designation,
                                // new contact person fields
                                contact_person_id: contact_person_id,
                                contact_person_text: cps,
                                fire_safety_compliance_status: fire_safety_compliance_status,
                                fire_noc_certificate_number: fire_noc_certificate_number,
                                safety_equipment_available: safety_equipment_available,
                                fire_noc_expiry_date: fire_noc_expiry_date,
                                last_safety_inspection_date: last_safety_inspection_date,
                                file_id: fileId
                            };

                            var $row = $('#warehouseTableBody').find('.warehouse-row').eq(idx);
                            if ($row.length) {
                                // $row.find('td').eq(0).text(warehouse_code);
                                $row.find('td').eq(0).text(warehouse_name);
                                $row.find('td').eq(1).text([warehouse_building_name, warehouse_area].filter(Boolean)
                                    .join(', '));
                                $row.find('td').eq(2).text(warehouse_city || '—');
                                // show country in table
                                $row.find('td').eq(3).text(warehouse_country || '—');
                                $row.find('td').eq(4).text(warehouse_state || '—');
                                // Determine contact person display: prefer recently selected text, then preserved data on the new entry or old entry, then resolve from select option (if id present), otherwise fallback to manual fields
                                var cpText = contact_person_text || $newEntry.data('contact_person_text') || $oldEntry.data('contact_person_text') || (contact_person_id ? ($('#contact_person_name option[value="' + contact_person_id + '"]').text() || '').trim() : '');
                                var contactPersonDisplay = cpText || [contact_salutation, contact_first_name, contact_last_name, contact_mobile].filter(Boolean).join(' ').trim();
                                $row.find('td').eq(5).text(contactPersonDisplay);
                                $row.find('td').eq(6).text(fire_safety_compliance_status || '—');
                                $row.find('td').eq(7).text(fire_noc_certificate_number || '—');
                                $row.find('td').eq(8).text(fire_noc_expiry_date || '—');
                                $row.find('td').eq(9).text(safety_equipment_available || '—');
                                $row.find('td').eq(10).text(last_safety_inspection_date || '—');
                                $row.find('td').eq(11).text($newEntry.data('existing-file-names') || '—');
                            }
                        } else {
                            var whIndex = $('#warehouseTableBody').find('.warehouse-row').length || 0;
                            var fileId = Date.now() + Math.floor(Math.random() * 1000);

                            var $entry = $('<div class="warehouse-entry d-none" data-warehouse-index="' + whIndex +
                                '" data-file-id="' + fileId + '"></div>');
                            // $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_code]', warehouse_code));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_name]', warehouse_name));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_country_id]',
                                warehouse_country_id));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_country]',
                                warehouse_country));
                            // store both id and display name for state/city
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_state_id]',
                                warehouse_state_id));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_state]',
                                warehouse_state));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_city_id]',
                                warehouse_city_id));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_city]', warehouse_city));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_area]', warehouse_area));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_building_name]',
                                warehouse_building_name));
                            $entry.append(createHidden('warehouses[' + whIndex + '][warehouse_flat_office_no]',
                                warehouse_flat_office_no));

                            // New: contact person id + text
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_person_name]', contact_person_id));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_person_name_text]', contact_person_text));
                            // store client-side copy for immediate usage
                            if (contact_person_text) $entry.data('contact_person_text', contact_person_text);

                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_salutation]',
                                contact_salutation));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_first_name]',
                                contact_first_name));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_last_name]',
                                contact_last_name));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_mobile]', contact_mobile));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_email]', contact_email));
                            $entry.append(createHidden('warehouses[' + whIndex + '][contact_designation]',
                                contact_designation));
                            $entry.append(createHidden('warehouses[' + whIndex + '][fire_safety_compliance_status]',
                                fire_safety_compliance_status));
                            $entry.append(createHidden('warehouses[' + whIndex + '][fire_noc_certificate_number]',
                                fire_noc_certificate_number));
                            $entry.append(createHidden('warehouses[' + whIndex + '][safety_equipment_available]',
                                safety_equipment_available));
                            $entry.append(createHidden('warehouses[' + whIndex + '][fire_noc_expiry_date]',
                                fire_noc_expiry_date));
                            $entry.append(createHidden('warehouses[' + whIndex + '][last_safety_inspection_date]',
                                last_safety_inspection_date));

                            if ($fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0) {
                                var $f = $fileInput.detach();
                                // remove id to avoid collisions with modal input selector
                                $f.removeAttr('id');
                                $f.attr('name', 'warehouses[' + whIndex + '][contact_documents][]');
                                $entry.append($('<div class="file-holder d-none"></div>').append($f));

                                var names = [];
                                for (var k = 0; k < $f[0].files.length; k++) names.push($f[0].files[k].name);
                                $entry.data('existing-file-names', names.join(', '));
                            }

                            $container.append($entry);

                            var row = $('<tr class="warehouse-row" data-warehouse-index="' + whIndex + '"></tr>');
                            // row.append($('<td>').text(warehouse_code));
                            row.append($('<td>').text(warehouse_name));
                            row.append($('<td>').text([warehouse_building_name, warehouse_area].filter(Boolean).join(
                                ', ')));
                            row.append($('<td>').text(warehouse_city || '—'));
                            row.append($('<td>').text(warehouse_country || '—'));
                            row.append($('<td>').text(warehouse_state || '—'));
                            var cpTextNew = contact_person_text || (contact_person_id ? ($('#contact_person_name option[value="' + contact_person_id + '"]').text() || '').trim() : '');
                            var contactPersonDisplayNew = cpTextNew || [contact_salutation, contact_first_name, contact_last_name, contact_mobile].filter(Boolean).join(' ').trim();
                            if (cpTextNew) $entry.data('contact_person_text', cpTextNew);
                            row.append($('<td>').text(contactPersonDisplayNew));
                            row.append($('<td class="text-center">').text(fire_safety_compliance_status || '—'));
                            row.append($('<td>').text(fire_noc_certificate_number || '—'));
                            row.append($('<td>').text(fire_noc_expiry_date || '—'));
                            row.append($('<td>').text(safety_equipment_available || '—'));
                            row.append($('<td>').text(last_safety_inspection_date || '—'));
                            row.append($('<td>').text(($entry && $entry.data('existing-file-names')) ? $entry.data(
                                'existing-file-names') : '—'));

                            var actions = $(
                                '<td class="text-center d-flex justify-content-center align-items-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openWarehouseModal(' +
                                whIndex +
                                ')"><i class="ico icon-outline-pen-2" style="font-size:16px"></i></button>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-warehouse" onclick="removeWarehouseByIndex(' +
                                whIndex +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );

                            row.append(actions);

                            $('#warehouseTableBody').find('.no-warehouse-row').remove();
                            $('#warehouseTableBody').append(row);

                            warehouses.push({
                                // warehouse_code: warehouse_code,
                                warehouse_name: warehouse_name,
                                warehouse_country: warehouse_country,
                                warehouse_state_id: warehouse_state_id,
                                warehouse_state: warehouse_state,
                                warehouse_city_id: warehouse_city_id,
                                warehouse_city: warehouse_city,
                                warehouse_area: warehouse_area,
                                warehouse_building_name: warehouse_building_name,
                                warehouse_flat_office_no: warehouse_flat_office_no,
                                contact_salutation: contact_salutation,
                                contact_first_name: contact_first_name,
                                contact_last_name: contact_last_name,
                                contact_mobile: contact_mobile,
                                contact_email: contact_email,
                                contact_designation: contact_designation,
                                // new contact person fields
                                contact_person_id: contact_person_id,
                                contact_person_text: contact_person_text,
                                fire_safety_compliance_status: fire_safety_compliance_status,
                                fire_noc_certificate_number: fire_noc_certificate_number,
                                safety_equipment_available: safety_equipment_available,
                                fire_noc_expiry_date: fire_noc_expiry_date,
                                last_safety_inspection_date: last_safety_inspection_date,
                                file_id: fileId
                            });
                        }

                        // reset modal
                        resetContactDocumentsInput();
                        clearWarehouseForm();
                        if (typeof bootstrap !== 'undefined') {
                            var bs = bootstrap.Modal.getInstance(document.getElementById('warehouseModal'));
                            if (bs) bs.hide();
                        } else if (typeof $ !== 'undefined') {
                            $('#warehouseModal').modal('hide');
                        }

                        editingWarehouseIndex = -1;
                    });

                    function removeWarehouseByIndex(index) {
                        var idx = parseInt(index, 10);
                        $('#warehouseTableBody').find('.warehouse-row').eq(idx).remove();
                        var $container = $('#warehouses-container');
                        $container.find('.warehouse-entry').eq(idx).remove();

                        if (typeof warehouses.splice === 'function') warehouses.splice(idx, 1);

                        $container.find('.warehouse-entry').each(function(i) {
                            $(this).attr('data-warehouse-index', i);
                            $(this).find('input[type="hidden"]').each(function() {
                                var name = $(this).attr('name') || '';
                                var newName = name.replace(/warehouses\[\d+\]/, 'warehouses[' + i + ']');
                                $(this).attr('name', newName);
                            });

                            $(this).find('input[type="file"]').each(function() {
                                var fname = $(this).attr('name') || '';
                                if (fname.indexOf('[contact_documents]') !== -1) {
                                    var newFname = fname.replace(/warehouses\[\d+\]\[contact_documents\]\[\]/,
                                        'warehouses[' + i + '][contact_documents][]');
                                    $(this).attr('name', newFname);
                                }
                            });
                        });

                        $('#warehouseTableBody').find('.warehouse-row').each(function(i) {
                            $(this).attr('data-warehouse-index', i);
                            $(this).find('button').each(function() {
                                var on = $(this).attr('onclick');
                                if (!on) return;
                                on = on.replace(/\(\d+\)/, '(' + i + ')');
                                $(this).attr('onclick', on);
                            });
                        });

                        if ($('#warehouseTableBody').find('.warehouse-row').length === 0) {
                            $('#warehouseTableBody').append(
                                '<tr class="no-warehouse-row"><td colspan="12" class="text-center text-muted">No warehouses added yet.</td></tr>'
                            );
                        }
                    }

                    window.openWarehouseModal = openWarehouseModal;
                    window.removeWarehouseByIndex = removeWarehouseByIndex;

                    $(document).on('click', '#addWarehouseBtn', function() {
                        openWarehouseModal();
                    });

                    // show selected filenames in modal hint when user chooses files (immediate feedback)
                    $(document).on('change', '#contact_documents', function() {
                        var names = [];
                        var files = this.files || [];
                        for (var i = 0; i < files.length; i++) names.push(files[i].name);
                        if (names.length) {
                            $('#warehouse_docs_hint').text('Selected: ' + names.join(', '));
                        } else {
                            // if none selected, revert to existing hint for the entry being edited
                            var idx = typeof editingWarehouseIndex === 'number' ? editingWarehouseIndex : -1;
                            var namesExist = '';
                            if (idx >= 0) {
                                var $entry = $('#warehouses-container .warehouse-entry').eq(idx);
                                if ($entry.length) namesExist = $entry.data('existing-file-names') || '';
                            }
                            $('#warehouse_docs_hint').text(namesExist ? 'Existing: ' + namesExist +
                                ' — leave empty to keep' : '');
                        }
                    });

                    function loadExistingWarehouses() {
                        var $container = $('#warehouses-container');
                        if (!$container.length) return;
                        $container.find('.warehouse-entry').each(function(i) {
                            var $e = $(this);
                            var getVal = function(key) {
                                var inp = $e.find('input[name$="[' + key + ']"]').first();
                                return inp.length ? inp.val() : '';
                            };
                            // var warehouse_code = getVal('warehouse_code');
                            var warehouse_name = getVal('warehouse_name');
                            var warehouse_country_name = getVal('warehouse_country');
                            var warehouse_country_id = getVal('warehouse_country_id') || (/^\d+$/.test(
                                warehouse_country_name) ? warehouse_country_name : '');
                            var warehouse_country = '';
                            if (warehouse_country_id) {
                                var $opt = $('#warehouse_country option[value="' + warehouse_country_id + '"]');
                                warehouse_country = $opt.length ? $opt.text().trim() : warehouse_country_name;
                            } else {
                                warehouse_country = warehouse_country_name;
                                // try to find id by matching name to option text
                                var $match = $('#warehouse_country option').filter(function() {
                                    return $(this).text().trim() === warehouse_country_name;
                                }).first();
                                if ($match.length) warehouse_country_id = $match.val();
                            }

                            var warehouse_state = getVal('warehouse_state');
                            var warehouse_state_id = getVal('warehouse_state_id') || getVal('warehouse_state');
                            var warehouse_city = getVal('warehouse_city');
                            var warehouse_city_id = getVal('warehouse_city_id') || getVal('warehouse_city');

                            // if only id was rendered for state/city, try to get display text from corresponding selects
                            try {
                                if ((!warehouse_state || warehouse_state === warehouse_state_id) &&
                                    warehouse_state_id) {
                                    var st = $('#warehouse_state option[value="' + warehouse_state_id + '"]').text() ||
                                        '';
                                    if (st) warehouse_state = st.trim();
                                }
                                if ((!warehouse_city || warehouse_city === warehouse_city_id) && warehouse_city_id) {
                                    var ct = $('#warehouse_city option[value="' + warehouse_city_id + '"]').text() ||
                                        '';
                                    if (ct) warehouse_city = ct.trim();
                                }
                            } catch (e) {}
                            var warehouse_area = getVal('warehouse_area');
                            var warehouse_building_name = getVal('warehouse_building_name');
                            var warehouse_flat_office_no = getVal('warehouse_flat_office_no');
                            var contact_salutation = getVal('contact_salutation');
                            var contact_first_name = getVal('contact_first_name');
                            var contact_last_name = getVal('contact_last_name');
                            var contact_mobile = getVal('contact_mobile');
                            var contact_email = getVal('contact_email');
                            var contact_designation = getVal('contact_designation');
                            // load contact person id/text if present
                            var contact_person_id = getVal('contact_person_name');
                            var contact_person_text = getVal('contact_person_name_text') || '';
                            // if text missing but we have an id, try to resolve option text from select
                            if (!contact_person_text && contact_person_id) {
                                var $opt = $('#contact_person_name option[value="' + contact_person_id + '"]');
                                contact_person_text = $opt.length ? $opt.text().trim() : contact_person_id;
                            }
                            var fire_safety_compliance_status = getVal('fire_safety_compliance_status');
                            var fire_noc_certificate_number = getVal('fire_noc_certificate_number');
                            var safety_equipment_available = getVal('safety_equipment_available');
                            var fire_noc_expiry_date = getVal('fire_noc_expiry_date');
                            var last_safety_inspection_date = getVal('last_safety_inspection_date');
                            var fileId = $e.data('file-id') || Date.now() + i;
                            var existingNames = $e.data('existing-file-names') || '';

                            warehouses.push({
                                // warehouse_code: warehouse_code,
                                warehouse_name: warehouse_name,
                                warehouse_country: warehouse_country,
                                warehouse_state_id: warehouse_state_id,
                                warehouse_state: warehouse_state,
                                warehouse_city_id: warehouse_city_id,
                                warehouse_city: warehouse_city,
                                warehouse_area: warehouse_area,
                                warehouse_building_name: warehouse_building_name,
                                warehouse_flat_office_no: warehouse_flat_office_no,
                                contact_salutation: contact_salutation,
                                contact_first_name: contact_first_name,
                                contact_last_name: contact_last_name,
                                contact_mobile: contact_mobile,
                                contact_email: contact_email,
                                contact_designation: contact_designation,
                                // contact person fields
                                contact_person_id: contact_person_id,
                                contact_person_text: contact_person_text,
                                fire_safety_compliance_status: fire_safety_compliance_status,
                                fire_noc_certificate_number: fire_noc_certificate_number,
                                safety_equipment_available: safety_equipment_available,
                                fire_noc_expiry_date: fire_noc_expiry_date,
                                last_safety_inspection_date: last_safety_inspection_date,
                                file_id: fileId
                            });

                            var row = $('<tr class="warehouse-row" data-warehouse-index="' + i + '"></tr>');
                            // row.append($('<td>').text(warehouse_code));
                            row.append($('<td>').text(warehouse_name));
                            row.append($('<td>').text([warehouse_building_name, warehouse_area].filter(Boolean).join(
                                ', ')));
                            row.append($('<td>').text(warehouse_city || '—'));
                            row.append($('<td>').text(warehouse_country || '—'));
                            row.append($('<td>').text(warehouse_state || '—'));
                            var contactDisplay = contact_person_text || [contact_salutation, contact_first_name, contact_last_name, contact_mobile].filter(Boolean).join(' ').trim();
                            row.append($('<td>').text(contactDisplay));
                            row.append($('<td class="text-center">').text(fire_safety_compliance_status || '—'));
                            row.append($('<td>').text(fire_noc_certificate_number || '—'));
                            row.append($('<td>').text(fire_noc_expiry_date || '—'));
                            row.append($('<td>').text(safety_equipment_available || '—'));
                            row.append($('<td>').text(last_safety_inspection_date || '—'));
                            row.append($('<td>').text(existingNames || '—'));

                            var actions = $('<td class="text-center"></td>');
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light me-1" onclick="openWarehouseModal(' +
                                i +
                                ')"><i class="ico icon-outline-pen-2 text-dark" style="font-size:16px"></i></button>'
                            );
                            actions.append(
                                '<button type="button" class="btn btn-sm btn-light btn-delete-warehouse" onclick="removeWarehouseByIndex(' +
                                i +
                                ')"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                            );
                            row.append(actions);

                            $('#warehouseTableBody').find('.no-warehouse-row').remove();
                            $('#warehouseTableBody').append(row);
                        });
                    }

                    // run loader on ready
                    $(document).ready(function() {
                        loadExistingWarehouses();

                        // Initialize date pickers (flatpickr preferred, fallback to jQuery UI datepicker)
                        try {
                            if (typeof flatpickr !== 'undefined') {
                                flatpickr('.date-picker', {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true
                                });
                            } else if ($.fn.datepicker) {
                                $('.date-picker').not('.hasDatepicker').datepicker({
                                    dateFormat: 'dd/mm/yy'
                                });
                            }
                        } catch (e) {
                            // silent fail - date-picker polyfills not available
                        }
                    });

                    // Close any open modal on Escape keypress (fallback if Bootstrap keyboard option is not enabled)
                    $(document).on('keydown', function(e) {
                        if (e.key === 'Escape' || e.keyCode === 27) {
                            $('.modal.show').each(function() {
                                try {
                                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal && bootstrap.Modal
                                        .getInstance) {
                                        var inst = bootstrap.Modal.getInstance(this);
                                        if (inst) inst.hide();
                                        else $(this).modal('hide');
                                    } else {
                                        $(this).modal('hide');
                                    }
                                } catch (err) {
                                    $(this).modal('hide');
                                }
                            });
                        }
                    });

                })();


                /* =====================================================================
                       INDUSTRY → BUSINESS SECTOR
                       Note: Business Entity Type is completely independent from Industry Type.
                       Only Industry Type should trigger Business Sector dropdown changes.
                    ===================================================================== */


                /* =====================================================================
                   ON LOAD → populate Business Sector for existing company
                ===================================================================== */
                $(document).ready(function() {


                    /* =====================================================================
                       COUNTRY → STATE
                    ===================================================================== */
                    $(document).on("change", "#country_company", function() {

                        let id = $(this).val();
                        $("#state").html("<option>Loading...</option>");

                        $.get("{{ url('/get_state_company') }}?country_id=" + id, function(res) {
                            $("#state").empty().append('<option value="">Select</option>');
                            let states = Array.isArray(res[0]) ? res[0] : res;
                            states.forEach(s => {
                                $("#state").append(`<option value="${s.id}">${s.name}</option>`);
                            });
                        });
                    });


                    // Handle country change for warehouse
                    $(document).on('change', '#warehouse_country', function() {
                        let countryId = $(this).val();
                        $('#warehouse_state').html('<option value="">Loading...</option>');

                        if (countryId) {
                            $.get('{{ url('/get_state_company') }}?country_id=' + countryId, function(res) {
                                $('#warehouse_state').empty().append(
                                    '<option value="">Select State</option>');
                                let states = Array.isArray(res[0]) ? res[0] : res;
                                states.forEach(s => {
                                    $('#warehouse_state').append(
                                        `<option value="${s.id}">${s.name}</option>`);
                                });
                            }).fail(function() {
                                $('#warehouse_state').html(
                                    '<option value="">Error loading states</option>');
                            });
                        } else {
                            $('#warehouse_state').html('<option value="">Select State</option>');
                        }
                    });


                    $(document).on("change", "#industry_type_id", function() {

                        let id = $(this).val();
                        let sector = $("#business_sector_id");

                        sector.html('<option>Loading...</option>');

                        $.get("{{ url('get-business-sector') }}/" + id, function(res) {
                            sector.html('<option value="">Select Sector</option>');
                            res.forEach(s => {
                                sector.append(`<option value="${s.id}">${s.name}</option>`);
                            });
                        });
                    });

                    try {
                        let selectedSector = "{{ old('business_sector_id', $company->business_sector_id ?? '') }}";
                        let industryId = $('#industry_type_id').val();
                        if (industryId) {
                            let sector = $("#business_sector_id");
                            sector.html('<option>Loading...</option>');
                            $.get("{{ url('get-business-sector') }}/" + industryId, function(res) {
                                sector.html('<option value="">Select Sector</option>');
                                res.forEach(s => {
                                    let isSel = (String(s.id) === String(selectedSector)) ? 'selected' : '';
                                    sector.append(`<option value="${s.id}" ${isSel}>${s.name}</option>`);
                                });
                            });
                        }
                    } catch (e) {
                        console.error(e);
                    }
                });
            </script>


        </div>


        <div class="modal fade" id="addShiftModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="documentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" style="top:10%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentModalLabel">Add Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                                    <input type="time" class="form-control form-control-sm"
                                        id="add_shift_start_time" name="add_shift_start_time" placeholder="">
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
                    // $btn.prop('disabled', true).append(
                    //     '<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>'
                    // );

                    // $.ajax({
                    //     url: '{{ url('/company/working-shifts/store') }}',
                    //     method: 'POST',
                    //     data: {
                    //         _token: '{{ csrf_token() }}',
                    //         shift_name: name,
                    //         start_time: start,
                    //         end_time: end
                    //     },
                    //     success: function(res) {
                    //         if (res && res.ok) {
                    //             var s = res.shift;
                    //             var text = s.shift_name + ' (' + s.start_time + ' - ' + s.end_time +
                    //                 ')';
                    //             var $select = $('[name="shift_id"]');
                    //             // append and select new option
                    //             $select.append(new Option(text, s.id, true, true));
                    //             $select.trigger('change');
                    //             // close modal and reset
                    //             $('#addShiftModal').modal('hide');
                    //             $('#add_shiftname,#add_shift_start_time,#add_shift_end_time').val(
                    //                 '');

                    //             // small inline toast fallback
                    //             var $msg = $(
                    //                 '<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Shift added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                    //             );
                    //             $('.modal-body').first().prepend($msg);
                    //             setTimeout(function() {
                    //                 $msg.alert('close');
                    //             }, 3000);

                    //         } else if (res && res.errors) {
                    //             $.each(res.errors, function(k, v) {
                    //                 var msg = Array.isArray(v) ? v[0] : v;
                    //                 if (k === 'shift_name') $('#add_shiftname').after(
                    //                     '<div class="text-danger mt-1 shift-error">' +
                    //                     msg + '</div>');
                    //                 if (k === 'start_time') $('#add_shift_start_time')
                    //                     .after(
                    //                         '<div class="text-danger mt-1 shift-error">' +
                    //                         msg + '</div>');
                    //                 if (k === 'end_time') $('#add_shift_end_time').after(
                    //                     '<div class="text-danger mt-1 shift-error">' +
                    //                     msg + '</div>');
                    //             });
                    //         } else {
                    //             alert('Could not add shift. Please try again.');
                    //         }
                    //     },
                    //     error: function(xhr) {
                    //         if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                    //             .errors) {
                    //             var errs = xhr.responseJSON.errors;
                    //             $.each(errs, function(k, v) {
                    //                 var msg = v[0];
                    //                 if (k === 'shift_name') $('#add_shiftname').after(
                    //                     '<div class="text-danger mt-1 shift-error">' +
                    //                     msg + '</div>');
                    //                 if (k === 'start_time') $('#add_shift_start_time')
                    //                     .after(
                    //                         '<div class="text-danger mt-1 shift-error">' +
                    //                         msg + '</div>');
                    //                 if (k === 'end_time') $('#add_shift_end_time').after(
                    //                     '<div class="text-danger mt-1 shift-error">' +
                    //                     msg + '</div>');
                    //             });
                    //         } else {
                    //             alert('Server error. Please try again later.');
                    //         }
                    //     },
                    //     complete: function() {
                    //         $btn.prop('disabled', false);
                    //         $btn.find('.spinner-border').remove();
                    //     }
                    // });


                    
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

        <!-- Industry Type add modal (small side panel, AJAX) -->
        <div class="modal side-panel  fade" id="industryTypeAddModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Industry</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">

                        <label class="form-label">Industry Name <span class="text-danger">*</span></label>
                        <input type="text" id="industry_type_name" name="name" class="form-control" required
                            autocomplete="off" style="padding: 2px 5px;">

                        <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                            <button type="button" id="saveIndustryType" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '#saveIndustryType', function() {
                var $btn = $(this);
                var $input = $('#industry_type_name');
                var val = ($input.val() || '').trim();

                // clear previous validation
                $input.removeClass('is-invalid');
                if ($input.next('.invalid-feedback').length === 0) {
                    $input.after('<div class="invalid-feedback d-block" style="display:none"></div>');
                }
                $input.next('.invalid-feedback').hide().text('');

                if (!val) {
                    $input.addClass('is-invalid');
                    $input.next('.invalid-feedback').show().text('Industry name is required');
                    return;
                }

                $.ajax({
                    url: "{{ url('industry-store-ajax') }}",
                    type: 'POST',
                    data: {
                        name: val,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                        $btn.append(
                            '<span class="spinner-border spinner-border-sm ms-2" id="industryTypeLoader" role="status" aria-hidden="true"></span>'
                        );
                    },
                    success: function(res) {
                        if (res && res.status) {
                            var $sel = $('#industry_type_id');
                            if ($sel.length) {
                                var option = $('<option>').val(res.data.id).text(res.data.name).prop(
                                    'selected', true);
                                $sel.append(option);
                                if ($sel.hasClass('js-example-basic-single')) {
                                    $sel.trigger('change.select2');
                                } else {
                                    $sel.trigger('change');
                                }
                            }

                            var $sel2 = $('#industry_id_modal');
                            if ($sel2.length) {
                                var option2 = $('<option>').val(res.data.id).text(res.data.name).prop(
                                    'selected', true);
                                $sel2.append(option2);
                                if ($sel2.hasClass('js-example-basic-single')) {
                                    $sel2.trigger('change.select2');
                                } else {
                                    $sel2.trigger('change');
                                }
                            }

                            $('#industryTypeAddModal').modal('hide');
                            $input.val('');

                            toastr.success(res.message || 'Added', 'Success');
                        } else {
                            toastr.error(res.message || 'Operation failed', 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            if (errors.name) {
                                $input.addClass('is-invalid');
                                $input.next('.invalid-feedback').show().text(errors.name[0]);
                            }
                        } else {
                            toastr.error('Something went wrong', 'Error');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('#industryTypeLoader').remove();
                    }
                });
            });
        </script>



        <!-- Industry Type add modal (small side panel, AJAX) -->
        <div class="modal side-panel  fade" id="addBusinessSector" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Business Sector</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">

                        <label class="form-label">Industry <span class="text-danger">*</span></label>
                        <select class="form-control js-example-basic-single" id="industry_id_modal"
                            name="industry_id">
                            <option value="">Select Industry</option>
                            @foreach ($industries as $ind)
                                <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                            @endforeach
                        </select>


                        <label class="form-label mt-3">Business Sector <span class="text-danger">*</span></label>
                        <input id="business_sector_name" type="text" class="form-control" name="name">

                        <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                            <button type="button" id="saveBusinessSector" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '#saveBusinessSector', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $input = $('#business_sector_name');
                var name = ($input.val() || '').trim();
                var industryId = $('#industry_id_modal').val() || '';

                // clear validation
                $input.removeClass('is-invalid');
                if ($input.next('.invalid-feedback').length === 0) {
                    $input.after('<div class="invalid-feedback d-block" style="display:none"></div>');
                }
                $input.next('.invalid-feedback').hide().text('');

                if (!industryId) {
                    alert('Please select an Industry');
                    return;
                }
                if (!name) {
                    $input.addClass('is-invalid');
                    $input.next('.invalid-feedback').show().text('Business Sector name is required');
                    return;
                }

                $.ajax({
                    url: "{{ url('business-activity-store-ajax') }}",
                    type: 'POST',
                    data: {
                        industry_id: industryId,
                        name: name,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true);
                        $btn.append(
                            '<span class="spinner-border spinner-border-sm ms-2" id="businessSectorLoader" role="status" aria-hidden="true"></span>'
                        );
                    },
                    success: function(res) {
                        if (res && res.status) {
                            var $sel = $('#business_sector_id');
                            if ($sel.length) {
                                var option = $('<option>').val(res.data.id).text(res.data.name).prop(
                                    'selected', true);
                                $sel.append(option);
                                if ($sel.hasClass('js-example-basic-single')) {
                                    $sel.trigger('change.select2');
                                } else {
                                    $sel.trigger('change');
                                }
                            }

                            $('#addBusinessSector').modal('hide');
                            $input.val('');

                            toastr.success(res.message || 'Added', 'Success');
                        } else {
                            toastr.error(res.message || 'Operation failed', 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            if (errors.name) {
                                $input.addClass('is-invalid');
                                $input.next('.invalid-feedback').show().text(errors.name[0]);
                            }
                            if (errors.industry_id) {
                                alert(errors.industry_id[0]);
                            }
                        } else {
                            toastr.error('Something went wrong', 'Error');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('#businessSectorLoader').remove();
                    }
                });
            });
        </script>


        {{-- Form Validation Script --}}
        <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Initialize form validation for companyAllForm
                FormValidator.init('companyAllForm', {
                    showAllErrors: true,
                    scrollToFirst: true,
                    highlightFields: true,
                    toastrPosition: 'toast-top-right',
                    toastrTimeout: 6000
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
                            /email|mobile|phone|password|other_code|iban_number|swift_code|currency|docs\[joining\]\[passport_visa\]\[number\]|docs\[joining\]\[iban_letter\]\[number\]|docs\[joining\]\[prof_certs\]\[number\]|docs\[joining\]\[academic\]\[number\]/
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
                    var $form = $('#companyAllForm');
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

                        // If there is no next field, focus the main Save button
                        if (!found) {
                            $('#btnSaveAll').focus();
                        }

                    });

                    // Only allow form submission when Save buttons are clicked (not via Enter)
                    $('#btnSaveAll, #btnSaveAllBottom').on('click', function() {
                        allowSubmit = true;
                        // submit the form programmatically in case it's blocked elsewhere
                        setTimeout(function() {
                            $form.trigger('submit');
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
                        if (this.id === 'companyAllForm' && !allowSubmit) {
                            // Ignore programmatic submit unless allowed
                            return;
                        }
                        nativeSubmit.apply(this, arguments);
                    };
                })();
            });
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

    @endsection

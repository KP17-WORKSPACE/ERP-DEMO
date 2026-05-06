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

        .select2-container {
            width: 100% !important
        }

        /* Inactive tabs */
        .nav-tabs .nav-link {
            color: #727272;
            /* grey text */
        }

        /* Active tab */
        .nav-tabs .nav-link.active {
            background-color: #dff0d8;
            /* light green */
            color: #000;
            /* black text */
            border-color: #dee2e6 #dee2e6 #fff;
            /* keep clean border */
        }
    </style>

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <div class="form-scroll">
        <div class="content-container col-12">
            <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                <div class="" role="tabpanel" aria-labelledby="data-tab" id="companyApp">
                    <div class="purchase-order-content-header">
                        <h4 class="purchase-order-content-header-left">
                            <span>
                                Add Company (CID-@{{ form.company_id }})
                            </span>
                        </h4>

                        <span id="saveAllMsg" class="ms-2"></span>

                        <div class="purchase-order-content-header-right d-flex align-items-center gap-1">

                            <!-- Save Button -->
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2"
                                @click="submitAll" :disabled="loading">

                                <span class="spinner-border spinner-border-sm" v-show="loading" role="status"
                                    aria-hidden="true">
                                </span>

                                <i class="ico icon-outline-bookmark-opened text-success" v-show="!loading"></i>

                                <span>@{{ loading ? 'Saving...' : 'Save' }}</span>
                            </button>

                            <!-- Flash message -->
                            <span class="ms-2 text-success">@{{ flash }}</span>

                            <!-- Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>

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
                            </div>


                        </div>
                    </div>


                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form @submit.prevent="submit" enctype="multipart/form-data">
                                        @csrf
                                        <!-- ===================== BASIC COMPANY INFO ===================== -->
                                        <div class="row gy-2">
                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">Company Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="company_name" v-model.trim="form.company_name" required>
                                                    <small class="text-danger">@{{ err('company_name') }}</small>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">Trade Name</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="trade_name" v-model.trim="form.trade_name">
                                                    <small class="text-danger">@{{ err('trade_name') }}</small>
                                                </div>
                                            </div>


                                            <div class="col-lg-2">
                                                <div class="input-effect">

                                                    <label
                                                        class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                        <span>Business Entity Type</span>

                                                        <!-- SMALL ADD ICON -->
                                                        <button type="button" class="btn btn-sm p-0 ms-2"
                                                            style="border:none;background:none;"
                                                            title="Add Business Entity Type" data-bs-toggle="modal"
                                                            data-bs-target="#entityTypeAddModal">
                                                            <i class="ico icon-outline-add-square text-success"
                                                                style="font-size:18px;"></i>
                                                        </button>
                                                    </label>

                                                    <select class="form-control form-control-sm" name="legal_entity_type"
                                                        v-model.trim="form.legal_entity_type" required>
                                                        <option value="">Select Business Entity Type</option>

                                                        @foreach ($entities as $ent)
                                                            <option value="{{ $ent->id }}">{{ $ent->name }}</option>
                                                        @endforeach
                                                    </select>

                                                    <small class="text-danger">@{{ err('legal_entity_type') }}</small>
                                                </div>
                                            </div>





                                            <div class="col-lg-2">
                                                <div class="input-effect">

                                                    <label
                                                        class="form-label mb-1 d-flex justify-content-between align-items-center">
                                                        <span>Industry Type</span>

                                                        <!-- SMALL ADD ICON -->
                                                        <button type="button" class="btn btn-sm p-0 ms-2"
                                                            style="border:none;background:none;" title="Add Industry"
                                                            data-bs-toggle="modal" data-bs-target="#industryAddPopup">
                                                            <i class="ico icon-outline-add-square text-success"
                                                                style="font-size:18px;"></i>
                                                        </button>
                                                    </label>

                                                    <select class="form-control form-control-sm" v-model="form.industry"
                                                        @change="loadActivities" required>
                                                        <option value="">Select Industry</option>
                                                        <option v-for="ind in industries" :key="ind.id"
                                                            :value="ind.id">
                                                            @{{ ind.name }}
                                                        </option>
                                                    </select>

                                                    <small class="text-danger">@{{ errors.industry_id }}</small>
                                                </div>
                                            </div>



                                            <div class="col-lg-2">
                                                <div class="input-effect">

                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label mb-0">Business Sector</label>

                                                        <!-- ADD ICON NEXT TO LABEL -->
                                                        <button type="button" class="btn btn-sm p-0"
                                                            style="border:none;background:none;" data-bs-toggle="modal"
                                                            data-bs-target="#activityModal">
                                                            <i class="ico icon-outline-add-square text-success"
                                                                style="font-size:17px;"></i>
                                                        </button>
                                                    </div>

                                                    <select class="form-control form-control-sm"
                                                        v-model="form.business_activity_id" required>
                                                        <option value="">Select Business Activity</option>
                                                        <option v-for="act in filteredActivities" :key="act.id"
                                                            :value="act.id">
                                                            @{{ act.name }}
                                                        </option>
                                                    </select>

                                                    <small class="text-danger">@{{ errors.business_activity_id }}</small>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Company Code</label>
                                                <input type="text   " class="form-control form-control-sm"
                                                    name="digital_stamp" accept="image/*"
                                                    @change="onFile($event,'digital_stamp')">
                                                <small class="text-danger">@{{ errors.digital_stamp }}</small>
                                            </div>


                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Company Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="company_type"
                                                    v-model="form.company_type" @change="onCompanyTypeChange">

                                                    <option value="">Select Type</option>
                                                    <option value="parent">Parent Company</option>
                                                    <option value="subsidiary">Subsidiary Company</option>
                                                    <option value="branch">Branch</option>
                                                </select>

                                                <small class="text-danger">@{{ errors.company_type }}</small>
                                            </div>


                                            <!-- If parent → show company name (auto or manual) -->
                                            <div class="col-lg-2" v-if="isParent">
                                                <label class="form-label mb-1">Company Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="company_name" v-model="form.company_name">
                                            </div>

                                            <!-- If NOT parent → show parent company dropdown -->
                                            <div class="col-lg-2" v-if="!isParent">
                                                <label class="form-label mb-1">Select Main Company <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm"
                                                    v-model="form.parent_company_id">

                                                    <option value="">Select Company</option>
                                                    <option v-for="comp in parentCompanies" :key="comp.id"
                                                        :value="comp.id">
                                                        @{{ comp.company_name }}
                                                    </option>

                                                </select>
                                            </div>




                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Company Logo</label>
                                                <input type="file" class="form-control form-control-sm"
                                                    name="company_logo" accept="image/*"
                                                    @change="onFile($event,'company_logo')">
                                                <small class="text-danger">@{{ errors.company_logo }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Digital Stamp</label>
                                                <input type="file" class="form-control form-control-sm"
                                                    name="digital_stamp" accept="image/*"
                                                    @change="onFile($event,'digital_stamp')">
                                                <small class="text-danger">@{{ errors.digital_stamp }}</small>
                                            </div>

                                            <div class="col-lg-2 mb-5">
                                                <label class="form-label mb-1">Company Profile</label>
                                                <input type="file" class="form-control form-control-sm"
                                                    name="company_profile" accept=".pdf,.doc,.docx,.txt"
                                                    @change="onFile($event,'company_profile')">
                                                <small class="text-danger">@{{ errors.company_profile }}</small>
                                            </div>


                                        </div>
                                </div>


                                <!-- ===================== TABS (you already have nav tabs markup) ===================== -->
                                <div class="tab-wrap mb-3">

                                    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="contact-info" data-bs-toggle="tab"
                                                data-bs-target="#contactinfo" type="button" role="tab"
                                                aria-controls="contact-info" aria-selected="true">
                                                Contact Information
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="compliance-regulatory-tab" data-bs-toggle="tab"
                                                data-bs-target="#compliance-regulatory" type="button" role="tab"
                                                aria-controls="compliance-regulatory" aria-selected="false">
                                                Company Registration
                                            </button>
                                        </li>


                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="edu-tab" data-bs-toggle="tab"
                                                data-bs-target="#Banking-Finance" type="button" role="tab"
                                                aria-controls="Banking-Finance" aria-selected="false">
                                                Banking & Finance
                                            </button>
                                        </li>
                                        {{-- <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="edu-tab" data-bs-toggle="tab"
                                                data-bs-target="#hr-payroll" type="button" role="tab"
                                                aria-controls="hr-payroll" aria-selected="false">
                                                HR & Payroll Setup
                                            </button>
                                        </li> --}}

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="edu-tab" data-bs-toggle="tab"
                                                data-bs-target="#Policies" type="button" role="tab"
                                                aria-controls="Policies" aria-selected="false">
                                                Company Policies
                                            </button>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="docs-tab" data-bs-toggle="tab"
                                                data-bs-target="#documentation" type="button" role="tab"
                                                aria-controls="documentation" aria-selected="false">
                                                Documentation
                                            </button>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="docs-tab" data-bs-toggle="tab"
                                                data-bs-target="#company-setting" type="button" role="tab"
                                                aria-controls="company-setting" aria-selected="false">
                                                Company Setting
                                            </button>
                                        </li>

                                        <!-- your other nav items exactly as before -->
                                    </ul>
                                </div>

                                <div class="tab-content">

                                                    <!-- ===================== CONTACT INFO TAB ===================== -->
                                                    <div class="tab-pane fade show active" id="contactinfo" role="tabpanel"
                                                        aria-labelledby="contact-info">

                                                        <div class="accordion" id="contactInfoAccordion">

                                                            <!-- 1️⃣ Accordion: Basic & Contact Information -->
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingBasic">
                                                                    <button class="accordion-button" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapseBasic"
                                                                        aria-expanded="true">
                                                                        1. Basic & Contact Information
                                                                    </button>
                                                                </h2>

                                                                <div id="collapseBasic" class="accordion-collapse collapse show"
                                                                    data-bs-parent="#contactInfoAccordion">
                                                                    <div class="accordion-body">

                                                                        <div class="row gy-2">


                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Company Email *</label>
                                                                                <input type="email" class="form-control form-control-sm"
                                                                                    v-model="form.email">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Company Website</label>
                                                                                <input type="url" class="form-control form-control-sm"
                                                                                    v-model="form.website">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Office Phone *</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.telephone">
                                                                            </div>

                                                                            {{-- <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Fax Number</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.fax">
                                                                            </div> --}}

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Mobile Number</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.mobile">
                                                                            </div>



                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Date of
                                                                                    Incorporation</label>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    v-model.trim="form.date_of_incorporation">
                                                                                     <small class="text-danger">@{{ err('date_of_incorporation') }}</small>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Country *</label>
                                                                                <select class="form-select form-select-sm"
                                                                                    v-model="form.country">
                                                                                    <option value="">Select Country</option>
                                                                                    @foreach ($country as $c)
                                                                                        <option value="{{ $c->id }}">
                                                                                            {{ $c->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">State *</label>
                                                                                <select class="form-select form-select-sm"
                                                                                    v-model="form.state">
                                                                                    <option value="">Select State</option>
                                                                                    <option v-for="s in states" :value="s.id">
                                                                                        @{{ s.name }}</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-lg-10">
                                                                                <label class="form-label mb-1">Registered Address *</label>
                                                                                <textarea class="form-control form-control-sm" rows="1" v-model="form.company_address"></textarea>
                                                                            </div>


                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                </div>

                                                                <!-- 2️⃣ Accordion: Social Media Links -->
                                                                <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingSocial">
                                                                    <button class="accordion-button collapsed" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapseSocial">
                                                                        2. Social Media Links
                                                                    </button>
                                                                </h2>

                                                                <div id="collapseSocial" class="accordion-collapse collapse"
                                                                    data-bs-parent="#contactInfoAccordion">
                                                                    <div class="accordion-body">

                                                                        <div class="row gy-2">

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">LinkedIn</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_linkedin">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Facebook</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_facebook">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Instagram</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_instagram">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">X / Twitter</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_twitter">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">YouTube</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_youtube">
                                                                            </div>

                                                                            <div class="col-lg-2">
                                                                                <label class="form-label mb-1">Other (optional)</label>
                                                                                <input type="text" class="form-control form-control-sm"
                                                                                    v-model="form.social_other">
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                </div>

                                                             <!-- 3️⃣ Accordion: Owner Details -->
                                                                <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingOwner">
                                                                    <button class="accordion-button collapsed" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapseOwner">
                                                                        3. Owner Details
                                                                    </button>
                                                                </h2>

                                                                <div id="collapseOwner" class="accordion-collapse collapse"
                                                                    data-bs-parent="#contactInfoAccordion">
                                                                   <div class="accordion-body">
                                                                <div v-for="(own, i) in people.owners" :key="i" class="row gy-2 mb-1 p-2 rounded align-items-end">

                                                                    <div class="col-lg-2">
                                                                    <label class="form-label mb-1">Name</label>
                                                                    <input type="text" class="form-control form-control-sm" v-model="own.name">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                    <label class="form-label mb-1">Mobile</label>
                                                                    <input type="text" class="form-control form-control-sm" v-model="own.mobile">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                    <label class="form-label mb-1">Email</label>
                                                                    <input type="email" class="form-control form-control-sm" v-model="own.email">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                    <label class="form-label mb-1">Passport Copy</label>
                                                                    <input type="file" class="form-control form-control-sm" @change="own.passport_copy = $event.target.files[0]">
                                                                    </div>

                                                                    <div class="col-lg-2">
                                                                    <label class="form-label mb-1">Emirates ID</label>
                                                                    <input type="file" class="form-control form-control-sm" @change="own.emirates_id = $event.target.files[0]">
                                                                    </div>

                                                                    <!-- Visa Copy + Buttons in same column -->
                                                                    <div class="col-lg-2 d-flex flex-column">
                                                                    <label class="form-label mb-1">Visa Copy</label>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <input type="file" class="form-control form-control-sm" 
                                                                            @change="own.visa_copy = $event.target.files[0]">

                                                                        <!-- BUTTONS -->
                                                                        <button type="button" class="btn btn-light btn-sm p-1 text-success"
                                                                                @click="addOwner" title="Add Owner">
                                                                        <i class="ico icon-outline-add-square"></i>
                                                                        </button>

                                                                        <button type="button" class="btn btn-light btn-sm p-1 text-danger"
                                                                                v-if="people.owners.length > 1"
                                                                                @click="removeOwner(i)" title="Remove Owner">
                                                                        <i class="ico icon-bold-trash-bin-2"></i>
                                                                        </button>
                                                                    </div>
                                                                    </div>

                                                                </div>
                                                                </div>


                                                                </div>
                                                            </div>

                                                            <!-- 4️⃣ Accordion: Sponsor Details -->
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingSponsor">
                                                                    <button class="accordion-button collapsed" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapseSponsor">
                                                                        4. Sponsor Details
                                                                    </button>
                                                                </h2>

                                                                <div id="collapseSponsor" class="accordion-collapse collapse"
                                                                    data-bs-parent="#contactInfoAccordion">

                                                                                                            <div class="accordion-body">

                                            <div v-for="(sp, i) in people.sponsors" :key="i"
                                                class="row gy-2 mb-1 p-2 rounded align-items-end">

                                                <div class="col-lg-2">
                                                <label class="form-label mb-1">Name</label>
                                                <input type="text" class="form-control form-control-sm" v-model="sp.name">
                                                </div>

                                                <div class="col-lg-2">
                                                <label class="form-label mb-1">Mobile</label>
                                                <input type="text" class="form-control form-control-sm" v-model="sp.mobile">
                                                </div>

                                                <div class="col-lg-2">
                                                <label class="form-label mb-1">Email</label>
                                                <input type="email" class="form-control form-control-sm" v-model="sp.email">
                                                </div>

                                                <div class="col-lg-2">
                                                <label class="form-label mb-1">Passport Copy</label>
                                                <input type="file" class="form-control form-control-sm" @change="sp.passport_copy = $event.target.files[0]">
                                                </div>

                                                <div class="col-lg-2">
                                                <label class="form-label mb-1">Emirates ID</label>
                                                <input type="file" class="form-control form-control-sm" @change="sp.emirates_id = $event.target.files[0]">
                                                </div>

                                                <!-- Visa Copy + Buttons in same column -->
                                                <div class="col-lg-2 d-flex flex-column">
                                                <label class="form-label mb-1">Visa Copy</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="file" class="form-control form-control-sm" 
                                                        @change="sp.visa_copy = $event.target.files[0]">

                                                    <!-- Add Button -->
                                                    <button type="button" class="btn btn-light btn-sm p-1 text-success"
                                                            @click="addSponsor" title="Add Sponsor">
                                                    <i class="ico icon-outline-add-square"></i>
                                                    </button>

                                                    <!-- Remove Button -->
                                                    <button type="button" class="btn btn-light btn-sm p-1 text-danger"
                                                            v-if="people.sponsors.length > 1"
                                                            @click="removeSponsor(i)" title="Remove Sponsor">
                                                    <i class="ico icon-bold-trash-bin-2"></i>
                                                    </button>
                                                </div>
                                                </div>

                                            </div>

</div>

                                                                </div>

                                                            </div>

                                                            <!-- 5️⃣ Accordion: Contact Person Details -->
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingContact">
                                                                    <button class="accordion-button collapsed" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapseContact">
                                                                        5. Contact Person Details
                                                                    </button>
                                                                </h2>

                                                                <div id="collapseContact" class="accordion-collapse collapse"
                                                                    data-bs-parent="#contactInfoAccordion">
                                                               <div class="accordion-body">

            <div v-for="(ct, i) in people.contacts" :key="i"
                class="row gy-2 mb-1 p-2 rounded align-items-end">

                <div class="col-lg-3">
                <label class="form-label mb-1">Name</label>
                <input type="text" class="form-control form-control-sm" v-model="ct.name">
                </div>

                <div class="col-lg-3">
                <label class="form-label mb-1">Mobile</label>
                <input type="text" class="form-control form-control-sm" v-model="ct.mobile">
                </div>

                <div class="col-lg-3">
                <label class="form-label mb-1">Email</label>
                <input type="email" class="form-control form-control-sm" v-model="ct.email">
                </div>

                <div class="col-lg-3">
                <label class="form-label mb-1">Designation</label>
                <input type="text" class="form-control form-control-sm" v-model="ct.designation">
                </div>

                <div class="col-lg-3">
                <label class="form-label mb-1">Passport Copy</label>
                <input type="file" class="form-control form-control-sm" @change="ct.passport_copy = $event.target.files[0]">
                </div>

                <div class="col-lg-3">
                <label class="form-label mb-1">Emirates ID</label>
                <input type="file" class="form-control form-control-sm" @change="ct.emirates_id = $event.target.files[0]">
                </div>

                <!-- Visa Copy + Buttons in same column -->
                <div class="col-lg-6">
                <label class="form-label mb-1">Visa Copy</label>
                <div class="d-flex align-items-center gap-1">
                    <input type="file" class="form-control form-control-sm" @change="ct.visa_copy = $event.target.files[0]">

                    <!-- Add Button -->
                    <button type="button" class="btn btn-light btn-sm p-1 text-success"
                            @click="addContact" title="Add Contact">
                    <i class="ico icon-outline-add-square"></i>
                    </button>

                    <!-- Remove Button -->
                    <button type="button" class="btn btn-light btn-sm p-1 text-danger"
                            v-if="people.contacts.length > 1"
                            @click="removeContact(i)" title="Remove Contact">
                    <i class="ico icon-bold-trash-bin-2"></i>
                    </button>
                </div>
                </div>

  </div>

</div>


                                                                </div>
                                                            </div>

                                                        </div><!-- ACCORDION END -->

                                                    </div><!-- /contactinfo -->


                                                    <div class="tab-pane fade" id="compliance-regulatory" role="tabpanel"
                                                        aria-labelledby="compliance-regulatory-tab">
                                                        <div class="row gy-2 mt-2">

                                                            {{-- License Details --}}
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Trade License Number <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="business_license_number"
                                                                    v-model.trim="form.compliance.business_license_number" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.business_license_number">@{{ err('business_license_number') }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">License Issue Date <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm date-picker"
                                                                    name="license_issue_date"
                                                                    v-model.trim="form.compliance.license_issue_date" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.license_issue_date">@{{ err('license_issue_date') }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">License Expiry Date <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm date-picker"
                                                                    name="license_expiry_date"
                                                                    v-model.trim="form.compliance.license_expiry_date" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.license_expiry_date">@{{ errors.license_expiry_date[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Issuing Authority <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="issuing_authority"
                                                                    v-model.trim="form.compliance.issuing_authority" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.issuing_authority">@{{ errors.issuing_authority[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Trade License Upload <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="business_license_upload"
                                                                    @change="onComplianceFile($event, 'business_license_upload')" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.business_license_upload">@{{ errors.business_license_upload[0] }}</small>
                                                            </div>



                                                            {{-- Tax Details --}}
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">Tax Applicable</label>
                                                                <select class="form-control form-control-sm" name="tax_applicable"
                                                                    id="tax_applicable" v-model="form.compliance.tax_applicable">
                                                                    <option value="">Select</option>
                                                                    <option value="vat">VAT</option>
                                                                    <option value="ct">CT</option>
                                                                    <option value="both">Both (CT/VAT)</option>
                                                                    <option value="none">Not Applicable</option>
                                                                </select>
                                                                <small class="text-danger"
                                                                    v-if="errors.tax_applicable">@{{ errors.tax_applicable[0] }}</small>
                                                            </div>

                                                        </div>

                                                        {{-- VAT block --}}
                                                        <div class="row gy-2 mt-1" v-show="showVAT">
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">VAT Registration No. (TRN) <span
                                                                        class="text-danger" v-show="showVAT">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="vat_registration_number"
                                                                    v-model.trim="form.compliance.vat_registration_number">
                                                                <small class="text-danger"
                                                                    v-if="errors.vat_registration_number">@{{ errors.vat_registration_number[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">VAT %</label>
                                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                                    name="vat_percentage" v-model.number="form.compliance.vat_percentage">
                                                                <small class="text-danger"
                                                                    v-if="errors.vat_percentage">@{{ errors.vat_percentage[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">VAT Registration Date</label>
                                                                <input type="text" class="form-control form-control-sm date-picker"
                                                                    name="vat_date" v-model.trim="form.compliance.vat_date">
                                                                <small class="text-danger"
                                                                    v-if="errors.vat_date">@{{ errors.vat_date[0] }}</small>
                                                            </div>

                                                            
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">VAT Issuing Authority <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="vat_issuing_authority"
                                                                    v-model.trim="form.compliance.vat_issuing_authority" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.issuing_authority">@{{ errors.vat_issuing_authority[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">VAT Certificate Upload</label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="vat_certificate" accept="image/*,.pdf"
                                                                    @change="onComplianceFile($event, 'vat_certificate')">
                                                                <small class="text-danger"
                                                                    v-if="errors.vat_certificate">@{{ errors.vat_certificate[0] }}</small>
                                                            </div>
                                                        </div>

                                                        {{-- CT block --}}
                                                        <div class="row gy-2 mt-1" v-show="showCT">
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">CT Registration No. (CTN)</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="corporate_tax_number"
                                                                    v-model.trim="form.compliance.corporate_tax_number">
                                                                <small class="text-danger"
                                                                    v-if="errors.corporate_tax_number">@{{ errors.corporate_tax_number[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">CT %</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="corporate_tax_vat"
                                                                    v-model.trim="form.compliance.corporate_tax_vat">
                                                                <small class="text-danger"
                                                                    v-if="errors.corporate_tax_vat">@{{ errors.corporate_tax_vat[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">CT Registration Date</label>
                                                                <input type="text" class="form-control form-control-sm date-picker"
                                                                    name="corporate_tax_date"
                                                                    v-model.trim="form.compliance.corporate_tax_date">
                                                                <small class="text-danger"
                                                                    v-if="errors.corporate_tax_date">@{{ errors.corporate_tax_date[0] }}</small>
                                                            </div>

                                                            
                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">CT Issuing Authority <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="ct_issuing_authority"
                                                                    v-model.trim="form.compliance.issuing_authority" required>
                                                                <small class="text-danger"
                                                                    v-if="errors.issuing_authority">@{{ errors.issuing_authority[0] }}</small>
                                                            </div>

                                                            <div class="col-lg-2">
                                                                <label class="form-label mb-1">CT Certificate Upload</label>
                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="corporate_tax_certificate" accept="image/*,.pdf"
                                                                    @change="onComplianceFile($event, 'corporate_tax_certificate')">
                                                                <small class="text-danger"
                                                                    v-if="errors.corporate_tax_certificate">@{{ errors.corporate_tax_certificate[0] }}</small>
                                                            </div>
                                                        </div>
                                                    </div>


                                    <div class="tab-pane fade" id="Banking-Finance"role="tabpanel"
                                        aria-labelledby="edu-tab">
                                                     <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn btn-success btn-sm"
                @click="openBankForm"
                data-bs-toggle="modal" data-bs-target="#bankModal">
            <i class="ico icon-outline-add-square"></i> Add Bank
        </button>
    </div>

                                        {{-- <div class="table-responsive">
                                            <table class="table align-middle" id="bankTable">
                                                <thead class="table-light">
                                                    <tr>
                                                    <th style="width: 180px;">Bank Name <span class="text-danger">*</span></th>
                                                    <th style="width: 160px;">Branch Name</th>
                                                    <th style="width: 180px;">Account Number <span class="text-danger">*</span></th>
                                                    <th style="width: 180px;">IBAN <span class="text-danger">*</span></th>
                                                    <th style="width: 140px;">SWIFT Code</th>
                                                    <th style="width: 140px;">Finance Code</th>
                                                    <th style="width: 140px;">Currency</th>
                                                    <th style="width: 220px;">Bank Letter Upload <span class="text-danger">*</span></th>
                                                    <th style="width: 120px;">Action</th>
                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][bank_name]" required></td>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][branch_name]">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][account_number]" required></td>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][iban_number]" required></td>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][swift_code]">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="banks[0][finance_code]">
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="banks[0][currency]">
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
                                                        <td><input type="file" class="form-control"
                                                        name="banks[0][bank_letter]" accept="image/*,.pdf"
                                                        required></td>
                                                        <td style="display:flex; align-items:center; gap:6px;">
                                                        <button type="button" class="btn btn-light text-dark btn-sm delBankRow">
                                                        <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-light btn-sm" id="addBankRow">
                                                        <i class="ico icon-outline-add-square text-success"></i>
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
                                        </script> --}}

                                    </div>

                                    <div class="tab-pane fade" id="hr-payroll" role="tabpanel"
                                        aria-labelledby="hr-payroll">
                                        <div class="row gy-2">

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Establishment ID <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="wps_establishment_id"
                                                    v-model.trim="form.hr.wps_establishment_id" required>
                                                <small class="text-danger"
                                                    v-if="errors.wps_establishment_id">@{{ errors.wps_establishment_id[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Bank <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="wps_bank" v-model.trim="form.hr.wps_bank" required>
                                                <small class="text-danger"
                                                    v-if="errors.wps_bank">@{{ errors.wps_bank[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Salary File Code</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="wps_salary_file_code"
                                                    v-model.trim="form.hr.wps_salary_file_code">
                                                <small class="text-danger"
                                                    v-if="errors.wps_salary_file_code">@{{ errors.wps_salary_file_code[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll Cycle <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm" name="payroll_cycle"
                                                    v-model="form.hr.payroll_cycle" required>
                                                    <option value="">Select</option>
                                                    <option value="monthly">Monthly</option>
                                                    <option value="bi-weekly">Bi-Weekly</option>
                                                    <option value="weekly">Weekly</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.payroll_cycle">@{{ errors.payroll_cycle[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll Start Date</label>
                                                <select class="form-select form-select-sm" name="payroll_start"
                                                    v-model.number="form.hr.payroll_start">
                                                    <option value="">Select</option>
                                                    <option v-for="n in 30" :key="n"
                                                        :value="n">@{{ n }}</option>
                                                </select>
                                                <small class="text-danger" v-if="errors.payroll_start">
                                                    @{{ errors.payroll_start[0] }}
                                                </small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll End Date</label>
                                                <select class="form-select form-select-sm" name="payroll_end"
                                                    v-model.number="form.hr.payroll_end">
                                                    <option value="">Select</option>
                                                    <option v-for="n in 30" :key="n"
                                                        :value="n">@{{ n }}</option>
                                                </select>
                                                <small class="text-danger" v-if="errors.payroll_end">
                                                    @{{ errors.payroll_end[0] }}
                                                </small>
                                            </div>


                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Weekly Off</label>
                                                <select class="form-control form-control-sm" name="weekly_off"
                                                    v-model="form.hr.weekly_off">
                                                    <option value="">Select</option>
                                                    <option value="sunday">Sunday</option>
                                                    <option value="monday">Monday</option>
                                                    <option value="tuesday">Tuesday</option>
                                                    <option value="wednesday">Wednesday</option>
                                                    <option value="thursday">Thursday</option>
                                                    <option value="friday">Friday</option>
                                                    <option value="saturday">Saturday</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.weekly_off">@{{ errors.weekly_off[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Gratuity Calculation Method</label>
                                                <select class="form-control form-control-sm" name="gratuity_method"
                                                    v-model="form.hr.gratuity_method">
                                                    <option value="">Select</option>
                                                    <option value="basic_salary">Basic Salary</option>
                                                    <option value="gross_salary">Gross Salary</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.gratuity_method">@{{ errors.gratuity_method[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Provider</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="insurance_provider" v-model.trim="form.hr.insurance_provider">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_provider">@{{ errors.insurance_provider[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Policy Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="insurance_policy_number"
                                                    v-model.trim="form.hr.insurance_policy_number">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_policy_number">@{{ errors.insurance_policy_number[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Policy Expiry Date</label>
                                                <input type="date" class="form-control form-control-sm date-picker"
                                                    name="insurance_policy_expiry"
                                                    v-model.trim="form.hr.insurance_policy_expiry">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_policy_expiry">@{{ errors.insurance_policy_expiry[0] }}</small>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="Policies" role="tabpanel"
                                        aria-labelledby="policies-tab">
                                        <div class="d-flex justify-content-end mb-2">

                                            <!-- Add Policy Button -->
                                            <button type="button" class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#policyModal"
                                                    @click="openPolicyForm()">
                                                <i class="ico icon-outline-add-square"></i> Add Policy
                                            </button>

                                            <!-- Policy List Button -->
                                            <button type="button" class="btn btn-primary btn-sm ms-2"
                                                    data-bs-toggle="modal" data-bs-target="#policyListModal">
                                                <i class="ico icon-outline-document"></i> Policy List
                                            </button>

                                        </div>


                                            <div v-if="policies.length===0" class="text-muted">No policies added.
                                            </div>

                                        <div v-for="(p, idx) in policies" :key="p.uid" class=""
                                            :data-index="idx">
                                            <div class="row gy-2 align-items-end">
                                                <div class="col-lg-2">
                                                    <label class="form-label mb-1">Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control form-control-sm date-picker"
                                                        v-model="p.policy_date">
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_date']">
                                                        @{{ errors['policies.' + idx + '.policy_date'][0] }}
                                                    </small>
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label mb-1">Policy Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        v-model.trim="p.policy_name">
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_name']">
                                                        @{{ errors['policies.' + idx + '.policy_name'][0] }}
                                                    </small>
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label mb-1">Policy Category</label>
                                                    <select class="form-select form-select-sm"
                                                        v-model="p.policy_category">
                                                        <option value="">Select</option>
                                                        <option value="health">Health</option>
                                                        <option value="life">Life</option>
                                                        <option value="vehicle">Vehicle</option>
                                                    </select>
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_category']">
                                                        @{{ errors['policies.' + idx + '.policy_category'][0] }}
                                                    </small>
                                                </div>

                                                <div class="col-lg-1">
                                                    <label class="form-label mb-1">Valid</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        v-model="p.policy_valid">
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_valid']">
                                                        @{{ errors['policies.' + idx + '.policy_valid'][0] }}
                                                    </small>
                                                </div>



                                                <div class="col-lg-2">
                                                    <label class="form-label mb-1">View to Employees <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm"
                                                        v-model="p.view_to_employees">
                                                        <option :value="1">Yes</option>
                                                        <option :value="0">No</option>
                                                    </select>
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.view_to_employees']">
                                                        @{{ errors['policies.' + idx + '.view_to_employees'][0] }}
                                                    </small>
                                                </div>

                                                <div class="col-lg-2">
                                                    <label class="form-label mb-1">File Upload <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" class="form-control form-control-sm"
                                                        @change="onPolicyFile($event, idx)" accept=".pdf,image/*">
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_file']">
                                                        @{{ errors['policies.' + idx + '.policy_file'][0] }}
                                                    </small>
                                                </div>

                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-light btn-xs" id="addBankRow">
                                                        <i class="ico icon-outline-add-square text-success"></i>

                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row gy-2 mt-2">
                                                <div class="col-lg-12">
                                                    <label class="form-label mb-1">Details</label>
                                                    <!-- CKEditor host element -->
                                                    <textarea class="form-control form-control-sm" :id="'policy_details_' + p.uid" rows="6"></textarea>
                                                    <!-- keep value in Vue (not shown) -->
                                                    <input type="hidden" v-model="p.policy_details">
                                                    <small class="text-danger"
                                                        v-if="errors['policies.'+idx+'.policy_details']">
                                                        @{{ errors['policies.' + idx + '.policy_details'][0] }}
                                                    </small>
                                                </div>
                                                <div class="col-lg-2 d-flex justify-content-end align-items-start"
                                                    v-if="policies.length > 1">
                                                    <button type="button" class="btn btn-light text-danger btn-sm"
                                                        @click="removePolicy(idx)" title="Remove">
                                                        <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <style>
                                        .doc-row {
                                            margin-top: 20px;
                                            /* 👈 yahan se upar ka gap control kar sakte ho */
                                        }
                                    </style>

                                    <div class="tab-pane fade" id="documentation" role="tabpanel"
                                        aria-labelledby="docs-tab">
                                        <div class="row gy-3">

                                            <!-- Establishment Card -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Establishment Card</div>

                                                    <div class="col-lg-2">
                                                        <input type="file" name="establishment_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'establishment')">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="date" name="establishment_expiry"
                                                            class="form-control form-control-sm"
                                                            v-model="docs.establishment.expiry">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="text" name="establishment_number"
                                                            class="form-control form-control-sm" placeholder="Number"
                                                            v-model.trim="docs.establishment.number">
                                                    </div>

                                                    <div class="col-auto">
                                                        <button type="button" class="btn btn-light btn-sm"
                                                            id="addBankRow">
                                                            <i class="ico icon-outline-add-square text-success"></i>
                                                            Add Row
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Immigration Card -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Immigration Card</div>

                                                    <div class="col-lg-2">
                                                        <input type="file" name="immigration_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'immigration')">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="date" name="immigration_expiry"
                                                            class="form-control form-control-sm"
                                                            v-model="docs.immigration.expiry">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="text" name="immigration_number"
                                                            class="form-control form-control-sm" placeholder="Number"
                                                            v-model.trim="docs.immigration.number">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Labour Establishment Card -->
                                            <div class="col-12 mt-4">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Labour Establishment Card</div>

                                                    <div class="col-lg-2">
                                                        <input type="file" name="labour_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'labour')">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="date" name="labour_expiry"
                                                            class="form-control form-control-sm"
                                                            v-model="docs.labour.expiry">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="text" name="labour_number"
                                                            class="form-control form-control-sm" placeholder="Number"
                                                            v-model.trim="docs.labour.number">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Chamber -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Chamber of Commerce</div>

                                                    <div class="col-lg-2">
                                                        <input type="file" name="chamber_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'chamber')">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="date" name="chamber_expiry"
                                                            class="form-control form-control-sm"
                                                            v-model="docs.chamber.expiry">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="text" name="chamber_number"
                                                            class="form-control form-control-sm" placeholder="Number"
                                                            v-model.trim="docs.chamber.number">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Insurance -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Insurance Certificate</div>

                                                    <div class="col-lg-2">
                                                        <input type="file" name="insurance_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'insurance')">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="date" name="insurance_certificate_expiry"
                                                            class="form-control form-control-sm"
                                                            v-model="docs.insurance.expiry">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <input type="text" name="insurance_certificate_number"
                                                            class="form-control form-control-sm" placeholder="Number"
                                                            v-model.trim="docs.insurance.number">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- MOA / AOA -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">MOA / AOA</div>
                                                    <div class="col-lg-2">
                                                        <input type="file" name="moa_aoa_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'moa_aoa')">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Board Resolution -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Board Resolution</div>
                                                    <div class="col-lg-2">
                                                        <input type="file" name="board_resolution_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'board_resolution')">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Power of Attorney -->
                                            <div class="col-12 doc-row">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-2 fw-bold">Power of Attorney</div>
                                                    <div class="col-lg-2">
                                                        <input type="file" name="poa_file"
                                                            class="form-control form-control-sm"
                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                                            @change="onDocFile($event, 'poa')">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="company-setting" role="tabpanel"
                                        aria-labelledby="docs-tab">
                                        <div class="row">


                                            <div class="font-weight-600 title-15 me-3 p-2">Company Setting
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Currency</label>
                                                <select name="currency" class="form-select form-select-sm"
                                                    v-model="form.currency">
                                                    <option value="">Select Currency</option>
                                                    @foreach ($currency as $syscurrency)
                                                        <option value="{{ $syscurrency->code }}">
                                                            {{ $syscurrency->name }} ({{ $syscurrency->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger">@{{ errors.currency }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Currency Digit</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="currency_digit" min="0" max="4"
                                                    v-model.trim="form.currency_digit">
                                                <small class="text-danger">@{{ errors.currency_digit }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Book Closed</label>
                                                <input type="text" class="form-control form-control-sm date-picker"
                                                    name="book_closed" placeholder="dd-mm-yyyy"
                                                    v-model.trim="form.book_closed">
                                                <small class="text-danger">@{{ err('book_closed') }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Sales Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="sales_code" v-model.trim="form.sales_code">
                                                <small class="text-danger">@{{ errors.sales_code }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Other Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="other_code" v-model.trim="form.other_code">
                                                <small class="text-danger">@{{ errors.other_code }}</small>
                                            </div>

                                                                            <!-- ============================================
                                                HR PAYROLL SETTING SECTION (Moved Here)
                                            ============================================ -->
                                          <div class="font-weight-600 title-15 me-3 p-2 mt-3">HR Payroll Setting</div>



                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Establishment ID <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.wps_establishment_id">
                                                <small class="text-danger"
                                                    v-if="errors.wps_establishment_id">@{{ errors.wps_establishment_id[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Bank <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.wps_bank">
                                                <small class="text-danger"
                                                    v-if="errors.wps_bank">@{{ errors.wps_bank[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">WPS Salary File Code</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.wps_salary_file_code">
                                                <small class="text-danger"
                                                    v-if="errors.wps_salary_file_code">@{{ errors.wps_salary_file_code[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll Cycle <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control form-control-sm"
                                                    v-model="form.hr.payroll_cycle">
                                                    <option value="">Select</option>
                                                    <option value="monthly">Monthly</option>
                                                    <option value="bi-weekly">Bi-Weekly</option>
                                                    <option value="weekly">Weekly</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.payroll_cycle">@{{ errors.payroll_cycle[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll Start Date</label>
                                                <select class="form-select form-select-sm"
                                                    v-model.number="form.hr.payroll_start">
                                                    <option value="">Select</option>
                                                    <option v-for="n in 30" :key="n"
                                                        :value="n">@{{ n }}</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.payroll_start">@{{ errors.payroll_start[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Payroll End Date</label>
                                                <select class="form-select form-select-sm"
                                                    v-model.number="form.hr.payroll_end">
                                                    <option value="">Select</option>
                                                    <option v-for="n in 30" :key="n"
                                                        :value="n">@{{ n }}</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.payroll_end">@{{ errors.payroll_end[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Weekly Off</label>
                                                <select class="form-control form-control-sm"
                                                    v-model="form.hr.weekly_off">
                                                    <option value="">Select</option>
                                                    <option value="sunday">Sunday</option>
                                                    <option value="monday">Monday</option>
                                                    <option value="tuesday">Tuesday</option>
                                                    <option value="wednesday">Wednesday</option>
                                                    <option value="thursday">Thursday</option>
                                                    <option value="friday">Friday</option>
                                                    <option value="saturday">Saturday</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.weekly_off">@{{ errors.weekly_off[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Gratuity Calculation Method</label>
                                                <select class="form-control form-control-sm"
                                                    v-model="form.hr.gratuity_method">
                                                    <option value="">Select</option>
                                                    <option value="basic_salary">Basic Salary</option>
                                                    <option value="gross_salary">Gross Salary</option>
                                                </select>
                                                <small class="text-danger"
                                                    v-if="errors.gratuity_method">@{{ errors.gratuity_method[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Provider</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.insurance_provider">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_provider">@{{ errors.insurance_provider[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Policy Number</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.insurance_policy_number">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_policy_number">@{{ errors.insurance_policy_number[0] }}</small>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label mb-1">Insurance Policy Expiry Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    v-model.trim="form.hr.insurance_policy_expiry">
                                                <small class="text-danger"
                                                    v-if="errors.insurance_policy_expiry">@{{ errors.insurance_policy_expiry[0] }}</small>
                                            </div>

                                        </div>
                                    </div>


                                    </form>

                                </div>
                            </div>
                        </div>




                    </div> {{-- /.tab-content --}}
                </div>

            </div>
        </div>
    </div>

    <!-- Vue 2 + Axios (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    const GET_STATE_URL = "{{ url('get_state') }}";
    const PARENT_COMPANY_URL = "{{ url('/parent-companies') }}";

    const URLS = {
        basic: "{{ route('company.basic.store') }}",
        comp: "{{ route('company.compliance.store') }}",
        docs: "{{ route('company.docs.store') }}",
        bank: "{{ route('company.banking.store') }}",
        hr: "{{ route('company.hrpayroll.store') }}",
        policy: "{{ route('company.hrpolicy.store') }}"
    };
    const CSRF = "{{ csrf_token() }}";

    new Vue({
        el: '#companyApp',

        data: {
            loading: false,
            flash: '',
            errors: {},
            industries: @json($industries),
            activities: @json($activities),
            filteredActivities: [],

            isParent: false,
            parentCompanies: [],

            // ========== MULTI PEOPLE (accordion) ==========
            people: {
                owners: [{
                    name: "",
                    mobile: "",
                    email: "",
                    designation: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                }],
                sponsors: [{
                    name: "",
                    mobile: "",
                    email: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                }],
                contacts: [{
                    name: "",
                    mobile: "",
                    email: "",
                    designation: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                }]
            },

            // ========== FORM MAIN ==========
            form: {
                company_id: {{ $nextId }},
                company_name: @json(old('company_name')),
                trade_name: @json(old('trade_name')),
                legal_entity_type: @json(old('legal_entity_type')),
                industry_id: "",
                business_activity_id: "",
                parent_company: @json(old('parent_company')),
                country: @json(old('country')),
                city: @json(old('city')),
                company_address: @json(old('company_address')),
                sales_code: @json(old('sales_code')),
                other_code: @json(old('other_code')),
                currency: @json(old('currency')),
                currency_digit: @json(old('currency_digit')),
                book_closed: @json(old('book_closed')),
                company_logo: null,
                digital_stamp: null,
                company_profile: null,

                email: @json(old('company_email')),
                website: @json(old('website')),
                telephone: @json(old('telephone')),
                fax: @json(old('fax')),
                mobile: @json(old('mobile')),
                contact_sections: @json(old('contact_sections', [])) || [],

                company_type: "",
                parent_company_id: "",

                // ---- Compliance
                compliance: {
                    business_license_number: @json(old('business_license_number')),
                    license_issue_date: @json(old('license_issue_date')),
                    license_expiry_date: @json(old('license_expiry_date')),
                    issuing_authority: @json(old('issuing_authority')),
                    tax_applicable: @json(old('tax_applicable')) ?? 'both',
                    vat_registration_number: @json(old('vat_registration_number')),
                    vat_percentage: @json(old('vat_percentage')),
                    vat_date: @json(old('vat_date')),
                    vat_issuing_authority:@json(old('vat_issuing_authority')),
                    business_license_upload: null,
                    vat_certificate: null,
                    corporate_tax_number: @json(old('corporate_tax_number')),
                    corporate_tax_vat: @json(old('corporate_tax_vat')),
                    corporate_tax_date: @json(old('corporate_tax_date')),
                    ct_issuing_authority:@json(old('ct_issuing_authority')),
                    corporate_tax_certificate: null,
                },

                // ---- HR & Payroll
                hr: {
                    wps_establishment_id: @json(old('wps_establishment_id')),
                    wps_bank: @json(old('wps_bank')),
                    wps_salary_file_code: @json(old('wps_salary_file_code')),
                    payroll_cycle: @json(old('payroll_cycle')),
                    weekly_off: @json(old('weekly_off')),
                    gratuity_method: @json(old('gratuity_method')),
                    insurance_provider: @json(old('insurance_provider')),
                    insurance_policy_number: @json(old('insurance_policy_number')),
                    insurance_policy_expiry: @json(old('insurance_policy_expiry')),
                }
            },

            // ---- Policies (CKEditor)
            policies: [{
                uid: 'pol_' + Date.now(),
                policy_date: '',
                policy_name: '',
                policy_category: '',
                policy_valid: '',
                view_to_employees: 1,
                policy_details: '',
                policy_file: null
            }],
            editors: {},

            // ---- Documents tab
            docs: {
                establishment: { number: '', expiry: '', file: null },
                immigration:   { number: '', expiry: '', file: null },
                labour:        { number: '', expiry: '', file: null },
                chamber:       { number: '', expiry: '', file: null },
                insurance:     { number: '', expiry: '', file: null },
                moa_aoa:       { file: null },
                board_resolution: { file: null },
                poa:           { file: null },
            },

            states: []
        },

        mounted() {
            const $sel = $('#contactSections');
            if ($sel.length) {
                $sel.select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#contactinfo')
                });
                $sel.val(this.form.contact_sections).trigger('change');
                $sel.on('change', () => {
                    this.form.contact_sections = $sel.val() || [];
                });
            }

            this.$nextTick(() => {
                this.policies.forEach((p, i) => this.initPolicyEditor(p.uid, i));
            });

            this.initBankRows();
        },

        watch: {
            'form.contact_sections'(val) {
                const $sel = $('#contactSections');
                if ($sel.length) {
                    const cur = $sel.val() || [],
                          next = val || [];
                    if (cur.join(',') !== next.join(',')) {
                        $sel.val(next).trigger('change.select2');
                    }
                }
            },

            'form.company_name'(val) {
                this.form.trade_name = val;
            },

            'form.country'(val) {
                if (!val) {
                    this.states = [];
                    this.form.state = "";
                    return;
                }

                axios.get(GET_STATE_URL, { params: { country_id: val } })
                    .then(res => {
                        this.states = res.data[0] || [];
                        this.form.state = "";
                    })
                    .catch(err => console.error("STATE LOAD ERROR:", err));
            }
        },

        computed: {
            showVAT() {
                return ['vat', 'both'].includes(this.form.compliance.tax_applicable);
            },
            showCT() {
                return ['ct', 'both'].includes(this.form.compliance.tax_applicable);
            }
        },

        methods: {
            toYMD(val) {
                if (!val) return '';
                const s = String(val).trim();
                let m = s.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/);
                if (m) return `${m[3]}-${m[2]}-${m[1]}`;
                m = s.match(/^(\d{4})[\/\-](\d{2})[\/\-](\d{2})$/);
                if (m) return `${m[1]}-${m[2]}-${m[3]}`;
                return s;
            },
            activateTab(sel) {
                try {
                    const t = document.querySelector(`[data-bs-target="${sel}"]`);
                    if (t) new bootstrap.Tab(t).show();
                } catch (e) {}
            },
            has(section) {
                return (this.form.contact_sections || []).includes(section);
            },
            err(key) {
                return this.errors[key] ? this.errors[key][0] : '';
            },

            // FILE HANDLERS
            onFile(e, key) {
                this.form[key] = e.target.files[0] || null;
            },
            onSectionFile(e, section, key) {
                this.form[section].files[key] = e.target.files[0] || null;
            },
            onComplianceFile(e, key) {
                this.form.compliance[key] = e.target.files[0] || null;
            },
            onDocFile(e, key) {
                const f = e.target.files?.[0] || null;
                if (!f) return;
                if (this.docs[key]) this.docs[key].file = f;
            },

            onCompanyTypeChange() {
                this.isParent = this.form.company_type === 'parent';
                if (this.isParent) {
                    this.form.parent_company_id = "";
                    this.form.company_name = "";
                } else {
                    this.loadParentCompanies();
                }
            },

            // PEOPLE ARRAY METHODS
            addOwner() {
                this.people.owners.push({
                    name: "",
                    mobile: "",
                    email: "",
                    designation: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                });
            },
            removeOwner(i) {
                this.people.owners.splice(i, 1);
            },
            addSponsor() {
                this.people.sponsors.push({
                    name: "",
                    mobile: "",
                    email: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                });
            },
            removeSponsor(i) {
                this.people.sponsors.splice(i, 1);
            },
            addContact() {
                this.people.contacts.push({
                    name: "",
                    mobile: "",
                    email: "",
                    designation: "",
                    passport_copy: null,
                    emirates_id: null,
                    visa_copy: null
                });
            },
            removeContact(i) {
                this.people.contacts.splice(i, 1);
            },

            loadParentCompanies() {
                axios.get(PARENT_COMPANY_URL)
                    .then(res => { this.parentCompanies = res.data; })
                    .catch(err => console.error("Parent companies load error:", err));
            },

            // CKEDITOR, BANK TABLE, DOC ERRORS (same as your code) -------------
            async initPolicyEditor(uid, idx) {
                const el = document.getElementById('policy_details_' + uid);
                if (!el || !window.ClassicEditor) return;
                try {
                    const editor = await ClassicEditor.create(el, {
                        toolbar: ['heading', 'bold', 'italic', 'bulletedList', 'numberedList',
                            'blockQuote', 'undo', 'redo', 'link'
                        ]
                    });
                    this.$set(this.editors, uid, editor);
                    editor.setData(this.policies[idx].policy_details || '');
                    editor.model.document.on('change:data', () => {
                        const i = this.policies.findIndex(x => x.uid === uid);
                        if (i !== -1) this.policies[i].policy_details = editor.getData();
                    });
                } catch (e) {
                    console.error('CKEditor init failed', e);
                }
            },
            destroyPolicyEditor(uid) {
                const ed = this.editors[uid];
                if (ed && ed.destroy) ed.destroy();
                if (this.editors[uid]) this.$delete(this.editors, uid);
            },
            addPolicy() {
                const uid = 'pol_' + Date.now() + '_' + Math.floor(Math.random() * 1e6);
                this.policies.push({
                    uid,
                    policy_date: '',
                    policy_name: '',
                    policy_category: '',
                    policy_valid: '',
                    view_to_employees: 1,
                    policy_details: '',
                    policy_file: null
                });
                this.$nextTick(() => this.initPolicyEditor(uid, this.policies.length - 1));
            },
            removePolicy(idx) {
                const uid = this.policies[idx].uid;
                this.destroyPolicyEditor(uid);
                this.policies.splice(idx, 1);
            },
            onPolicyFile(e, idx) {
                const f = e.target.files?.[0] || null;
                this.$set(this.policies[idx], 'policy_file', f);
            },

            clearBankErrors() {
                const $tbody = $('#bankTable').find('tbody');
                $tbody.find('input, select').removeClass('is-invalid');
                $tbody.find('.invalid-feedback.bank-feedback').remove();
            },
            renderBankErrors(serverErrors) {
                const $tbody = $('#bankTable').find('tbody');
                const mapSelector = {
                    bank_name: 'input[name*="[bank_name]"]',
                    branch_name: 'input[name*="[branch_name]"]',
                    account_number: 'input[name*="[account_number]"]',
                    iban_number: 'input[name*="[iban_number]"]',
                    swift_code: 'input[name*="[swift_code]"]',
                    finance_code: 'input[name*="[finance_code]"]',
                    currency: 'select[name*="[currency]"]',
                    bank_letter: 'input[type="file"][name*="[bank_letter]"]'
                };

                let firstInvalidEl = null;

                Object.keys(serverErrors || {}).forEach((key) => {
                    if (!key.startsWith('banks.')) return;
                    const parts = key.split('.');
                    const idx = parseInt(parts[1], 10);
                    const field = parts[2];

                    const $row = $tbody.find('tr').eq(idx);
                    if (!$row.length) return;

                    const sel = mapSelector[field];
                    if (!sel) return;

                    const $el = $row.find(sel).first();
                    if (!$el.length) return;

                    const msg = (serverErrors[key] && serverErrors[key][0]) ? serverErrors[key][0] : 'Invalid';
                    $el.addClass('is-invalid');

                    const $fb = $('<div class="invalid-feedback d-block bank-feedback"></div>').text(msg);
                    $el.after($fb);
                    if (!firstInvalidEl) firstInvalidEl = $el[0];
                });

                if (firstInvalidEl) firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            },

            renderDocErrors(errs) {
                const map = {
                    establishment_number: 'input[name="establishment_number"]',
                    establishment_expiry: 'input[name="establishment_expiry"]',
                    establishment_file: 'input[name="establishment_file"]',
                    immigration_number: 'input[name="immigration_number"]',
                    immigration_expiry: 'input[name="immigration_expiry"]',
                    immigration_file: 'input[name="immigration_file"]',
                    labour_number: 'input[name="labour_number"]',
                    labour_expiry: 'input[name="labour_expiry"]',
                    labour_file: 'input[name="labour_file"]',
                    chamber_number: 'input[name="chamber_number"]',
                    chamber_expiry: 'input[name="chamber_expiry"]',
                    chamber_file: 'input[name="chamber_file"]',
                    insurance_certificate_number: 'input[name="insurance_certificate_number"]',
                    insurance_certificate_expiry: 'input[name="insurance_certificate_expiry"]',
                    insurance_file: 'input[name="insurance_file"]',
                    moa_aoa_file: 'input[name="moa_aoa_file"]',
                    board_resolution_file: 'input[name="board_resolution_file"]',
                    poa_file: 'input[name="poa_file"]'
                };
                Object.values(map).forEach(sel => {
                    const el = document.querySelector(sel);
                    if (!el) return;
                    el.classList.remove('is-invalid');
                    const n = el.nextElementSibling;
                    if (n && n.classList.contains('invalid-feedback')) n.remove();
                });
                let first = null;
                Object.keys(errs || {}).forEach(k => {
                    if (!map[k]) return;
                    const el = document.querySelector(map[k]);
                    if (!el) return;
                    el.classList.add('is-invalid');
                    const fb = document.createElement('div');
                    fb.className = 'invalid-feedback d-block';
                    fb.textContent = (errs[k] && errs[k][0]) ? errs[k][0] : 'Invalid';
                    el.insertAdjacentElement('afterend', fb);
                    if (!first) first = el;
                });
                if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            },

            initBankRows() {
                const $tbody = $('#bankTable').find('tbody');

                if ($tbody.find('tr').length === 0) {
                    this.addBankRow(0);
                }

                $(document).off('click.addBankRow').on('click.addBankRow', '#addBankRow', (e) => {
                    e.preventDefault();
                    const idx = $tbody.find('tr').length;
                    this.addBankRow(idx);
                });

                $(document).off('click.delBankRow').on('click.delBankRow', '.delBankRow', (e) => {
                    e.preventDefault();
                    const $tr = $(e.currentTarget).closest('tr');
                    const $all = $tbody.find('tr');
                    if ($all.length === 1) {
                        $tr.find('input[type="text"], input[type="file"]').val('');
                        $tr.find('select').val('');
                        return;
                    }
                    $tr.remove();
                    this.reindexBankRows();
                });
            },

            addBankRow(index) {
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
                $('#bankTable').find('tbody').append(rowHtml);
            },

            reindexBankRows() {
                const $tbody = $('#bankTable').find('tbody');
                $tbody.find('tr').each(function(i, tr) {
                    $(tr).find('input, select').each(function(_, el) {
                        const $el = $(el);
                        const name = $el.attr('name');
                        if (!name) return;
                        $el.attr('name', name.replace(/banks\[\d+\]/, `banks[${i}]`));
                    });
                });
            },

            // ===== BUILDERS =====
            buildFdBasic() {
                const fd = new FormData();
                fd.append('date_of_incorporation', this.toYMD(this.form.date_of_incorporation));
                fd.append('book_closed', this.toYMD(this.form.book_closed));

                [
                    'company_id', 'company_name', 'trade_name', 'legal_entity_type',
                    'industry', 'parent_company',
                    'country', 'city', 'company_address', 'sales_code', 'other_code',
                    'currency', 'currency_digit'
                ].forEach(k => fd.append(k, this.form[k] ?? ''));

                if (this.form.company_logo)   fd.append('company_logo', this.form.company_logo);
                if (this.form.digital_stamp)  fd.append('digital_stamp', this.form.digital_stamp);
                if (this.form.company_profile)fd.append('company_profile', this.form.company_profile);

                ['email', 'website', 'telephone', 'fax', 'mobile'].forEach(k => {
                    fd.append(k, this.form[k] ?? '');
                });

                (this.form.contact_sections || []).forEach((v, i) => {
                    fd.append(`contact_sections[${i}]`, v);
                });

                // ✅ MULTI OWNERS / SPONSORS / CONTACTS arrays
                this.people.owners.forEach((own, index) => {
                    fd.append(`owners[${index}][name]`, own.name);
                    fd.append(`owners[${index}][mobile]`, own.mobile);
                    fd.append(`owners[${index}][email]`, own.email);
                    fd.append(`owners[${index}][designation]`, own.designation || '');

                    if (own.passport_copy)
                        fd.append(`owners[${index}][passport_copy]`, own.passport_copy);
                    if (own.emirates_id)
                        fd.append(`owners[${index}][emirates_id]`, own.emirates_id);
                    if (own.visa_copy)
                        fd.append(`owners[${index}][visa_copy]`, own.visa_copy);
                });

                this.people.sponsors.forEach((sp, index) => {
                    fd.append(`sponsors[${index}][name]`, sp.name);
                    fd.append(`sponsors[${index}][mobile]`, sp.mobile);
                    fd.append(`sponsors[${index}][email]`, sp.email);

                    if (sp.passport_copy)
                        fd.append(`sponsors[${index}][passport_copy]`, sp.passport_copy);
                    if (sp.emirates_id)
                        fd.append(`sponsors[${index}][emirates_id]`, sp.emirates_id);
                    if (sp.visa_copy)
                        fd.append(`sponsors[${index}][visa_copy]`, sp.visa_copy);
                });

                this.people.contacts.forEach((ct, index) => {
                    fd.append(`contacts[${index}][name]`, ct.name);
                    fd.append(`contacts[${index}][mobile]`, ct.mobile);
                    fd.append(`contacts[${index}][email]`, ct.email);
                    fd.append(`contacts[${index}][designation]`, ct.designation || '');

                    if (ct.passport_copy)
                        fd.append(`contacts[${index}][passport_copy]`, ct.passport_copy);
                    if (ct.emirates_id)
                        fd.append(`contacts[${index}][emirates_id]`, ct.emirates_id);
                    if (ct.visa_copy)
                        fd.append(`contacts[${index}][visa_copy]`, ct.visa_copy);
                });

                return fd;
            },

            buildFdCompliance() {
                const c = this.form.compliance || {};
                const fd = new FormData();
                fd.append('license_issue_date', this.toYMD(c.license_issue_date));
                fd.append('license_expiry_date', this.toYMD(c.license_expiry_date));
                fd.append('vat_date', this.toYMD(c.vat_date));
                fd.append('corporate_tax_date', this.toYMD(c.corporate_tax_date));
                [
                    'business_license_number', 'issuing_authority', 'tax_applicable',
                    'vat_registration_number', 'vat_percentage',
                    'corporate_tax_number', 'corporate_tax_vat'
                ].forEach(k => fd.append(k, c[k] ?? ''));
                if (c.business_license_upload)
                    fd.append('business_license_upload', c.business_license_upload);
                if (['vat','both'].includes(c.tax_applicable) && c.vat_certificate)
                    fd.append('vat_certificate', c.vat_certificate);
                if (['ct','both'].includes(c.tax_applicable) && c.corporate_tax_certificate)
                    fd.append('corporate_tax_certificate', c.corporate_tax_certificate);

                fd.append('company_id', this.form.company_id ?? '');
                return fd;
            },

            buildFdHRPayroll() {
                const h = this.form.hr || {};
                const fd = new FormData();
                const insuranceDate = this.toYMD(h.insurance_policy_expiry);
                if (insuranceDate) fd.append('insurance_policy_expiry', insuranceDate);

                fd.append('wps_establishment_id', h.wps_establishment_id ?? '');
                fd.append('wps_bank', h.wps_bank ?? '');
                fd.append('payroll_cycle', h.payroll_cycle ?? '');
                fd.append('payroll_start', h.payroll_start ?? '');
                fd.append('payroll_end', h.payroll_end ?? '');
                if (h.weekly_off) fd.append('weekly_off', h.weekly_off);
                if (h.gratuity_method) fd.append('gratuity_method', h.gratuity_method);

                fd.append('wps_salary_file_code', h.wps_salary_file_code ?? '');
                fd.append('insurance_provider', h.insurance_provider ?? '');
                fd.append('insurance_policy_number', h.insurance_policy_number ?? '');
                fd.append('company_id', this.form.company_id ?? '');
                return fd;
            },

            buildFdBanking() {
                const fd = new FormData();
                fd.append('company_id', this.form.company_id ?? '');

                const $tbody = $('#bankTable').find('tbody');
                const $rows = $tbody.find('tr');

                $rows.each(function(rowIdx, tr) {
                    const $tr = $(tr);

                    const bank_name     = $tr.find('input[name*="[bank_name]"]').val() || '';
                    const branch_name   = $tr.find('input[name*="[branch_name]"]').val() || '';
                    const account_number= $tr.find('input[name*="[account_number]"]').val() || '';
                    const iban_number   = $tr.find('input[name*="[iban_number]"]').val() || '';
                    const swift_code    = $tr.find('input[name*="[swift_code]"]').val() || '';
                    const finance_code  = $tr.find('input[name*="[finance_code]"]').val() || '';
                    const currency      = $tr.find('select[name*="[currency]"]').val() || '';
                    const letterInput   = $tr.find('input[type="file"][name*="[bank_letter]"]')[0];
                    const bank_letter   = letterInput && letterInput.files ? letterInput.files[0] : null;

                    fd.append(`banks[${rowIdx}][bank_name]`, bank_name);
                    fd.append(`banks[${rowIdx}][branch_name]`, branch_name);
                    fd.append(`banks[${rowIdx}][account_number]`, account_number);
                    fd.append(`banks[${rowIdx}][iban_number]`, iban_number);
                    fd.append(`banks[${rowIdx}][swift_code]`, swift_code);
                    fd.append(`banks[${rowIdx}][finance_code]`, finance_code);
                    fd.append(`banks[${rowIdx}][currency]`, currency);
                    if (bank_letter) {
                        fd.append(`banks[${rowIdx}][bank_letter]`, bank_letter);
                    }
                });

                return fd;
            },

            buildFdPolicies() {
                const fd = new FormData();
                fd.append('company_id', this.form.company_id ?? '');
                this.policies.forEach((p, i) => {
                    const d = this.toYMD(p.policy_date);
                    const v = this.toYMD(p.policy_valid);
                    if (d) fd.append(`policies[${i}][policy_date]`, d);
                    if (v) fd.append(`policies[${i}][policy_valid]`, v);

                    fd.append(`policies[${i}][policy_name]`, p.policy_name || '');
                    fd.append(`policies[${i}][policy_category]`, p.policy_category || '');
                    fd.append(`policies[${i}][view_to_employees]`, p.view_to_employees ?? '');
                    fd.append(`policies[${i}][policy_details]`, p.policy_details || '');
                    if (p.policy_file) fd.append(`policies[${i}][policy_file]`, p.policy_file);
                });
                return fd;
            },

            buildFdDocs() {
                const fd = new FormData();
                fd.append('company_id', this.form.company_id ?? '');
                const norm = (v) => this.toYMD(v);
                const p = this.docs;

                if (p.establishment.number) fd.append('establishment_number', p.establishment.number);
                if (p.establishment.expiry) fd.append('establishment_expiry', norm(p.establishment.expiry));
                if (p.establishment.file)   fd.append('establishment_file', p.establishment.file);

                if (p.immigration.number)   fd.append('immigration_number', p.immigration.number);
                if (p.immigration.expiry)   fd.append('immigration_expiry', norm(p.immigration.expiry));
                if (p.immigration.file)     fd.append('immigration_file', p.immigration.file);

                if (p.labour.number)        fd.append('labour_number', p.labour.number);
                if (p.labour.expiry)        fd.append('labour_expiry', norm(p.labour.expiry));
                if (p.labour.file)          fd.append('labour_file', p.labour.file);

                if (p.chamber.number)       fd.append('chamber_number', p.chamber.number);
                if (p.chamber.expiry)       fd.append('chamber_expiry', norm(p.chamber.expiry));
                if (p.chamber.file)         fd.append('chamber_file', p.chamber.file);

                if (p.insurance.number)     fd.append('insurance_certificate_number', p.insurance.number);
                if (p.insurance.expiry)     fd.append('insurance_certificate_expiry', norm(p.insurance.expiry));
                if (p.insurance.file)       fd.append('insurance_file', p.insurance.file);

                if (p.moa_aoa.file)         fd.append('moa_aoa_file', p.moa_aoa.file);
                if (p.board_resolution.file)fd.append('board_resolution_file', p.board_resolution.file);
                if (p.poa.file)             fd.append('poa_file', p.poa.file);

                return fd;
            },

            // ===== SUBMIT ALL =====
            async submitAll() {
                this.loading = true;
                this.flash   = '';
                this.errors  = {};

                // 1) Basic + Contact + People
                try {
                    await axios.post(URLS.basic, this.buildFdBasic(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    this.errors = e.response?.data?.errors || {};
                    if (e.response?.status === 422) this.activateTab('#contactinfo');
                    else alert('Server error (basic)');
                    this.loading = false;
                    return;
                }

                // 2) Compliance
                try {
                    await axios.post(URLS.comp, this.buildFdCompliance(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    this.errors = e.response?.data?.errors || {};
                    if (e.response?.status === 422) this.activateTab('#compliance-regulatory');
                    else alert('Server error (compliance)');
                    this.loading = false;
                    return;
                }

                // 3) Documents
                try {
                    await axios.post(URLS.docs, this.buildFdDocs(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    const errs = e.response?.data?.errors || {};
                    this.errors = errs;
                    this.activateTab('#documentation');
                    if (e.response?.status === 422) this.renderDocErrors(errs);
                    else alert('Server error (documents)');
                    this.loading = false;
                    return;
                }

                // 4) Banking
                try {
                    this.clearBankErrors();
                    await axios.post(URLS.bank, this.buildFdBanking(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    const errs = e.response?.data?.errors || null;
                    this.errors = errs || {};
                    this.activateTab('#Banking-Finance');
                    if (errs) this.renderBankErrors(errs);
                    else alert('Server error (banking)');
                    this.loading = false;
                    return;
                }

                // 5) HR & Payroll
                try {
                    await axios.post(URLS.hr, this.buildFdHRPayroll(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    this.errors = e.response?.data?.errors || {};
                    if (e.response?.status === 422) this.activateTab('#hr-payroll');
                    else alert('Server error (HR & Payroll)');
                    this.loading = false;
                    return;
                }

                // 6) Policies
                try {
                    await axios.post(URLS.policy, this.buildFdPolicies(), {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                } catch (e) {
                    this.errors = e.response?.data?.errors || {};
                    if (e.response?.status === 422) this.activateTab('#Policies');
                    else alert('Server error (Policies)');
                    this.loading = false;
                    return;
                }

                this.flash   = 'All data saved!';
                this.loading = false;
            },

            loadActivities() {
                if (this.form.industry_id) {
                    this.filteredActivities = this.activities.filter(
                        act => act.industry_id == this.form.industry_id
                    );
                } else {
                    this.filteredActivities = [];
                }
                this.form.business_activity_id = "";
            }
        }
    });
</script>

@endsection
<div class="modal fade admin-query" id="industryAddPopup" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Industry</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">

                <form method="POST" action="{{ url('/industry') }}" id="industryAddForm">
                    @csrf

                    <label class="form-label">Industry Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>

                    <button class="btn btn-success w-100 mt-3">Save</button>
                </form>

            </div>

        </div>
    </div>
</div>


<div class="modal fade admin-query" id="activityModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Business Activity</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">

                <form method="POST" action="{{ url('/business-activity') }}">
                    @csrf

                    <label class="form-label">Industry <span class="text-danger">*</span></label>
                    <select class="form-control" name="industry_id" required>
                        <option value="">Select Industry</option>
                        @foreach ($industries as $ind)
                            <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mt-3">Business Sector <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>

                    <button type="submit" class="btn btn-success mt-3 w-100">Save</button>
                </form>

            </div>

        </div>
    </div>
</div>
<div class="modal fade admin-query" id="entityTypeAddModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Business Entity Type</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                <form id="addEntityTypeForm" method="POST" action="{{ url('business-entity-type') }}">
                    @csrf

                    <label class="form-label">Entity Type Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>

                    <button class="btn btn-success mt-3 w-100">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- ADD POLICY MODAL -->
<div class="modal fade" id="policyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">@{{ policyFormMode=='add' ? 'Add Policy' : 'Edit Policy' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row gy-2">

                    <div class="col-lg-3">
                        <label class="form-label">Date *</label>
                        <input type="date" class="form-control form-control-sm"
                               v-model="policyForm.policy_date">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Policy Name *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="policyForm.policy_name">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select form-select-sm"
                                v-model="policyForm.policy_category">
                            <option value="">Select</option>
                            <option value="health">Health</option>
                            <option value="life">Life</option>
                            <option value="vehicle">Vehicle</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Valid Till</label>
                        <input type="date" class="form-control form-control-sm"
                               v-model="policyForm.policy_valid">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">View to Employees *</label>
                        <select class="form-select form-select-sm"
                                v-model="policyForm.view_to_employees">
                            <option :value="1">Yes</option>
                            <option :value="0">No</option>
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Upload File</label>
                        <input type="file" class="form-control form-control-sm"
                               @change="policyFormFile($event)">
                    </div>

                    <div class="col-lg-12">
                        <label class="form-label">Policy Details</label>
                        <textarea :id="'policy_editor_modal'" class="form-control" rows="5"></textarea>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success btn-sm" @click="savePolicy()">Save</button>
            </div>

        </div>
    </div>
</div>
<!-- POLICY LIST MODAL -->
<div class="modal fade" id="policyListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">All Policies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(p,i) in policies" :key="p.uid">
                            <td>@{{ p.policy_date }}</td>
                            <td>@{{ p.policy_name }}</td>
                            <td>@{{ p.policy_category }}</td>
                            <td>
                                <a v-if="p.file_preview" :href="p.file_preview" target="_blank">
                                    View
                                </a>
                                <span v-else>—</span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                        @click="editPolicy(i)"
                                        data-bs-toggle="modal" data-bs-target="#policyModal">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm ms-1"
                                        @click="removePolicy(i)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>
<!-- ADD / EDIT BANK MODAL -->
<div class="modal fade" id="bankModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    @{{ bankFormMode=='add' ? 'Add Bank Account' : 'Edit Bank Account' }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row gy-2">

                    <div class="col-lg-4">
                        <label class="form-label">Bank Name *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.bank_name">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Branch Name</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.branch_name">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Account Number *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.account_number">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">IBAN *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.iban_number">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">SWIFT Code</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.swift_code">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Finance Code</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.finance_code">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Currency</label>
                        <select class="form-select form-select-sm"
                                v-model="bankForm.currency">
                            <option value="">Select</option>
                            <option>AED</option>
                            <option>USD</option>
                            <option>INR</option>
                            <option>EUR</option>
                            <option>GBP</option>
                            <option>SAR</option>
                            <option>QAR</option>
                            <option>OMR</option>
                            <option>KWD</option>
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Bank Letter *</label>
                        <input type="file" class="form-control form-control-sm"
                               @change="bankFile($event)" accept=".pdf,image/*">
                    </div>

                    <div class="col-lg-4" v-if="bankForm.file_preview">
                        <label class="form-label">Current File</label><br>
                        <a :href="bankForm.file_preview" target="_blank">View File</a>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2">
                <span role="status" aria-hidden="true"></span>
                 <i class="ico icon-outline-bookmark-opened text-success"></i> <span>Save</span></button>
            </div>

        </div>
    </div>
</div>

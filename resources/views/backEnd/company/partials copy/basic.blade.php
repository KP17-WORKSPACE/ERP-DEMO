<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Basic Company Information</h5>

        <div class="row gy-3">

            {{-- Company Name --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.company_name">
                <small class="text-danger">@{{ errors.company_name }}</small>
            </div>

            {{-- Trade Name --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Trade Name</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.trade_name">
                <small class="text-danger">@{{ errors.trade_name }}</small>
            </div>

            {{-- Business Entity Type --}}
         {{-- Business Entity Type --}}
<div class="col-lg-2">
    <label class="form-label mb-1 d-flex justify-content-between align-items-center">
        <span>Business Entity Type</span>

        <!-- ADD ICON -->
        <button type="button" class="btn btn-sm p-0"
                data-bs-toggle="modal" data-bs-target="#entityTypeAddModal"
                style="border:none;background:none;">
            <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
        </button>
    </label>

    <select class="form-control form-control-sm"
            v-model="form.legal_entity_type">
        <option value="">Select Business Entity Type</option>
        <option v-for="ent in entities" :key="ent.id" :value="ent.id">
            @{{ ent.name }}
        </option>
    </select>

    <small class="text-danger">@{{ errors.legal_entity_type }}</small>
</div>


            {{-- Industry --}}
            <div class="col-lg-2">
                <label class="form-label mb-1 d-flex justify-content-between align-items-center">
                    <span>Industry Type</span>

                    <button type="button" class="btn btn-sm p-0"
                            data-bs-toggle="modal" data-bs-target="#industryAddPopup"
                            style="border:none;background:none;">
                        <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                    </button>
                </label>

                <select class="form-control form-control-sm"
                        v-model="form.industry_id"
                        @change="loadActivities">
                    <option value="">Select Industry</option>
                    <option v-for="ind in industries" :value="ind.id">@{{ ind.name }}</option>
                </select>

                <small class="text-danger">@{{ errors.industry_id }}</small>
            </div>

            {{-- Business Sector --}}
            <div class="col-lg-2">
                <label class="form-label mb-1 d-flex justify-content-between align-items-center">
                    <span>Business Sector</span>

                    <button type="button" class="btn btn-sm p-0"
                            data-bs-toggle="modal" data-bs-target="#activityModal"
                            style="border:none;background:none;">
                        <i class="ico icon-outline-add-square text-success" style="font-size:17px;"></i>
                    </button>
                </label>

                <select class="form-control form-control-sm"
                        v-model="form.business_activity_id">
                    <option value="">Select Business Activity</option>
                    <option v-for="act in filteredActivities" :value="act.id">@{{ act.name }}</option>
                </select>

                <small class="text-danger">@{{ errors.business_activity_id }}</small>
            </div>

            {{-- Company Type --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Company Type <span class="text-danger">*</span></label>

                <select class="form-select form-select-sm"
                        v-model="form.company_type"
                        @change="onCompanyTypeChange">

                    <option value="">Select Type</option>
                    <option value="parent">Parent Company</option>
                    <option value="subsidiary">Subsidiary Company</option>
                    <option value="branch">Branch</option>
                </select>

                <small class="text-danger">@{{ errors.company_type }}</small>
            </div>

            {{-- If company is NOT parent → select parent company --}}
            <div class="col-lg-2" v-if="form.company_type !== 'parent'">
                <label class="form-label mb-1">Select Main Company <span class="text-danger">*</span></label>

                <select class="form-select form-select-sm"
                        v-model="form.parent_company_id">
                    <option value="">Select Parent</option>
                    <option v-for="p in parentCompanies" :value="p.id">@{{ p.company_name }}</option>
                </select>

                <small class="text-danger">@{{ errors.parent_company_id }}</small>
            </div>

            {{-- Company Logo --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Company Logo</label>
                <input type="file" class="form-control form-control-sm"
                       @change="onFile($event,'company_logo')">
                <small class="text-danger">@{{ errors.company_logo }}</small>
            </div>

            {{-- Digital Stamp --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Digital Stamp</label>
                <input type="file" class="form-control form-control-sm"
                       @change="onFile($event,'digital_stamp')">
                <small class="text-danger">@{{ errors.digital_stamp }}</small>
            </div>

            {{-- Company Profile --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Company Profile</label>
                <input type="file" class="form-control form-control-sm"
                       accept=".pdf,.doc,.docx"
                       @change="onFile($event,'company_profile')">
                <small class="text-danger">@{{ errors.company_profile }}</small>
            </div>

        </div>

    </div>
</div>

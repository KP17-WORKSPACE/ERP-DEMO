<div class="card">
    <div class="card-body">

        <h5 class="mb-3">HR & Payroll Setup</h5>

        <div class="row gy-3">

            {{-- WPS Establishment ID --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">WPS Establishment ID <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.hr.wps_establishment_id">
                <small class="text-danger">@{{ errors['hr.wps_establishment_id'] }}</small>
            </div>

            {{-- WPS Bank --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">WPS Bank <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.hr.wps_bank">
                <small class="text-danger">@{{ errors['hr.wps_bank'] }}</small>
            </div>

            {{-- WPS Code --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">WPS Code</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.hr.wps_code">
                <small class="text-danger">@{{ errors['hr.wps_code'] }}</small>
            </div>

        </div>

    </div>
</div>

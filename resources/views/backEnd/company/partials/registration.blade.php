<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Company Registration Details</h5>

        <div class="row gy-3">

            {{-- Date of Incorporation --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Date of Incorporation <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm"
                       v-model="form.registration.date_of_incorporation">
                <small class="text-danger">@{{ errors['registration.date_of_incorporation'] }}</small>
            </div>

            {{-- Country --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Country <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm"
                        v-model="form.registration.country"
                        @change="loadStates">    
                    <option value="">Select</option>
                    <option v-for="c in countries" :value="c.id">@{{ c.name }}</option>
                </select>
                <small class="text-danger">@{{ errors['registration.country'] }}</small>
            </div>

            {{-- State --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">State <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm"
                        v-model="form.registration.state">
                    <option value="">Select</option>
                    <option v-for="s in states" :value="s.id">@{{ s.name }}</option>
                </select>
                <small class="text-danger">@{{ errors['registration.state'] }}</small>
            </div>

            {{-- Registered Address --}}
            <div class="col-lg-9">
                <label class="form-label mb-1">Registered Address <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-sm"
                          v-model.trim="form.registration.registered_address"
                          rows="2"></textarea>
                <small class="text-danger">@{{ errors['registration.registered_address'] }}</small>
            </div>

        </div>

    </div>
</div>

<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Contact Information</h5>

        <div class="row gy-3">

            {{-- Company Email --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Company Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control form-control-sm"
                       v-model.trim="form.contact.email"
                       placeholder="Enter Company Email">

                <small class="text-danger">@{{ errors['contact.email'] }}</small>
            </div>

            {{-- Website --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Company Website</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.contact.website"
                       placeholder="https://example.com">

                <small class="text-danger">@{{ errors['contact.website'] }}</small>
            </div>

            {{-- Office Phone --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Office Phone No <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.contact.phone"
                       placeholder="04-XXXXXXX">

                <small class="text-danger">@{{ errors['contact.phone'] }}</small>
            </div>

            {{-- Fax --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Fax Number</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.contact.fax">

                <small class="text-danger">@{{ errors['contact.fax'] }}</small>
            </div>

            {{-- Mobile --}}
            <div class="col-lg-2">
                <label class="form-label mb-1">Mobile Number</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.contact.mobile">

                <small class="text-danger">@{{ errors['contact.mobile'] }}</small>
            </div>

        </div>

    </div>
</div>

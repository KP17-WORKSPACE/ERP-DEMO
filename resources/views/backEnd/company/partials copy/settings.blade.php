<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Company Settings</h5>

        <div class="row gy-3">

            {{-- Sales Code --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Sales Code</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.settings.sales_code">
                <small class="text-danger">@{{ errors['settings.sales_code'] }}</small>
            </div>

            {{-- Other Code --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Other Code</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.settings.other_code">
                <small class="text-danger">@{{ errors['settings.other_code'] }}</small>
            </div>

        </div>

    </div>
</div>

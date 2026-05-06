<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Company Policies</h5>

        <div class="row gy-3">

            {{-- Attendance Policy --}}
            <div class="col-lg-6">
                <label class="form-label mb-1">Attendance Policy</label>
                <textarea class="form-control form-control-sm"
                          rows="3"
                          v-model.trim="form.policies.attendance"></textarea>
                <small class="text-danger">@{{ errors['policies.attendance'] }}</small>
            </div>

            {{-- Leave Policy --}}
            <div class="col-lg-6">
                <label class="form-label mb-1">Leave Policy</label>
                <textarea class="form-control form-control-sm"
                          rows="3"
                          v-model.trim="form.policies.leave_policy"></textarea>
                <small class="text-danger">@{{ errors['policies.leave_policy'] }}</small>
            </div>

        </div>

    </div>
</div>

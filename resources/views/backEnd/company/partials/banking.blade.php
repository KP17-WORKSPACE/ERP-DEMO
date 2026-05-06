<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Banking & Finance Information</h5>

        <div class="row gy-3">

            {{-- Account Name --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Account Name</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.banking.account_name">
                <small class="text-danger">@{{ errors['banking.account_name'] }}</small>
            </div>

            {{-- Bank Name --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">Bank Name</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.banking.bank_name">
                <small class="text-danger">@{{ errors['banking.bank_name'] }}</small>
            </div>

            {{-- IBAN --}}
            <div class="col-lg-3">
                <label class="form-label mb-1">IBAN</label>
                <input type="text" class="form-control form-control-sm"
                       v-model.trim="form.banking.iban">
                <small class="text-danger">@{{ errors['banking.iban'] }}</small>
            </div>

        </div>

    </div>
</div>

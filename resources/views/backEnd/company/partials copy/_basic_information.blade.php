<div class="row gy-2">

    {{-- Company Name --}}
    <div class="col-lg-3">
        <div class="input-effect">
            <label class="form-label mb-1">Company Name</label>
            <input type="text" class="form-control form-control-sm" name="company_name" id="company_name"
                value="{{ old('company_name', $company->company_name ?? '') }}">

            @if ($errors->has('company_name'))
                <small class="text-danger">{{ $errors->first('company_name') }}</small>
            @endif
        </div>
    </div>

    {{-- Trade Name --}}
    <div class="col-lg-3">
        <div class="input-effect">
            <label class="form-label mb-1">Trade Name</label>
            <input type="text" class="form-control form-control-sm" name="trade_name"
                value="{{ old('trade_name', $company->trade_name ?? '') }}">

            @if ($errors->has('trade_name'))
                <small class="text-danger">{{ $errors->first('trade_name') }}</small>
            @endif
        </div>
    </div>



    {{-- Business Entity Type --}}
    <div class="col-lg-2 mt-n1">
        <div class="input-effect">
            <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                <span>Business Entity Type</span>
                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;"
                    data-bs-toggle="modal" data-bs-target="#entityTypeAddModal">
                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                </button>
            </label>

                <select name="business_entity_type_id" class="form-control form-control-sm js-example-basic-single">
    <option value="">Select Business Entity Type</option>
    @foreach ($entities as $ent)
        <option value="{{ $ent->id }}"
            {{ (string) old('business_entity_type_id', $company->business_entity_type_id ?? '') === (string) $ent->id ? 'selected' : '' }}>
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
                    data-bs-target="#industryAddPopup">
                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                </button>
            </label>

            <select name="industry_type_id" id="industry_type_id" class="form-control form-control-sm js-example-basic-single">
                <option value="">Select Industry</option>
                @foreach ($industries as $ind)
                    <option value="{{ $ind->id }}"
                        {{ old('industry_type_id', $company->industry_type_id ?? '') == $ind->id ? 'selected' : '' }}>
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
                <button type="button" class="btn btn-sm p-0" data-bs-toggle="modal" data-bs-target="#activityModal">
                    <i class="ico icon-outline-add-square text-success" style="font-size:17px;"></i>
                </button>
            </label>

            <select name="business_sector_id" id="business_sector_id" class="form-control form-control-sm js-example-basic-single">
                <option value="">Select Sector</option>
            </select>
        </div>
    </div>



        <input type="text" hidden class="form-control form-control-sm" name="company_code"
            value="{{ old('company_code', isset($company) && $company ? $company->id : $nextId) }}" readonly>
  


    <div class="col-1-5">
        <label class="form-label mb-1">Date of Incorporation</label>
        <input type="text" name="date_of_incorporation" class="form-control form-control-sm date-picker"
            value="{{ old('date_of_incorporation', $company->date_of_incorporation ?? '') }}">
    </div>

    <input type="hidden" name="company_id" id="company_id" value="{{ $company->id ?? '' }}">
    {{-- Company Type --}}
    <div class="col-1-5">
        <label class="form-label mb-1">Company Type</label>

        <select name="company_type" id="company_type" class="form-control form-control-sm">
            <option value="">Select Type</option>
            <option value="parent"
                {{ old('company_type', $company->company_type ?? '') == 'parent' ? 'selected' : '' }}>Parent</option>
            <option value="subsidiary"
                {{ old('company_type', $company->company_type ?? '') == 'subsidiary' ? 'selected' : '' }}>Subsidiary
            </option>
            <option value="branch"
                {{ old('company_type', $company->company_type ?? '') == 'branch' ? 'selected' : '' }}>Branch</option>
            <option value="sub_branch"
                {{ old('company_type', $company->company_type ?? '') == 'sub_branch' ? 'selected' : '' }}>Sub Branch
            </option>
        </select>

        @if ($errors->has('company_type'))
            <small class="text-danger">{{ $errors->first('company_type') }}</small>
        @endif
    </div>

   



    {{-- Parent Name (ONLY FOR PARENT) --}}
    <div class="col-lg-3 {{ old('company_type', $company->company_type ?? '') == 'parent' ? '' : 'd-none' }}"
        id="parentNameBox">
        <label class="form-label mb-1">Parent Company Name</label>
        <input type="text" name="parent_company" id="parent_company_name" class="form-control form-control-sm"
            value="{{ old('parent_company', $company->parent_company ?? '') }}">
    </div>

    {{-- Parent Company Dropdown (Subsidiary/Branch/Sub Branch) --}}
    <div class="col-lg-2 {{ in_array(old('company_type', $company->company_type ?? ''), ['subsidiary', 'branch', 'sub_branch']) ? '' : 'd-none' }}"
        id="parentDropdownBox">
        <label class="form-label mb-1">Select Parent Company</label>

        <select name="parent_company_id" id="parent_company_id" class="form-select form-select-sm">
            <option value="">Select Company</option>
            @foreach ($parentCompanies as $comp)
                <option value="{{ $comp->id }}"
                    {{ old('parent_company_id', $company->parent_company_id ?? '') == $comp->id ? 'selected' : '' }}>
                    {{ $comp->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Company Logo --}}
    <div class="col-lg-2">
        <label class="form-label mb-1">Company Logo</label>
        @if (!empty($company->company_logo))
            <div class="mb-2">
                <img src="{{ asset('public/' . $company->company_logo) }}" alt="Logo"
                    class="img-thumbnail" width="80">
            </div>
        @endif
        <input type="file" id="company_logo_input" class="form-control form-control-sm" name="company_logo" accept=".pdf,.jpg,.jpeg,.png,.webp">
    </div>

    {{-- Digital Stamp --}}
    <div class="col-lg-2">
        <label class="form-label mb-1">Digital Stamp</label>
        @if (!empty($company->digital_stamp))
            <div class="mb-2">
                <img src="{{ asset('public/' . $company->digital_stamp) }}" alt="Digital Stamp"
                    class="img-thumbnail" width="80">
            </div>
        @endif
        <input type="file" id="digital_stamp_input" class="form-control form-control-sm" name="digital_stamp" accept=".pdf,.jpg,.jpeg,.png,.webp">
    </div>

    {{-- Company Profile --}}
    <div class="col-lg-2 mb-4">
        <label class="form-label mb-1">Company Profile</label>
        @if (!empty($company->company_profile))
            <div class="mb-2">
                <a href="{{ asset('public/' . $company->company_profile) }}" target="_blank">View
                    Current Profile</a>
            </div>
        @endif
        <input type="file" id="company_profile_input" class="form-control form-control-sm" name="company_profile" accept=".pdf,.jpg,.jpeg,.png,.webp">
    </div>

</div>



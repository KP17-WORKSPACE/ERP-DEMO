<div class="row gy-2">

    {{-- Company Name --}}
    <div class="col-lg-3">
        <div class="input-effect">
            <label class="form-label mb-1">Company Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control form-control-sm"
                   name="company_name"
                   id="company_name"
                   value="{{ old('company_name', $company->company_name ?? '') }}">

            @if($errors->has('company_name'))
                <small class="text-danger">{{ $errors->first('company_name') }}</small>
            @endif
        </div>
    </div>

    {{-- Trade Name --}}
    <div class="col-lg-3">
        <div class="input-effect">
            <label class="form-label mb-1">Trade Name</label>
            <input type="text"
                   class="form-control form-control-sm"
                   name="trade_name"
                   value="{{ old('trade_name', $company->trade_name ?? '') }}">

            @if($errors->has('trade_name'))
                <small class="text-danger">{{ $errors->first('trade_name') }}</small>
            @endif
        </div>
    </div>

    {{-- Business Entity Type --}}
    <div class="col-lg-2">
        <div class="input-effect">
            <label class="form-label mb-1 d-flex justify-content-between align-items-center">
                <span>Business Entity Type</span>
                <button type="button"
                        class="btn btn-sm p-0 ms-2"
                        style="border:none;background:none;"
                        data-bs-toggle="modal"
                        data-bs-target="#entityTypeAddModal">
                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                </button>
            </label>

            <select name="business_entity_id" class="form-control form-control-sm">
                <option value="">Select Business Entity Type</option>
                @foreach ($entities as $ent)
                    <option value="{{ $ent->id }}"
                        {{ (old('business_entity_id', $company->business_entity_id ?? '') == $ent->id) ? 'selected' : '' }}>
                        {{ $ent->name }}
                    </option>
                @endforeach
            </select>

            @if($errors->has('business_entity_id'))
                <small class="text-danger">{{ $errors->first('business_entity_id') }}</small>
            @endif
        </div>
    </div>

    {{-- Industry Type --}}
    <div class="col-lg-2">
        <div class="input-effect">
            <label class="form-label mb-1 d-flex justify-content-between align-items-center">
                <span>Industry Type</span>
                <button type="button" class="btn btn-sm p-0 ms-2"
                        data-bs-toggle="modal" data-bs-target="#industryAddPopup">
                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                </button>
            </label>

            <select name="industry_id" id="industry_id" class="form-control form-control-sm">
                <option value="">Select Industry</option>
                @foreach ($industries as $ind)
                    <option value="{{ $ind->id }}"
                        {{ (old('industry_id', $company->industry_id ?? '') == $ind->id) ? 'selected' : '' }}>
                        {{ $ind->name }}
                    </option>
                @endforeach
            </select>

            @if($errors->has('industry_id'))
                <small class="text-danger">{{ $errors->first('industry_id') }}</small>
            @endif
        </div>
    </div>

    {{-- Business Sector (Dependent) --}}
    <div class="col-lg-2">
        <div class="input-effect">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0">Business Sector</label>
                <button type="button" class="btn btn-sm p-0"
                        data-bs-toggle="modal" data-bs-target="#activityModal">
                    <i class="ico icon-outline-add-square text-success" style="font-size:17px;"></i>
                </button>
            </div>

            <select name="business_sector_id"
                    id="business_sector_id"
                    class="form-control form-control-sm">

                <option value="">Select Sector</option>

                {{-- Pre-select for edit --}}
                @if(old('business_sector_id', $company->business_sector_id ?? false))
                    @php
                        $selectedSector = \App\SmBusinessActivity::find(old('business_sector_id', $company->business_sector_id ?? 0));
                    @endphp
                    @if($selectedSector)
                        <option value="{{ $selectedSector->id }}" selected>
                            {{ $selectedSector->name }}
                        </option>
                    @endif
                @endif

            </select>

            @if($errors->has('business_sector_id'))
                <small class="text-danger">{{ $errors->first('business_sector_id') }}</small>
            @endif
        </div>
    </div>

    {{-- Company Code --}}
   <div class="col-lg-2">
    <label class="form-label mb-1">Company Code</label>
    <input type="text"
           class="form-control form-control-sm"
           name="company_code"
           value="{{ old('company_code', $company->company_code ?? $nextId) }}"
           readonly>
</div>

<input type="hidden" name="company_id" id="company_id" value="{{ $company->id ?? '' }}">
    {{-- Company Type --}}
    <div class="col-lg-2">
        <label class="form-label mb-1">Company Type <span class="text-danger">*</span></label>

        <select name="company_type" id="company_type" class="form-control form-control-sm">
            <option value="">Select Type</option>
            <option value="parent"     {{ (old('company_type', $company->company_type ?? '') == 'parent') ? 'selected' : '' }}>Parent</option>
            <option value="subsidiary" {{ (old('company_type', $company->company_type ?? '') == 'subsidiary') ? 'selected' : '' }}>Subsidiary</option>
            <option value="branch"     {{ (old('company_type', $company->company_type ?? '') == 'branch') ? 'selected' : '' }}>Branch</option>
        </select>

        @if($errors->has('company_type'))
            <small class="text-danger">{{ $errors->first('company_type') }}</small>
        @endif
    </div>

    {{-- Parent Name (ONLY FOR PARENT) --}}
    <div class="col-lg-2 {{ (old('company_type', $company->company_type ?? '') == 'parent') ? '' : 'd-none' }}" id="parentNameBox">
        <label class="form-label mb-1">Parent Company Name</label>
        <input type="text" name="parent_company" id="parent_company_name"
               class="form-control form-control-sm"
               value="{{ old('parent_company', $company->parent_company ?? '') }}">
    </div>

    {{-- Parent Company Dropdown (Subsidiary/Branch) --}}
    <div class="col-lg-2 {{ in_array(old('company_type', $company->company_type ?? ''), ['subsidiary','branch']) ? '' : 'd-none' }}" id="parentDropdownBox">
        <label class="form-label mb-1">Select Parent Company</label>

        <select name="parent_company_id" id="parent_company_id" class="form-select form-select-sm">
            <option value="">Select Company</option>
            @foreach($parentCompanies as $comp)
                <option value="{{ $comp->id }}"
                    {{ (old('parent_company_id', $company->parent_company_id ?? '') == $comp->id) ? 'selected' : '' }}>
                    {{ $comp->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Company Logo --}}
    <div class="col-lg-2">
        <label class="form-label mb-1">Company Logo</label>
        @if(!empty($company->company_logo))
            <div class="mb-2">
                <img src="{{ asset('storage/company_logo/'.$company->company_logo) }}" alt="Logo" class="img-thumbnail" width="80">
            </div>
        @endif
        <input type="file" class="form-control form-control-sm" name="company_logo">
    </div>

    {{-- Digital Stamp --}}
    <div class="col-lg-2">
        <label class="form-label mb-1">Digital Stamp</label>
        @if(!empty($company->digital_stamp))
            <div class="mb-2">
                <img src="{{ asset('storage/digital_stamp/'.$company->digital_stamp) }}" alt="Digital Stamp" class="img-thumbnail" width="80">
            </div>
        @endif
        <input type="file" class="form-control form-control-sm" name="digital_stamp">
    </div>

    {{-- Company Profile --}}
    <div class="col-lg-2 mb-4">
        <label class="form-label mb-1">Company Profile</label>
        @if(!empty($company->company_profile))
            <div class="mb-2">
                <a href="{{ asset('storage/company_profile/'.$company->company_profile) }}" target="_blank">View Current Profile</a>
            </div>
        @endif
        <input type="file" class="form-control form-control-sm" name="company_profile">
    </div>

</div>

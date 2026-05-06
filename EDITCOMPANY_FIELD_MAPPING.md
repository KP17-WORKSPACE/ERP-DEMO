# Edit Company Form - Field Population Guide

This document provides the complete mapping for all 130+ fields in `editCompany.blade.php`.

## Controller Setup ✓

The `companyEdit()` method in `SysCompanyController.php` now fetches all necessary data:

```php
- $company (main company record with relationships)
- $countries, $states, $currencies (lookup data)
- $industries, $businessSectors, $entityTypes (business classification)
- $parentCompanies (for subsidiary companies)
- $workingShifts (for HR attendance)
- $hrPayrollSetting (HR payroll settings)
- $setting (company general settings)
- $banks (banking records)
- $policies (HR policies)
- $warehouses (warehouse locations)
- $compliance (registration/compliance data)
- $owners, $sponsors, $contacts (company people)
- $nonUaeDocuments (non-UAE compliance docs)
- $nationalities (for people dropdowns)
```

---

## SECTION 1: BASIC COMPANY INFORMATION

### Company Type & Structure
```blade
<select name="company_type" value="{{ $company->company_type ?? '' }}">
    <option value="parent" {{ ($company->company_type ?? '') == 'parent' ? 'selected' : '' }}>Parent Company</option>
    <option value="subsidiary" {{ ($company->company_type ?? '') == 'subsidiary' ? 'selected' : '' }}>Subsidiary</option>
    <option value="branch" {{ ($company->company_type ?? '') == 'branch' ? 'selected' : '' }}>Branch</option>
</select>

<!-- Parent Company (if subsidiary) -->
<select name="parent_company_id">
    <option value="">Select Parent Company</option>
    @foreach($parentCompanies as $parent)
        <option value="{{ $parent->id }}" {{ ($company->parent_company_id ?? '') == $parent->id ? 'selected' : '' }}>
            {{ $parent->company_name }}
        </option>
    @endforeach
</select>
```

### Basic Details (15 fields)
```blade
<input type="text" name="company_name" value="{{ $company->company_name ?? '' }}">
<input type="text" name="company_name_arabic" value="{{ $company->company_name_arabic ?? '' }}">
<input type="text" name="company_short_name" value="{{ $company->company_short_name ?? '' }}">
<input type="text" name="trade_name" value="{{ $company->trade_name ?? '' }}">
<input type="text" name="trade_name_arabic" value="{{ $company->trade_name_arabic ?? '' }}">

<!-- Logo Upload -->
<input type="file" name="company_logo" accept="image/*">
@if($company->company_logo ?? false)
    <img src="{{ asset($company->company_logo) }}" alt="Current Logo" style="max-width: 100px;">
@endif

<!-- Industry & Business Sector -->
<select name="industry_id">
    @foreach($industries as $industry)
        <option value="{{ $industry->id }}" {{ ($company->industry_id ?? '') == $industry->id ? 'selected' : '' }}>
            {{ $industry->name }}
        </option>
    @endforeach
</select>

<select name="business_sector_id">
    @foreach($businessSectors as $sector)
        <option value="{{ $sector->id }}" {{ ($company->business_sector_id ?? '') == $sector->id ? 'selected' : '' }}>
            {{ $sector->name }}
        </option>
    @endforeach
</select>

<select name="business_entity_type_id">
    @foreach($entityTypes as $entity)
        <option value="{{ $entity->id }}" {{ ($company->business_entity_type_id ?? '') == $entity->id ? 'selected' : '' }}>
            {{ $entity->name }}
        </option>
    @endforeach
</select>
```

---

## SECTION 2: CONTACT INFORMATION (20 fields)

```blade
<!-- Contact Details -->
<input type="email" name="company_email" value="{{ $company->company_email ?? '' }}">
<input type="url" name="website" value="{{ $company->website ?? '' }}">
<input type="tel" name="office_phone" value="{{ $company->office_phone ?? '' }}">
<input type="tel" name="mobile" value="{{ $company->mobile ?? '' }}">
<input type="tel" name="fax" value="{{ $company->fax ?? '' }}">
<input type="text" name="po_box" value="{{ $company->po_box ?? '' }}">

<!-- Date of Incorporation -->
<input type="date" name="date_of_incorporation" value="{{ $company->date_of_incorporation ?? '' }}">

<!-- Address Fields -->
<select name="country">
    @foreach($countries as $country)
        <option value="{{ $country->id }}" {{ ($company->country ?? '') == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
    @endforeach
</select>

<select name="state">
    @foreach($states as $state)
        <option value="{{ $state->id }}" {{ ($company->state ?? '') == $state->id ? 'selected' : '' }}>
            {{ $state->name }}
        </option>
    @endforeach
</select>

<input type="text" name="city" value="{{ $company->city ?? '' }}">
<input type="text" name="address" value="{{ $company->address ?? '' }}">
<textarea name="address_arabic">{{ $company->address_arabic ?? '' }}</textarea>
<input type="text" name="building_name" value="{{ $company->building_name ?? '' }}">
<input type="text" name="office_number" value="{{ $company->office_number ?? '' }}">
<input type="text" name="zip_code" value="{{ $company->zip_code ?? '' }}">

<!-- Social Media -->
<input type="url" name="facebook" value="{{ $company->facebook ?? '' }}">
<input type="url" name="twitter" value="{{ $company->twitter ?? '' }}">
<input type="url" name="linkedin" value="{{ $company->linkedin ?? '' }}">
<input type="url" name="instagram" value="{{ $company->instagram ?? '' }}">
```

---

## SECTION 3: OWNERS (Dynamic Rows with Documents)

```blade
<div id="ownerWrapper">
    @forelse($owners as $index => $owner)
        <div class="ownerRow" data-index="{{ $index }}">
            <input type="hidden" name="owners[{{ $index }}][id]" value="{{ $owner->id ?? '' }}">
            
            <select name="owners[{{ $index }}][salutation]">
                <option value="Mr" {{ ($owner->salutation ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                <option value="Mrs" {{ ($owner->salutation ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                <option value="Miss" {{ ($owner->salutation ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                <option value="Ms" {{ ($owner->salutation ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                <option value="Dr" {{ ($owner->salutation ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
            </select>
            
            <input type="text" name="owners[{{ $index }}][first_name]" value="{{ $owner->first_name ?? '' }}">
            <input type="text" name="owners[{{ $index }}][last_name]" value="{{ $owner->last_name ?? '' }}">
            <input type="text" name="owners[{{ $index }}][name]" value="{{ $owner->name ?? '' }}">
            <input type="tel" name="owners[{{ $index }}][mobile]" value="{{ $owner->mobile ?? '' }}">
            <input type="email" name="owners[{{ $index }}][email]" value="{{ $owner->email ?? '' }}">
            <input type="number" name="owners[{{ $index }}][share_percentage]" value="{{ $owner->share_percentage ?? '' }}" min="0" max="100" step="0.01">
            
            <!-- Owner Documents -->
            <button type="button" onclick="ownerdocumentModal(this)">Manage Documents</button>
            
            <div class="owner-documents-container" style="display:none;">
                @if($owner->documents ?? false)
                    @foreach($owner->documents as $docIndex => $doc)
                        <input type="hidden" name="owners[{{ $index }}][documents][{{ $docIndex }}][id]" value="{{ $doc->id ?? '' }}">
                        <input type="text" name="owners[{{ $index }}][documents][{{ $docIndex }}][document_type]" value="{{ $doc->document_type ?? '' }}">
                        <input type="file" name="owners[{{ $index }}][documents][{{ $docIndex }}][file]">
                        @if($doc->file_path ?? false)
                            <a href="{{ asset($doc->file_path) }}" target="_blank">View Current</a>
                        @endif
                    @endforeach
                @endif
            </div>
            
            <button type="button" class="removeOwner">Remove</button>
        </div>
    @empty
        <!-- Template row for adding new owners -->
        <div class="ownerRow" data-index="0">
            <!-- Same structure as above but with empty values -->
        </div>
    @endforelse
</div>
<button type="button" class="addOwner">Add Owner</button>
```

---

## SECTION 4: SPONSORS (Dynamic Rows)

```blade
<div id="sponsorWrapper">
    @forelse($sponsors as $index => $sponsor)
        <div class="sponsorRow" data-index="{{ $index }}">
            <input type="hidden" name="sponsors[{{ $index }}][id]" value="{{ $sponsor->id ?? '' }}">
            
            <select name="sponsors[{{ $index }}][salutation]">
                <option value="Mr" {{ ($sponsor->salutation ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                <option value="Mrs" {{ ($sponsor->salutation ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                <option value="Miss" {{ ($sponsor->salutation ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                <option value="Ms" {{ ($sponsor->salutation ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                <option value="Dr" {{ ($sponsor->salutation ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
            </select>
            
            <input type="text" name="sponsors[{{ $index }}][first_name]" value="{{ $sponsor->first_name ?? '' }}">
            <input type="text" name="sponsors[{{ $index }}][last_name]" value="{{ $sponsor->last_name ?? '' }}">
            <input type="text" name="sponsors[{{ $index }}][name]" value="{{ $sponsor->name ?? '' }}">
            <input type="tel" name="sponsors[{{ $index }}][mobile]" value="{{ $sponsor->mobile ?? '' }}">
            <input type="email" name="sponsors[{{ $index }}][email]" value="{{ $sponsor->email ?? '' }}">
            
            <!-- Sponsor Documents -->
            <button type="button" onclick="sponsordocumentModal(this)">Manage Documents</button>
            
            <button type="button" class="removeSponsor">Remove</button>
        </div>
    @empty
        <div class="sponsorRow" data-index="0">
            <!-- Template row -->
        </div>
    @endforelse
</div>
<button type="button" class="addSponsor">Add Sponsor</button>
```

---

## SECTION 5: CONTACTS (Dynamic Rows)

```blade
<div id="contactWrapper">
    @forelse($contacts as $index => $contact)
        <div class="contactRow" data-index="{{ $index }}">
            <input type="hidden" name="contacts[{{ $index }}][id]" value="{{ $contact->id ?? '' }}">
            
            <select name="contacts[{{ $index }}][salutation]">
                <option value="Mr" {{ ($contact->salutation ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                <option value="Mrs" {{ ($contact->salutation ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                <option value="Miss" {{ ($contact->salutation ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                <option value="Ms" {{ ($contact->salutation ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                <option value="Dr" {{ ($contact->salutation ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
            </select>
            
            <input type="text" name="contacts[{{ $index }}][first_name]" value="{{ $contact->first_name ?? '' }}">
            <input type="text" name="contacts[{{ $index }}][last_name]" value="{{ $contact->last_name ?? '' }}">
            <input type="text" name="contacts[{{ $index }}][name]" value="{{ $contact->name ?? '' }}">
            <input type="tel" name="contacts[{{ $index }}][mobile]" value="{{ $contact->mobile ?? '' }}">
            <input type="email" name="contacts[{{ $index }}][email]" value="{{ $contact->email ?? '' }}">
            <input type="text" name="contacts[{{ $index }}][designation]" value="{{ $contact->designation ?? '' }}">
            
            <!-- Contact Documents -->
            <button type="button" onclick="contactdocumentModal(this)">Manage Documents</button>
            
            <button type="button" class="removeContact">Remove</button>
        </div>
    @empty
        <div class="contactRow" data-index="0">
            <!-- Template row -->
        </div>
    @endforelse
</div>
<button type="button" class="addContact">Add Contact</button>
```

---

## SECTION 6: COMPLIANCE/REGISTRATION (UAE - 8 Documents)

```blade
@php
    $comp = $compliance ?? null;
@endphp

<!-- Trade License -->
<input type="text" name="trade_license_number" value="{{ $comp->trade_license_number ?? '' }}">
<input type="date" name="trade_license_issue_date" value="{{ $comp->trade_license_issue_date ?? '' }}">
<input type="date" name="trade_license_expiry_date" value="{{ $comp->trade_license_expiry_date ?? '' }}">
<input type="file" name="establishment_file">
@if($comp->establishment_file ?? false)
    <a href="{{ asset($comp->establishment_file) }}" target="_blank">View Current</a>
@endif

<!-- Immigration Card -->
<input type="text" name="immigration_card_number" value="{{ $comp->immigration_card_number ?? '' }}">
<input type="date" name="immigration_card_issue_date" value="{{ $comp->immigration_card_issue_date ?? '' }}">
<input type="date" name="immigration_card_expiry_date" value="{{ $comp->immigration_card_expiry_date ?? '' }}">
<input type="file" name="immigration_file">

<!-- Labour Card -->
<input type="text" name="labour_card_number" value="{{ $comp->labour_card_number ?? '' }}">
<input type="date" name="labour_card_issue_date" value="{{ $comp->labour_card_issue_date ?? '' }}">
<input type="date" name="labour_card_expiry_date" value="{{ $comp->labour_card_expiry_date ?? '' }}">
<input type="file" name="labour_file">

<!-- Chamber of Commerce -->
<input type="text" name="chamber_of_commerce_number" value="{{ $comp->chamber_of_commerce_number ?? '' }}">
<input type="date" name="chamber_of_commerce_issue_date" value="{{ $comp->chamber_of_commerce_issue_date ?? '' }}">
<input type="date" name="chamber_of_commerce_expiry_date" value="{{ $comp->chamber_of_commerce_expiry_date ?? '' }}">
<input type="file" name="chamber_file">

<!-- Insurance Policy -->
<input type="text" name="insurance_policy_number" value="{{ $comp->insurance_policy_number ?? '' }}">
<input type="date" name="insurance_policy_issue_date" value="{{ $comp->insurance_policy_issue_date ?? '' }}">
<input type="date" name="insurance_policy_expiry_date" value="{{ $comp->insurance_policy_expiry_date ?? '' }}">
<input type="file" name="insurance_file">

<!-- MOA/AOA -->
<input type="file" name="moa_aoa_file">
@if($comp->moa_aoa_file ?? false)
    <a href="{{ asset($comp->moa_aoa_file) }}" target="_blank">View Current</a>
@endif

<!-- Board Resolution -->
<input type="file" name="board_resolution_file">
@if($comp->board_resolution_file ?? false)
    <a href="{{ asset($comp->board_resolution_file) }}" target="_blank">View Current</a>
@endif

<!-- Power of Attorney -->
<input type="file" name="poa_file">
@if($comp->poa_file ?? false)
    <a href="{{ asset($comp->poa_file) }}" target="_blank">View Current</a>
@endif
```

---

## SECTION 7: NON-UAE COMPLIANCE DOCUMENTS (Dynamic)

```blade
<div id="nonUaeDocumentsWrapper">
    @forelse($nonUaeDocuments as $index => $doc)
        <div class="complianceDocRow" data-index="{{ $index }}">
            <input type="hidden" name="compliance_docs[{{ $index }}][id]" value="{{ $doc->id ?? '' }}">
            <input type="text" name="compliance_docs[{{ $index }}][document_name]" value="{{ $doc->document_name ?? '' }}">
            <input type="text" name="compliance_docs[{{ $index }}][document_number]" value="{{ $doc->document_number ?? '' }}">
            <input type="date" name="compliance_docs[{{ $index }}][issue_date]" value="{{ $doc->issue_date ?? '' }}">
            <input type="date" name="compliance_docs[{{ $index }}][expiry_date]" value="{{ $doc->expiry_date ?? '' }}">
            <input type="file" name="compliance_docs[{{ $index }}][file]">
            @if($doc->file_path ?? false)
                <a href="{{ asset($doc->file_path) }}" target="_blank">View Current</a>
            @endif
            <button type="button" class="removeComplianceDoc">Remove</button>
        </div>
    @empty
        <!-- Empty template -->
    @endforelse
</div>
<button type="button" onclick="openComplianceDocumentModal()">Add Document</button>
```

---

## SECTION 8: BANKING (Dynamic Rows)

```blade
<div id="bankingWrapper">
    @forelse($banks as $index => $bank)
        <div class="bankRow" data-index="{{ $index }}">
            <input type="hidden" name="banks[{{ $index }}][id]" value="{{ $bank->id ?? '' }}">
            <input type="text" name="banks[{{ $index }}][bank_name]" value="{{ $bank->bank_name ?? '' }}" required>
            <input type="text" name="banks[{{ $index }}][branch_name]" value="{{ $bank->branch_name ?? '' }}">
            <input type="text" name="banks[{{ $index }}][account_number]" value="{{ $bank->account_number ?? '' }}" required>
            <input type="text" name="banks[{{ $index }}][iban_number]" value="{{ $bank->iban_number ?? '' }}" required>
            <input type="text" name="banks[{{ $index }}][swift_code]" value="{{ $bank->swift_code ?? '' }}">
            <input type="text" name="banks[{{ $index }}][finance_code]" value="{{ $bank->finance_code ?? '' }}">
            
            <select name="banks[{{ $index }}][currency]">
                @foreach($currencies as $currency)
                    <option value="{{ $currency->code }}" {{ ($bank->currency ?? '') == $currency->code ? 'selected' : '' }}>
                        {{ $currency->code }} - {{ $currency->name }}
                    </option>
                @endforeach
            </select>
            
            <input type="file" name="banks[{{ $index }}][bank_letter]">
            @if($bank->bank_letter ?? false)
                <a href="{{ asset($bank->bank_letter) }}" target="_blank">View Current Letter</a>
            @endif
            
            <button type="button" class="removeBank">Remove</button>
        </div>
    @empty
        <!-- Template row -->
    @endforelse
</div>
<button type="button" class="addBank">Add Bank</button>
```

---

## SECTION 9: WAREHOUSES (Dynamic Rows)

```blade
<div id="warehouseWrapper">
    @forelse($warehouses as $index => $warehouse)
        <div class="warehouseRow" data-index="{{ $index }}">
            <input type="hidden" name="warehouses[{{ $index }}][id]" value="{{ $warehouse->id ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][warehouse_name]" value="{{ $warehouse->warehouse_name ?? '' }}" required>
            <input type="text" name="warehouses[{{ $index }}][warehouse_code]" value="{{ $warehouse->warehouse_code ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][address]" value="{{ $warehouse->address ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][city]" value="{{ $warehouse->city ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][state]" value="{{ $warehouse->state ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][country]" value="{{ $warehouse->country ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][zip_code]" value="{{ $warehouse->zip_code ?? '' }}">
            <input type="tel" name="warehouses[{{ $index }}][phone]" value="{{ $warehouse->phone ?? '' }}">
            <input type="email" name="warehouses[{{ $index }}][email]" value="{{ $warehouse->email ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][manager_name]" value="{{ $warehouse->manager_name ?? '' }}">
            <input type="number" name="warehouses[{{ $index }}][capacity]" value="{{ $warehouse->capacity ?? '' }}">
            <input type="text" name="warehouses[{{ $index }}][capacity_unit]" value="{{ $warehouse->capacity_unit ?? '' }}">
            
            <select name="warehouses[{{ $index }}][status]">
                <option value="active" {{ ($warehouse->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($warehouse->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            
            <button type="button" class="removeWarehouse">Remove</button>
        </div>
    @empty
        <!-- Template row -->
    @endforelse
</div>
<button type="button" class="addWarehouse">Add Warehouse</button>
```

---

## SECTION 10: HR POLICIES (Dynamic Rows)

```blade
<div id="policyWrapper">
    @forelse($policies as $index => $policy)
        <div class="policyRow" data-index="{{ $index }}">
            <input type="hidden" name="policies[{{ $index }}][id]" value="{{ $policy->id ?? '' }}">
            <input type="text" name="policies[{{ $index }}][policy_name]" value="{{ $policy->policy_name ?? '' }}" required>
            <input type="date" name="policies[{{ $index }}][policy_date]" value="{{ $policy->policy_date ?? '' }}" required>
            <textarea name="policies[{{ $index }}][policy_description]">{{ $policy->policy_description ?? '' }}</textarea>
            
            <select name="policies[{{ $index }}][view_to_employees]">
                <option value="1" {{ ($policy->view_to_employees ?? '0') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ ($policy->view_to_employees ?? '0') == '0' ? 'selected' : '' }}>No</option>
            </select>
            
            <input type="file" name="policies[{{ $index }}][policy_file]">
            @if($policy->policy_file ?? false)
                <a href="{{ asset($policy->policy_file) }}" target="_blank">View Current</a>
            @endif
            
            <button type="button" class="removePolicy">Remove</button>
        </div>
    @empty
        <!-- Template row -->
    @endforelse
</div>
<button type="button" class="addPolicy">Add Policy</button>
```

---

## SECTION 11: COMPANY SETTINGS (sys_company_setting)

```blade
@php
    $set = $setting ?? null;
@endphp

<!-- Currency Settings -->
<select name="currency">
    @foreach($currencies as $currency)
        <option value="{{ $currency->code }}" {{ ($set->currency ?? '') == $currency->code ? 'selected' : '' }}>
            {{ $currency->code }} - {{ $currency->name }}
        </option>
    @endforeach
</select>

<input type="text" name="currency_symbol" value="{{ $set->currency_symbol ?? '' }}">
<input type="number" name="currency_digit" value="{{ $set->currency_digit ?? 2 }}" min="0" max="10">

<!-- Code Generation Settings -->
<input type="checkbox" name="is_customer_code" value="1" {{ ($set->is_customer_code ?? false) ? 'checked' : '' }}>
<input type="checkbox" name="is_supplier_code" value="1" {{ ($set->is_supplier_code ?? false) ? 'checked' : '' }}>
<input type="checkbox" name="is_account_code" value="1" {{ ($set->is_account_code ?? false) ? 'checked' : '' }}>
<input type="checkbox" name="is_subaccount_code" value="1" {{ ($set->is_subaccount_code ?? false) ? 'checked' : '' }}>

<input type="text" name="r_code" value="{{ $set->r_code ?? '' }}">
<input type="text" name="p_code" value="{{ $set->p_code ?? '' }}">
<input type="text" name="sales_code" value="{{ $set->sales_code ?? '' }}">
<input type="text" name="other_code" value="{{ $set->other_code ?? '' }}">

<!-- Book Closed Date -->
<input type="date" name="book_closed" value="{{ $set->book_closed ?? '' }}">

<!-- HR Settings in Company Settings -->
<input type="text" name="hr_wps_establishment_id" value="{{ $set->hr_wps_establishment_id ?? '' }}">
<input type="text" name="hr_wps_bank" value="{{ $set->hr_wps_bank ?? '' }}">
<input type="text" name="hr_wps_salary_file_code" value="{{ $set->hr_wps_salary_file_code ?? '' }}">

<select name="hr_payroll_cycle">
    <option value="">Select Cycle</option>
    <option value="monthly" {{ ($set->hr_payroll_cycle ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
    <option value="bi-weekly" {{ ($set->hr_payroll_cycle ?? '') == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
    <option value="weekly" {{ ($set->hr_payroll_cycle ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
</select>

<input type="number" name="hr_payroll_start" value="{{ $set->hr_payroll_start ?? '' }}" min="1" max="30">
<input type="number" name="hr_payroll_end" value="{{ $set->hr_payroll_end ?? '' }}" min="1" max="30">

<!-- Weekly Off Days (can be JSON array) -->
@php
    $weeklyOffDays = [];
    if (isset($set->hr_weekly_off) && $set->hr_weekly_off) {
        $decoded = json_decode($set->hr_weekly_off, true);
        $weeklyOffDays = is_array($decoded) ? $decoded : [$set->hr_weekly_off];
    }
@endphp

<select name="hr_weekly_off[]" multiple>
    <option value="Monday" {{ in_array('Monday', $weeklyOffDays) ? 'selected' : '' }}>Monday</option>
    <option value="Tuesday" {{ in_array('Tuesday', $weeklyOffDays) ? 'selected' : '' }}>Tuesday</option>
    <option value="Wednesday" {{ in_array('Wednesday', $weeklyOffDays) ? 'selected' : '' }}>Wednesday</option>
    <option value="Thursday" {{ in_array('Thursday', $weeklyOffDays) ? 'selected' : '' }}>Thursday</option>
    <option value="Friday" {{ in_array('Friday', $weeklyOffDays) ? 'selected' : '' }}>Friday</option>
    <option value="Saturday" {{ in_array('Saturday', $weeklyOffDays) ? 'selected' : '' }}>Saturday</option>
    <option value="Sunday" {{ in_array('Sunday', $weeklyOffDays) ? 'selected' : '' }}>Sunday</option>
</select>

<select name="hr_gratuity_method">
    <option value="">Select Method</option>
    <option value="basic_salary" {{ ($set->hr_gratuity_method ?? '') == 'basic_salary' ? 'selected' : '' }}>Basic Salary</option>
    <option value="gross_salary" {{ ($set->hr_gratuity_method ?? '') == 'gross_salary' ? 'selected' : '' }}>Gross Salary</option>
</select>

<!-- Insurance -->
<input type="text" name="hr_insurance_provider" value="{{ $set->hr_insurance_provider ?? '' }}">
<input type="text" name="hr_insurance_policy_number" value="{{ $set->hr_insurance_policy_number ?? '' }}">
<input type="date" name="hr_insurance_policy_expiry" value="{{ $set->hr_insurance_policy_expiry ?? '' }}">
```

---

## SECTION 12: HR PAYROLL SETTINGS (sys_company_hrpayrollsetting)

```blade
@php
    $hrp = $hrPayrollSetting ?? null;
@endphp

<!-- LEAVES POLICY -->
<input type="number" name="annual_leave_days" value="{{ $hrp->annual_leave_days ?? '' }}" min="0">
<input type="number" name="sick_leave_days" value="{{ $hrp->sick_leave_days ?? '' }}" min="0">
<input type="number" name="emergency_leave_days" value="{{ $hrp->emergency_leave_days ?? '' }}" min="0">
<input type="number" name="maternity_leave_days" value="{{ $hrp->maternity_leave_days ?? '' }}" min="0">
<input type="number" name="paternity_leave_days" value="{{ $hrp->paternity_leave_days ?? '' }}" min="0">
<input type="number" name="unpaid_leave_days" value="{{ $hrp->unpaid_leave_days ?? '' }}" min="0">

<select name="leave_accrual_method">
    <option value="monthly" {{ ($hrp->leave_accrual_method ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
    <option value="annually" {{ ($hrp->leave_accrual_method ?? '') == 'annually' ? 'selected' : '' }}>Annually</option>
</select>

<select name="leave_carry_forward">
    <option value="1" {{ ($hrp->leave_carry_forward ?? '0') == '1' ? 'selected' : '' }}>Yes</option>
    <option value="0" {{ ($hrp->leave_carry_forward ?? '0') == '0' ? 'selected' : '' }}>No</option>
</select>

<input type="number" name="max_carry_forward_days" value="{{ $hrp->max_carry_forward_days ?? '' }}" min="0">

<!-- ATTENDANCE POLICY -->
<select name="working_shift_id">
    <option value="">Select Shift</option>
    @foreach($workingShifts as $shift)
        <option value="{{ $shift->id }}" {{ ($hrp->working_shift_id ?? '') == $shift->id ? 'selected' : '' }}>
            {{ $shift->shift_name }}
        </option>
    @endforeach
</select>

<input type="number" name="grace_period_minutes" value="{{ $hrp->grace_period_minutes ?? '' }}" min="0">
<input type="number" name="half_day_threshold_minutes" value="{{ $hrp->half_day_threshold_minutes ?? '' }}" min="0">
<input type="number" name="absent_threshold_minutes" value="{{ $hrp->absent_threshold_minutes ?? '' }}" min="0">

<!-- OVERTIME -->
<select name="overtime_calculation_method">
    <option value="hourly" {{ ($hrp->overtime_calculation_method ?? '') == 'hourly' ? 'selected' : '' }}>Hourly</option>
    <option value="daily" {{ ($hrp->overtime_calculation_method ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
</select>

<input type="number" name="overtime_rate_multiplier" value="{{ $hrp->overtime_rate_multiplier ?? '1.5' }}" step="0.1" min="1">

<!-- PROBATION -->
<input type="number" name="probation_period_days" value="{{ $hrp->probation_period_days ?? '' }}" min="0">
<input type="number" name="notice_period_days" value="{{ $hrp->notice_period_days ?? '' }}" min="0">

<!-- PAYROLL -->
<input type="text" name="wps_establishment_id" value="{{ $hrp->wps_establishment_id ?? '' }}">
<input type="text" name="wps_bank" value="{{ $hrp->wps_bank ?? '' }}">
<input type="text" name="wps_salary_file_code" value="{{ $hrp->wps_salary_file_code ?? '' }}">

<select name="payroll_cycle">
    <option value="monthly" {{ ($hrp->payroll_cycle ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
    <option value="bi-weekly" {{ ($hrp->payroll_cycle ?? '') == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
    <option value="weekly" {{ ($hrp->payroll_cycle ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
</select>

<input type="number" name="payroll_start_day" value="{{ $hrp->payroll_start_day ?? '' }}" min="1" max="31">
<input type="number" name="payroll_end_day" value="{{ $hrp->payroll_end_day ?? '' }}" min="1" max="31">

<select name="gratuity_calculation_method">
    <option value="basic_salary" {{ ($hrp->gratuity_calculation_method ?? '') == 'basic_salary' ? 'selected' : '' }}>Basic Salary</option>
    <option value="gross_salary" {{ ($hrp->gratuity_calculation_method ?? '') == 'gross_salary' ? 'selected' : '' }}>Gross Salary</option>
</select>

<!-- LOANS -->
<input type="number" name="max_loan_amount" value="{{ $hrp->max_loan_amount ?? '' }}" step="0.01">
<input type="number" name="max_loan_percentage" value="{{ $hrp->max_loan_percentage ?? '' }}" min="0" max="100" step="0.01">
<input type="number" name="max_loan_installments" value="{{ $hrp->max_loan_installments ?? '' }}" min="1">
```

---

## FIELD COUNT SUMMARY

| Section | Field Count |
|---------|-------------|
| Basic Company Info | 15 |
| Contact Information | 20 |
| Owners (per row) | 8 + documents |
| Sponsors (per row) | 7 + documents |
| Contacts (per row) | 8 + documents |
| Compliance/Registration (UAE) | 24 (8 docs × 3 fields each) |
| Non-UAE Documents (per doc) | 5 |
| Banking (per bank) | 9 |
| Warehouses (per warehouse) | 14 |
| HR Policies (per policy) | 5 |
| Company Settings | 18 |
| HR Payroll Settings | 30 |

**Total Base Fields: 130+** (excluding dynamic rows)

---

## IMPORTANT NOTES

### 1. Hidden Company ID
```blade
<input type="hidden" name="company_id" value="{{ $company->id ?? '' }}">
```

### 2. Date Format Handling
PHP stores dates as `Y-m-d`, but HTML date inputs need the same format. If dates are stored as `d/m/Y`, convert them:
```blade
@php
    $dateFormatted = '';
    if (isset($company->date_of_incorporation) && $company->date_of_incorporation) {
        try {
            $dateFormatted = \Carbon\Carbon::parse($company->date_of_incorporation)->format('Y-m-d');
        } catch (\Exception $e) {
            $dateFormatted = '';
        }
    }
@endphp
<input type="date" name="date_of_incorporation" value="{{ $dateFormatted }}">
```

### 3. Null Safety
Always use `??` operator for null safety:
```blade
{{ $company->field_name ?? '' }}
{{ ($company->field_name ?? false) ? 'checked' : '' }}
```

### 4. File Upload Preview
Show existing files with option to replace:
```blade
<input type="file" name="company_logo">
@if($company->company_logo ?? false)
    <div class="current-file">
        <img src="{{ asset($company->company_logo) }}" alt="Current Logo">
        <label><input type="checkbox" name="remove_company_logo" value="1"> Remove</label>
    </div>
@endif
```

### 5. Dynamic Row Indexing
Use proper indexing for dynamic rows to maintain data integrity:
```blade
@foreach($owners as $index => $owner)
    <input name="owners[{{ $index }}][field_name]">
@endforeach
```

### 6. JavaScript Data Initialization
Pass existing data to JavaScript for client-side manipulation:
```blade
<script>
var existingOwners = @json($owners ?? []);
var existingBanks = @json($banks ?? []);
// Use these in your JS for dynamic row management
</script>
```

---

## VALIDATION

Ensure form validation is enabled in the view:
```blade
<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
$(document).ready(function() {
    FormValidator.init('companyAllForm', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>
```

---

## TESTING CHECKLIST

- [ ] All text inputs populated
- [ ] All select dropdowns show correct selected value
- [ ] All checkboxes/radios properly checked
- [ ] Date fields show correct format (Y-m-d)
- [ ] File upload fields show existing files with preview
- [ ] Dynamic rows (owners, sponsors, contacts) populated correctly
- [ ] Documents for people entities loaded
- [ ] Banking records shown correctly
- [ ] Warehouse data populated
- [ ] HR Policies listed
- [ ] Compliance documents loaded (UAE vs non-UAE)
- [ ] Company settings all populated
- [ ] HR Payroll settings all populated
- [ ] Form submits correctly
- [ ] Validation works on all fields

---

**Created:** {{ date('Y-m-d H:i:s') }}
**Controller Method:** `SysCompanyController@companyEdit`
**View:** `backEnd.company.editCompany`

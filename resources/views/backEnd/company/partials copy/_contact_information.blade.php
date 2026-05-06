<div class="tab-pane fade show active" id="contactTab">
    <div class="accordion" id="contactInfoAccordion">

        <!-- 1️⃣ ADDRESS INFORMATION -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseAddress">
                    1. Address Information
                </button>
            </h2>
            <div id="collapseAddress" class="accordion-collapse collapse show" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div class="row gy-2">
                        
                        <div class="col-lg-2">
                            <label>Country</label>
                            <select name="country" id="country_company"
                                class="form-select form-select-sm js-example-basic-single">
                                <option value="">Select</option>
                                @foreach ($country as $c)
                                    <option value="{{ $c->id }}"
                                        data-iso2="{{ strtolower($c->iso2 ?? '') }}"
                                        data-dial-code="{{ $c->dial_code ?? '' }}"
                                        {{ old('country', $company->country ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>


                             <!-- External JS for country codes -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>


                        <script>
                            $(document).ready(function() {
                                // Build mapping ISO2 -> dial code using intl-tel-input data if available
                                var countryCodes = {};
                                if (window.intlTelInputGlobals && typeof window.intlTelInputGlobals.getCountryData === 'function') {
                                    $.each(window.intlTelInputGlobals.getCountryData(), function(index, country) {
                                        countryCodes[(country.iso2 || '').toLowerCase()] = country.dialCode;
                                    });
                                }

                                function stripLeadingDialCode(value) {
                                    return (value || '').replace(/^\+\d+\s?/, '');
                                }

                                function applyDialCodeToMobile(dialCode) {
                                    // Update hidden mobile code input (servers can use this separately)
                                    $('#mobile_code').val(dialCode ? ('+' + dialCode) : '');

                                    // Utility to strip existing code and reformat
                                    var formatWithDial = function($el) {
                                        var current = stripLeadingDialCode($el.val());
                                        if (dialCode) {
                                            $el.val('+' + dialCode + (current ? ' ' + current : ''));
                                        } else {
                                            $el.val(current);
                                        }
                                    };

                                    // Primary company mobile input
                                    var $mobile = $('#company_mobile');
                                    if ($mobile.length) {
                                        formatWithDial($mobile);
                                    }

                                    // Backwards compatibility: update other individual fields
                                    var $companyPhone = $('#company_mobile_phone');
                                    if ($companyPhone.length) {
                                        formatWithDial($companyPhone);
                                    }

                                    var $officePhone = $('#office_telephone');
                                    if ($officePhone.length) {
                                        formatWithDial($officePhone);
                                    }

                                    // Apply to dynamic bracketed mobile fields (owners, sponsors, contacts) and other known mobile inputs
                                    $('input[name$="[mobile]"], input[name="mobile"], input[name^="e_work_phone"], input[name^="e_mobile"]').each(function() {
                                        formatWithDial($(this));
                                    });
                                }

                                // Helper: get dial code from <option> element or fallback to countryCodes mapping
                                function getCountryDialCode($opt) {
                                    if (!$opt || !$opt.length) return '';
                                    var iso2 = ($opt.data('iso2') || '').toString().toLowerCase();
                                    var code = ($opt.data('dial-code') || '') || countryCodes[iso2] || '';
                                    return code ? code.toString() : '';
                                }
                                window.getCountryDialCode = getCountryDialCode;

                                // Expose helper so other functions can re-apply the code after dynamic row insertion
                                window.applyDialCodeToMobile = applyDialCodeToMobile; 

                                // When country changes, set country code in mobile fields
                                $('#country_company').on('change', function() {
                                    var $opt = $(this).find('option:selected');
                                    var dial = window.getCountryDialCode($opt) || '';

                                    // If there's a hidden country input for server side, set it (if exists)
                                    var ctry_id = $(this).val();
                                    var $hiddenCountry = $('#country');
                                    if ($hiddenCountry.length) {
                                        $hiddenCountry.val(ctry_id).trigger('change');
                                    }

                                    applyDialCodeToMobile(dial);
                                });

                                // Initialize on page load if a country is already selected
                                (function initCountryOnLoad() {
                                    var $selected = $('#country_company').find('option:selected');
                                    if ($selected.length && $selected.val()) {
                                        $('#country_company').trigger('change');
                                    }
                                })();
                            });
                        </script>

                        </div>
                        <div class="col-lg-2">
                            <label>State</label>
                            <select name="state" id="state"
                                class="form-select form-select-sm js-example-basic-single">
                                <option value="">Select</option>
                                @foreach ($states ?? [] as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('state', $company->state ?? '') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-lg-2">
                            <label>City</label>
                            <input type="text" name="city" class="form-control form-control-sm"
                                value="{{ old('city', $company->city ?? '') }}">
                        </div>

                        <div class="col-lg-2">
                            <label>Area</label>
                            <input type="text" name="area" class="form-control form-control-sm"
                                value="{{ old('area', $company->area ?? '') }}">
                        </div>

                        <div class="col-lg-2">
                            <label>Building Name</label>
                            <input type="text" name="building_no" class="form-control form-control-sm"
                                value="{{ old('building_no', $company->building_no ?? '') }}">
                        </div>

                        <div class="col-lg-2">
                            <label>Flat / Office  No</label>
                            <input type="text" name="floor_shop_no" class="form-control form-control-sm"
                                value="{{ old('floor_shop_no', $company->floor_shop_no ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- 2️⃣ CONTACT INFORMATION -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseBasicInfo">
                    2. Contact Information
                </button>
            </h2>

            <div id="collapseBasicInfo" class="accordion-collapse collapse" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div class="row gy-2">

                        <div class="col-lg-2">
                            <label>Company Email</label>
                            <input type="email" name="email" class="form-control form-control-sm"
                                value="{{ old('email', $company->email ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Website</label>
                            <input type="text" name="website" class="form-control form-control-sm"
                                value="{{ old('website', $company->website ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Office Phone</label>
                            <input type="text" name="telephone" class="form-control form-control-sm" id="office_telephone"
                                value="{{ old('telephone', $company->telephone ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Mobile Number</label>
                            <input type="hidden" id="mobile_code" name="mobile_code" value="{{ old('mobile_code', '') }}">
                            <input type="text" id="company_mobile" name="mobile" class="form-control form-control-sm"
                                value="{{ old('mobile', $company->mobile ?? '') }}">
                        </div>
                         {{-- <div class="col-lg-2">
                            <label>Fax No *</label>
                            <input type="text" name="fax" class="form-control form-control-sm"
                                value="{{ old('fax', $company->fax ?? '') }}">
                        </div> --}}
                
                    </div>
                </div>
            </div>
        </div>

        <!-- 3️⃣ OWNER DETAILS -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOwners">
                    3. Owner Details
                </button>
            </h2>
            <div id="collapseOwners" class="accordion-collapse collapse" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div id="ownerWrapper">
                        @php
                            $owners = old('owners', $owners ?? [['salutation' => '', 'first_name' => '', 'last_name' => '', 'mobile' => '', 'email' => '', 'share_percentage' => '']]);
                            $all_designations = @App\SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
                        @endphp

                        <!-- Designation select template for dynamic rows -->
                        <script type="text/template" id="designation-template">
                            <div class="col-1-5">
                                <div class="input-effect">
                                    <label class="form-label"><span>@lang('Designation')</span></label>
                                    <select class="form-select form-select-sm js-example-basic-single" name="owners[__INDEX__][designation_id]" id="designation_id__INDEX__">
                                        <option value=""></option>
                                        @foreach ($all_designations as $key => $d)
                                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </script>
                        @foreach ($owners as $index => $owner)
                            <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
                                <div class="col-lg-1">
                                    <label>Salutation</label>
                                    <select name="owners[{{ $index }}][salutation]" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="Mr" {{ ($owner['salutation'] ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                        <option value="Mrs" {{ ($owner['salutation'] ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                        <option value="Miss" {{ ($owner['salutation'] ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        <option value="Ms" {{ ($owner['salutation'] ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                        <option value="Dr" {{ ($owner['salutation'] ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
                                    </select>
                                </div>
                                <div class="col-1-5">
                                    <label>First Name</label>
                                    <input type="text" name="owners[{{ $index }}][first_name]"
                                        class="form-control form-control-sm" value="{{ $owner['first_name'] ?? '' }}">
                                </div>
                                <div class="col-1-5">
                                    <label>Last Name</label>
                                    <input type="text" name="owners[{{ $index }}][last_name]"
                                        class="form-control form-control-sm" value="{{ $owner['last_name'] ?? '' }}">
                                </div>
                                <div class="col-1-5">
                                    <label>Mobile</label>
                                    <input type="text" name="owners[{{ $index }}][mobile]"
                                        class="form-control form-control-sm" value="{{ $owner['mobile'] ?? '' }}">
                                </div>
                                <div class="col-1-5">
                                    <label>Email</label>
                                    <input type="email" name="owners[{{ $index }}][email]"
                                        class="form-control form-control-sm" value="{{ $owner['email'] ?? '' }}">
                                </div>
                                    <div class="col-1-5">
                                                                        <div class="input-effect">
                                                                            <label
                                                                                class="form-label">
                                                                                <span>@lang('Designation')
                                                                                   </span>
                                                                                
                                                                            </label>
                                                                             @php
            $designations = @App\SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
                                                                                
                                                                            @endphp
                                                                            <select
                                                                                class="form-select form-select-sm js-example-basic-single"
                                                                                name="owners[{{ $index }}][designation_id]" id="designation_id_{{ $index }}">
                                                                                <option value="">
                                                                                </option>
                                                                                @foreach ($designations as $key => $value)
                                                                                    @php
                                                                                        // Normalize the title (remove spaces and lowercase) to match "owners/shareholders" reliably
                                                                                        $normTitle = strtolower(str_replace(' ', '', trim(@$value->title ?? '')));
                                                                                        $target = strtolower(str_replace(' ', '', 'Owners / Shareholders'));
                                                                                        // Determine selected state: prefer old input (per-owner), then existing owner designation string, otherwise default to target match
                                                                                        $selected = '';
                                                                                        $oldDesign = old("owners.$index.designation_id");
                                                                                        if ($oldDesign !== null) {
                                                                                            $selected = ($oldDesign == @$value->id) ? 'selected' : '';
                                                                                        } elseif (!empty($owner['designation'])) {
                                                                                            $ownerNorm = strtolower(str_replace(' ', '', trim($owner['designation'])));
                                                                                            $selected = ($ownerNorm == $normTitle) ? 'selected' : '';
                                                                                        } elseif ($normTitle === $target) {
                                                                                            $selected = 'selected';
                                                                                        }
                                                                                    @endphp
                                                                                    <option value="{{ @$value->id }}" {{ $selected }}>
                                                                                        {{ @$value->title }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                <div class="col-lg-1">
                                    <label>Share %</label>
                                    <input type="number" name="owners[{{ $index }}][share_percentage]" 
                                        class="form-control form-control-sm" min="0" max="100" 
                                        value="{{ $owner['share_percentage'] ?? '' }}" placeholder="0">
                                </div>
                                <div class="col-1-5">
                                    <div class="d-flex gap-1 mt-4">
                                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" 
                                            onclick="openDocumentModal('owner', '{{ $index }}')">
                                            <i class="ico icon-outline-add-square"></i> Documents
                                        </button>
                                        @if ($loop->last)
                                            <button type="button" class="btn btn-light btn-sm addOwner"> <i class="ico icon-outline-add-square"></i> </button>
                                        @endif
                                        @if (!$loop->first)
                                            <button type="button" class="btn btn-light btn-sm removeOwner"> <i class="ico icon-outline-minus-square"></i> </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div id="owner-documents-{{ $index }}" class="mt-2" style="display: none;">
                                        <small class="text-muted">Added Documents:</small>
                                        <div class="owner-doc-list-{{ $index }}"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 4️⃣ SPONSOR DETAILS -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseSponsors">
                    4. Sponsor Details
                </button>
            </h2>
            <div id="collapseSponsors" class="accordion-collapse collapse" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div id="sponsorWrapper">
                        @php
                            $sponsors = old('sponsors', $sponsors ?? [['salutation' => '', 'first_name' => '', 'last_name' => '', 'mobile' => '', 'email' => '']]);
                        @endphp
                        @foreach ($sponsors as $index => $sponsor)
                            <div class="sponsorRow row gy-2 p-2 mb-2 border rounded">
                                <div class="col-lg-1">
                                    <label>Salutation</label>
                                    <select name="sponsors[{{ $index }}][salutation]" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="Mr" {{ ($sponsor['salutation'] ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                        <option value="Mrs" {{ ($sponsor['salutation'] ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                        <option value="Miss" {{ ($sponsor['salutation'] ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        <option value="Ms" {{ ($sponsor['salutation'] ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                        <option value="Dr" {{ ($sponsor['salutation'] ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label>First Name</label>
                                    <input type="text" name="sponsors[{{ $index }}][first_name]"
                                        class="form-control form-control-sm" value="{{ $sponsor['first_name'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Last Name</label>
                                    <input type="text" name="sponsors[{{ $index }}][last_name]"
                                        class="form-control form-control-sm" value="{{ $sponsor['last_name'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Mobile</label>
                                    <input type="text" name="sponsors[{{ $index }}][mobile]"
                                        class="form-control form-control-sm" value="{{ $sponsor['mobile'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Email</label>
                                    <input type="email" name="sponsors[{{ $index }}][email]"
                                        class="form-control form-control-sm" value="{{ $sponsor['email'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex gap-1 mt-4">
                                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" 
                                            onclick="openDocumentModal('sponsor', '{{ $index }}')">
                                            <i class="ico icon-outline-add-square"></i> Documents
                                        </button>
                                        @if ($loop->last)
                                            <button type="button" class="btn btn-light btn-sm addSponsor"> <i class="ico icon-outline-add-square"></i> </button>
                                        @endif
                                        @if (!$loop->first)
                                            <button type="button" class="btn btn-light btn-sm removeSponsor"> <i class="ico icon-outline-minus-square"></i> </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div id="sponsor-documents-{{ $index }}" class="mt-2" style="display: none;">
                                        <small class="text-muted">Added Documents:</small>
                                        <div class="sponsor-doc-list-{{ $index }}"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 5️⃣ CONTACT PERSON DETAILS -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseContacts">
                    5. Contact Person Details
                </button>
            </h2>
            <div id="collapseContacts" class="accordion-collapse collapse" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div id="contactWrapper">
                        @php
                            $contacts = old('contacts', $contacts ?? [['salutation' => '', 'first_name' => '', 'last_name' => '', 'mobile' => '', 'email' => '', 'designation' => '']]);
                        @endphp
                        @foreach ($contacts as $index => $contact)
                            <div class="contactRow row gy-2 p-2 mb-2 border rounded">
                                <div class="col-lg-1">
                                    <label>Salutation</label>
                                    <select name="contacts[{{ $index }}][salutation]" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="Mr" {{ ($contact['salutation'] ?? '') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                        <option value="Mrs" {{ ($contact['salutation'] ?? '') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                        <option value="Miss" {{ ($contact['salutation'] ?? '') == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        <option value="Ms" {{ ($contact['salutation'] ?? '') == 'Ms' ? 'selected' : '' }}>Ms</option>
                                        <option value="Dr" {{ ($contact['salutation'] ?? '') == 'Dr' ? 'selected' : '' }}>Dr</option>
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <label>First Name</label>
                                    <input type="text" name="contacts[{{ $index }}][first_name]"
                                        class="form-control form-control-sm" value="{{ $contact['first_name'] ?? '' }}">
                                </div>
                                <div class="col-lg-1">
                                    <label>Last Name</label>
                                    <input type="text" name="contacts[{{ $index }}][last_name]"
                                        class="form-control form-control-sm" value="{{ $contact['last_name'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Mobile</label>
                                    <input type="text" name="contacts[{{ $index }}][mobile]"
                                        class="form-control form-control-sm" value="{{ $contact['mobile'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Email</label>
                                    <input type="email" name="contacts[{{ $index }}][email]"
                                        class="form-control form-control-sm" value="{{ $contact['email'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <label>Designation</label>
                                    <input type="text" name="contacts[{{ $index }}][designation]"
                                        class="form-control form-control-sm"
                                        value="{{ $contact['designation'] ?? '' }}">
                                </div>
                                <div class="col-lg-2">
                                    <div class="d-flex gap-1 mt-4">
                                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" 
                                            onclick="openDocumentModal('contact', '{{ $index }}')">
                                            <i class="ico icon-outline-add-square"></i> Documents
                                        </button>
                                        @if ($loop->last)
                                            <button type="button" class="btn btn-light btn-sm addContact"> <i class="ico icon-outline-add-square"></i> </button>
                                        @endif
                                        @if (!$loop->first)
                                            <button type="button" class="btn btn-light btn-sm removeContact"> <i class="ico icon-outline-minus-square"></i> </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div id="contact-documents-{{ $index }}" class="mt-2" style="display: none;">
                                        <small class="text-muted">Added Documents:</small>
                                        <div class="contact-doc-list-{{ $index }}"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 6️⃣ SOCIAL MEDIA LINKS -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseSocial">
                    6. Social Media Links
                </button>
            </h2>
            <div id="collapseSocial" class="accordion-collapse collapse" data-bs-parent="#contactInfoAccordion">
                <div class="accordion-body">
                    <div class="row gy-2">
                        <div class="col-lg-2">
                            <label>LinkedIn</label>
                            <input type="text" name="linkedin" class="form-control form-control-sm"
                                value="{{ old('linkedin', $company->linkedin ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Facebook</label>
                            <input type="text" name="facebook" class="form-control form-control-sm"
                                value="{{ old('facebook', $company->facebook ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Instagram</label>
                            <input type="text" name="instagram" class="form-control form-control-sm"
                                value="{{ old('instagram', $company->instagram ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Twitter (X)</label>
                            <input type="text" name="twitter_x" class="form-control form-control-sm"
                                value="{{ old('twitter_x', $company->twitter_x ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>YouTube</label>
                            <input type="text" name="youtube" class="form-control form-control-sm"
                                value="{{ old('youtube', $company->youtube ?? '') }}">
                        </div>
                        <div class="col-lg-2">
                            <label>Other Social</label>
                            <input type="text" name="other_social" class="form-control form-control-sm"
                                value="{{ old('other_social', $company->other_social ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-capitalize first letter of each word for name fields
    function capitalizeWords(input) {
        const cursorPos = input.selectionStart;
        const value = input.value;
        
        // Capitalize first letter of each word
        const capitalized = value.replace(/(?:^|\s)\S/g, function(char) {
            return char.toUpperCase();
        });
        
        if (value !== capitalized) {
            input.value = capitalized;
            input.setSelectionRange(cursorPos, cursorPos);
        }
    }

    // Convert email to lowercase
    function lowercaseEmail(input) {
        const cursorPos = input.selectionStart;
        const value = input.value;
        const lowered = value.toLowerCase();
        
        if (value !== lowered) {
            input.value = lowered;
            input.setSelectionRange(cursorPos, cursorPos);
        }
    }

    // Attach to existing and dynamically added inputs
    function attachEvents(container) {
        // Name inputs - capitalize
        const nameInputs = container.querySelectorAll('input[name*="[first_name]"], input[name*="[last_name]"]');
        nameInputs.forEach(function(input) {
            if (!input.dataset.capitalizeAttached) {
                input.addEventListener('input', function() {
                    capitalizeWords(this);
                });
                input.dataset.capitalizeAttached = 'true';
            }
        });

        // Email inputs - lowercase
        const emailInputs = container.querySelectorAll('input[name*="[email]"]');
        emailInputs.forEach(function(input) {
            if (!input.dataset.lowercaseAttached) {
                input.addEventListener('input', function() {
                    lowercaseEmail(this);
                });
                input.dataset.lowercaseAttached = 'true';
            }
        });
    }

    // Initial attachment
    attachEvents(document);

    // Observer for dynamically added rows
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    attachEvents(node);
                }
            });
        });
    });

    const ownerWrapper = document.getElementById('ownerWrapper');
    const sponsorWrapper = document.getElementById('sponsorWrapper');
    const contactWrapper = document.getElementById('contactWrapper');

    if (ownerWrapper) observer.observe(ownerWrapper, { childList: true, subtree: true });
    if (sponsorWrapper) observer.observe(sponsorWrapper, { childList: true, subtree: true });
    if (contactWrapper) observer.observe(contactWrapper, { childList: true, subtree: true });
    
    // Add row functionality - use event delegation to prevent duplicate handlers
    $(document).off('click', '.addOwner').on('click', '.addOwner', function(e) {
        e.preventDefault();
        e.stopPropagation();
        addOwnerRow();
    });
    
    $(document).off('click', '.addSponsor').on('click', '.addSponsor', function(e) {
        e.preventDefault();
        e.stopPropagation();
        addSponsorRow();
    });
    
    $(document).off('click', '.addContact').on('click', '.addContact', function(e) {
        e.preventDefault();
        e.stopPropagation();
        addContactRow();
    });

    // Remove row functionality
    $(document).off('click', '.removeOwner').on('click', '.removeOwner', function(e) {
        e.preventDefault();
        e.stopPropagation();
        removeOwnerRow(this);
    });
    
    $(document).off('click', '.removeSponsor').on('click', '.removeSponsor', function(e) {
        e.preventDefault();
        e.stopPropagation();
        removeSponsorRow(this);
    });
    
    $(document).off('click', '.removeContact').on('click', '.removeContact', function(e) {
        e.preventDefault();
        e.stopPropagation();
        removeContactRow(this);
    });
});

// Add Owner Row Function
function addOwnerRow() {
    const ownerWrapper = document.getElementById('ownerWrapper');
    if (!ownerWrapper) return;
    
    const currentRows = ownerWrapper.querySelectorAll('.ownerRow').length;
    const newIndex = currentRows;
    
    // Remove ALL existing + buttons to prevent duplicates
    const existingAddButtons = ownerWrapper.querySelectorAll('.addOwner');
    existingAddButtons.forEach(button => button.remove());
    
    const newRowHTML = `
        <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
            <div class="col-lg-1">
                <label>Salutation</label>
                <select name="owners[${newIndex}][salutation]" class="form-select form-select-sm">
                    <option value="">Select</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option>
                    <option value="Ms">Ms</option>
                    <option value="Dr">Dr</option>
                </select>
            </div>
            <div class="col-1-5">
                <label>First Name</label>
                <input type="text" name="owners[${newIndex}][first_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-1-5">
                <label>Last Name</label>
                <input type="text" name="owners[${newIndex}][last_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-1-5">
                <label>Mobile</label>
                <input type="text" name="owners[${newIndex}][mobile]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-1-5">
                <label>Email</label>
                <input type="email" name="owners[${newIndex}][email]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-1">
                <label>Share %</label>
                <input type="number" name="owners[${newIndex}][share_percentage]" 
                    class="form-control form-control-sm" min="0" max="100" value="" placeholder="0">
            </div>
            <div class="col-1-5">
                <div class="d-flex gap-1 mt-4">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" 
                        onclick="openDocumentModal('owner', '${newIndex}')">
                        <i class="ico icon-outline-add-square"></i> Documents
                    </button>
                    <button type="button" class="btn btn-light btn-sm addOwner"> <i class="ico icon-outline-add-square"></i> </button>
                    <button type="button" class="btn btn-light btn-sm removeOwner"> <i class="ico icon-outline-minus-square"></i> </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div id="owner-documents-${newIndex}" class="mt-2" style="display: none;">
                    <small class="text-muted">Added Documents:</small>
                    <div class="owner-doc-list-${newIndex}"></div>
                </div>
            </div>
        </div>
    `;
    
    ownerWrapper.insertAdjacentHTML('beforeend', newRowHTML);

    // Insert designation select into the newly added row (if template exists)
    if ($('#designation-template').length) {
        var designationHtml = $('#designation-template').html().replace(/__INDEX__/g, newIndex);
        var $lastRow = $(ownerWrapper).find('.ownerRow').last();
        var $shareCol = $lastRow.find('input[name="owners[' + newIndex + '][share_percentage]"]').closest('div');
        if ($shareCol.length) {
            $shareCol.before(designationHtml);
        } else {
            // Fallback: insert before the last column
            $lastRow.find('.col-lg-12').first().before(designationHtml);
        }
        // initialize select2 only for this select
        if (typeof $.fn.select2 !== 'undefined') {
            $lastRow.find('.js-example-basic-single').select2({ width: '100%' });
        }
        // Auto-select 'Owners / Shareholders' if present
        var target = 'Owners / Shareholders'.replace(/\s+/g, '').toLowerCase();
        var $sel = $lastRow.find('select[name="owners[' + newIndex + '][designation_id]"]');
        $sel.find('option').each(function() {
            var norm = $(this).text().replace(/\s+/g, '').toLowerCase();
            if (norm === target) {
                $(this).prop('selected', true);
                $sel.trigger('change');
            }
        });
    }

    // Initialize document session for new row
    if (!documentSessions.owner[newIndex]) {
        documentSessions.owner[newIndex] = [];
    }

    // Apply current country dial code only to the newly added row's mobile input
    setTimeout(function() {
        var dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
        if (dial) {
            // Only apply to the newly added row - ensure a leading '+' (handle if code already includes '+')
            var formatted = (String(dial).charAt(0) === '+') ? String(dial) : ('+' + String(dial));
            var $newMobileInput = $('input[name="owners[' + newIndex + '][mobile]"]');
            if ($newMobileInput.length && !$newMobileInput.val().trim()) {
                $newMobileInput.val(formatted);
            }
        }
    }, 20);
}

// Add Sponsor Row Function
function addSponsorRow() {
    const sponsorWrapper = document.getElementById('sponsorWrapper');
    const currentRows = sponsorWrapper.querySelectorAll('.sponsorRow').length;
    const newIndex = currentRows;
    
    const newRowHTML = `
        <div class="sponsorRow row gy-2 p-2 mb-2 border rounded">
            <div class="col-lg-1">
                <label>Salutation</label>
                <select name="sponsors[${newIndex}][salutation]" class="form-select form-select-sm">
                    <option value="">Select</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option>
                    <option value="Ms">Ms</option>
                    <option value="Dr">Dr</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label>First Name</label>
                <input type="text" name="sponsors[${newIndex}][first_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Last Name</label>
                <input type="text" name="sponsors[${newIndex}][last_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Mobile</label>
                <input type="text" name="sponsors[${newIndex}][mobile]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Email</label>
                <input type="email" name="sponsors[${newIndex}][email]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <div class="d-flex gap-1 mt-4">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" onclick="openDocumentModal('sponsor', '${newIndex}')">
                        <i class="ico icon-outline-add-square"></i> Documents
                    </button>
                    <button type="button" class="btn btn-light btn-sm addSponsor"> <i class="ico icon-outline-add-square"></i> </button>
                    <button type="button" class="btn btn-light btn-sm removeSponsor"> <i class="ico icon-outline-minus-square"></i> </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div id="sponsor-documents-${newIndex}" class="mt-2" style="display: none;">
                    <small class="text-muted">Added Documents:</small>
                    <div class="sponsor-doc-list-${newIndex}"></div>
                </div>
            </div>
        </div>
    `;
    
    // Remove + button from previous last row
    const lastAddButton = sponsorWrapper.querySelector('.addSponsor');
    if (lastAddButton) {
        lastAddButton.remove();
    }
    
    sponsorWrapper.insertAdjacentHTML('beforeend', newRowHTML);
    
    // Initialize document session for new row
    if (!documentSessions.sponsor[newIndex]) {
        documentSessions.sponsor[newIndex] = [];
    }

    // Apply current country dial code only to the newly added row's mobile input
    setTimeout(function() {
        var dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
        if (dial) {
            // Only apply to the newly added row - ensure a leading '+' (handle if code already includes '+')
            var formatted = (String(dial).charAt(0) === '+') ? String(dial) : ('+' + String(dial));
            var $newMobileInput = $('input[name="sponsors[' + newIndex + '][mobile]"]');
            if ($newMobileInput.length && !$newMobileInput.val().trim()) {
                $newMobileInput.val(formatted);
            }
        }
    }, 20);
}

// Add Contact Row Function
function addContactRow() {
    const contactWrapper = document.getElementById('contactWrapper');
    const currentRows = contactWrapper.querySelectorAll('.contactRow').length;
    const newIndex = currentRows;
    
    const newRowHTML = `
        <div class="contactRow row gy-2 p-2 mb-2 border rounded">
            <div class="col-lg-1">
                <label>Salutation</label>
                <select name="contacts[${newIndex}][salutation]" class="form-select form-select-sm">
                    <option value="">Select</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Miss">Miss</option>
                    <option value="Ms">Ms</option>
                    <option value="Dr">Dr</option>
                </select>
            </div>
            <div class="col-lg-1">
                <label>First Name</label>
                <input type="text" name="contacts[${newIndex}][first_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-1">
                <label>Last Name</label>
                <input type="text" name="contacts[${newIndex}][last_name]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Mobile</label>
                <input type="text" name="contacts[${newIndex}][mobile]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Email</label>
                <input type="email" name="contacts[${newIndex}][email]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <label>Designation</label>
                <input type="text" name="contacts[${newIndex}][designation]" class="form-control form-control-sm" value="">
            </div>
            <div class="col-lg-2">
                <div class="d-flex gap-1 mt-4">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" onclick="openDocumentModal('contact', '${newIndex}')">
                        <i class="ico icon-outline-add-square"></i> Documents
                    </button>
                    <button type="button" class="btn btn-light btn-sm addContact"> <i class="ico icon-outline-add-square"></i> </button>
                    <button type="button" class="btn btn-light btn-sm removeContact"> <i class="ico icon-outline-minus-square"></i> </button>
                </div>
            </div>
            <div class="col-lg-12">
                <div id="contact-documents-${newIndex}" class="mt-2" style="display: none;">
                    <small class="text-muted">Added Documents:</small>
                    <div class="contact-doc-list-${newIndex}"></div>
                </div>
            </div>
        </div>
    `;
    
    // Remove + button from previous last row
    const lastAddButton = contactWrapper.querySelector('.addContact');
    if (lastAddButton) {
        lastAddButton.remove();
    }
    
    contactWrapper.insertAdjacentHTML('beforeend', newRowHTML);
    
    // Initialize document session for new row
    if (!documentSessions.contact[newIndex]) {
        documentSessions.contact[newIndex] = [];
    }

    // Apply current country dial code only to the newly added row's mobile input
    setTimeout(function() {
        var dial = window.getCountryDialCode($('#country_company').find('option:selected')) || '';
        if (dial) {
            // Only apply to the newly added row - ensure a leading '+' (handle if code already includes '+')
            var formatted = (String(dial).charAt(0) === '+') ? String(dial) : ('+' + String(dial));
            var $newMobileInput = $('input[name="contacts[' + newIndex + '][mobile]"]');
            if ($newMobileInput.length && !$newMobileInput.val().trim()) {
                $newMobileInput.val(formatted);
            }
        }
    }, 20);
}

// Initialize modal close events
$(document).ready(function() {
    // Handle modal close buttons
    $(document).on('click', '[data-bs-dismiss="modal"]', function() {
        closeDocumentModal();
    });
    
    // Handle escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#documentModal').hasClass('show')) {
            closeDocumentModal();
        }
    });
});

// Document Management System - Global variables
let currentDocumentType = '';
let currentDocumentIndex = 0;
let documentSessions = {
    owner: {},
    sponsor: {},
    contact: {}
};

// Document Management Functions - Make them global
window.openDocumentModal = function(type, index) {
    // Debug: Basic alert to test if function is called
 
    
    // Ensure DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            openDocumentModal(type, index);
        });
        return;
    }
    
    currentDocumentType = type;
    currentDocumentIndex = parseInt(index);
    
    // Add a small delay to ensure all elements are rendered
    setTimeout(function() {
        // Check if modal elements exist
        const documentForm = document.getElementById('documentForm');
        const documentEditIndex = document.getElementById('documentEditIndex');
        const documentModalLabel = document.getElementById('documentModalLabel');
        const saveDocumentBtn = document.getElementById('saveDocumentBtn');
        const modalElement = document.getElementById('documentModal');
        
        console.log('Looking for elements:', {
            documentForm: !!documentForm,
            documentEditIndex: !!documentEditIndex,
            documentModalLabel: !!documentModalLabel,
            saveDocumentBtn: !!saveDocumentBtn,
            modalElement: !!modalElement
        });
        
        if (!modalElement) {
            console.error('Modal element not found - creating it');
            createDocumentModal();
            return;
        }
        
        if (!documentForm) {
            console.error('Document form not found - modal HTML may not be loaded properly');
            return;
        }
        
        // Clear modal form
        documentForm.reset();
        if (documentEditIndex) documentEditIndex.value = -1;
        if (documentModalLabel) documentModalLabel.textContent = 'Add Document';
        if (saveDocumentBtn) {
            const spanElement = saveDocumentBtn.querySelector('span:last-child');
            if (spanElement) spanElement.textContent = 'Save';
        }
        
        // Load existing documents first
        loadDocumentList();
        
        // Show modal using jQuery (fallback) or Bootstrap 5
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else if (typeof $ !== 'undefined') {
            $('#documentModal').modal('show');
        } else {
            // Fallback: show modal manually
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.style.paddingRight = '17px';
            document.body.classList.add('modal-open');
            
            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'documentModalBackdrop';
            document.body.appendChild(backdrop);
        }
    }, 100); // 100ms delay
}

// Function to create modal if it doesn't exist
window.createDocumentModal = function() {
    const modalHTML = `
        <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentModalLabel">Add Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="documentForm">
                            <input type="hidden" id="documentEditIndex" value="-1">
                            <div class="row gy-2">
                                <div class="col-md-2">
                                    <label for="document_name" class="form-label mb-1">Document Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="document_name" name="document_name" placeholder="Enter document name">
                                </div>
                                <div class="col-md-2">
                                    <label for="document_number" class="form-label mb-1">Document Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="document_number" name="document_number" placeholder="Enter document number">
                                </div>
                                <div class="col-md-2">
                                    <label for="document_date" class="form-label mb-1">Issue Date</label>
                                    <input type="date" class="form-control form-control-sm" id="document_date" name="document_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="expiry_date" class="form-label mb-1">Expiry Date</label>
                                    <input type="date" class="form-control form-control-sm" id="expiry_date" name="expiry_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="document_attachment" class="form-label mb-1">Attachment</label>
                                    <input type="file" class="form-control form-control-sm" id="document_attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted d-block">PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</small>
                                </div>
                            </div>
                        </form>
                        <div class="mt-3 mb-3 text-center">
                            <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="saveDocumentBtn" onclick="saveDocument()">
                                <span class="spinner-border spinner-border-sm d-none" id="documentLoader"></span>
                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                <span>Save</span>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Document Name</th>
                                        <th>Document Number</th>
                                        <th>Issue Date</th>
                                        <th>Expiry Date</th>
                                        <th>Attachment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="documentList">
                                    <tr><td colspan="6" class="text-muted text-center">No documents added yet.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Try to open the modal again
    setTimeout(function() {
        openDocumentModal(currentDocumentType, currentDocumentIndex);
    }, 200);
}

window.saveDocument = function() {
 
    
    const form = document.getElementById('documentForm');
    
    if (!form) {
        console.error('Document form not found');
        alert('Error: Document form not found!');
        return;
    }
    
    // Get form data
    const documentName = document.getElementById('document_name').value.trim();
    const documentNumber = document.getElementById('document_number').value.trim();
    const documentDate = document.getElementById('document_date').value;
    const expiryDate = document.getElementById('expiry_date').value;

    // Check if we're editing an existing document
    const editIndexEl = document.getElementById('documentEditIndex');
    const editIndex = editIndexEl ? parseInt(editIndexEl.value) : -1;

    // Safely find any possible attachment input (compatibility with both modal versions)
    const attachmentEl = document.getElementById('document_attachment') || document.getElementById('attachment');
    let attachmentFile = null;
    if (attachmentEl && attachmentEl.files && attachmentEl.files.length > 0) {
        attachmentFile = attachmentEl.files[0];

        // Client-side validation: size <= 5MB
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (attachmentFile.size > maxSize) {
            alert('Attachment must be 5MB or smaller.');
            return;
        }

        // Client-side validation: allowed types (fallback to extension if type is empty)
        const allowedTypes = ['application/pdf','image/jpeg','image/png','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const allowedExts = ['pdf','jpg','jpeg','png','doc','docx'];
        const fileType = (attachmentFile.type || '').toLowerCase();
        const fileName = (attachmentFile.name || '').toLowerCase();
        const fileExt = fileName.split('.').pop();
        if (fileType && allowedTypes.indexOf(fileType) === -1 && allowedExts.indexOf(fileExt) === -1) {
            alert('Invalid attachment type. Allowed: PDF, JPG, PNG, DOC, DOCX.');
            return;
        }
    }
    
    // Validate required fields
    if (!documentName || !documentNumber) {
        alert('Document Name and Document Number are required!');
        return;
    }

    // Disable save button and show spinner
    const saveBtn = document.getElementById('saveDocumentBtn');
    const loader = document.getElementById('documentLoader');
    if (saveBtn) saveBtn.disabled = true;
    if (loader) loader.classList.remove('d-none');
    
    // Create FormData for file upload
    const formData = new FormData();
    formData.append('document_name', documentName);
    formData.append('document_number', documentNumber);
    formData.append('document_date', documentDate);
    formData.append('expiry_date', expiryDate);
    formData.append('person_type', currentDocumentType);
    formData.append('person_index', currentDocumentIndex);
    
    if (attachmentFile) {
        formData.append('attachment', attachmentFile);
    }

    // If editing, include document id
    if (editIndex >= 0) {
        const documentsArray = documentSessions[currentDocumentType] && documentSessions[currentDocumentType][currentDocumentIndex] ? documentSessions[currentDocumentType][currentDocumentIndex] : [];
        const existing = documentsArray[editIndex];
        if (existing && existing.id) {
            formData.append('document_id', existing.id);
        }
    }
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }
    
    console.log('Saving document:', {
        name: documentName,
        number: documentNumber,
        date: documentDate,
        expiry: expiryDate,
        type: currentDocumentType,
        index: currentDocumentIndex
    });
    
    // Log form data contents
    console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Log the URL that will be called
    const ajaxUrl = '{{ url("/company/documents/session/store") }}';
    console.log('AJAX URL:', ajaxUrl);

    // Save to session via AJAX
    console.log('Starting AJAX request...');
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('AJAX Response received - Status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (!response.ok) {
            console.error('HTTP Error:', response.status, response.statusText);
            return response.text().then(text => {
                console.error('Error response body:', text);
                alert('Error saving document: ' + response.status + ' - ' + text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        
        console.log('Response OK, parsing JSON...');
        return response.json();
    })
    .then(data => {
        console.log('SUCCESS - Response data received:', data);
        if (data.ok) {
            console.log('Document saved successfully to session!');
            // Initialize session storage for this type/index if not exists
            if (!documentSessions[currentDocumentType]) {
                documentSessions[currentDocumentType] = [];
            }
            if (!documentSessions[currentDocumentType][currentDocumentIndex]) {
                documentSessions[currentDocumentType][currentDocumentIndex] = [];
            }

            // If this was an edit, replace the item at editIndex, otherwise push
            if (editIndex >= 0 && data.updated) {
                documentSessions[currentDocumentType][currentDocumentIndex][editIndex] = data.document;
                // reset edit index and UI
                if (editIndexEl) editIndexEl.value = -1;
                const modalLabelEl = document.getElementById('documentModalLabel');
                if (modalLabelEl) modalLabelEl.textContent = 'Add Document';
                const saveBtnEl = document.getElementById('saveDocumentBtn');
                if (saveBtnEl) {
                    const spanElement = saveBtnEl.querySelector('span:last-child');
                    if (spanElement) spanElement.textContent = 'Save';
                }
            } else {
                documentSessions[currentDocumentType][currentDocumentIndex].push(data.document);
            }

            console.log('Updated local document sessions:', documentSessions);
            
            // Update document count and list
            updateDocumentDisplay();
            loadDocumentList();


             const fields = [
        'document_name',
        'document_number',
        'document_date',
        'expiry_date',
        'document_attachment'
    ];

    fields.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        if (el.type === 'file') {
            el.value = '';            // clear file input
        } else {
            el.value = '';            // clear text/date inputs
        }
    });
            
            // Close modal and reset form
            // closeDocumentModal();
            form.reset();
            
            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success('Document saved successfully!');
            }
        } else {
            console.error('Error saving document:', data);
            alert('Error saving document. Please try again.');
        }
    })
    .catch(error => {
        console.error('AJAX ERROR occurred:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        alert('Error saving document: ' + error.message + '. Please check console for details.');
    })
    .finally(() => {
        // Re-enable save UI
        const saveBtnFinal = document.getElementById('saveDocumentBtn');
        const loaderFinal = document.getElementById('documentLoader');
        if (saveBtnFinal) saveBtnFinal.disabled = false;
        if (loaderFinal) loaderFinal.classList.add('d-none');
    });
}

window.closeDocumentModal = function() {
    const modalElement = document.getElementById('documentModal');
    
    if (typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    } else if (typeof $ !== 'undefined') {
        $('#documentModal').modal('hide');
    } else {
        // Manual close
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
        
        // Remove backdrop
        const backdrop = document.getElementById('documentModalBackdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

function formatDateDMY(dateStr) {
  if (!dateStr) return '-';

  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return '-';

  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
}


window.loadDocumentList = function() {
    const listContainer = document.getElementById('documentList');
    
    if (!listContainer) {
        console.error('Document list container not found');
        return;
    }
    
    listContainer.innerHTML = '';
    
    const documents = documentSessions[currentDocumentType][currentDocumentIndex] || [];
    
    if (documents.length === 0) {
        listContainer.innerHTML = '<tr><td colspan="6" class="text-muted text-center">No documents added yet.</td></tr>';
        return;
    }
    
    documents.forEach((doc, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${doc.name}</td>
            <td class="text-center">${doc.number}</td>
            <td class="text-center">${formatDateDMY(doc.date)}</td>
            <td class="text-center">${formatDateDMY(doc.expiry_date)}</td>
            <td class="text-center">${doc.attachment || 'No file'}</td>
            <td>
                <div class="d-flex justify-content-center gap-2">
                <a href="#" type='button' class=" btn-sm btn-light" onclick="editDocument(${index})" title="Edit">
                    <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                </a>
                <a href="#" type='button' class=" btn-sm btn-light" onclick="deleteDocument(${index})" title="Delete">
                    <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                </a>
                </div>
            </td>
        `;
        listContainer.appendChild(row);
    });
}

window.editDocument = function(index) {
    const documents = documentSessions[currentDocumentType][currentDocumentIndex];
    const doc = documents[index];
    
    // Fill form with document data
    const form = document.getElementById('documentForm');
    const editIndexElement = document.getElementById('documentEditIndex');
    const modalLabel = document.getElementById('documentModalLabel');
    const saveBtn = document.getElementById('saveDocumentBtn');
    
    if (!form || !editIndexElement) {
        console.error('Document form elements not found');
        return;
    }
    
    document.getElementById('document_name').value = doc.name;
    document.getElementById('document_number').value = doc.number;
    // Format dates to d/m/Y for user-friendly display (leave empty if not available)
    const formattedDate = formatDateDMY(doc.date);
    const formattedExpiry = formatDateDMY(doc.expiry_date);

    document.getElementById('document_date').value = (formattedDate === '-') ? '' : formattedDate;
    document.getElementById('expiry_date').value = (formattedExpiry === '-') ? '' : formattedExpiry;
    
    editIndexElement.value = index;
    if (modalLabel) modalLabel.textContent = 'Edit Document';
    if (saveBtn) {
        const spanElement = saveBtn.querySelector('span:last-child');
        if (spanElement) spanElement.textContent = 'Update';
    }
    
    loadDocumentList();
}

window.deleteDocument = function(index) {
    if (confirm('Are you sure you want to delete this document?')) {
        const documents = documentSessions[currentDocumentType][currentDocumentIndex] || [];
        const doc = documents[index];
        
        if (!doc || !doc.id) {
            console.error('Document not found');
            return;
        }
        
        // Create FormData for the request
        const formData = new FormData();
        formData.append('person_type', currentDocumentType);
        formData.append('person_index', currentDocumentIndex);
        formData.append('document_id', doc.id);
        
        // Add CSRF token (use window.document to avoid accidental shadowing)
        const csrfToken = window.document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        // Delete from session via AJAX
        fetch('/company/documents/session/delete', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // Remove from local session
                documentSessions[currentDocumentType][currentDocumentIndex].splice(index, 1);
                
                // Update display
                updateDocumentDisplay();
                loadDocumentList();
                
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Document deleted successfully!');
                }
            } else {
                console.error('Error deleting document:', data);
                alert('Error deleting document. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting document. Please try again.');
        });
    }
}

window.updateDocumentDisplay = function() {
    const documents = documentSessions[currentDocumentType][currentDocumentIndex] || [];
    const count = documents.length;
    
    // Update document list display only
    const docContainer = document.getElementById(`${currentDocumentType}-documents-${currentDocumentIndex}`);
    const docList = document.querySelector(`.${currentDocumentType}-doc-list-${currentDocumentIndex}`);
    
    if (count > 0) {
        docContainer.style.display = 'block';
        docList.innerHTML = documents.map(doc => 
            `<small class="badge bg-light text-dark me-1">${doc.name}</small>`
        ).join('');
    } else {
        docContainer.style.display = 'none';
        docList.innerHTML = '';
    }
}

// Remove Row Functions
function removeOwnerRow(button) {
    if (confirm('Are you sure you want to remove this owner?')) {
        const row = button.closest('.ownerRow');
        const ownerWrapper = document.getElementById('ownerWrapper');
        const remainingRows = ownerWrapper.querySelectorAll('.ownerRow').length;
        
        if (remainingRows > 1) {
            row.remove();
            // Add + button to the new last row if it doesn't have one
            const lastRow = ownerWrapper.querySelector('.ownerRow:last-child');
            if (lastRow && !lastRow.querySelector('.addOwner')) {
                const buttonContainer = lastRow.querySelector('.d-flex');
                if (buttonContainer) {
                    buttonContainer.insertAdjacentHTML('beforeend', '<button type="button" class="btn btn-light btn-sm addOwner"> <i class="ico icon-outline-add-square"></i> </button>');
                }
            }
        }
    }
}

function removeSponsorRow(button) {
    if (confirm('Are you sure you want to remove this sponsor?')) {
        const row = button.closest('.sponsorRow');
        const sponsorWrapper = document.getElementById('sponsorWrapper');
        const remainingRows = sponsorWrapper.querySelectorAll('.sponsorRow').length;
        
        if (remainingRows > 1) {
            row.remove();
            // Add + button to the new last row if it doesn't have one
            const lastRow = sponsorWrapper.querySelector('.sponsorRow:last-child');
            if (lastRow && !lastRow.querySelector('.addSponsor')) {
                const buttonContainer = lastRow.querySelector('.d-flex');
                if (buttonContainer) {
                    buttonContainer.insertAdjacentHTML('beforeend', '<button type="button" class="btn btn-light btn-sm addSponsor"> <i class="ico icon-outline-add-square"></i> </button>');
                }
            }
        }
    }
}

function removeContactRow(button) {
    if (confirm('Are you sure you want to remove this contact?')) {
        const row = button.closest('.contactRow');
        const contactWrapper = document.getElementById('contactWrapper');
        const remainingRows = contactWrapper.querySelectorAll('.contactRow').length;
        
        if (remainingRows > 1) {
            row.remove();
            // Add + button to the new last row if it doesn't have one
            const lastRow = contactWrapper.querySelector('.contactRow:last-child');
            if (lastRow && !lastRow.querySelector('.addContact')) {
                const buttonContainer = lastRow.querySelector('.d-flex');
                if (buttonContainer) {
                    buttonContainer.insertAdjacentHTML('beforeend', '<button type="button" class="btn btn-light btn-sm addContact"> <i class="ico icon-outline-add-square"></i> </button>');
                }
            }
        }
    }
}
</script>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Add Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Document Form -->
                <form id="documentForm">
                    <input type="hidden" id="documentEditIndex" value="-1">
                    <div class="row gy-2">
                        <div class="col-md-3">
                            <label for="document_name" class="form-label mb-1">Document Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_name" name="document_name" placeholder="">
                        </div>
                        <div class="col-md-3">
                            <label for="document_number" class="form-label mb-1">Document No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_number" name="document_number" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label for="document_date" class="form-label mb-1">Issue Date</label>
                            <input type="date" class="form-control form-control-sm date-picker" id="document_date" name="document_date">
                        </div>
                        <div class="col-md-2">
                            <label for="expiry_date" class="form-label mb-1">Expiry Date</label>
                            <input type="date" class="form-control form-control-sm date-picker" id="expiry_date" name="expiry_date">
                        </div>
                        <div class="col-md-2">
                            <label for="document_attachment" class="form-label mb-1">Attachment</label>
                            <input type="file" class="form-control form-control-sm" id="document_attachment" name="attachment" 
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

                        </div>
                    </div>
                </form>

                <!-- Add Document Button -->
                <div class="mt-3 mb-4 text-center">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="saveDocumentBtn" onclick="saveDocument()">
                        <span class="spinner-border spinner-border-sm d-none" id="documentLoader"></span>
                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                        <span>Save</span>
                    </button>
                </div>

                <!-- Document List -->
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th class="text-center">Document Number</th>
                                <th class="text-center">Issue Date</th>
                                <th class="text-center">Expiry Date</th>
                                <th class="text-center">Attachment</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="documentList">
                            <tr>
                                <td colspan="6" class="text-muted text-center">No documents added yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Add Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="documentForm">
                    <input type="hidden" id="documentEditIndex" value="-1">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="document_name" class="form-label">Document Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_name" 
                                name="document_name">
                        </div>
                        <div class="col-md-6">
                            <label for="document_number" class="form-label">Document Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_number" 
                                name="document_number">
                        </div>
                        <div class="col-md-6">
                            <label for="document_date" class="form-label">Issue Date</label>
                            <input type="date" class="form-control form-control-sm" id="document_date" 
                                name="document_date">
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control form-control-sm" id="expiry_date" 
                                name="expiry_date">
                        </div>
                        <div class="col-md-12">
                            <label for="document_attachment" class="form-label">Attachment</label>
                            <input type="file" class="form-control form-control-sm" id="document_attachment" 
                                name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Allowed formats: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveDocumentBtn" onclick="saveDocument()">
                    <i class="ico icon-outline-add-square"></i>
                    <span>Save Document</span>
                </button>
            </div>
        </div>
    </div>
</div>

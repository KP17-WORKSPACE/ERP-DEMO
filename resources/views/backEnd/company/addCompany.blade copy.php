@extends('backEnd.newmasterpage')

@section('mainContent')
    <style>
        .form-scroll {
            overflow-y: auto;
            padding-right: 6px;
        }

        .nav-tabs .nav-link.active {
            background: #dff0d8;
        }

        .modal-backdrop {
            position: relative !important;
        }
        
    </style>

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <div class="form-scroll">
        <div class="content-container col-12">
            <div id="companyApp">

                <div class="purchase-order-content-header">
                    <h4>{{ isset($company) && $company->id ? 'Edit (COM - '.$company->id.')' : 'COM - '.$nextId }}</h4>
                    <div class="purchase-order-content-header-right d-flex align-items-center gap-1">

                        <span id="basicSuccess" class="text-success ms-2"></span>

                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="saveBasicBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="basicLoader"></span>
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>{{ isset($company) && $company->id ? 'Update' : 'Save' }}</span>
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item" href="{{ url('/department') }}">
                                        <i class="ico icon-outline-folder text-primary"></i>
                                        Company Policy
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/department') }}">
                                        <i class="ico icon-outline-folder text-primary"></i>
                                        Department
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/designation') }}">
                                        <i class="ico icon-outline-document text-warning"></i>
                                        Designation
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/legal-entity') }}">
                                        <i class="ico icon-outline-document text-warning"></i>
                                        Business Entity
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/industry') }}">
                                        <i class="ico icon-outline-layers text-secondary"></i>
                                        Industry Type
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/business-activity') }}">
                                        <i class="ico icon-outline-layers text-secondary"></i>
                                        Business Sector
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>


                </div>

                <div class="card mb-3">
                    <div class="card-body">

                        <form id="companyForm" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>

                            @csrf

                            {{-- Container for documents added locally (hidden inputs will be appended here) --}}
                            <div id="documentHiddenContainer" style="display:none"></div>

                            {{-- HIDDEN COMPANY ID --}}
                            <input type="hidden" name="company_id" id="company_id">

                            {{-- BASIC INFORMATION --}}
                            @include('backEnd.company.partials._basic_information')

                            {{-- TABS --}}
                            <div class="tab-wrap mb-3 mt-3">
                                <ul class="nav nav-tabs">

                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#contactTab" data-tab-key="contact">
                                            Contact Information
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#companySetting" data-tab-key="settings">
                                            Company Setting
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#complianceTab" data-tab-key="compliance">
                                            Company Registration
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#bankTab" data-tab-key="bank">
                                            Banking & Finance
                                            <span class="badge bg-primary ms-1 d-none" id="bankCountBadge"></span>
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#warehouseTab" data-tab-key="warehouse">
                                            Warehouse Info
                                            <span class="badge bg-primary ms-1 d-none" id="warehouseCountBadge"></span>
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#companyPolicies" data-tab-key="policy">
                                            Company Policies
                                            <span class="badge bg-primary ms-1 d-none" id="policyCountBadge"></span>
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#hrPayroll" data-tab-key="hr-payroll">
                                            HRMS Setting
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#documentations" data-tab-key="docs">
                                            Documentations
                                        </button>
                                    </li>

                                </ul>

                            </div>

                            <div class="tab-content">

                                <!-- CONTACT TAB -->
                                <div class="tab-pane fade show active" id="contactTab">
                                    @include('backEnd.company.partials._contact_information')
                                </div>

                                <!-- COMPLIANCE TAB -->
                                <div class="tab-pane fade" id="complianceTab">
                                    @include('backEnd.company.partials._compliance')
                                </div>

                                <!-- BANK TAB -->
                                <div class="tab-pane fade" id="bankTab">
                                    @include('backEnd.company.partials._banking_finance')

                                </div>

                                <!-- WAREHOUSE TAB -->
                                <div class="tab-pane fade" id="warehouseTab">
                                    @include('backEnd.company.partials._warehouse')

                                </div>

                                <div class="tab-pane fade" id="companyPolicies">
                                    @include('backEnd.company.partials._company_policies')

                                </div>

                                <div class="tab-pane fade" id="hrPayroll">
                                    @include('backEnd.company.partials._hr_payroll_setting')

                                </div>

                                <div class="tab-pane fade" id="documentations">
                                    @include('backEnd.company.partials._documentations')

                                </div>

                                <div class="tab-pane fade" id="companySetting">
                                    @include('backEnd.company.partials._company_setting')

                                </div>

                                @include('backEnd.company.partials._modals')

                            </div>


                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        /* =====================================================================
           SETUP CSRF TOKEN FOR AJAX
        ===================================================================== */
        $(document).ready(function() {
            // Get CSRF token from meta tag or input
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (!csrfToken) {
                csrfToken = $('input[name="_token"]').val();
            }
            
            // Set it in jQuery AJAX headers globally
            if (csrfToken) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }

            // Load HR Payroll Settings if editing company
            let companyId = $("#company_id").val();
            if (companyId) {
                console.log('📊 Loading HR Payroll Settings for company:', companyId);
                loadHrPayrollSettings(companyId);
            }
        });

        /* =====================================================================
           GLOBAL STATE & TAB HANDLING
        ===================================================================== */

        let lastVisited = "contactTab";

        // Track last visited tab
        $(document).on("shown.bs.tab", "button[data-bs-toggle='tab']", function() {
            lastVisited = $(this).attr("data-bs-target").replace("#", "");
            // console.log("🟢 Last visited:", lastVisited);
        });

        function showLastVisitedTab() {
            if (lastVisited) {
                $('[data-bs-target="#' + lastVisited + '"]').tab("show");
            }
        }

        /* =====================================================================
           MASTER SAVE → BASIC → CONTACT → COMPLIANCE → DOCUMENTS
        ===================================================================== */

        $(document).on("click", "#saveBasicBtn", function(e) {
            e.preventDefault();

            // Run validation first
            if (!validateFormAndJumpToTab()) {
                return false;
            }

            $("#basicLoader").removeClass("d-none");
            $("#basicSuccess").html("");
            $(".text-danger").remove();

            let form = $("#companyForm")[0];
            let fd = new FormData(form);
            
            // Ensure CSRF token is included
            let token = $('meta[name="csrf-token"]').attr('content');
            if (!token) {
                token = $('input[name="_token"]').val();
            }
            if (token && !fd.has('_token')) {
                fd.append('_token', token);
            }

            // STEP 1: BASIC INFO SAVE
            $.ajax({
                url: "{{ route('company.basic.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': token
                },

                success: function(res) {

                    if (!(res.ok || res.success)) {
                        $("#basicLoader").addClass("d-none");
                        return;
                    }

                    // Set company id
                    $("#company_id").val(res.company_id);
                    fd.set("company_id", res.company_id);

                    // Enable Policy button once basic is saved (do not auto-enable banking)
                    enablePolicyButton();

                    // Next → CONTACT
                    saveContact(fd);
                },

                error: function(xhr) {
                    $("#basicLoader").addClass("d-none");
                    handleErrors(xhr);
                }
            });
        });

        /* =====================================================================
           STEP 2 → CONTACT STORE
        ===================================================================== */
        function saveContact(fd) {
            let token = $('meta[name="csrf-token"]').attr('content');
            if (!token) {
                token = $('input[name="_token"]').val();
            }

            $.ajax({
                url: "{{ route('company.contact.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': token
                },

                success: function(res) {

                    // Even if response doesn't return anything, just go next
                    saveCompliance(fd);
                },

                error: function(xhr) {
                    $("#basicLoader").addClass("d-none");
                    handleErrors(xhr);
                }
            });
        }

        /* =====================================================================
           STEP 3 → COMPLIANCE STORE
        ===================================================================== */
        function saveCompliance(fd) {
            let token = $('meta[name="csrf-token"]').attr('content');
            if (!token) {
                token = $('input[name="_token"]').val();
            }

            $.ajax({
                url: "{{ route('company.compliance.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': token
                },

                success: function(res) {

                    if (res.ok || res.success) {
                        // console.log("✔ Compliance Saved → Moving to Documents");
                        saveDocuments(fd); // NEXT → DOCUMENTS
                    } else {
                        $("#basicLoader").addClass("d-none");
                    }
                },

                error: function(xhr) {
                    $("#basicLoader").addClass("d-none");

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, msg) {
                            let input = $('[name="' + key + '"]');
                            input.after(`<small class="text-danger">${msg[0]}</small>`);
                        });

                        // Switch to compliance tab
                        $('[data-bs-target="#complianceTab"]').tab("show");

                        let firstError = Object.keys(errors)[0];
                        $('[name="' + firstError + '"]').focus();
                    }
                }
            });
        }

        /* =====================================================================
           STEP 4 → DOCUMENTATION STORE
        ===================================================================== */
        function saveDocuments(fd) {
            let token = $('meta[name="csrf-token"]').attr('content');
            if (!token) {
                token = $('input[name="_token"]').val();
            }

            // console.log("📂 Sending Documentation API…");

            fd.set("company_id", $("#company_id").val());
            $(".doc-error").remove();

            // Collect documentation inputs
            $(".doc-input").each(function() {
                let name = $(this).attr("name");

                if (this.type === "file") {
                    if (this.files.length > 0) {
                        fd.set(name, this.files[0]);
                    }
                } else {
                    fd.set(name, $(this).val());
                }
            });

            $.ajax({
                url: "{{ route('company.docs.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': token
                },

                success: function(res) {
                    $("#basicLoader").addClass("d-none");

                    if (res.ok || res.success) {

                        $("#basicLoader").addClass("d-none");
                        $("#basicSuccess").html("All company details saved successfully!");
                        toastr && toastr.success ? toastr.success("Company Saved") : alert("Company Saved");

                        // Do not auto-navigate to Banking tab after save

                        // Persist Company Settings first, then banks/policies
                        (function(){
                            const cid = $("#company_id").val();
                            if (!cid) return;

                            // Collect settings
                            let settingsData = { _token: '{{ csrf_token() }}', company_id: cid };
                            $('.setting-input').each(function(){
                                let name = $(this).attr('name');
                                if (!name) return;
                                if ($(this).is(':checkbox')) {
                                    settingsData[name] = $(this).is(':checked') ? 1 : 0;
                                } else {
                                    settingsData[name] = $(this).val();
                                }
                            });

                            // Save settings via API; if success -> proceed to banks/policies
                            $.post('{{ route('company.setting.store') }}', settingsData)
                                .done(function(sres){
                                    if (!(sres && sres.ok)) {
                                        // server returned not-ok
                                        toastr && toastr.error ? toastr.error(sres.message || 'Could not save settings') : alert('Could not save settings');
                                        return;
                                    }

                                    // Enable bank UI now that company exists
                                    try { enableBankButton(); } catch (e) { $('#addBankBtn').prop('disabled', false); }

                                    // Persist any session-stored banks into DB for this company
                                    $.ajax({
                                        url: "{{ route('company.banking.saveAll') }}",
                                        type: "POST",
                                        data: { company_id: cid, _token: '{{ csrf_token() }}' },
                                        success: function(resp) {
                                            console.log('💳 Banking saveAll response:', resp);
                                            if (resp && resp.ok) {
                                                toastr && toastr.success ? toastr.success(resp.message || 'Banking details saved successfully') : alert('Banking details saved');
                                                try { updateSessionBadges(); } catch(e){}
                                            } else {
                                                console.warn('⚠️ Banking saveAll returned not-ok:', resp);
                                                toastr && toastr.warning ? toastr.warning(resp.message || 'No banking data to save') : console.log(resp.message);
                                            }
                                        },
                                        error: function(xhr){
                                            console.error('❌ Could not save session banks:', xhr);
                                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                                let errors = xhr.responseJSON.errors;
                                                console.error('Validation errors:', errors);
                                                toastr && toastr.error ? toastr.error('Banking validation error') : alert('Banking validation error');
                                            } else {
                                                toastr && toastr.error ? toastr.error('Error saving banking details') : alert('Error saving banking details');
                                            }
                                        }
                                    });

                                    // Persist any session-stored policies into DB for this company
                                    $.ajax({
                                        url: '{{ route('company.policy.saveAll') }}',
                                        type: 'POST',
                                        data: { company_id: cid, _token: '{{ csrf_token() }}' },
                                        success: function(resp) {
                                            if (resp && resp.ok) {
                                                toastr && toastr.success ? toastr.success(resp.message || 'Policies saved') : alert('Policies saved');
                                                try { updateSessionBadges(); } catch(e){}
                                            } else {
                                                console.warn('saveAllPolicies returned not-ok', resp);
                                                toastr && toastr.error ? toastr.error(resp.message || 'Could not save policies') : alert('Could not save policies');
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('Could not save session policies: status=', xhr.status);
                                            toastr && toastr.error ? toastr.error('Server error while saving policies') : alert('Server error while saving policies');
                                        }
                                    });

                                    // Save HR Payroll Settings directly to database
                                    saveHrPayrollSettings(cid);

                                    // Save Warehouse data from session to database
                                    saveWarehousesFromSession(cid);

                                })
                                .fail(function(xhr){
                                    // Show validation errors if present
                                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                        let errors = xhr.responseJSON.errors;
                                        $.each(errors, function(key, msg){
                                            let input = $('[name="'+key+'"]').first();
                                            input.after(`<small class="text-danger setting-error">${msg[0]}</small>`);
                                        });
                                        toastr && toastr.error ? toastr.error('Validation error in settings') : alert('Validation error in settings');
                                    } else {
                                        toastr && toastr.error ? toastr.error('Server error while saving settings') : alert('Server error while saving settings');
                                    }
                                });

                        })();

                    }

                },

                error: function(xhr) {
                    $("#basicLoader").addClass("d-none");
                    console.log("❌ DOC ERROR:", xhr);

                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, msg) {
                            let input = $('[name="' + key + '"]');
                            input.after(`<small class="text-danger doc-error">${msg[0]}</small>`);
                        });

                        let first = Object.keys(errors)[0];
                        $('[name="' + first + '"]').focus();

                        // Switch to Documentations Tab (ID FIXED)
                        $('[data-bs-target="#documentations"]').tab("show");
                    }
                }
            });
        }

        /* =====================================================================
           GLOBAL ERROR HANDLER (BASIC + CONTACT)
        ===================================================================== */
        function handleErrors(xhr) {

            if (xhr.status !== 422) return;

            let errors = xhr.responseJSON.errors;

            $.each(errors, function(key, msg) {
                let input = $('[name="' + key + '"]');
                input.after(`<small class="text-danger">${msg[0]}</small>`);
            });

            let firstError = Object.keys(errors)[0];
            let el = $('[name="' + firstError + '"]');

            if (el.closest("#contactTab").length) {
                $('[data-bs-target="#contactTab"]').tab("show");
            } else if (el.closest("#complianceTab").length) {
                $('[data-bs-target="#complianceTab"]').tab("show");
            }

            el.focus();
        }

        /* =====================================================================
           INDUSTRY → BUSINESS SECTOR
           Note: Business Entity Type is completely independent from Industry Type.
           Only Industry Type should trigger Business Sector dropdown changes.
        ===================================================================== */
        $(document).on("change", "#industry_type_id", function() {

            let id = $(this).val();
            let sector = $("#business_sector_id");

            sector.html('<option>Loading...</option>');

            $.get("{{ url('get-business-sector') }}/" + id, function(res) {
                sector.html('<option value="">Select Sector</option>');
                res.forEach(s => {
                    sector.append(`<option value="${s.id}">${s.name}</option>`);
                });
            });
        });

        /* =====================================================================
           ON LOAD → populate Business Sector for existing company
        ===================================================================== */
        $(document).ready(function() {
            try {
                let selectedSector = "{{ old('business_sector_id', $company->business_sector_id ?? '') }}";
                let industryId = $('#industry_type_id').val();
                if (industryId) {
                    let sector = $("#business_sector_id");
                    sector.html('<option>Loading...</option>');
                    $.get("{{ url('get-business-sector') }}/" + industryId, function(res) {
                        sector.html('<option value="">Select Sector</option>');
                        res.forEach(s => {
                            let isSel = (String(s.id) === String(selectedSector)) ? 'selected' : '';
                            sector.append(`<option value="${s.id}" ${isSel}>${s.name}</option>`);
                        });
                    });
                }
            } catch(e) { console.error(e); }
        });

        /* =====================================================================
           COMPANY TYPE SWITCH
        ===================================================================== */
        $(document).ready(function() {

            // Convert full string to uppercase
            function updateCompanyType() {
                let t = $("#company_type").val();
                let name = $("#company_name").val().toUpperCase();

                if (t === "parent") {
                    $("#parentNameBox").removeClass("d-none");
                    $("#parentDropdownBox").addClass("d-none");
                    $("#parent_company_name").val(name);
                } else if (t === "subsidiary" || t === "branch" || t === "sub_branch") {
                    $("#parentDropdownBox").removeClass("d-none");
                    $("#parentNameBox").addClass("d-none");
                } else {
                    $("#parentDropdownBox").addClass("d-none");
                    $("#parentNameBox").addClass("d-none");
                }
            }

            // Auto-fill & Uppercase company and trade name
            $(document).on("keyup", "#company_name", function() {
                let name = $(this).val().toUpperCase();

                $(this).val(name); // Set uppercase in company name
                $("[name='trade_name']").val(name); // Auto-fill uppercase in trade name
            });


            updateCompanyType();
            $("#company_type").on("change", updateCompanyType);

            $("#company_name").on("keyup", function() {
                if ($("#company_type").val() === "parent") {
                    $("#parent_company_name").val($(this).val());
                }
            });
        });

        /* =====================================================================
           COUNTRY → STATE
        ===================================================================== */
        $(document).on("change", "#country_company", function() {

            let id = $(this).val();
            $("#state").html("<option>Loading...</option>");

            $.get("{{ url('/get_state_company') }}?country_id=" + id, function(res) {
                $("#state").empty().append('<option value="">Select</option>');
                let states = Array.isArray(res[0]) ? res[0] : res;
                states.forEach(s => {
                    $("#state").append(`<option value="${s.id}">${s.name}</option>`);
                });
            });
        });

        /* =====================================================================
           OWNER ADD/REMOVE
        ===================================================================== */
        let ownerIndex = $("#ownerWrapper .ownerRow").length;

        $(document).on("click", ".addOwner", function() {
            $("#ownerWrapper").append(`
            <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-1">
                    <label>Salutation</label>
                    <select name="owners[${ownerIndex}][salutation]" class="form-select form-select-sm">
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
                    <input type="text" name="owners[${ownerIndex}][first_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Last Name</label>
                    <input type="text" name="owners[${ownerIndex}][last_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Mobile</label>
                    <input type="text" name="owners[${ownerIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Email</label>
                    <input type="email" name="owners[${ownerIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Passport</label>
                    <input type="file" name="owner_files[${ownerIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Emirates ID</label>
                    <input type="file" name="owner_files[${ownerIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Visa</label>
                    <div class="d-flex gap-1">
                        <input type="file" name="owner_files[${ownerIndex}][visa_copy]" class="form-control form-control-sm">
                        <button type="button" class="btn btn-danger btn-sm removeOwner">-</button>
                    </div>
                </div>
            </div>
        `);
            ownerIndex++;
        });

        $(document).on("click", ".removeOwner", function() {
            $(this).closest(".ownerRow").remove();
        });

        /* =====================================================================
           SPONSOR ADD/REMOVE
        ===================================================================== */
        let sponsorIndex = $("#sponsorWrapper .sponsorRow").length;

        $(document).on("click", ".addSponsor", function() {
            $("#sponsorWrapper").append(`
            <div class="sponsorRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-1">
                    <label>Salutation</label>
                    <select name="sponsors[${sponsorIndex}][salutation]" class="form-select form-select-sm">
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
                    <input type="text" name="sponsors[${sponsorIndex}][first_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Last Name</label>
                    <input type="text" name="sponsors[${sponsorIndex}][last_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Mobile</label>
                    <input type="text" name="sponsors[${sponsorIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Email</label>
                    <input type="email" name="sponsors[${sponsorIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Passport</label>
                    <input type="file" name="sponsor_files[${sponsorIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Emirates ID</label>
                    <input type="file" name="sponsor_files[${sponsorIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Visa</label>
                    <div class="d-flex gap-1">
                        <input type="file" name="sponsor_files[${sponsorIndex}][visa_copy]" class="form-control form-control-sm">
                        <button type="button" class="btn btn-danger btn-sm removeSponsor">-</button>
                    </div>
                </div>
            </div>
        `);
            sponsorIndex++;
        });

        $(document).on("click", ".removeSponsor", function() {
            $(this).closest(".sponsorRow").remove();
        });

        /* =====================================================================
           CONTACT PERSON ADD/REMOVE
        ===================================================================== */
        let contactIndex = $("#contactWrapper .contactRow").length;

        $(document).on("click", ".addContact", function() {
            $("#contactWrapper").append(`
            <div class="contactRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-1">
                    <label>Salutation</label>
                    <select name="contacts[${contactIndex}][salutation]" class="form-select form-select-sm">
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
                    <input type="text" name="contacts[${contactIndex}][first_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Last Name</label>
                    <input type="text" name="contacts[${contactIndex}][last_name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Mobile</label>
                    <input type="text" name="contacts[${contactIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Email</label>
                    <input type="email" name="contacts[${contactIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <label>Designation</label>
                    <input type="text" name="contacts[${contactIndex}][designation]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Passport</label>
                    <input type="file" name="contact_files[${contactIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Emirates ID</label>
                    <input type="file" name="contact_files[${contactIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1">
                    <label>Visa</label>
                    <div class="d-flex gap-1">
                        <input type="file" name="contact_files[${contactIndex}][visa_copy]" class="form-control form-control-sm">
                        <button type="button" class="btn btn-danger btn-sm removeContact">-</button>
                    </div>
                </div>
            </div>
        `);
            contactIndex++;
        });

        $(document).on("click", ".removeContact", function() {
            $(this).closest(".contactRow").remove();
        });

        /* =====================================================================
           TAX BLOCK (VAT / CT) SHOW / HIDE
        ===================================================================== */
        $(document).on("change", "#tax_applicable", function() {

            let v = $(this).val();

            $(".vat-section").toggleClass("d-none", !(v === "vat" || v === "both"));
            $(".ct-section").toggleClass("d-none", !(v === "ct" || v === "both"));
        });

        /* =====================================================================
           BANKING TAB — Add Bank button behavior
           Keep the Add Bank button available so users can add banks into session
           even before the company record is persisted. The banking partial
           handles injecting `company_id` where needed and the session-backed
           endpoints do not require a company id to store temporary banks.
        ===================================================================== */
        function enableBankButton() {
            // Always enable the Add Bank button on the add-company page so
            // users can add multiple banks into session prior to saving.
            $("#addBankBtn").prop("disabled", false);
        }
        // Ensure button is enabled on load
        enableBankButton();

        /* =====================================================================
           BANK SAVE
        ===================================================================== */
        $(document).on("click", "#bankSaveBtn", function(e) {

            e.preventDefault();

            let cid = $("#company_id").val();
            if (!cid) {
                alert("⚠ Please save Basic Information first.");
                return;
            }

            $(".bank-error").remove();

            let data = new FormData();
            data.append("_token", "{{ csrf_token() }}");
            data.append("company_id", cid);

            $("#bankForm .bank-input").each(function() {

                let name = $(this).attr("name");

                if ($(this).attr("type") === "file") {
                    if (this.files[0]) {
                        data.append(name, this.files[0]);
                    }
                } else {
                    data.append(name, $(this).val());
                }

            });

            $.ajax({
                url: "{{ route('company.banking.store') }}",
                type: "POST",
                data: data,
                contentType: false,
                processData: false,

                success: function(res) {

                    if (res.ok || res.success) {
                        $("#bankAddModal").modal("hide");
                        appendBankRow(res.bank);
                        toastr && toastr.success ? toastr.success("Bank Added Successfully!") : alert(
                            "Bank Added Successfully!");
                    }
                },

                error: function(xhr) {
                    console.log("❌ BANK SAVE ERROR", xhr);

                    if (xhr.status === 419) {
                        alert("⚠ CSRF token missing/expired. Refresh page and try again.");
                    }

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, msg) {
                            let input = $('#bankForm [name="' + key + '"]');
                            input.after('<small class="text-danger bank-error">' + msg[0] +
                                '</small>');
                        });
                    }
                }
            });

        });

        /* =====================================================================
           APPEND BANK ROW IN TABLE (ALL COLUMNS)
        ===================================================================== */
        function appendBankRow(bank) {

            // Remove placeholder row
            $("#bankTableBody").find(".no-bank-row").remove();

            let fileName = bank.bank_letter ? bank.bank_letter.split("/").pop() : "-";

            let row = `
            <tr id="bankRow_${bank.id}">
                <td>${bank.bank_name || "-"}</td>
                <td>${bank.branch_name || "-"}</td>
                <td>${bank.account_number || "-"}</td>
                <td>${bank.iban_number || "-"}</td>
                <td>${bank.swift_code || "-"}</td>
                <td>${bank.finance_code || "-"}</td>
                <td>${bank.currency || "-"}</td>
                <td>${fileName}</td>
                <td>
                    <button class="btn btn-light d-inline-flex align-items-center" data-id="${bank.id}">
                        <i class="ico icon-outline-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning editBankBtn" data-id="${bank.id}">
                        <i class="ico icon-outline-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBankBtn" data-id="${bank.id}">
                        <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                    </button>
                </td>
            </tr>
        `;

            $("#bankTableBody").append(row);
        }

        /* =====================================================================
           DELETE BANK
        ===================================================================== */
        $(document).on("click", ".deleteBankBtn", function() {

            if (!confirm("Delete this bank?")) return;

            let id = $(this).data("id");

            $.ajax({
                url: "/company/banking/delete/" + id,
                type: "GET",
                success: function() {
                    $("#bankRow_" + id).remove();
                }
            });
        });

        /* =====================================================================
       ENABLE POLICY BUTTON ONCE BASIC IS SAVED
    ===================================================================== */
        function enablePolicyButton() {
            // Always enable Add Policy button so users can open the modal
            // even before the company record is saved (similar to Banking behavior).
            $("#addPolicyBtn").prop("disabled", false);
        }
        enablePolicyButton();

        /* =====================================================================
           SAVE HR PAYROLL SETTINGS DIRECTLY TO DATABASE
        ===================================================================== */
        function saveHrPayrollSettings(companyId) {
            if (!companyId) {
                console.warn('⚠️ No company ID for HR Payroll Settings');
                return;
            }

            // Collect all HR Payroll Setting form inputs
            // IMPORTANT: All fields are optional - send only if user provided a value
            let hrPayrollData = {
                company_id: companyId,
                _token: '{{ csrf_token() }}'
            };

            // Helper to add non-empty fields
            const addField = (dbName, formName) => {
                let val = $(`[name="${formName}"]`).val();
                if (val && val !== '' && val !== 'null') {
                    hrPayrollData[dbName] = val;
                }
            };

            // WPS / Salary Fields (Optional)
            addField('hr_wps_establishment_id', 'hr_wps_establishment_id');
            addField('hr_wps_bank', 'hr_wps_bank');
            addField('hr_wps_salary_file_code', 'hr_wps_salary_file_code');

            // Payroll Cycle Fields (Optional)
            addField('hr_payroll_cycle', 'hr_payroll_cycle');
            addField('hr_payroll_start', 'hr_payroll_start');
            addField('hr_payroll_end', 'hr_payroll_end');
            addField('hr_weekly_off', 'hr_weekly_off[]');
            addField('hr_gratuity_method', 'hr_gratuity_method');

            // Attendance Policy Fields (Optional)
            addField('attendance_policy', 'attendance_policy');
            addField('min_working_hours', 'min_working_hours');
            addField('grace_period', 'grace_period');
            addField('half_day_after', 'half_day_after');
            addField('absent_below_hours', 'absent_below_hours');
            addField('late_mark_allowed', 'late_mark_allowed');
            addField('late_mark_halfday', 'late_mark_halfday');
            addField('auto_absent_after', 'auto_absent_after');

            // Shift & Time Fields (Optional)
            addField('shift_start_time', 'shift_start_time');
            addField('shift_end_time', 'shift_end_time');

            // Leave Policy Fields (Optional)
            addField('leave_policy_type', 'leave_policy_type');
            addField('annual_leave', 'annual_leave');
            addField('sick_leave', 'sick_leave');
            addField('casual_leave', 'casual_leave');
            addField('comp_off_allowed', 'comp_off_allowed');
            addField('carry_forward', 'carry_forward');
            addField('max_carry_forward', 'max_carry_forward');
            addField('leave_encashment', 'leave_encashment');

            // Weekly Off Days (Optional - handle as array)
            let weeklyOffDays = $('[name="weekly_off_days[]"]').val();
            if (weeklyOffDays && Array.isArray(weeklyOffDays) && weeklyOffDays.length > 0) {
                hrPayrollData.weekly_off_days = weeklyOffDays;
            }

            console.log('📤 Sending HR Payroll Settings:', hrPayrollData);

            $.ajax({
                url: '{{ route("company.hrpayroll.setting.store") }}',
                type: 'POST',
                data: hrPayrollData,
                success: function(res) {
                    if (res.status) {
                        console.log('✅ HR Payroll Settings saved successfully');
                        toastr && toastr.success ? toastr.success(res.message || 'HR Payroll Settings saved') : console.log('HR Payroll Settings saved');
                    } else {
                        console.warn('⚠️ HR Payroll Settings save returned not-ok:', res);
                        toastr && toastr.warning ? toastr.warning(res.message || 'Could not save HR Payroll Settings') : console.log(res.message);
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error saving HR Payroll Settings:', xhr);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        console.error('Validation errors:', xhr.responseJSON.errors);
                    }
                    let errorMsg = 'Error saving HR Payroll Settings';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastr && toastr.error ? toastr.error(errorMsg) : console.log(errorMsg);
                }
            });
        }

        /* =====================================================================
           LOAD HR PAYROLL SETTINGS FOR EDIT MODE
        ===================================================================== */
        function loadHrPayrollSettings(companyId) {
            if (!companyId) {
                console.warn('⚠️ No company ID for loading HR Payroll Settings');
                return;
            }

            $.ajax({
                url: '{{ route("company.hrpayroll.setting.get", "") }}/' + companyId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.status && res.data) {
                        console.log('✅ HR Payroll Settings loaded');
                        let data = res.data;
                        
                        // Populate form fields with saved data
                        $('[name="hr_wps_establishment_id"]').val(data.wps_establishment_id || '');
                        $('[name="hr_wps_bank"]').val(data.wps_bank || '');
                        $('[name="hr_wps_salary_file_code"]').val(data.wps_salary_file_code || '');
                        $('[name="hr_payroll_cycle"]').val(data.payroll_cycle || '');
                        $('[name="hr_payroll_start"]').val(data.payroll_start || '');
                        $('[name="hr_payroll_end"]').val(data.payroll_end || '');
                        // hr_weekly_off may be a JSON array string or an array — handle both and populate the multi-select
                        if (data.weekly_off) {
                            let weekly = data.weekly_off;
                            if (typeof weekly === 'string') {
                                try {
                                    weekly = JSON.parse(weekly);
                                } catch (e) {
                                    // Not JSON — wrap single value in array
                                    weekly = [weekly];
                                }
                            }
                            $('[name="hr_weekly_off[]"]').val(Array.isArray(weekly) ? weekly : [weekly]).trigger('change');
                        } else {
                            $('[name="hr_weekly_off[]"]').val([]);
                        }
                        $('[name="hr_gratuity_method"]').val(data.gratuity_method || '');
                        $('[name="attendance_policy"]').val(data.attendance_policy || '');
                        $('[name="min_working_hours"]').val(data.minimum_working_hours || '');
                        $('[name="grace_period"]').val(data.grace_period || '');
                        $('[name="half_day_after"]').val(data.half_day_after || '');
                        $('[name="absent_below_hours"]').val(data.absent_below_hours || '');
                        $('[name="late_mark_allowed"]').val(data.late_mark_allowed || '');
                        $('[name="late_mark_halfday"]').val(data.late_mark_halfday || '');
                        $('[name="auto_absent_after"]').val(data.auto_absent_after || '');
                        $('[name="shift_start_time"]').val(data.shift_start_time || '');
                        $('[name="shift_end_time"]').val(data.shift_end_time || '');
                        
                        // Handle weekly_off_days as array
                        if (data.weekly_off_days && typeof data.weekly_off_days === 'string') {
                            try {
                                let daysArray = JSON.parse(data.weekly_off_days);
                                $('[name="weekly_off_days[]"]').val(daysArray);
                            } catch (e) {
                                console.warn('Could not parse weekly_off_days:', e);
                            }
                        } else if (data.weekly_off_days) {
                            $('[name="weekly_off_days[]"]').val(data.weekly_off_days);
                        }
                        
                        $('[name="leave_policy_type"]').val(data.leave_policy_type || '');
                        $('[name="annual_leave"]').val(data.annual_leave || '');
                        $('[name="sick_leave"]').val(data.sick_leave || '');
                        $('[name="casual_leave"]').val(data.casual_leave || '');
                        $('[name="comp_off_allowed"]').val(data.comp_off_allowed || '');
                        $('[name="carry_forward"]').val(data.carry_forward || '');
                        $('[name="max_carry_forward"]').val(data.max_carry_forward || '');
                        $('[name="leave_encashment"]').val(data.leave_encashment || '');
                    } else {
                        console.warn('No HR Payroll Settings found for this company');
                    }
                },
                error: function(xhr) {
                    console.warn('Could not load HR Payroll Settings:', xhr.status);
                }
            });
        }

        /* =====================================================================
           Session badges (Banks / Policies / Warehouses)
        ===================================================================== */
        function updateSessionBadges() {
            // Policies
            $.post('{{ url("company/policy/session/get") }}', { _token: '{{ csrf_token() }}' }, function(resp){
                let cnt = (resp.policies || []).length;
                if (cnt > 0) {
                    $('#policyCountBadge').text(cnt).removeClass('d-none');
                } else {
                    $('#policyCountBadge').addClass('d-none').text('');
                }
            }).fail(function(){ $('#policyCountBadge').addClass('d-none').text(''); });

            // Banks
            $.post('{{ url("company/bank/session/get") }}', { _token: '{{ csrf_token() }}' }, function(resp){
                let cnt = (resp.banks || []).length;
                if (cnt > 0) {
                    $('#bankCountBadge').text(cnt).removeClass('d-none');
                } else {
                    $('#bankCountBadge').addClass('d-none').text('');
                }
            }).fail(function(){ $('#bankCountBadge').addClass('d-none').text(''); });

            // Warehouses (from session storage)
            try {
                const warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
                const cnt = warehouses.length;
                if (cnt > 0) {
                    $('#warehouseCountBadge').text(cnt).removeClass('d-none');
                } else {
                    $('#warehouseCountBadge').addClass('d-none').text('');
                }
            } catch(e) {
                $('#warehouseCountBadge').addClass('d-none').text('');
            }
        }

        // Initialize badges on load
        updateSessionBadges();

        /* =====================================================================
           OPEN MODAL → RESET FORM
        ===================================================================== */
        $(document).on("click", "#addPolicyBtn", function() {
            let cid = $("#company_id").val();

            // Allow opening the modal even if company_id is not yet set.
            $("#policyForm")[0].reset();
            $(".policy-error").remove();

            // If company id exists, populate hidden field; otherwise leave blank
            if (cid) {
                $("#policy_company_id").val(cid);
            } else {
                $("#policy_company_id").val('');
            }
        });

        // SAVE POLICY
        $(document).on("click", "#savePolicyBtn", function(e) {

            e.preventDefault();
            $(".policy-error").remove();

            let cid = $("#company_id").val();
            // Build form data
            let fd = new FormData();
            fd.append("_token", "{{ csrf_token() }}");
            if (cid) fd.append("company_id", cid);

            fd.append("policy_date", $("[name='policy_date']").val());
            fd.append("policy_name", $("[name='policy_name']").val());
            fd.append("policy_category", $("[name='policy_category']").val());
            fd.append("policy_valid", $("[name='policy_valid']").val());
            fd.append("view_to_employees", $("[name='view_to_employees']").val());
            fd.append("policy_details", $("[name='policy_details']").val());

            let file = $("[name='policy_file']")[0].files[0];
            if (file) fd.append("policy_file", file);

            // Always save into session (realtime) — we'll persist to DB when company is saved
            $.ajax({
                url: '{{ url("company/policy/session/store") }}',
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp && resp.policies) {
                        hrPolicies = resp.policies;
                        if (typeof renderPolicyTable === 'function') {
                            renderPolicyTable(hrPolicies);
                        } else {
                            // fallback rendering if partial script not yet loaded
                            let body = $("#policyTableBody");
                            body.empty();
                            if (!hrPolicies.length) {
                                body.append(`<tr><td colspan="7" class="text-center text-muted">No policies added yet.</td></tr>`);
                            } else {
                                hrPolicies.forEach(p => {
                                    body.append(`
                                        <tr>
                                            <td>${p.policy_date?.substring(0,10) ?? '-'}</td>
                                            <td>${p.policy_name}</td>
                                            <td>${p.policy_category ?? '-'}</td>
                                            <td>${p.policy_valid?.substring(0,10) ?? '-'}</td>
                                            <td>${p.view_to_employees ? 'Yes' : 'No'}</td>
                                            <td>
                                                ${p.policy_file ? `<a href="/${p.policy_file}" target="_blank" class="btn btn-sm btn-light">View</a>` : ''}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary editPolicyBtn" data-item='${JSON.stringify(p)}'>Edit</button>
                                            </td>
                                        </tr>
                                    `);
                                });
                            }
                        }
                    }
                    $("#policyModal").modal("hide");
                    $("#policyForm")[0].reset();
                    toastr.success('Policy saved to session');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors || {};
                        $.each(errors, function(key, msg) {
                            $(`[name="${key}"]`).after(`<small class="text-danger policy-error">${msg[0]}</small>`);
                        });
                    }
                }
            });
        });

        function loadPolicies() {
            let cid = $("#company_id").val();
            if (!cid) return;

            $("#policyTableBody").html(`
        <tr>
            <td colspan="7" class="text-center">Loading...</td>
        </tr>
    `);

            $.get(`/company/hr-policy/list/${cid}`, function(html) {
                $("#policyTableBody").html(html);
            });
        }

        // Ensure tabs can be enabled programmatically.
        // Usage: enableTab('#bankTab') — removes disabled state on the associated tab button.
        function enableTab(tabSelector) {
            try {
                var btn = document.querySelector('[data-bs-target="' + tabSelector + '"]');
                if (!btn) return;

                btn.removeAttribute('disabled');
                btn.classList.remove('disabled');
                btn.setAttribute('data-bs-toggle', 'tab');
                btn.setAttribute('aria-disabled', 'false');
            } catch (e) {
                console.warn('enableTab failed', e);
            }
        }

        // Prevent native form submits which can cause full-page navigation
        // We handle all submits via AJAX buttons in this page.
        $(document).on('submit', '#companyForm', function(e) {
            e.preventDefault();
            return false;
        });

        // Enter key navigation - jump to next input field
        $(document).on('keydown', '#companyForm input:not([type="submit"]):not([type="button"]), #companyForm select, #companyForm textarea', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                // Don't navigate for textarea (allow new lines with Enter)
                if (this.tagName === 'TEXTAREA') {
                    return;
                }
                
                e.preventDefault();
                
                // Get all focusable inputs in the form
                var $form = $('#companyForm');
                var $inputs = $form.find('input:visible:not([type="submit"]):not([type="button"]):not([readonly]), select:visible, textarea:visible').filter(function() {
                    return !$(this).prop('disabled');
                });
                
                // Find current index
                var currentIndex = $inputs.index(this);
                
                // Focus next input
                if (currentIndex > -1 && currentIndex < $inputs.length - 1) {
                    $inputs.eq(currentIndex + 1).focus();
                    
                    // If it's a select2, open it
                    var $next = $inputs.eq(currentIndex + 1);
                    if ($next.hasClass('select2-hidden-accessible')) {
                        $next.select2('open');
                    }
                }
            }
        });

        /* =====================================================================
           WAREHOUSE SAVE FUNCTIONALITY
        ===================================================================== */
        function saveWarehousesFromSession(companyId) {
            // Get warehouse data from session storage
            const warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
            
            if (warehouses.length === 0) {
                console.log('📦 No warehouses to save from session');
                return;
            }

            console.log('📦 Saving ' + warehouses.length + ' warehouses to database for company:', companyId);

            $.ajax({
                url: "{{ route('company.warehouse.saveAll') }}",
                type: "POST",
                data: { 
                    company_id: companyId, 
                    warehouses: warehouses,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(resp) {
                    console.log('📦 Warehouse saveAll response:', resp);
                    if (resp && resp.ok) {
                        toastr && toastr.success ? toastr.success(resp.message || 'Warehouses saved successfully') : alert('Warehouses saved');
                        // Clear session storage after successful save
                        sessionStorage.removeItem('company_warehouses');
                        // Update badge
                        updateWarehouseBadge();
                    } else {
                        console.warn('⚠️ Warehouse saveAll returned not-ok:', resp);
                        toastr && toastr.warning ? toastr.warning(resp.message || 'No warehouse data to save') : console.log(resp.message);
                    }
                },
                error: function(xhr) {
                    console.error('❌ Could not save session warehouses:', xhr);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        console.error('Validation errors:', errors);
                        toastr && toastr.error ? toastr.error('Warehouse validation error') : alert('Warehouse validation error');
                    } else {
                        toastr && toastr.error ? toastr.error('Error saving warehouse details') : alert('Error saving warehouse details');
                    }
                }
            });
        }

        // Custom validation for tab jumping
        function validateFormAndJumpToTab() {
            const form = document.getElementById('companyForm');
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let firstInvalidInput = null;
            let targetTabId = null;
            let invalidFields = [];
            let targetTabName = '';

            // Reset previous validation states
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            });

            // Check each required field
            inputs.forEach(input => {
                const value = input.value.trim();
                let isValid = true;

                if (input.hasAttribute('required') && !value) {
                    isValid = false;
                }

                if (!isValid) {
                    // Debug: Log the input details
                    console.log('Invalid field found:', {
                        name: input.getAttribute('name'),
                        id: input.getAttribute('id'),
                        placeholder: input.getAttribute('placeholder'),
                        type: input.type,
                        tagName: input.tagName,
                        closest_tab: input.closest('.tab-pane')?.id || 'basic-info'
                    });

                    if (!firstInvalidInput) {
                        firstInvalidInput = input;
                        
                        // Find which tab this input belongs to
                        const tabPane = input.closest('.tab-pane');
                        if (tabPane) {
                            targetTabId = tabPane.id;
                            // Get tab name from the tab button
                            const tabButton = document.querySelector(`[data-bs-target="#${targetTabId}"]`);
                            if (tabButton) {
                                targetTabName = tabButton.textContent.trim();
                            }
                        }
                    }

                    // Add validation styling
                    input.classList.add('is-invalid');
                    
                    // Get field label or name for error message
                    let fieldLabel = '';
                    const inputName = input.getAttribute('name') || '';
                    
                    // First try to get label from closest parent container
                    const label = input.closest('.input-effect, .col-lg-3, .col-lg-2, .col-md-6, .col-md-4, .col-md-3, .col-md-2, .col-12, .form-group')?.querySelector('label');
                    if (label) {
                        fieldLabel = label.textContent.replace('*', '').trim();
                    } else {
                        // Fallback to input name or placeholder
                        fieldLabel = inputName || input.getAttribute('placeholder') || 'Unknown field';
                    }
                    
                    // Clean up field label and make it more specific
                    fieldLabel = fieldLabel.replace(/:/g, '').trim();
                    
                    // Add input name in parentheses for better identification if it's different
                    if (inputName && !fieldLabel.toLowerCase().includes(inputName.toLowerCase())) {
                        fieldLabel += ` (${inputName})`;
                    }
                    
                    // Only add unique field labels to avoid duplicates
                    if (fieldLabel && !invalidFields.includes(fieldLabel)) {
                        invalidFields.push(fieldLabel);
                    }
                    
                    // Add error message
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required.';
                    input.parentNode.appendChild(feedback);
                }
            });

            // If validation failed and field is in a tab, jump to that tab
            if (firstInvalidInput && targetTabId) {
                const tabButton = document.querySelector(`[data-bs-target="#${targetTabId}"]`);
                if (tabButton) {
                    // Activate the tab
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                    
                    // Focus the invalid input after tab transition
                    setTimeout(() => {
                        firstInvalidInput.focus();
                        firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 150);
                    
                    // Create detailed error message with field names
                    let errorMessage = `Please fill in the following required fields in the ${targetTabName} tab:\n`;
                    if (invalidFields.length > 0) {
                        errorMessage += '• ' + invalidFields.join('\n• ');
                    }
                    
                    // Show toastr notification
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage, 'Validation Error', {
                            timeOut: 8000,
                            positionClass: 'toast-top-right',
                            escapeHtml: false
                        });
                    }
                }
                return false;
            } else if (firstInvalidInput) {
                // Field is in basic info section (not in tabs)
                firstInvalidInput.focus();
                firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Create detailed error message with field names for basic info
                let errorMessage = 'Please fill in the following required fields:\n';
                if (invalidFields.length > 0) {
                    errorMessage += '• ' + invalidFields.join('\n• ');
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage, 'Validation Error', {
                        timeOut: 8000,
                        positionClass: 'toast-top-right',
                        escapeHtml: false
                    });
                }
                return false;
            }

            return true;
        }

        // Override the existing save function to include validation
        const originalSaveBasic = window.saveBasic || function() {};
        window.saveBasic = function() {
            if (validateFormAndJumpToTab()) {
                originalSaveBasic();
            }
        };

        // Add real-time validation on input blur
        $(document).on('blur', '#companyForm input[required], #companyForm select[required], #companyForm textarea[required]', function() {
            const input = this;
            const value = input.value.trim();
            
            if (input.hasAttribute('required') && !value) {
                input.classList.add('is-invalid');
                
                // Add error message if not exists
                if (!input.parentNode.querySelector('.invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required.';
                    input.parentNode.appendChild(feedback);
                }
            } else {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            }
        });
    </script>

    {{-- Form Validation Script --}}
    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for company form
            FormValidator.init('companyForm', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000,
                customValidation: true // Enable our custom tab jumping validation
            });
        });
    </script>

        <script>
        $(document).ready(function() {
            // Auto-add class 'capitalize-title' to inputs that should be title-cased
            $('input').each(function() {
                var $i = $(this);
                var type = ($i.attr('type') || '').toLowerCase();
                var name = ($i.attr('name') || '').toLowerCase();

                // Skip excluded input types
                var excludedTypes = ['email', 'tel', 'date', 'file', 'number', 'hidden', 'checkbox',
                    'radio', 'submit', 'button', 'password'
                ];
                if (type && excludedTypes.indexOf(type) !== -1) return;

                // Skip date-picker, selects and inputs that look like contact details
                if ($i.hasClass('date-picker')) return;
                if (name.match(
                        /email|website|document_number|mobile|phone|password|iban_number|swift_code|currency|docs\[joining\]\[passport_visa\]\[number\]|docs\[joining\]\[iban_letter\]\[number\]|docs\[joining\]\[prof_certs\]\[number\]|docs\[joining\]\[academic\]\[number\]/
                        )) return;

                $i.addClass('capitalize-title');
            });

            // Title-case inputs having class 'capitalize-title'
            $(document).on('input', '.capitalize-title', function() {
                var val = $(this).val() || '';
                // Make everything lowercase first, then uppercase word-start letters
                val = val.toLowerCase().replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
                $(this).val(val);
            });
        });
    </script>
@endpush

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
                    <h4>Add Company</h4>
                    <div class="purchase-order-content-header-right d-flex align-items-center gap-1">

                        <span id="basicSuccess" class="text-success ms-2"></span>

                        <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="saveBasicBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="basicLoader"></span>
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span>Save</span>
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

                        <form id="companyForm" method="POST" enctype="multipart/form-data">

                            @csrf

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
                                            data-bs-target="#companyPolicies" data-tab-key="policy">
                                            Company Policies
                                            <span class="badge bg-primary ms-1 d-none" id="policyCountBadge"></span>
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#documentations" data-tab-key="docs">
                                            Documentations
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#companySetting" data-tab-key="settings">
                                            Company Setting
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

                                <div class="tab-pane fade" id="companyPolicies">
                                    @include('backEnd.company.partials._company_policies')

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

            $("#basicLoader").removeClass("d-none");
            $("#basicSuccess").html("");
            $(".text-danger").remove();

            let form = $("#companyForm")[0];
            let fd = new FormData(form);

            // STEP 1: BASIC INFO SAVE
            $.ajax({
                url: "{{ route('company.basic.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,

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

            $.ajax({
                url: "{{ route('company.contact.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,

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

            $.ajax({
                url: "{{ route('company.compliance.store') }}",
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,

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

                success: function(res) {
                    $("#basicLoader").addClass("d-none");

                    if (res.ok || res.success) {

                        $("#basicLoader").addClass("d-none");
                        $("#basicSuccess").html("All company details saved successfully!");
                        toastr && toastr.success ? toastr.success("Company Saved") : alert("Company Saved");

                        // Do not auto-navigate to Banking tab after save

                        // Persist any session-stored banks into DB for this company
                        (function(){
                            const cid = $("#company_id").val();
                            if (!cid) return;

                            // Enable bank UI now that company exists
                            try { enableBankButton(); } catch (e) { $('#addBankBtn').prop('disabled', false); }

                            $.ajax({
                                url: "{{ route('company.banking.saveAll') }}",
                                type: "POST",
                                data: { company_id: cid, _token: '{{ csrf_token() }}' },
                                success: function(resp) {
                                    if (resp && resp.ok) {
                                        toastr && toastr.success ? toastr.success(resp.message || 'Banks saved') : alert('Banks saved');
                                        try { updateSessionBadges(); } catch(e){}
                                    }
                                },
                                error: function(xhr){
                                    console.warn('Could not save session banks:', xhr);
                                }
                            });
                            // Persist any session-stored policies into DB for this company
                            $.ajax({
                                url: '{{ route('company.policy.saveAll') }}',
                                type: 'POST',
                                data: { company_id: cid, _token: '{{ csrf_token() }}' },
                                success: function(resp) {
                                    console.log('saveAllPolicies success', resp);
                                    if (resp && resp.ok) {
                                        toastr && toastr.success ? toastr.success(resp.message || 'Policies saved') : alert('Policies saved');
                                        try { updateSessionBadges(); } catch(e){}
                                    } else {
                                        // server responded but flagged as not-ok
                                        console.warn('saveAllPolicies returned not-ok', resp);
                                        toastr && toastr.error ? toastr.error(resp.message || 'Could not save policies') : alert('Could not save policies');
                                    }
                                },
                                error: function(xhr) {
                                    // Detailed logging for debugging
                                    console.error('Could not save session policies: status=', xhr.status);
                                    try {
                                        console.error('responseText:', xhr.responseText);
                                    } catch(e){}
                                    try {
                                        console.error('responseJSON:', xhr.responseJSON);
                                    } catch(e){}
                                    toastr && toastr.error ? toastr.error('Server error while saving policies') : alert('Server error while saving policies');
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
        ===================================================================== */
        $(document).on("change", "#industry_id", function() {

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
                let industryId = $('#industry_id').val();
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
                } else if (t === "subsidiary" || t === "branch") {
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
        let ownerIndex = 1;

        $(document).on("click", ".addOwner", function() {
            $("#ownerWrapper").append(`
            <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-2">
                    <input type="text" name="owners[${ownerIndex}][name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="text" name="owners[${ownerIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="email" name="owners[${ownerIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="file" name="owner_files[${ownerIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="file" name="owner_files[${ownerIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2 d-flex gap-1">
                    <input type="file" name="owner_files[${ownerIndex}][visa_copy]" class="form-control form-control-sm">
                    <button type="button" class="btn btn-light add-btn ms-2 removeOwner">-</button>
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
        let sponsorIndex = 1;

        $(document).on("click", ".addSponsor", function() {
            $("#sponsorWrapper").append(`
            <div class="sponsorRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-2">
                    <input type="text" name="sponsors[${sponsorIndex}][name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="text" name="sponsors[${sponsorIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="email" name="sponsors[${sponsorIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="file" name="sponsor_files[${sponsorIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                    <input type="file" name="sponsor_files[${sponsorIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2 d-flex gap-1">
                    <input type="file" name="sponsor_files[${sponsorIndex}][visa_copy]" class="form-control form-control-sm">
                    <button type="button" class="btn btn-light add-btn ms-2 btn-sm removeSponsor">-</button>
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
        let contactIndex = 1;

        $(document).on("click", ".addContact", function() {

            $("#contactWrapper").append(`
            <div class="contactRow row gy-2 p-2 mb-2 border rounded">
                <div class="col-lg-3">
                    <input type="text" name="contacts[${contactIndex}][name]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                    <input type="text" name="contacts[${contactIndex}][mobile]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                    <input type="email" name="contacts[${contactIndex}][email]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                    <input type="text" name="contacts[${contactIndex}][designation]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                    <input type="file" name="contact_files[${contactIndex}][passport_copy]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                    <input type="file" name="contact_files[${contactIndex}][emirates_id]" class="form-control form-control-sm">
                </div>
                <div class="col-lg-6 d-flex gap-1">
                    <input type="file" name="contact_files[${contactIndex}][visa_copy]" class="form-control form-control-sm">
                    <button type="button" class="btn btn-light add-btn ms-2 removeContact">-</button>
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
           Session badges (Banks / Policies)
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
    </script>
@endpush

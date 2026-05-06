
    <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn btn-success btn-sm"
            id="addPolicyBtn" data-bs-toggle="modal" data-bs-target="#policyModal">
              <i class="ico icon-outline-add-square"></i> Add Policy
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover data-table" style="table-layout: fixed;width:100%">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Policy Name</th>
                    <th>Category</th>
                    <th>Valid Till</th>
                    <th>View to Employees</th>
                    <th>File</th>
                    <th width="100">Action</th>
                </tr>
            </thead>
            <tbody id="policyTableBody">
                <tr>
                    <td colspan="7" class="text-center text-muted">No policies added yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


{{-- =================== MODAL =================== --}}
<div class="modal fade" id="policyModal">
    <div class="modal-dialog modal-lg">
        <form id="policyForm" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="policy_id" id="policy_id">
            <input type="hidden" name="company_id" id="policy_company_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="policyModalTitle">Add Policy</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row gy-2">

                    <div class="col-lg-3">
                        <label>Date *</label>
                        <input type="date" name="policy_date"
                               class="form-control form-control-sm policy-input">
                    </div>

                    <div class="col-lg-3">
                        <label>Policy Name *</label>
                        <input type="text" name="policy_name"
                               class="form-control form-control-sm policy-input">
                    </div>

                    {{-- <div class="col-lg-3">
                        <label>Category</label>
                        <select name="policy_category" class="form-select form-select-sm policy-input">
                            <option value="">Select</option>
                            <option value="health">Health</option>
                            <option value="life">Life</option>
                            <option value="vehicle">Vehicle</option>
                        </select>
                    </div> --}}

                    <div class="col-lg-2">
                        <label>Valid Till</label>
                        <input type="date" name="policy_valid"
                               class="form-control form-control-sm policy-input">
                    </div>

                    <div class="col-lg-2">
                        <label>View to All *</label>
                        <select name="view_to_employees"
                                class="form-select form-select-sm policy-input">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <label>Policy File *</label>
                        <input type="file" name="policy_file"
                               class="form-control form-control-sm policy-input">
                        <div id="existingFile" class="mt-1"></div>
                    </div>

                    <div class="col-lg-12">
                        <label>Details</label>
                        <!-- Simple Text Editor Toolbar -->
                        <div class="btn-toolbar mb-1" id="policyEditorToolbar">
                            <div class="btn-group btn-group-sm me-1">
                                <button type="button" class="btn btn-light" data-cmd="bold" title="Bold"><b>B</b></button>
                                <button type="button" class="btn btn-light" data-cmd="italic" title="Italic"><i>I</i></button>
                                <button type="button" class="btn btn-light" data-cmd="underline" title="Underline"><u>U</u></button>
                            </div>
                            <div class="btn-group btn-group-sm me-1">
                                <button type="button" class="btn btn-light" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
                                <button type="button" class="btn btn-light" data-cmd="insertOrderedList" title="Numbered List">1. List</button>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light" data-cmd="undo" title="Undo">↩</button>
                                <button type="button" class="btn btn-light" data-cmd="redo" title="Redo">↪</button>
                            </div>
                        </div>
                        <!-- Editable Content Area -->
                        <div id="policyDetailsEditor" contenteditable="true" 
                            class="form-control" 
                            style="min-height: 120px; max-height: 200px; overflow-y: auto;"></div>
                        <!-- Hidden textarea for form submission -->
                        <textarea name="policy_details" id="policyDetailsHidden" class="d-none"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm" id="savePolicyBtn">Save</button>
                </div>
            </div>
        </form>
</div>


{{-- =================== POLICY SCRIPT =================== --}}
<script>
    const POLICY_BASE_URL = "{{ url('/') }}";
    let hrPolicies = @json($hr_policies ?? []);

    // ========== SIMPLE TEXT EDITOR ==========
    $('#policyEditorToolbar button[data-cmd]').on('click', function(e) {
        e.preventDefault();
        var cmd = $(this).data('cmd');
        document.execCommand(cmd, false, null);
        $('#policyDetailsEditor').focus();
    });

    // Sync editor content to hidden textarea before form actions
    function syncEditorToTextarea() {
        $('#policyDetailsHidden').val($('#policyDetailsEditor').html());
    }

    // ========== RENDER TABLE ==========
    function renderPolicyTable(data) {
        let body = $("#policyTableBody");
        body.empty();

        if (!data || !data.length) {
            body.append(`<tr>
                <td colspan="7" class="text-center text-muted">No policies added yet.</td>
            </tr>`);
            return;
        }

        data.forEach(p => {
            body.append(`
                <tr>
                    <td>${p.policy_date?.substring(0,10) ?? '-'}</td>
                    <td>${p.policy_name}</td>
                    <td>${p.policy_category ?? '-'}</td>
                    <td>${p.policy_valid?.substring(0,10) ?? '-'}</td>
                    <td>${p.view_to_employees ? 'Yes' : 'No'}</td>
                    <td>
                        <a href="/${p.policy_file}" target="_blank" class="btn btn-sm btn-light">View</a>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary editPolicyBtn" data-item='${JSON.stringify(p)}'>Edit</button>
                    </td>
                </tr>
            `);
        });
    }

    // Initial render
    renderPolicyTable(hrPolicies);

    // ========== LOAD SESSION POLICIES (for create flow) ==========
    (function loadSessionPolicies() {
        if (hrPolicies && hrPolicies.length) return;

        $.post(POLICY_BASE_URL + '/company/policy/session/get', {_token: '{{ csrf_token() }}'}, function(resp) {
            if (resp && resp.policies) {
                hrPolicies = resp.policies;
            } else {
                hrPolicies = [];
            }
            renderPolicyTable(hrPolicies);
            try { if (typeof updateSessionBadges === 'function') updateSessionBadges(); } catch(e){}
        }).fail(function(){ renderPolicyTable([]); });
    })();

    // ========== EDIT POLICY ==========
    $(document).on("click", ".editPolicyBtn", function () {
        let p = $(this).data("item");

        $("#policyModalTitle").text("Edit Policy");
        $("#policy_id").val(p.id);

        $("input[name='policy_date']").val(p.policy_date ? p.policy_date.substring(0,10) : '');
        $("input[name='policy_name']").val(p.policy_name);
        $("select[name='policy_category']").val(p.policy_category);
        $("input[name='policy_valid']").val(p.policy_valid ? p.policy_valid.substring(0,10) : '');
        $("select[name='view_to_employees']").val(p.view_to_employees ? 1 : 0);
        
        // Set editor content
        $('#policyDetailsEditor').html(p.policy_details || '');

        $("#existingFile").html(`
            <a href="/${p.policy_file}" target="_blank" class="text-primary">Current File</a>
        `);

        $("#policyModal").modal("show");
    });

    // ========== OPEN ADD MODAL ==========
    $("#addPolicyBtn").click(function(){
        $("#policyModalTitle").text("Add Policy");
        $("#policyForm")[0].reset();
        $("#policy_id").val('');
        $("#existingFile").html('');
        $('#policyDetailsEditor').html('');
    });

    // ========== SYNC BEFORE SAVE ==========
    $('#savePolicyBtn').on('click', function() {
        syncEditorToTextarea();
    });

    // ========== DELETE POLICY ==========
    $(document).on('click', '.deletePolicyBtn', function() {
        let id = $(this).data('id');
        if (!confirm('Delete this policy?')) return;
        $.post(POLICY_BASE_URL + '/company/policy/session/delete', { policy_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            hrPolicies = res.policies || [];
            renderPolicyTable(hrPolicies);
            try { if (typeof updateSessionBadges === 'function') updateSessionBadges(); } catch(e){}
        });
    });
</script>

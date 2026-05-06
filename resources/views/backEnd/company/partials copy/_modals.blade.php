{{-- =================== INDUSTRY ADD MODAL =================== --}}
<div class="modal fade admin-query" id="industryAddPopup" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Industry</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                    <form method="POST" action="{{ url('/industry') }}" id="industryAddForm">
                    @csrf
                    <label class="form-label">Industry Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" >
                    <button class="btn btn-light d-inline-flex align-items-center gap-2 mt-3">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>


{{-- =================== BUSINESS ACTIVITY MODAL =================== --}}
<div class="modal fade admin-query" id="activityModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Business Activity</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                    <form method="POST" action="{{ url('/business-activity') }}">
                    @csrf

                    <label class="form-label">Industry <span class="text-danger">*</span></label>
                    <select class="form-control" name="industry_id">
                        <option value="">Select Industry</option>
                        @foreach ($industries as $ind)
                            <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mt-3">Business Sector <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name">

                    <button type="submit" class="btn btn-light d-inline-flex align-items-center gap-2 mt-3">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>


{{-- =================== ENTITY TYPE MODAL =================== --}}
<div class="modal fade admin-query" id="entityTypeAddModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title">Add Business Entity Type</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                    <form method="POST" action="{{ url('business-entity-type') }}">
                    @csrf

                    <label class="form-label">Entity Type Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" >

                    <button class="btn btn-light d-inline-flex align-items-center gap-2 mt-3">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="bankAddModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title">
                    @{{ bankModalMode === 'add' ? 'Add Bank' :
                        bankModalMode === 'edit' ? 'Edit Bank' : 'View Bank' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <div class="row gy-2">

                    <div class="col-lg-4">
                        <label class="form-label">Bank Name *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.bank_name"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Branch Name</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.branch_name"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Account Number *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.account_number"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">IBAN *</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.iban_number"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">SWIFT</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.swift_code"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Finance Code</label>
                        <input type="text" class="form-control form-control-sm"
                               v-model="bankForm.finance_code"
                               :disabled="bankModalMode === 'view'">
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Currency</label>
                        <select class="form-control form-control-sm"
                                v-model="bankForm.currency"
                                :disabled="bankModalMode === 'view'">
                            <option value="">Select</option>
                            <option>AED</option>
                            <option>USD</option>
                            <option>INR</option>
                            <option>EUR</option>
                            <option>GBP</option>
                            <option>SAR</option>
                        </select>
                    </div>

                    <div class="col-lg-8">
                        <label class="form-label">Bank Letter *</label>

                        <input type="file" class="form-control form-control-sm"
                               @change="onBankFileModal"
                               v-if="bankModalMode !== 'view'">

                        <p v-if="bankModalMode==='view'">
                            @{{ bankForm.bank_letter_name || 'No file uploaded' }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>

                <button class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm"
                        v-if="bankModalMode !== 'view'"
                        @click="saveBank">
                    Save
                </button>
            </div>

        </div>
    </div>
</div>

{{-- =================== DOCUMENT MODAL =================== --}}
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
                        <div class="col-lg-2">
                            <label for="document_name" class="form-label mb-1">Document Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_name" name="document_name"  placeholder="Enter document name">
                        </div>
                        <div class="col-lg-2">
                            <label for="document_number" class="form-label mb-1">Document Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="document_number" name="document_number"  placeholder="Enter document number">
                        </div>
                        <div class="col-lg-2">
                            <label for="document_date" class="form-label mb-1">Issue Date</label>
                            <input type="date" class="form-control form-control-sm" id="document_date" name="document_date">
                        </div>
                        <div class="col-lg-2">
                            <label for="expiry_date" class="form-label mb-1">Expiry Date</label>
                            <input type="date" class="form-control form-control-sm" id="expiry_date" name="expiry_date">
                        </div>
                        <div class="col-lg-4">
                            <label for="attachment" class="form-label mb-1">Attachment</label>
                            <input type="file" class="form-control form-control-sm" id="attachment" name="attachment" 
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted d-block">PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</small>
                        </div>
                    </div>
                </form>

                <!-- Document List -->
                <div class="mt-4">
                    <h6>Current Documents</h6>
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
                                <tr>
                                    <td colspan="6" class="text-muted text-center">No documents added yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDocumentBtn" onclick="saveDocument()">Add Document</button>
            </div>
        </div>
    </div>
</div>

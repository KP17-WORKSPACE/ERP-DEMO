@php
    $tax_applicable_value = old('tax_applicable', $company->compliance->tax_applicable ?? 'both');
    $selected_country = old('country', $company->country ?? '');
@endphp

<!-- UAE Compliance Section - Show only if UAE is selected -->
<div id="uae-compliance-section" class="{{ $selected_country == '231' ? '' : 'd-none' }}">
<div class="row gy-2 mt-2">

    <!-- Trade License Number -->
    <div class="col-lg-2">
        <label class="form-label mb-1">Trade License Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm"
               name="trade_license_no"
               value="{{ old('trade_license_no', $company->compliance->trade_license_no ?? '') }}">
        <small class="text-danger error" data-error="trade_license_no"></small>
    </div>

    <!-- License Issue Date -->
    <div class="col-lg-2">
        <label class="form-label mb-1">License Issue Date <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm date-picker"
               name="license_issue_date" id="license_issue_date"
               value="{{ old('license_issue_date', isset($company->compliance->license_issue_date) ? \Carbon\Carbon::parse($company->compliance->license_issue_date)->format('Y-m-d') : '') }}">
        <small class="text-danger error" data-error="license_issue_date"></small>
    </div>

    <!-- License Expiry Date -->
    <div class="col-lg-2">
        <label class="form-label mb-1">License Expiry Date <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm date-picker"
               name="license_expiry_date" id="license_expiry_date"
               value="{{ old('license_expiry_date', isset($company->compliance->license_expiry_date) ? \Carbon\Carbon::parse($company->compliance->license_expiry_date)->format('Y-m-d') : '') }}">
        <small class="text-danger error" data-error="license_expiry_date"></small>
    </div>

    <!-- Issuing Authority -->
    <div class="col-lg-2">
        <label class="form-label mb-1">Issuing Authority <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm"
               name="issuing_authority"
               value="{{ old('issuing_authority', $company->compliance->issuing_authority ?? '') }}">
        <small class="text-danger error" data-error="issuing_authority"></small>
    </div>

    <!-- License Upload -->
    <div class="col-lg-2">
        <label class="form-label mb-1">Trade License Upload <span class="text-danger">*</span></label>
        <input type="file" class="form-control form-control-sm" name="business_license_upload">
        @if(!empty($company->compliance->business_license_upload))
            <a href="{{ asset('storage/'.$company->compliance->business_license_upload) }}" target="_blank" class="mt-1 d-block">View Existing File</a>
        @endif
        <small class="text-danger error" data-error="business_license_upload"></small>
    </div>

    <!-- TAX APPLICABLE -->
    <div class="col-lg-2">
        <label class="form-label mb-1">Tax Applicable</label>
        <select class="form-control form-control-sm" name="tax_applicable" id="tax_applicable">
            <option value="">Select</option>
            <option value="vat" {{ $tax_applicable_value == 'vat' ? 'selected' : '' }}>VAT</option>
            <option value="ct" {{ $tax_applicable_value == 'ct' ? 'selected' : '' }}>CT</option>
            <option value="both" {{ $tax_applicable_value == 'both' ? 'selected' : '' }}>Both (CT/VAT)</option>
            <option value="none" {{ $tax_applicable_value == 'none' ? 'selected' : '' }}>Not Applicable</option>
        </select>
        <small class="text-danger error" data-error="tax_applicable"></small>
    </div>

</div>

<!-- VAT SECTION -->
<div class="row gy-2 mt-2 vat-section {{ in_array($tax_applicable_value, ['vat','both']) ? '' : 'd-none' }}">
    <div class="col-lg-2">
        <label class="form-label mb-1">VAT Registration No (TRN)</label>
        <input type="text" name="vat_registration_number" class="form-control form-control-sm"
               value="{{ old('vat_registration_number', $company->compliance->vat_registration_number ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">VAT %</label>
        <input type="number" step="0.01" name="vat_percentage" class="form-control form-control-sm"
               value="{{ old('vat_percentage', $company->compliance->vat_percentage ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">VAT Registration Date</label>
        <input type="text" name="vat_date" class="form-control form-control-sm date-picker"
               value="{{ old('vat_date', isset($company->compliance->vat_date) ? \Carbon\Carbon::parse($company->compliance->vat_date)->format('Y-m-d') : '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">VAT Issuing Authority</label>
        <input type="text" name="vat_issuing_authority" class="form-control form-control-sm"
               value="{{ old('vat_issuing_authority', $company->compliance->vat_issuing_authority ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">VAT Certificate Upload</label>
        <input type="file" name="vat_certificate" class="form-control form-control-sm">
        @if(!empty($company->compliance->vat_certificate))
            <a href="{{ asset('storage/'.$company->compliance->vat_certificate) }}" target="_blank" class="mt-1 d-block">View Existing File</a>
        @endif
    </div>
</div>

<!-- CT SECTION -->
<div class="row gy-2 mt-2 ct-section {{ in_array($tax_applicable_value, ['ct','both']) ? '' : 'd-none' }}">
    <div class="col-lg-2">
        <label class="form-label mb-1">CT Registration No (CTN)</label>
        <input type="text" name="corporate_tax_number" class="form-control form-control-sm"
               value="{{ old('corporate_tax_number', $company->compliance->corporate_tax_number ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">CT %</label>
        <input type="text" name="corporate_tax_vat" class="form-control form-control-sm"
               value="{{ old('corporate_tax_vat', $company->compliance->corporate_tax_vat ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">CT Registration Date</label>
        <input type="text" name="corporate_tax_date" class="form-control form-control-sm date-picker"
               value="{{ old('corporate_tax_date', isset($company->compliance->corporate_tax_date) ? \Carbon\Carbon::parse($company->compliance->corporate_tax_date)->format('Y-m-d') : '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">CT Issuing Authority</label>
        <input type="text" name="ct_issuing_authority" class="form-control form-control-sm"
               value="{{ old('ct_issuing_authority', $company->compliance->corporate_issuing_authority ?? '') }}">
    </div>
    <div class="col-lg-2">
        <label class="form-label mb-1">CT Certificate Upload</label>
        <input type="file" name="corporate_tax_certificate" class="form-control form-control-sm">
        @if(!empty($company->compliance->corporate_tax_certificate))
            <a href="{{ asset('storage/'.$company->compliance->corporate_tax_certificate) }}" target="_blank" class="mt-1 d-block">View Existing File</a>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation: License expiry date should be after license issue date
    const licenseIssueDate = document.getElementById('license_issue_date');
    const licenseExpiryDate = document.getElementById('license_expiry_date');
    
    function validateLicenseDates() {
        if (licenseIssueDate.value && licenseExpiryDate.value) {
            const issueDate = new Date(licenseIssueDate.value);
            const expiryDate = new Date(licenseExpiryDate.value);
            
            if (expiryDate <= issueDate) {
                licenseExpiryDate.classList.add('is-invalid');
                let errorElement = licenseExpiryDate.parentNode.querySelector('.date-validation-error');
                if (!errorElement) {
                    errorElement = document.createElement('small');
                    errorElement.classList.add('text-danger', 'date-validation-error');
                    licenseExpiryDate.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = 'License expiry date must be after issue date';
                return false;
            } else {
                licenseExpiryDate.classList.remove('is-invalid');
                const errorElement = licenseExpiryDate.parentNode.querySelector('.date-validation-error');
                if (errorElement) {
                    errorElement.remove();
                }
                return true;
            }
        }
        return true;
    }
    
    if (licenseIssueDate && licenseExpiryDate) {
        licenseIssueDate.addEventListener('change', validateLicenseDates);
        licenseExpiryDate.addEventListener('change', validateLicenseDates);
        
        // Initial validation on page load
        validateLicenseDates();
    }
});
</script>
</div>

<!-- Non-UAE Countries Document Management -->
<div id="non-uae-compliance-section" class="{{ $selected_country != '231' && $selected_country != '' ? '' : 'd-none' }}">
    <div class="row gy-2 mt-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-light gap-2" onclick="openComplianceDocumentModal()">
                     <i class="ico icon-outline-add-square"></i> Add Document
                </button>
            </div>
            
            <!-- Documents List -->
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Document Number</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Issuing Authority</th>
                            <th>Attachment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="complianceDocumentsList">
                        <tr>
                            <td colspan="6" class="text-muted text-center">No compliance documents added yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- No Country Selected Message -->
<div id="no-country-compliance-section" class="{{ $selected_country == '' ? '' : 'd-none' }}">
    <div class="row gy-2 mt-2">
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please select a country in the Contact Information tab to view compliance requirements.
            </div>
        </div>
    </div>
</div>

<script>
// Country-based compliance visibility
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_company');
    const uaeComplianceSection = document.getElementById('uae-compliance-section');
    const nonUaeComplianceSection = document.getElementById('non-uae-compliance-section');
    const noCountryComplianceSection = document.getElementById('no-country-compliance-section');
    
    function toggleComplianceSection() {
        const selectedCountry = countrySelect ? countrySelect.value : '';
        
        console.log('Country selected:', selectedCountry); // Debug log
        
        if (selectedCountry === '231') {
            // UAE selected - show compliance form
            uaeComplianceSection.classList.remove('d-none');
            nonUaeComplianceSection.classList.add('d-none');
            noCountryComplianceSection.classList.add('d-none');
            console.log('Showing UAE compliance section');
        } else if (selectedCountry === '') {
            // No country selected
            uaeComplianceSection.classList.add('d-none');
            // nonUaeComplianceSection.classList.add('d-none');
            // noCountryComplianceSection.classList.remove('d-none');
            console.log('Showing no country section');
        } else {
            // Other country selected
            uaeComplianceSection.classList.add('d-none');
            nonUaeComplianceSection.classList.remove('d-none');
            noCountryComplianceSection.classList.add('d-none');
            console.log('Showing non-UAE section');
        }
    }
    
    if (countrySelect) {
        // Handle regular change event
        countrySelect.addEventListener('change', toggleComplianceSection);
        
        // Handle Select2 change event (if using Select2)
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#country_company').on('change.select2', function() {
                setTimeout(toggleComplianceSection, 100); // Small delay to ensure value is updated
            });
        }
        
        // Initial check on page load with delay
        setTimeout(toggleComplianceSection, 500);
        
        // Additional check with MutationObserver for Select2 changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-select2-id') {
                    setTimeout(toggleComplianceSection, 200);
                }
            });
        });
        
        if (countrySelect) {
            observer.observe(countrySelect, {
                attributes: true,
                attributeFilter: ['data-select2-id']
            });
        }
    } else {
        // Fallback: check every 2 seconds for the first 10 seconds
        let attempts = 0;
        const fallbackCheck = setInterval(function() {
            attempts++;
            const fallbackCountrySelect = document.getElementById('country_company');
            if (fallbackCountrySelect) {
                toggleComplianceSection();
                clearInterval(fallbackCheck);
            } else if (attempts >= 5) {
                clearInterval(fallbackCheck);
            }
        }, 2000);
    }
    
    // Initialize compliance documents session
    initializeComplianceDocuments();
});

// Compliance Documents Management for Non-UAE Countries
let complianceDocuments = [];
let editingDocumentIndex = -1;

function initializeComplianceDocuments() {
    // Load from server-side session
    $.post('{{ url("company/compliance/session/get") }}', { _token: '{{ csrf_token() }}' }, function(resp) {
        if (resp && resp.documents) {
            complianceDocuments = resp.documents;
            updateComplianceDocumentsList();
        }
    }).fail(function() {
        console.warn('Failed to load compliance documents from session');
    });
}

function openComplianceDocumentModal(editIndex = -1) {
    editingDocumentIndex = editIndex;
    const modal = document.getElementById('complianceDocumentModal');
    const form = document.getElementById('complianceDocumentForm');
    const modalTitle = document.getElementById('complianceDocumentModalLabel');
    const saveBtn = document.getElementById('saveComplianceDocumentBtn');
    
    // Reset form
    form.reset();
    
    if (editIndex >= 0) {
        // Edit mode
        const doc = complianceDocuments[editIndex];
        document.getElementById('compliance_document_number').value = doc.document_number;
        document.getElementById('compliance_issue_date').value = doc.issue_date;
        document.getElementById('compliance_expiry_date').value = doc.expiry_date;
        document.getElementById('compliance_issuing_authority').value = doc.issuing_authority;
        modalTitle.textContent = 'Edit Compliance Document';
        const spanElement = saveBtn.querySelector('span:last-child');
        if (spanElement) spanElement.textContent = 'Update';
    } else {
        // Add mode
        modalTitle.textContent = 'Add Compliance Document';
        const spanElement = saveBtn.querySelector('span:last-child');
        if (spanElement) spanElement.textContent = 'Save';
    }
    
    // Show modal
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else if (typeof $ !== 'undefined') {
        $('#complianceDocumentModal').modal('show');
    }
}

function saveComplianceDocument() {
    const form = document.getElementById('complianceDocumentForm');
    const formData = new FormData(form);
    
    // Add CSRF token
    formData.append('_token', '{{ csrf_token() }}');
    
    // Validate required fields
    const documentNumber = formData.get('compliance_document_number')?.trim();
    const issuingAuthority = formData.get('compliance_issuing_authority')?.trim();
    
    if (!documentNumber || !issuingAuthority) {
        alert('Document Number and Issuing Authority are required!');
        return;
    }
    
    // Show loading state
    const saveBtn = document.getElementById('saveComplianceDocumentBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    saveBtn.disabled = true;
    
    // Save to server session
    $.ajax({
        url: '{{ url("company/compliance/session/store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            if (resp.ok) {
                // Update local array
                if (editingDocumentIndex >= 0) {
                    complianceDocuments[editingDocumentIndex] = resp.document;
                } else {
                    complianceDocuments.push(resp.document);
                }
                
                // Update display
                updateComplianceDocumentsList();
                
                // Close modal
                closeComplianceDocumentModal();
                
                // Reset editing index
                editingDocumentIndex = -1;
                
                // Show success message
                toastr.success('Document saved successfully');
            } else {
                alert('Failed to save document. Please try again.');
            }
        },
        error: function(xhr) {
            console.error('Error saving compliance document:', xhr.responseText);
            if (xhr.status === 422) {
                // Handle validation errors
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = 'Validation errors:\n';
                    for (const [field, messages] of Object.entries(errors)) {
                        errorMsg += `${field}: ${messages.join(', ')}\n`;
                    }
                    alert(errorMsg);
                } else {
                    alert('Validation failed. Please check your inputs.');
                }
            } else {
                alert('Server error. Please try again.');
            }
        },
        complete: function() {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });
}

function updateComplianceDocumentsList() {
    const tbody = document.getElementById('complianceDocumentsList');
    
    if (complianceDocuments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted text-center">No compliance documents added yet.</td></tr>';
        return;
    }
    
    tbody.innerHTML = complianceDocuments.map((doc, index) => {
        const attachmentDisplay = doc.attachment_name ? 
            `<a href="#" class="text-primary">${doc.attachment_name}</a>` : 
            '<span class="text-muted">No file</span>';
            
        return `
        <tr>
            <td>${doc.document_number}</td>
            <td>${doc.issue_date || '-'}</td>
            <td>${doc.expiry_date || '-'}</td>
            <td>${doc.issuing_authority}</td>
            <td>${attachmentDisplay}</td>
            <td>
                <button type="button" class="btn btn-sm btn-light d-inline-flex gap-2" onclick="openComplianceDocumentModal(${index})">
                    <i class="ico icon-outline-pen-2"></i>
                </button>
                <button type="button" class="btn btn-sm btn-light d-inline-flex gap-2" onclick="deleteComplianceDocument(${index})">
                    <i class="ico icon-outline-trash-bin-minimalistic"></i>
                </button>
            </td>
        </tr>
        `;
    }).join('');
}

function deleteComplianceDocument(index) {
    if (confirm('Are you sure you want to delete this compliance document?')) {
        const document = complianceDocuments[index];
        
        $.post('{{ url("company/compliance/session/delete") }}', {
            _token: '{{ csrf_token() }}',
            document_id: document.id
        }, function(resp) {
            if (resp.ok) {
                complianceDocuments.splice(index, 1);
                updateComplianceDocumentsList();
                toastr.success('Document deleted successfully');
            } else {
                alert('Failed to delete document. Please try again.');
            }
        }).fail(function() {
            alert('Server error. Please try again.');
        });
    }
}

function closeComplianceDocumentModal() {
    const modal = document.getElementById('complianceDocumentModal');
    
    if (typeof bootstrap !== 'undefined') {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    } else if (typeof $ !== 'undefined') {
        $('#complianceDocumentModal').modal('hide');
    }
}
</script>

<!-- Compliance Document Modal for Non-UAE Countries -->
<div class="modal fade" id="complianceDocumentModal" tabindex="-1" aria-labelledby="complianceDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complianceDocumentModalLabel">Add Compliance Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="complianceDocumentForm">
                    <div class="row gy-3">
                        <div class="col-md-3">
                            <label for="compliance_document_number" class="form-label">Document Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="compliance_document_number" 
                                name="compliance_document_number" >
                        </div>
                        <div class="col-md-3">
                            <label for="compliance_issue_date" class="form-label">Issue Date</label>
                            <input type="text" class="form-control form-control-sm date-picker" id="compliance_issue_date" 
                                name="compliance_issue_date">
                        </div>
                        <div class="col-md-2">
                            <label for="compliance_expiry_date" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control form-control-sm date-picker" id="compliance_expiry_date" 
                                name="compliance_expiry_date">
                        </div>
                        <div class="col-md-2">
                            <label for="compliance_issuing_authority" class="form-label">Issuing Authority <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="compliance_issuing_authority" 
                                name="compliance_issuing_authority" >
                        </div>
                        <div class="col-md-2">
                            <label for="compliance_attachment" class="form-label">Attachment</label>
                            <input type="file" class="form-control form-control-sm" id="compliance_attachment" 
                                name="compliance_attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="saveComplianceDocumentBtn" onclick="saveComplianceDocument()">
                    <span class="spinner-border spinner-border-sm d-none" id="complianceLoader"></span>
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    <span>Save</span>
                </button>
            </div>
        </div>
    </div>
</div>

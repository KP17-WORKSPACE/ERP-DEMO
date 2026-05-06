@php
    // safe doc object (could be null)
    $doc = null;
    if(isset($company) && $company && $company->documents && $company->documents->count()){
        $doc = $company->documents->first();
    }
    
    // Check if UAE is selected
    $selected_country = old('country', $company->country ?? '');
    $is_uae = $selected_country == '231';
@endphp

<!-- UAE Documents Section -->
<div id="uae-documents-section" class="{{ $is_uae ? '' : 'd-none' }}">
    <div class="d-flex justify-content-end gap-2 mb-2">
        <button type="button" class="btn btn-success btn-sm" id="addDocumentRowTop">
            <i class="ico icon-outline-add-square me-1"></i> Add Rows
        </button>
        <button type="button" id="saveDocsBtn" class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm">
            <i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents
        </button>
    </div>

    <div class="table-responsive">
        <table id="documentationTable" class="table table-hover" style="table-layout: fixed; width: 100%;">
            <thead class="table-light">
                <tr class="text-center">
                    <th style="width: 18%;">Document Name</th>
                    <th style="width: 14%;">Document Number</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 12%;">Expire Date</th>
                    <th style="width: 20%;">Attachment</th>
                </tr>
            </thead>
            <tbody>
            {{-- Establishment Card --}}
            <tr>
                <td class="fw-bold">Establishment Card</td>
                
                <td>
                    <input type="text" name="establishment_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('establishment_number', optional($doc)->establishment_number) }}">
                </td>
                <td>
                    <input name="establishment_date" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('establishment_date', optional($doc)->establishment_date) }}">
                </td>
                <td>
                    <input name="establishment_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('establishment_expiry', optional($doc)->establishment_expiry) }}">
                </td>
                <td>
                    <input type="file" name="establishment_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->establishment_file)
                        <a href="{{ asset($doc->establishment_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Immigration Card --}}
            <tr>
                <td class="fw-bold">Immigration Card</td>
                <td>
                    <input type="text" name="immigration_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('immigration_number', optional($doc)->immigration_number) }}">
                </td>
                <td>
                    <input name="immigration_date" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('immigration_date', optional($doc)->immigration_date) }}">
                </td>
                <td>
                    <input name="immigration_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('immigration_expiry', optional($doc)->immigration_expiry) }}">
                </td>
                <td>
                    <input type="file" name="immigration_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->immigration_file)
                        <a href="{{ asset($doc->immigration_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Labour Establishment Card --}}
            <tr>
                <td class="fw-bold">Labour Establishment Card</td>
                <td>
                    <input type="text" name="labour_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('labour_number', optional($doc)->labour_number) }}">
                </td>
                <td>
                    <input name="labour_date" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('labour_date', optional($doc)->labour_date) }}">
                </td>
                <td>
                    <input name="labour_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('labour_expiry', optional($doc)->labour_expiry) }}">
                </td>
                <td>
                    <input type="file" name="labour_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->labour_file)
                        <a href="{{ asset($doc->labour_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Chamber of Commerce --}}
            <tr>
                <td class="fw-bold">Chamber of Commerce</td>
                <td>
                    <input type="text" name="chamber_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('chamber_number', optional($doc)->chamber_number) }}">
                </td>
                <td>
                    <input name="chamber_date" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('chamber_date', optional($doc)->chamber_date) }}">
                </td>
                <td>
                    <input name="chamber_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('chamber_expiry', optional($doc)->chamber_expiry) }}">
                </td>
                <td>
                    <input type="file" name="chamber_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->chamber_file)
                        <a href="{{ asset($doc->chamber_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Insurance Certificate --}}
            <tr>
                <td class="fw-bold">Medical Insurance </td>
                <td>
                    <input type="text" name="insurance_certificate_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('insurance_certificate_number', optional($doc)->insurance_certificate_number) }}">
                </td>
                <td>
                    <input name="insurance_certificate_date" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('insurance_certificate_date', optional($doc)->insurance_certificate_date) }}">
                </td>
                <td>
                    <input name="insurance_certificate_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('insurance_certificate_expiry', optional($doc)->insurance_certificate_expiry) }}">
                </td>
                <td>
                    <input type="file" name="insurance_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->insurance_file)
                        <a href="{{ asset($doc->insurance_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- MOA / AOA --}}
            <tr>
                <td class="fw-bold">MOA / AOA</td>
                <td>
                    <input type="text" name="moa_aoa_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('moa_aoa_number', optional($doc)->moa_aoa_number) }}">
                </td>
                <td>
                    {{-- Not used, but keep for consistency --}}
                </td>
                <td>
                    <input name="moa_aoa_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('moa_aoa_expiry', optional($doc)->moa_aoa_expiry) }}">
                </td>
                <td>
                    <input type="file" name="moa_aoa_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->moa_aoa_file)
                        <a href="{{ asset($doc->moa_aoa_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Board Resolution --}}
            <tr>
                <td class="fw-bold">Board Resolution</td>
                <td>
                    <input type="text" name="board_resolution_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('board_resolution_number', optional($doc)->board_resolution_number) }}">
                </td>
                <td>
                    {{-- Not used, but keep for consistency --}}
                </td>
                <td>
                    <input name="board_resolution_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('board_resolution_expiry', optional($doc)->board_resolution_expiry) }}">
                </td>
                <td>
                    <input type="file" name="board_resolution_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->board_resolution_file)
                        <a href="{{ asset($doc->board_resolution_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

            {{-- Power of Attorney --}}
            <tr>
                <td class="fw-bold">Power of Attorney</td>
                <td>
                    <input type="text" name="poa_number" class="form-control form-control-sm doc-input" 
                           placeholder="Number"
                           value="{{ old('poa_number', optional($doc)->poa_number) }}">
                </td>
                <td>
                    {{-- Not used, but keep for consistency --}}
                </td>
                <td>
                    <input name="poa_expiry" class="form-control form-control-sm date-picker doc-input"
                           value="{{ old('poa_expiry', optional($doc)->poa_expiry) }}">
                </td>
                <td>
                    <input type="file" name="poa_file" class="form-control form-control-sm doc-input">
                    @if($doc && $doc->poa_file)
                        <a href="{{ asset($doc->poa_file) }}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>
                    @endif
                </td>
            </tr>

                {{-- Dynamic Document Rows will be inserted here --}}

            </tbody>
        </table>
    </div>
</div>

<!-- Non-UAE Documents Section -->
<div id="non-uae-documents-section" class="{{ !$is_uae && $selected_country != '' ? '' : 'd-none' }}">
    <div class="d-flex justify-content-end gap-2 mb-2">
        <button type="button" class="btn btn-success btn-sm" id="addDocumentRowTopNonUae">
            <i class="ico icon-outline-add-square me-1"></i> Add Rows
        </button>
        <button type="button" id="saveDocsBtn" class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm">
            <i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents
        </button>
    </div>

    <div class="table-responsive">
        <table id="nonUaeDocumentationTable" class="table table-hover" style="table-layout: fixed; width: 100%;">
            <thead class="table-light">
                <tr class="text-center">
                    <th style="width: 18%;">Document Name</th>
                    <th style="width: 14%;">Document Number</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 12%;">Expire Date</th>
                    <th style="width: 20%;">Attachment</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dynamic Document Rows for Non-UAE countries --}}
            </tbody>
        </table>
    </div>
</div>

<!-- No Country Selected Section -->
<div id="no-country-documents-section" class="{{ $selected_country == '' ? '' : 'd-none' }}">
    <div class="row gy-2 mt-2">
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please select a country in the Contact Information tab to view documentation requirements.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Non-UAE Document Session Management
    const nonUaeDocSessionKey = 'company_document_items';
    let documentRowIndex = 0;

    /**
     * Add a new non-UAE document row
     */
    function addDocumentRow(isNonUae = false) {
        const tableId = isNonUae ? '#nonUaeDocumentationTable tbody' : '#documentationTable tbody';
        const tbody = document.querySelector(tableId);
        
        if (!tbody) return;

        const rowId = 'doc_row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const newRowHTML = `
            <tr class="dynamic-document-row" data-row-id="${rowId}">
                <td>
                    <input type="text" class="form-control form-control-sm doc-name" 
                           placeholder="Document Name">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm doc-number" 
                           placeholder="Number">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm date-picker doc-date"
                           placeholder="Date">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm date-picker doc-expiry"
                           placeholder="Expiry Date">
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <input type="file" class="form-control form-control-sm doc-attachment">
                        <button type="button" class="btn btn-danger btn-sm remove-document-row" title="Remove Row">
                            <i class="ico icon-outline-minus-square"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        tbody.insertAdjacentHTML('beforeend', newRowHTML);
        
        // Initialize date picker on new inputs with a small delay to ensure DOM is ready
        setTimeout(function() {
            const newRow = tbody.querySelector(`[data-row-id="${rowId}"]`);
            if (newRow && typeof $.fn.datepicker !== 'undefined') {
                newRow.querySelectorAll('.date-picker').forEach(function(input) {
                    if (!input.classList.contains('hasDatepicker')) {
                        $(input).datepicker({
                            dateFormat: 'yy-mm-dd',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '1900:2100'
                        });
                    }
                });
            }
        }, 50);
    }

    /**
     * Initialize date pickers for new elements
     */
    function initializeDatePickers() {
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.date-picker').not('.hasDatepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '1900:2100',
                onClose: function(dateText, inst) {
                    // Ensure format is correct
                    if (dateText && !/^\d{4}-\d{2}-\d{2}$/.test(dateText)) {
                        $(this).val('');
                    }
                }
            });
        }
    }

    /**
     * Toggle documentation section based on selected country
     */
    function toggleDocumentationSection() {
        const countrySelect = document.getElementById('country_company');
        const selectedCountry = countrySelect ? countrySelect.value : '';
        
        const uaeSection = document.getElementById('uae-documents-section');
        const nonUaeSection = document.getElementById('non-uae-documents-section');
        const noCountrySection = document.getElementById('no-country-documents-section');
        
        if (selectedCountry === '231') {
            // UAE selected - show UAE documents
            uaeSection?.classList.remove('d-none');
            nonUaeSection?.classList.add('d-none');
            noCountrySection?.classList.add('d-none');
        } else if (selectedCountry === '') {
            // No country selected
            uaeSection?.classList.add('d-none');
            nonUaeSection?.classList.add('d-none');
            noCountrySection?.classList.remove('d-none');
        } else {
            // Other country selected - show flexible document input
            uaeSection?.classList.add('d-none');
            nonUaeSection?.classList.remove('d-none');
            noCountrySection?.classList.add('d-none');
        }
    }

    /**
     * Load non-UAE documents from session (for editing)
     */
    function loadNonUaeDocumentsFromSession() {
        const cid = $('#company_id').val();
        if (!cid) return;

        $.ajax({
            url: '{{ url("/company/document-items/session/get") }}',
            type: 'POST',
            dataType: 'json',
            data: { 
                company_id: cid,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.ok && res.items && res.items.length > 0) {
                    const tbody = document.querySelector('#nonUaeDocumentationTable tbody');
                    if (tbody) {
                        tbody.innerHTML = ''; // Clear existing rows
                        res.items.forEach(item => {
                            renderDocumentRow(tbody, item);
                        });
                        initializeDatePickers();
                    }
                }
            },
            error: function(xhr) {
                console.error('Failed to load non-UAE documents', xhr);
            }
        });
    }

    /**
     * Render a single document row from data
     */
    function renderDocumentRow(tbody, itemData) {
        const rowId = itemData.id || 'doc_row_' + Date.now();
        const attachmentUrl = itemData.attachment_file ? `<a href="${itemData.attachment_file}" target="_blank" class="btn btn-light btn-sm mt-1">View</a>` : '';
        
        const rowHTML = `
            <tr class="dynamic-document-row" data-row-id="${rowId}">
                <td>
                    <input type="text" class="form-control form-control-sm doc-name" 
                           placeholder="Document Name" value="${itemData.document_name || ''}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm doc-number" 
                           placeholder="Number" value="${itemData.document_number || ''}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm date-picker doc-date"
                           placeholder="Date" value="${itemData.document_date || ''}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm date-picker doc-expiry"
                           placeholder="Expiry Date" value="${itemData.expiry_date || ''}">
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <input type="file" class="form-control form-control-sm doc-attachment">
                        ${attachmentUrl}
                        <button type="button" class="btn btn-danger btn-sm remove-document-row" title="Remove Row">
                            <i class="ico icon-outline-minus-square"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        tbody.insertAdjacentHTML('beforeend', rowHTML);
        initializeDatePickers(); // Initialize date pickers for newly added row
    }

    /**
     * Collect non-UAE document rows for saving
     */
    function collectNonUaeDocuments() {
        const rows = document.querySelectorAll('#nonUaeDocumentationTable tbody tr');
        const documents = [];

        rows.forEach(row => {
            const name = row.querySelector('.doc-name')?.value?.trim();
            if (!name) return; // document_name is required
            
            const docDate = row.querySelector('.doc-date')?.value?.trim() || '';
            const expiryDate = row.querySelector('.doc-expiry')?.value?.trim() || '';
            
            // Validate dates - must be in YYYY-MM-DD format or empty
            const isValidDate = (dateStr) => {
                if (!dateStr) return true; // Empty dates are OK
                return /^\d{4}-\d{2}-\d{2}$/.test(dateStr); // Matches YYYY-MM-DD
            };
            
            if (!isValidDate(docDate)) {
                toastr && toastr.error ? toastr.error('Invalid date format in document date. Use YYYY-MM-DD format.') : alert('Invalid date format in document date');
                return;
            }
            if (!isValidDate(expiryDate)) {
                toastr && toastr.error ? toastr.error('Invalid date format in expiry date. Use YYYY-MM-DD format.') : alert('Invalid date format in expiry date');
                return;
            }
            
            documents.push({
                document_name: name,
                document_number: row.querySelector('.doc-number')?.value?.trim() || '',
                document_date: docDate,
                expiry_date: expiryDate,
                attachment: row.querySelector('.doc-attachment')
            });
        });

        return documents;
    }

    /**
     * Collect UAE document fields (for direct save)
     */
    function collectUaeDocuments() {
        const fd = new FormData();
        const cid = $('#company_id').val() || $('[name="company_id"]').val();
        
        if (cid) fd.set('company_id', cid);
        fd.set('_token', '{{ csrf_token() }}');

        // Collect all UAE doc inputs
        const uaeInputs = document.querySelectorAll('#uae-documents-section .doc-input');
        uaeInputs.forEach(input => {
            const name = input.getAttribute('name');
            if (!name) return;
            
            if (input.type === 'file') {
                if (input.files && input.files.length) {
                    fd.set(name, input.files[0]);
                }
            } else {
                fd.set(name, input.value);
            }
        });

        return fd;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Add document row buttons
        const addBtnUae = document.getElementById('addDocumentRowTop');
        const addBtnNonUae = document.getElementById('addDocumentRowTopNonUae');
        
        if (addBtnUae) {
            addBtnUae.addEventListener('click', function(e) {
                e.preventDefault();
                addDocumentRow(false);
            });
        }
        
        if (addBtnNonUae) {
            addBtnNonUae.addEventListener('click', function(e) {
                e.preventDefault();
                addDocumentRow(true);
            });
        }

        // Country change detection
        const countrySelect = document.getElementById('country_company');
        if (countrySelect) {
            countrySelect.addEventListener('change', toggleDocumentationSection);
            
            // Handle Select2 change event
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#country_company').on('change.select2', function() {
                    setTimeout(toggleDocumentationSection, 100);
                });
            }
            
            // Initial check on page load
            setTimeout(function() {
                toggleDocumentationSection();
                // Load non-UAE documents if company is in edit mode with non-UAE country
                const cid = $('#company_id').val();
                const selectedCountry = document.getElementById('country_company')?.value;
                if (cid && selectedCountry && selectedCountry !== '231') {
                    loadNonUaeDocumentsFromSession();
                }
            }, 500);
        }

        // Remove document row (event delegation)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-document-row')) {
                e.preventDefault();
                if (confirm('Are you sure you want to remove this document row?')) {
                    const row = e.target.closest('.dynamic-document-row');
                    const rowId = row.getAttribute('data-row-id');
                    
                    // If row has an id from DB, delete from session
                    if (rowId && rowId.match(/^\d+$/)) {
                        deleteNonUaeDocumentFromSession(rowId);
                    } else {
                        // Local row, just remove from DOM
                        row.remove();
                    }
                }
            }
        });
    });

    /**
     * Delete non-UAE document from session
     */
    function deleteNonUaeDocumentFromSession(rowId) {
        const cid = $('#company_id').val();
        if (!cid) return;

        $.ajax({
            url: '{{ url("/company/document-items/session/delete") }}',
            type: 'POST',
            dataType: 'json',
            data: { 
                company_id: cid,
                row_id: rowId,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.ok) {
                    $('tr[data-row-id="' + rowId + '"]').remove();
                    toastr && toastr.success ? toastr.success('Row removed') : alert('Row removed');
                }
            },
            error: function(xhr) {
                console.error('Failed to delete document', xhr);
                toastr && toastr.error ? toastr.error('Could not remove row') : alert('Could not remove row');
            }
        });
    }

    /**
     * Save all documents (UAE + Non-UAE)
     */
    function saveAllDocuments() {
        const cid = $('#company_id').val();
        if (!cid) {
            toastr && toastr.warning ? toastr.warning('Save basic company info first') : alert('Save basic company info first');
            return;
        }

        const countrySelect = document.getElementById('country_company');
        const selectedCountry = countrySelect ? countrySelect.value : '';
        const $btn = $('#saveDocsBtn');

        if (!selectedCountry) {
            toastr && toastr.warning ? toastr.warning('Select a country first') : alert('Select a country first');
            return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        if (selectedCountry === '231') {
            // UAE: Save directly to DB
            saveUaeDocuments($btn);
        } else {
            // Non-UAE: Save to session, then to DB
            saveNonUaeDocuments($btn);
        }
    }

    /**
     * Save UAE documents directly to DB
     */
    function saveUaeDocuments($btn) {
        const fd = collectUaeDocuments();

        $.ajax({
            url: '{{ route("company.docs.store") }}',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                if (res && (res.ok || res.success)) {
                    toastr && toastr.success ? toastr.success('Documents saved') : alert('Documents saved');
                } else {
                    toastr && toastr.error ? toastr.error(res.message || 'Could not save documents') : alert('Could not save documents');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                console.error('Docs save failed', xhr);
                handleDocumentSaveErrors(xhr);
            }
        });
    }

    /**
     * Save non-UAE documents to session then to DB
     */
    function saveNonUaeDocuments($btn) {
        const cid = $('#company_id').val();
        const documents = collectNonUaeDocuments();

        // First, store all rows in session
        if (documents.length === 0) {
            // No documents to save
            $.ajax({
                url: '{{ url("/company/document-items/session/clear") }}',
                type: 'POST',
                data: { 
                    company_id: cid,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                    toastr && toastr.success ? toastr.success('Documents cleared') : alert('Documents cleared');
                }
            });
            return;
        }

        // Save all documents to session, then persist to DB
        let uploadedCount = 0;
        const totalDocs = documents.length;

        documents.forEach((doc, index) => {
            const formData = new FormData();
            formData.set('company_id', cid);
            formData.set('document_name', doc.document_name);
            formData.set('document_number', doc.document_number);
            formData.set('document_date', doc.document_date);
            formData.set('expiry_date', doc.expiry_date);
            formData.set('_token', '{{ csrf_token() }}');
            
            if (doc.attachment && doc.attachment.files && doc.attachment.files.length) {
                formData.set('attachment', doc.attachment.files[0]);
            }

            $.ajax({
                url: '{{ url("/company/document-items/session/store") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    uploadedCount++;
                    if (uploadedCount === totalDocs) {
                        // All uploaded, now save to DB
                        persistNonUaeDocumentsToDb($btn);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                    console.error('Failed to upload document', xhr);
                    toastr && toastr.error ? toastr.error('Error uploading document ' + (index + 1)) : alert('Error uploading document ' + (index + 1));
                }
            });
        });
    }

    /**
     * Persist session documents to DB
     */
    function persistNonUaeDocumentsToDb($btn) {
        const cid = $('#company_id').val();

        $.ajax({
            url: '{{ url("/company/document-items/persist") }}',
            type: 'POST',
            dataType: 'json',
            data: { 
                company_id: cid,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                if (res.ok) {
                    toastr && toastr.success ? toastr.success('Documents saved successfully') : alert('Documents saved successfully');
                } else {
                    toastr && toastr.error ? toastr.error(res.message || 'Could not save documents') : alert('Could not save documents');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="ico icon-outline-bookmark-opened text-success"></i> Save Documents');
                console.error('Failed to persist documents', xhr);
                toastr && toastr.error ? toastr.error('Server error while saving documents') : alert('Server error while saving documents');
            }
        });
    }

    /**
     * Handle document save validation errors
     */
    function handleDocumentSaveErrors(xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            let errors = xhr.responseJSON.errors;
            $.each(errors, function(key, msg) {
                let input = $('[name="' + key + '"]');
                input.addClass('is-invalid');
                input.after(`<small class="text-danger doc-error">${msg[0]}</small>`);
            });
        }
        toastr && toastr.error ? toastr.error('Server error while saving documents') : alert('Server error while saving documents');
    }

    // Bind save button click
    $(document).on('click', '#saveDocsBtn', function(e) {
        e.preventDefault();
        saveAllDocuments();
    });
</script>
@endpush

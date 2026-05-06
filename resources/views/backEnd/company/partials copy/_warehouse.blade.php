{{-- WAREHOUSE TAB --}}
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button type="button" class="btn btn-success btn-sm"
                id="addWarehouseBtn" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                <i class="ico icon-outline-add-square"></i> Add Warehouse
            </button>
        </div>

        {{-- WAREHOUSE LIST --}}
        <div id="warehouseList" class="table-responsive">
            <table class="table table-hover data-table" style="table-layout: fixed;width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Warehouse Code</th>
                        <th>Warehouse Name</th>
                        <th>Address</th>
                        <th>Contact Person</th>
                        <th>Fire Safety Status</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody id="warehouseTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">No warehouses added yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- WAREHOUSE MODAL --}}
<div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="warehouseForm" enctype="multipart/form-data" novalidate>
            @csrf
            
            <input type="hidden" name="warehouse_id" id="warehouse_id">
            <input type="hidden" name="company_id" id="warehouse_company_id">
            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body row gy-2">
                    
                    {{-- BASIC WAREHOUSE INFO --}}
                    <div class="col-lg-4">
                        <label>Warehouse Code</label>
                        <input type="text" name="warehouse_code" id="warehouse_code"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-8">
                        <label>Warehouse Name</label>
                        <input type="text" name="warehouse_name" id="warehouse_name"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                
                    <div class="col-lg-2">
                        <label>Country</label>
                        <select name="warehouse_country" id="warehouse_country"
                                class="form-select form-select-sm warehouse-input js-example-basic-single">
                            <option value="">Select Country</option>
                            @foreach ($country ?? [] as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-2">
                        <label>State</label>
                        <select name="warehouse_state" id="warehouse_state"
                                class="form-select form-select-sm warehouse-input js-example-basic-single">
                            <option value="">Select State</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-2">
                        <label>City</label>
                        <input type="text" name="warehouse_city" id="warehouse_city"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Area</label>
                        <input type="text" name="warehouse_area" id="warehouse_area"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Building Name</label>
                        <input type="text" name="warehouse_building_name" id="warehouse_building_name"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Flat / Office No</label>
                        <input type="text" name="warehouse_flat_office_no" id="warehouse_flat_office_no"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    {{-- CONTACT PERSON DETAILS --}}
                    <div class="col-lg-2">
                        <label>Salutation</label>
                        <select name="contact_salutation" id="contact_salutation" class="form-select form-select-sm warehouse-input">
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
                        <input type="text" name="contact_first_name" id="contact_first_name"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Last Name</label>
                        <input type="text" name="contact_last_name" id="contact_last_name"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Mobile</label>
                        <input type="tel" name="contact_mobile" id="contact_mobile" placeholder="+"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Email</label>
                        <input type="email" name="contact_email" id="contact_email"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Designation</label>
                        <input type="text" name="contact_designation" id="contact_designation"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    {{-- SAFETY & COMPLIANCE CONFIRMATION --}}
                    <div class="col-lg-2">
                        <label style="font-size:12px">Fire/Safety Status</label>
                        <select name="fire_safety_compliance_status" id="fire_safety_compliance_status"
                                class="form-select form-select-sm warehouse-input">
                            <option value="">Select Status</option>
                            <option value="compliant">Compliant</option>
                            <option value="non_compliant">Non-Compliant</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-2">
                        <label style="font-size:12px">Fire NOC / Cert No</label>
                        <input type="text" name="fire_noc_certificate_number" id="fire_noc_certificate_number"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Safety Equipment</label>
                        <select name="safety_equipment_available" id="safety_equipment_available"
                                class="form-select form-select-sm warehouse-input">
                            <option value="">Select Status</option>
                            <option value="yes">Yes Available</option>
                            <option value="no">Not Available</option>
                            <option value="partial">Partial Available</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Fire NOC Expiry</label>
                        <input type="date" name="fire_noc_expiry_date" id="fire_noc_expiry_date"
                               class="form-control form-control-sm warehouse-input">
                    </div>
                    
                    <div class="col-lg-2">
                        <label>Last Safety Insp</label>
                        <input type="date" name="last_safety_inspection_date" id="last_safety_inspection_date"
                               class="form-control form-control-sm warehouse-input">
                    </div>

                    <div class="col-lg-2">
                        <label>Documents</label>
                        <input type="file" name="contact_documents[]" id="contact_documents"
                               class="form-control form-control-sm warehouse-input" multiple>
                        {{-- <small class="form-text text-muted">Upload multiple documents if needed</small> --}}
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2 btn-sm" id="saveWarehouseBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="warehouseLoader"></span>
                        Save Warehouse
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.table-responsive {
    min-height: 200px;
}

#warehouseList .table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-top: 1px solid #dee2e6;
}

.warehouse-actions {
    white-space: nowrap;
}

.warehouse-actions .btn {
    padding: 0.25rem 0.5rem;
    margin: 0 0.125rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize warehouse functionality
    initializeWarehouse();
});

function initializeWarehouse() {
    // Load existing warehouses on page load
    loadWarehouses();
    
    // Handle warehouse form submission
    $('#saveWarehouseBtn').on('click', function() {
        saveWarehouse();
    });

    // Handle warehouse modal reset
    $('#warehouseModal').on('hidden.bs.modal', function() {
        resetWarehouseForm();
    });

    // Reset form when Add Warehouse button is clicked  
    $('#addWarehouseBtn').on('click', function() {
        resetWarehouseForm();
    });

    // Handle country change for warehouse
    $(document).on('change', '#warehouse_country', function() {
        let countryId = $(this).val();
        $('#warehouse_state').html('<option value="">Loading...</option>');
        
        if (countryId) {
            $.get('{{ url("/get_state_company") }}?country_id=' + countryId, function(res) {
                $('#warehouse_state').empty().append('<option value="">Select State</option>');
                let states = Array.isArray(res[0]) ? res[0] : res;
                states.forEach(s => {
                    $('#warehouse_state').append(`<option value="${s.id}">${s.name}</option>`);
                });
            }).fail(function() {
                $('#warehouse_state').html('<option value="">Error loading states</option>');
            });
        } else {
            $('#warehouse_state').html('<option value="">Select State</option>');
        }
    });
}

function saveWarehouse() {
    $('#warehouseLoader').removeClass('d-none');
    $('#saveWarehouseBtn').prop('disabled', true);

    const formData = new FormData($('#warehouseForm')[0]);
    const warehouseData = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        warehouseData[key] = value;
    }

    // Get existing warehouses from session
    let warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
    
    // Generate unique ID for new warehouse
    const warehouseId = $('#warehouse_id').val() || 'temp_' + Date.now();
    warehouseData.id = warehouseId;

    // Check if editing existing warehouse
    const existingIndex = warehouses.findIndex(w => w.id === warehouseId);
    
    if (existingIndex >= 0) {
        warehouses[existingIndex] = warehouseData;
    } else {
        warehouses.push(warehouseData);
    }

    // Save to session
    sessionStorage.setItem('company_warehouses', JSON.stringify(warehouses));

    // Update UI
    loadWarehouses();
    
    // Hide modal and reset form
    $('#warehouseModal').modal('hide');
    resetWarehouseForm();

    $('#warehouseLoader').addClass('d-none');
    $('#saveWarehouseBtn').prop('disabled', false);

    // Show success message
    showNotification('Warehouse saved successfully!', 'success');
    
    // Update badge count
    updateWarehouseBadge();
}

function loadWarehouses() {
    const warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
    const tbody = $('#warehouseTableBody');
    
    tbody.empty();
    
    if (warehouses.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">No warehouses added yet.</td></tr>');
    } else {
        warehouses.forEach(warehouse => {
            const row = createWarehouseRow(warehouse);
            tbody.append(row);
        });
    }
    
    updateWarehouseBadge();
}

function createWarehouseRow(warehouse) {
    // Get country and state names from dropdowns for display
    const countryName = warehouse.warehouse_country ? 
        $('#warehouse_country option[value="' + warehouse.warehouse_country + '"]').text() : '';
    const stateName = warehouse.warehouse_state ? 
        $('#warehouse_state option[value="' + warehouse.warehouse_state + '"]').text() : '';
    
    const address = [warehouse.warehouse_address, warehouse.warehouse_city, stateName, countryName]
        .filter(item => item && item.trim())
        .join(', ');
    const salutation = warehouse.contact_salutation ? warehouse.contact_salutation + ' ' : '';
    const contactPerson = `${salutation}${warehouse.contact_first_name || ''} ${warehouse.contact_last_name || ''}`.trim();
    const fireStatus = warehouse.fire_safety_compliance_status || 'Not Set';
    
    return `
        <tr data-warehouse-id="${warehouse.id}">
            <td>${warehouse.warehouse_code || ''}</td>
            <td>${warehouse.warehouse_name || ''}</td>
            <td>${address}</td>
            <td>${contactPerson}<br><small class="text-muted">${warehouse.contact_mobile || ''}</small></td>
            <td>
                <span class="badge bg-${getStatusBadgeClass(fireStatus)}">${fireStatus}</span>
            </td>
            <td class="warehouse-actions">
                <a href="javascript:void(0)" onclick="editWarehouse('${warehouse.id}')" class="text-primary">Edit</a>
                <span> | </span>
                <a href="javascript:void(0)" onclick="deleteWarehouse('${warehouse.id}')" class="text-danger">Delete</a>
            </td>
        </tr>
    `;
}

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'compliant': return 'success';
        case 'non_compliant': return 'danger';
        case 'pending': return 'warning';
        default: return 'secondary';
    }
}

function editWarehouse(warehouseId) {
    const warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
    const warehouse = warehouses.find(w => w.id === warehouseId);
    
    if (!warehouse) {
        showNotification('Warehouse not found!', 'error');
        return;
    }
    
    // Populate form with warehouse data
    $('#warehouse_id').val(warehouse.id);
    $('#warehouse_code').val(warehouse.warehouse_code || '');
    $('#warehouse_name').val(warehouse.warehouse_name || '');
    $('#warehouse_address').val(warehouse.warehouse_address || '');
    
    // Handle country dropdown
    if (warehouse.warehouse_country) {
        $('#warehouse_country').val(warehouse.warehouse_country).trigger('change');
        
        // Wait for states to load, then set state
        setTimeout(() => {
            if (warehouse.warehouse_state) {
                $('#warehouse_state').val(warehouse.warehouse_state);
            }
        }, 500);
    }
    
    $('#warehouse_city').val(warehouse.warehouse_city || '');
    $('#warehouse_area').val(warehouse.warehouse_area || '');
    $('#warehouse_building_name').val(warehouse.warehouse_building_name || '');
    $('#warehouse_flat_office_no').val(warehouse.warehouse_flat_office_no || '');
    $('#contact_salutation').val(warehouse.contact_salutation || '');
    $('#contact_first_name').val(warehouse.contact_first_name || '');
    $('#contact_last_name').val(warehouse.contact_last_name || '');
    $('#contact_mobile').val(warehouse.contact_mobile || '');
    $('#contact_email').val(warehouse.contact_email || '');
    $('#contact_designation').val(warehouse.contact_designation || '');
    $('#fire_safety_compliance_status').val(warehouse.fire_safety_compliance_status || '');
    $('#fire_noc_certificate_number').val(warehouse.fire_noc_certificate_number || '');
    $('#safety_equipment_available').val(warehouse.safety_equipment_available || '');
    $('#fire_noc_expiry_date').val(warehouse.fire_noc_expiry_date || '');
    $('#last_safety_inspection_date').val(warehouse.last_safety_inspection_date || '');
    
    // Change modal title
    $('#warehouseModal .modal-title').text('Edit Warehouse');
    
    // Show modal
    $('#warehouseModal').modal('show');
}

function deleteWarehouse(warehouseId) {
    if (!confirm('Are you sure you want to delete this warehouse?')) {
        return;
    }
    
    let warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
    warehouses = warehouses.filter(w => w.id !== warehouseId);
    sessionStorage.setItem('company_warehouses', JSON.stringify(warehouses));
    
    loadWarehouses();
    showNotification('Warehouse deleted successfully!', 'success');
}

function resetWarehouseForm() {
    $('#warehouseForm')[0].reset();
    $('#warehouse_id').val('');
    $('#contact_salutation').val('');
    $('#warehouseModal .modal-title').text('Add Warehouse');
}

function updateWarehouseBadge() {
    const warehouses = JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
    const badge = $('#warehouseCountBadge');
    
    if (warehouses.length > 0) {
        badge.text(warehouses.length).removeClass('d-none');
    } else {
        badge.addClass('d-none');
    }
}

function showNotification(message, type = 'info') {
    // You can implement your notification system here
    // For now, using alert as placeholder
    alert(message);
}

// Export warehouses data for main form submission
function getWarehousesData() {
    return JSON.parse(sessionStorage.getItem('company_warehouses') || '[]');
}

// Function to clear warehouse session data (call after successful main form submission)
function clearWarehouseSession() {
    sessionStorage.removeItem('company_warehouses');
    updateWarehouseBadge();
}
</script>
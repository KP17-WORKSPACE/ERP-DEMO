
    <div class="d-flex justify-content-between mb-2">
        <h5 class="mb-0">Banking & Finance</h5>
        <button type="button" id="addBankBtn" class="btn btn-sm btn-success" data-bs-toggle="modal"
            data-bs-target="#bankModal">
            <i class="ico icon-outline-add-square"></i> Add Bank
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Bank Name</th>
                    <th>Branch Name</th>
                    <th>Account No</th>
                    <th>IBAN</th>
                    <th>SWIFT</th>
                    <th>Finance Code</th>
                    <th>Currency</th>
                    <th>Letter</th>
                    <th style="width:120px;">Action</th>
                </tr>
            </thead>
            <tbody id="bankTableBody">
                @if($company && $company->banking)
                    @forelse($company->banking as $bank)
                    <tr id="bankRow_{{ $bank->id }}">
                        <td>{{ $bank->bank_name }}</td>
                        <td>{{ $bank->branch_name ?? '-' }}</td>
                        <td>{{ $bank->account_number }}</td>
                        <td>{{ $bank->iban_number }}</td>
                        <td>{{ $bank->swift_code ?? '-' }}</td>
                        <td>{{ $bank->finance_code ?? '-' }}</td>
                        <td>{{ $bank->currency ?? '-' }}</td>
                        <td>{{ $bank->bank_letter ? basename($bank->bank_letter) : '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-light btn-sm editBankBtn" data-id="{{ $bank->id }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteBankBtn" data-id="{{ $bank->id }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="no-bank-row"><td colspan="9" class="text-center text-muted">No banks added yet.</td></tr>
                    @endforelse
                @else
                    <tr class="no-bank-row"><td colspan="9" class="text-center text-muted">No banks added yet.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="bankModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bankForm">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id ?? '' }}">
                        <input type="hidden" name="bank_id" id="bank_id">

                        <div class="mb-2">
                            <label>Bank Name *</label>
                            <input type="text" name="bank_name" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Branch Name</label>
                            <input type="text" name="branch_name" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Account Number *</label>
                            <input type="text" name="account_number" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>IBAN *</label>
                            <input type="text" name="iban_number" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>SWIFT Code</label>
                            <input type="text" name="swift_code" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Finance Code</label>
                            <input type="text" name="finance_code" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Currency</label>
                            <input type="text" name="currency" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Bank Letter <span id="bankLetterRequired">*</span></label>
                            <input type="file" name="bank_letter" class="form-control form-control-sm">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="bankSaveBtn" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
@push('scripts')
<script>
$(document).ready(function(){

    // Open modal for Add
    $('#addBankBtn').click(function(){
        $('#bankForm')[0].reset();
        $('#bank_id').val('');
        $('#bankModal .modal-title').text('Add Bank');
        $('#bankLetterRequired').show();
    });

    // Edit button click
    $(document).on('click', '.editBankBtn', function(){
        let row = $(this).closest('tr');
        $('#bank_id').val($(this).data('id'));
        $('#bankModal .modal-title').text('Edit Bank');
        $('#bankLetterRequired').hide(); // file optional on edit

        $('#bankForm [name="bank_name"]').val(row.find('td:eq(0)').text());
        $('#bankForm [name="branch_name"]').val(row.find('td:eq(1)').text());
        $('#bankForm [name="account_number"]').val(row.find('td:eq(2)').text());
        $('#bankForm [name="iban_number"]').val(row.find('td:eq(3)').text());
        $('#bankForm [name="swift_code"]').val(row.find('td:eq(4)').text());
        $('#bankForm [name="finance_code"]').val(row.find('td:eq(5)').text());
        $('#bankForm [name="currency"]').val(row.find('td:eq(6)').text());

        $('#bankModal').modal('show');
    });

    // Save (Add/Edit)
    $('#bankSaveBtn').click(function(e){
        e.preventDefault();

        let form = new FormData($('#bankForm')[0]);
        let bankId = $('#bank_id').val();
        let url = bankId ? '/company/banking/' + bankId : '{{ route("company.banking.store") }}';

        $.ajax({
            url: url,
            type: 'POST',
            data: form,
            contentType: false,
            processData: false,
            success: function(res){
                if(res.bank){
                    $('#bankModal').modal('hide');
                    let row = `
                        <tr id="bankRow_${res.bank.id}">
                            <td>${res.bank.bank_name}</td>
                            <td>${res.bank.branch_name || '-'}</td>
                            <td>${res.bank.account_number}</td>
                            <td>${res.bank.iban_number}</td>
                            <td>${res.bank.swift_code || '-'}</td>
                            <td>${res.bank.finance_code || '-'}</td>
                            <td>${res.bank.currency || '-'}</td>
                            <td>${res.bank.bank_letter ? res.bank.bank_letter.split('/').pop() : '-'}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-light btn-sm editBankBtn" data-id="${res.bank.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm deleteBankBtn" data-id="${res.bank.id}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    if(bankId){
                        $('#bankRow_' + res.bank.id).replaceWith(row);
                    } else {
                        $('.no-bank-row').remove();
                        $('#bankTableBody').append(row);
                    }
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    $('#bankForm small.text-danger').remove();
                    $.each(errors, function(k,v){
                        $('[name="'+k+'"]').after('<small class="text-danger">'+v[0]+'</small>');
                    });
                }
            }
        });
    });

    // Delete button click
    $(document).on('click', '.deleteBankBtn', function(){
        if(!confirm('Delete this bank?')) return;

        let id = $(this).data('id');
        $.get('/company/banking/delete/' + id, function(){
            $('#bankRow_' + id).remove();
            if($('#bankTableBody tr').length == 0){
                $('#bankTableBody').append('<tr class="no-bank-row"><td colspan="9" class="text-center text-muted">No banks added yet.</td></tr>');
            }
        });
    });

});
</script>
@endpush

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="d-flex justify-content-end mb-2">
    <button type="button" id="addBankBtn" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bankModal">
        <i class="ico icon-outline-add-square"></i> Add
    </button>
</div>


<div class="table-responsive">
    <table class="table table-hover data-table" style="table-layout: fixed;width:100%">
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
            @php $banks = session('company_banks', []); @endphp
            @forelse($banks as $bank)
                <tr id="bankRow_{{ $bank['id'] }}">
                    <td>{{ $bank['bank_name'] ?? '-' }}</td>
                    <td>{{ $bank['branch_name'] ?? '-' }}</td>
                    <td>{{ $bank['account_number'] ?? '-' }}</td>
                    <td>{{ $bank['iban_number'] ?? '-' }}</td>
                    <td>{{ $bank['swift_code'] ?? '-' }}</td>
                    <td>{{ $bank['finance_code'] ?? '-' }}</td>
                    <td>{{ $bank['currency'] ?? '-' }}</td>
                    <td>
                        @if (isset($bank['bank_letter']) && $bank['bank_letter'])
                            <a href="{{ asset('storage/' . $bank['bank_letter']) }}" target="_blank"
                                class="btn btn-sm btn-light">
                                View
                            </a>
                        @else
                            -
                        @endif
                    </td>


                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-light btn-sm editBankBtn"
                                data-id="{{ $bank['id'] }}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteBankBtn"
                                data-id="{{ $bank['id'] }}">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="no-bank-row">
                    <td colspan="9" class="text-center text-muted">No banks added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


<!-- =================== MODAL (NO FORM TAG) =================== -->
<div class="modal fade" id="bankModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div id="bankForm"> <!-- ✔ REPLACED FORM -->
                @csrf

                <div class="modal-body">

                    <input type="hidden" name="company_id" id="bank_company_id">
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
                        <label>Bank Letter</label>
                        <input type="file" name="bank_letter" class="form-control form-control-sm">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="bankSaveBtn"
                        class="btn btn-light d-inline-flex align-items-center gap-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                        Save
                    </button>
                </div>

            </div> <!-- END #bankForm -->
        </div>
    </div>
</div>


<script>
    const BASE_URL = "{{ asset('') }}".slice(0, -1);

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    // Ensure banks is an indexed array (not an associative object) so JS `.length` works
    let banks = @json(array_values(session('company_banks', [])));

    function renderBankTable() {
        let body = $("#bankTableBody");
        body.empty();

        if (!banks.length) {
            body.append(`<tr><td colspan="9" class="text-center text-muted">No banks added yet.</td></tr>`);
            return;
        }

        banks.forEach(bank => {
            body.append(`
                <tr id="bankRow_${bank.id}">
                    <td>${bank.bank_name}</td>
                    <td>${bank.branch_name ?? '-'}</td>
                    <td>${bank.account_number}</td>
                    <td>${bank.iban_number}</td>
                    <td>${bank.swift_code ?? '-'}</td>
                    <td>${bank.finance_code ?? '-'}</td>
                    <td>${bank.currency ?? '-'}</td>
                  <td>
    ${bank.bank_letter 
        ? `<a href="${BASE_URL}/storage/${bank.bank_letter}" target="_blank" class="btn btn-sm btn-light">View</a>`
        : '-'
    }
</td>


                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-light btn-sm editBankBtn" data-id="${bank.id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteBankBtn" data-id="${bank.id}">Delete</button>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    renderBankTable();


    $("#addBankBtn").click(function() {
        $(".modal-title").text("Add Bank");
        $("#bank_company_id").val($("#company_id").val()); // inject company id
        $("#bankForm").find("input, textarea, select").val("");
        $("input[name='bank_letter']").val(null);
        $("#bank_id").val("");

        $("#bankModal").modal("show");
    });



    $(document).on("click", ".editBankBtn", function() {
        let id = $(this).data("id");
        $("#bank_company_id").val($("#company_id").val());
        $.post(
            BASE_URL + "/company/bank/session/get", {
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            function(res) {
                let data = res.banks.find(b => b.id == id);

                if (!data) {
                    alert("Bank data not found!");
                    return;
                }

                $(".modal-title").text("Edit Bank");

                $("#bank_id").val(data.id);
                $("input[name='bank_name']").val(data.bank_name);
                $("input[name='branch_name']").val(data.branch_name);
                $("input[name='account_number']").val(data.account_number);
                $("input[name='iban_number']").val(data.iban_number);
                $("input[name='swift_code']").val(data.swift_code);
                $("input[name='finance_code']").val(data.finance_code);
                $("input[name='currency']").val(data.currency);

                $("#bankModal").modal("show");
            }
        );
    });




    $("#bankSaveBtn").click(function() {

        let formData = new FormData();

        $("#bankForm")
            .find("input[type='text'], input[type='hidden'], select, textarea")
            .each(function() {
                formData.append($(this).attr("name"), $(this).val());
            });

        let file = $("input[name='bank_letter']")[0].files[0];
        if (file) formData.append("bank_letter", file);

        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

        $.ajax({
            url: BASE_URL + "/company/bank/session/store",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res) {
                banks = res.banks;
                renderBankTable();
                $("#bankModal").modal("hide");
            }
        });
    });


    $(document).on("click", ".deleteBankBtn", function() {
        let id = $(this).data("id");

        $.post(
            BASE_URL + "/company/bank/session/delete", {
                _token: $('meta[name="csrf-token"]').attr("content"),
                bank_id: id
            },
            function(res) {
                banks = res.banks;
                renderBankTable();
            }
        );
    });
</script>

{{-- <script>
    $(document).ready(function() {
        $('#autoModal').modal({
            keyboard: false, // disable ESC
            backdrop: 'static' // disable click outside to close
        });

        $('#autoModal').modal('show'); // show modal on page load
    });
</script> --}}

<style>
    .table-input {
        border: solid 0px #f0f1f3;
        background: transparent;
        width: 100%;
        padding: 2px 4px;
        text-align: inherit;
    }

    .table-input:focus {
        outline: none;
        border-bottom: 1px solid #000;
        /* subtle underline when editing */
        background: #f9f9f9;
        /* light background while editing */
    }

    .popover {
        --bs-popover-bg: #000 !important;
        --bs-popover-body-color: #fff !important;
    }
    
    .popover-body {
        background-color: #000 !important;
        color: #fff !important;
    }
    
    .popover .popover-arrow::before {
        border-top-color: #000 !important;
    }
    
    .popover .popover-arrow::after {
        border-top-color: #000 !important;
    }
    .form-item-table .select2-container--default .select2-selection--single {
        border: 0 !important;
        background: transparent !important;
        height: auto !important;
    }
    .form-item-table .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: inherit !important;
        padding-left: 4px !important;
    }
    .form-item-table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
    }
</style>


@if (isset($account_edit))
    @foreach ($account_edit as $value)
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ @$value->account_code }} -
                    {{ @$value->accounts->account_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body p-0">
                @php $invoice_list = $invoice->where('account_id',$value->account_id); @endphp
                    <div class="table-responsive">
                        <table class="table table-hover form-item-table" id="dataTable_{{ $value->account_id }}"
                            width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:150px">Invoice No</th>
                                    <th class="text-center" style="width:150px">Invoice Date</th>
                                    <th class="text-center" style="width:180px">Payment Terms</th>
                                    <th class="text-end" style="width:150px">Debit Amount</th>
                                    <th class="text-end" style="width:150px">Credit Amount</th>
                                    <th class="text-center" style="width:150px">Action
                                        <button type="button" class="btn btn-sm btn-light ms-2 p-1 d-inline-flex align-items-center"
                                            onclick="add_invoice_row({{ $value->account_id }})">
                                            <i class="ico icon-outline-add-square text-success" style="font-size: 16px;"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="invoice_table_body_{{ $value->account_id }}">
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($invoice_list as $list)
                                @php
                                    $i++;
                                @endphp
                                    <tr id="row_{{ $list->id }}">
                                        <td class="text-center"><input type="text"
                                                class="form-control table-input text-center p-0"
                                                id="invoice_no_{{ $list->id }}"
                                                value="{{ $list->transaction_no }}" /></td>
                                        <td class="text-center"><input type="text"
                                                class="form-control  table-input date-picker-2 p-0"
                                                id="invoice_date_{{ $list->id }}"
                                                value="{{ \Carbon\Carbon::parse($list->transaction_date)->format('d/m/Y') }}" />
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control select2-popup p-0 border-0" id="payment_terms_{{ $list->id }}">
                                                <option value="">-Select-</option>
                                                @foreach($payment_terms as $term)
                                                    <option value="{{ $term->id }}" @if((string)$list->payment_terms == (string)$term->id) selected @endif>{{ $term->title }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center"><input type="text"
                                                class="form-control table-input text-end p-0"
                                                id="debit_amount_{{ $list->id }}"
                                                value="{{ @App\SysHelper::com_curr_format($list->debit_amount ?? 0, 2, '.', ',') }}" />
                                        </td>
                                        <td class="text-center"> <input type="text"
                                                class="form-control table-input text-end p-0"
                                                id="credit_amount_{{ $list->id }}"
                                                value="{{ @App\SysHelper::com_curr_format($list->credit_amount ?? 0, 2, '.', ',') }}" /></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="hidden" id="account_id_{{ $list->id }}"
                                                    value="{{ $value->account_id }}" />
                                                <button class="btn-sm btn btn-light"
                                                    onclick="update_invoice({{ $list->id }})"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="hover"
                                                    data-bs-delay="500"
                                                    data-bs-content="Update Record"
                                                    data-bs-placement="top"><i
                                                        class="ico icon-outline-bookmark-opened"
                                                        style="font-size: 16px;"></i></button>
                                                <button class="btn-sm btn btn-light"   data-bs-toggle="popover"
                                                    data-bs-trigger="hover"
                                                    data-bs-delay="500"
                                                    data-bs-content="Delete Record"
                                                    data-bs-placement="top"
                                                    onclick="delete_invoice({{ $list->id }})"><i
                                                        class="ico icon-outline-trash-bin-minimalistic"
                                                        style="font-size: 16px;"></i></button>
                                            </div>

                                        </td>
                                    </tr>
                                    
                                @endforeach
                                <tr><td colspan="6" style="height:18px"></td></tr>
                            </tbody>
                            <tfoot>
                                @php
                                    $d = $invoice_list->sum('debit_amount');
                                    $c = $invoice_list->sum('credit_amount');
                                @endphp
                                <thead>
                                    <th class="text-center">Invoices: {{ $i }}</th>
                                    <th colspan="2" class="text-end">Total </th>
                                    <th class="text-end font-weight-bold">
                                        &nbsp; {{ @App\SysHelper::com_curr_format($d, 2, '.', ',') }}</th>
                                    <th class="text-end font-weight-bold">
                                        &nbsp; {{ @App\SysHelper::com_curr_format($c, 2, '.', ',') }}</th>
                                    <th></th>
                                </thead>
                                <thead>
                                    <td colspan="3" class="text-end"><span class="font-weight-600">Opening Balance</span> </td>
                           
                                    @if ($d > $c)
                                        <td class="text-end font-weight-bold">
                                            {{ @App\SysHelper::com_curr_format($d - $c, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end font-weight-bold">
                                            {{ @App\SysHelper::com_curr_format(0, 2, '.', ',') }}
                                        </td>
                                    @else
                                        <td class="text-end font-weight-bold">
                                            {{ @App\SysHelper::com_curr_format(0, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end font-weight-bold">
                                            {{ @App\SysHelper::com_curr_format($c - $d, 2, '.', ',') }}
                                        </td>
                                    @endif
                                    <td></td>
                                </thead>





                            </tfoot>
                        </table>
                    </div>
                    @if (count($receiptAdjustments) > 0 || count($returnAdjustments) > 0)
                        <div class="table-responsive">
                            <b style="padding-left:11px">Adjusted Items</b>
                            <table class="table table-hover" id="br-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-center">@lang('#')
                                        </th>
                                        <th class="text-center">@lang('Receipt Number')
                                        </th>
                                        <th class="text-center">@lang('Receipt Date')
                                        </th>
                                        <th class="text-end">
                                            Total</th>
                                        <th class="text-end">
                                            Paid</th>
                                        <th class="text-end">
                                            Balance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($receiptAdjustments) > 0)
                                        @foreach ($receiptAdjustments as $item)
                                            <tr>
                                                <td class="text-center">{{ @$loop->iteration }}</td>
                                                <td class="text-center">{{ @$item->bi_doc_number }}
                                                </td>
                                                <td class="text-center">{{ @App\SysHelper::normalizeToDmy(@$item->bi_doc_date) }}</td>
                                                <td class="text-end">
                                                    {{ @$item->bi_total }}</td>
                                                <td class="text-end">
                                                    {{ @$item->bi_paid }}</td>
                                                <td class="text-end">
                                                    {{ @$item->bi_balance }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (count($returnAdjustments) > 0)
                                        @foreach ($returnAdjustments as $item)
                                            <tr>
                                                <td>{{ @$loop->iteration }}</td>
                                                <td>{{ @$item->srn_no }}</td>
                                                <td>{{ @$item->doc_date }}</td>
                                                <td class="text-end">
                                                    {{ @$item->total_amount }}</td>
                                                <td class="text-end">
                                                    {{ @$item->paid_amount }}</td>
                                                <td class="text-end">
                                                    {{ @$item->balance_amount }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
            </div>

        </div>
    @endforeach
@endif



<script>
    let newRowCounter = 0;
    function add_invoice_row(accountId) {
        newRowCounter++;
        let rowId = 'new_' + newRowCounter;
        let paymentTermsOptions = '<option value="">-Select-</option>';
        @foreach($payment_terms as $term)
            paymentTermsOptions += '<option value="{{ $term->id }}">{{ $term->title }}</option>';
        @endforeach
        let html = `
            <tr id="row_${rowId}">
                <td class="text-center">
                    <input type="text" class="form-control table-input text-center p-0" id="invoice_no_${rowId}" value="" placeholder="Invoice No" />
                </td>
                <td class="text-center">
                    <input type="text" class="form-control table-input date-picker-2 p-0" id="invoice_date_${rowId}" value="" placeholder="Date" />
                </td>
                <td class="text-center">
                    <select class="form-control select2-popup p-0 border-0" id="payment_terms_${rowId}">
                        ${paymentTermsOptions}
                    </select>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control table-input text-end p-0" id="debit_amount_${rowId}" value="0.00" />
                </td>
                <td class="text-center">
                    <input type="text" class="form-control table-input text-end p-0" id="credit_amount_${rowId}" value="0.00" />
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="btn-sm btn btn-light" onclick="save_new_invoice('${rowId}', ${accountId})">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size: 16px;"></i>
                        </button>
                        <button type="button" class="btn-sm btn btn-light" onclick="$('#row_${rowId}').remove()">
                            <i class="ico icon-outline-trash-bin-minimalistic text-danger" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        $('#invoice_table_body_' + accountId).append(html);
        flatpickr("#invoice_date_" + rowId, {
            dateFormat: "d/m/Y",
            allowInput: true
        });
        $('#payment_terms_' + rowId).select2({
            dropdownParent: $('#invoiceModal')
        });
    }

    function save_new_invoice(rowId, accountId) {
        var invoice_no = $('#invoice_no_' + rowId).val();
        var invoice_date = $('#invoice_date_' + rowId).val();
        var debit_amount = $('#debit_amount_' + rowId).val();
        var credit_amount = $('#credit_amount_' + rowId).val();
        var payment_terms = $('#payment_terms_' + rowId).val();
        if (!invoice_no || !invoice_date) {
            toastr.error("Please fill Invoice No and Invoice Date!");
            return;
        }
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('chartofaccounts-invoice-store') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                invoice_no: invoice_no,
                invoice_date: invoice_date,
                debit_amount: debit_amount,
                credit_amount: credit_amount,
                account_id: accountId,
                payment_terms: payment_terms,
            },
            cache: false,
            success: function(dataResult) {
                var res = JSON.parse(dataResult);
                if (res.data == "ERROR") {
                    toastr.error("Error occurred while saving!");
                } else {
                    toastr.success("Saved Successfully!");
                    loadInvoiceModal(accountId);
                }
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function update_invoice(id) {
        $("#loading_bg").css("display", "block");
        var invoice_no = $('#invoice_no_' + id).val();
        var invoice_date = $('#invoice_date_' + id).val();
        var debit_amount = $('#debit_amount_' + id).val();
        var credit_amount = $('#credit_amount_' + id).val();
        var account_id = $('#account_id_' + id).val();
        var payment_terms = $('#payment_terms_' + id).val();
        var action = "{{ URL::to('chartofaccounts-invoice-update') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                invoice_no: invoice_no,
                invoice_date: invoice_date,
                debit_amount: debit_amount,
                credit_amount: credit_amount,
                account_id: account_id,
                payment_terms: payment_terms,
            },
            cache: false,
            success: function(dataResult) {
                console.log(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                } else {
                    $("#loading_bg").css("display", "none");
                    // alert("Updated Successfully!");
                    //show toastr
                    toastr.success("Updated Successfully!");    
                    loadInvoiceModal(account_id);
                    // location.reload(true);
                }
            },
            complete: function() {
                $("#loading_bg").css("display", "none"); // Always hide loader after request completes
            }
        });
    }

    function delete_invoice(id) {
        $("#loading_bg").css("display", "block");
        var account_id = $('#account_id_' + id).val();
        var action = "{{ URL::to('chartofaccounts-invoice-delete') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                account_id: account_id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                } else {
                    $("#loading_bg").css("display", "none");
                    // alert("Deleted Successfully!");

                    // $("#row_" + id).fadeOut(300, function() {
                    //     $(this).remove();
                    // });


                    loadInvoiceModal(account_id);

                    // location.reload(true);
                }
            },
            complete: function() {
                $("#loading_bg").css("display", "none"); // Always hide loader after request completes
            }
        });
    }


    flatpickr(".date-picker-2", {
        dateFormat: "d/m/Y", // dd/mm/yyyy
        allowInput: true
    });

   $(document).ready(function() {
       $('.select2-popup').select2({
           dropdownParent: $('#invoiceModal')
       });

   // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });
        });

</script>

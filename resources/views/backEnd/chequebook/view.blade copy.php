@extends('backEnd.newmasterpage')
@section('mainContent')

        @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', auth()->user()->role_id)->get();
        @endphp

        <style>
            .border {
                border: solid 1px #e3e6f0;
            }
            .chequebook-stats-card {
                border-radius: 0.75rem;
                background: linear-gradient(120deg, #ffffff 0%, #f8f9fc 60%, #eef2ff 100%);
                border: 1px solid #d4d9e9;
                box-shadow: 0 2px 6px rgba(33, 37, 41, 0.06);
                margin-bottom: 1rem;
            }
            .chequebook-stats-card .stat-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: #2a2f45;
            }
            .status-chip {
                border-radius: 12px;
                padding: 0.35rem 0.65rem;
                font-size: 0.78rem;
                font-weight: 600;
                display: inline-block;
            }
            .progress-row {
                background: #f8f9fc;
                border-radius: 10px;
                overflow: hidden;
                height: 10px;
            }
            .text-nowrap {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>

        <?php try { ?>
        <div class="content-container col-12">
            <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    <div class="purchase-order-content-header" id="card-1">
                        <h4 class="purchase-order-content-header-left">
                            Cheque Book
                        </h4>
                        <div class="purchase-order-content-header-right">

                            <a class="btn btn-light text-dark" href="#" data-bs-toggle="modal" data-bs-target="#addchequebook">
                                <i class="ico icon-outline-add-square text-success"></i> Add Cheque Book
                            </a>

                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu">

                        <li><a class="dropdown-item" href="{{url('stl')}}"><i class="ico icon-outline-document-text text-success"></i> STL</a></li>

                        <li><a class="dropdown-item" href="{{ url("payment-cheque-list") }}"><i class="ico icon-outline-document-text text-success"></i> Cheque</a></li>

                                    <li><a class="dropdown-item" href="{{ url('receipt-add/cashbook') }}"><i
                                                class="ico icon-outline-document-text text-success"></i> Receipts</a></li>
                                    <li><a class="dropdown-item" href="{{ url('payment-add/cashbook') }}"><i
                                                class="ico icon-outline-document-text text-success"></i> Payments</a></li>
                                    <li><a class="dropdown-item" href="{{ url('journalvoucher-add/cashbook') }}"><i
                                                class="ico icon-outline-document-text text-success"></i> Journal Voucher</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="card mb-3" id="card-2">
                        <div class="card-body">
                            <form class="form-horizontal" method="POST" action="{{ url('chequebook') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                <div class="col-md-3 mb-20">
                                    <div class="input-effect">
                                        <label>@lang('Bank')</label>
                                        <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                                            <option value="" @if(!isset($selectedBankId) || $selectedBankId === '') selected @endif>Select Bank</option>
                                            <option value="all" @if(isset($selectedBankId) && $selectedBankId === 'all') selected @endif>All Banks</option>
                                            @foreach ($accounts as $val)
                                                <option value="{{ @$val->id }}" @if(isset($selectedBankId) && $selectedBankId == $val->id) selected @endif>{{ @$val->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-1" style="margin-top:1.4rem">
                                    <button class="btn btn-light" id="btnSubmit">
                                        <i class="ico icon-outline-minimalistic-magnifer text-success"
                                            style="font-size: 18px;"></i> Filter
                                    </button>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="" class="form-check-label">Search in List</label>
                                    <input type="text" id="tableSearch_custom" class="form-control mb-2" placeholder="">
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>


                 <script>
$(document).ready(function () {
    $("#tableSearch_custom").on("keyup", function () {
        let value = $(this).val().trim();
        let lowerValue = value.toLowerCase();
        let numericValue = value.replace(/,/g, '');

        // Determine if the query is a plain integer for start/end range matching
        let numericQuery = /^\d+$/.test(numericValue);

        $(".data-table tbody tr").each(function () {
            let $row = $(this);
            let show = false;

            if (numericQuery) {
                let targetNum = parseInt(numericValue, 10);
                let startNo = parseInt(
                    $row.find('td:nth-child(4)').text().replace(/,/g, '').trim(),
                    10
                );
                let endNo = parseInt(
                    $row.find('td:nth-child(5)').text().replace(/,/g, '').trim(),
                    10
                );

                if (
                    !isNaN(startNo) &&
                    !isNaN(endNo) &&
                    !isNaN(targetNum) &&
                    targetNum >= startNo &&
                    targetNum <= endNo
                ) {
                    show = true;
                }
            }

            if (!show) {
                show = $row.text().toLowerCase().indexOf(lowerValue) > -1;
            }

            $row.toggle(show);
        });
    });
});
</script>

                    <div class="card mb-3">
                        <div class="card-body p-0">
                            <table id="long-list" class="table table-hover data-table"
                                style="table-layout: fixed;width:100%">

                                <thead>
                                    <tr>
                                        <th class="text-center">Doc No</th>
                                        <th class="text-center">Doc Date</th>
                                        <th>Bank Name</th>

                                        <th class="text-center">Start No</th>
                                        <th class="text-center">End No</th>
                                        <th class="text-center" style="width:10%">Usage</th>
                                        <th class="text-center" style="width:35%">Status</th>
                                         <th class="text-start" style="width:18%">Remarks</th>
                                        <th class="text-center" style="width:10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chequebooks as $chequebook)
                                                    <tr @if($chequebook->trashed()) style="color: rgba(0,0,0,0.45); background-color: #f7f7f7;" @endif>
                                                        <td class="text-center">
                                                            <a href="#" class="chequebook-doc-link" data-id="{{ $chequebook->id }}" data-bank="{{ $chequebook->bank_id }}" data-doc="{{ $chequebook->doc_number }}">
                                                                {{ $chequebook->doc_number }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">{{ optional($chequebook->created_at)->format('d/m/Y') }}</td>
                                                        <td>{{ optional($chequebook->bank)->account_name }}</td>

                                                        <td class="text-center">{{ $chequebook->start_no }}</td>
                                                        <td class="text-center">{{ $chequebook->end_no }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $used = $chequebook->used_count ?? 0;
                                                            $total = $chequebook->total_count ?? $chequebook->no_of_cheques;
                                                            $rate = $total > 0 ? round(($used / $total) * 100) : 0;
                                                            $barClass = $rate >= 85 ? 'bg-success' : ($rate >= 60 ? 'bg-info' : ($rate >= 40 ? 'bg-warning' : 'bg-danger'));
                                                        @endphp

                                                        <div class="d-flex align-items-center justify-content-center gap-2">

                                                            <span class="text-muted" style="font-size: 12px;">
                                                                {{ $used }} / {{ $total }}
                                                            </span>

                                                            <div style="width: 100px; height: 8px; background: #e9ecef; border-radius: 5px; overflow: hidden;">
                                                                <div class="{{ $barClass }}" style="width: {{ $rate }}%; height: 100%;"></div>
                                                            </div>

                                                            <span style="font-size: 11px;">
                                                                <strong>{{ $rate }}%</strong>
                                                            </span>

                                                        </div>
                                                    </td>
                                                      
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center gap-1 justify-content-center flex-wrap">
                                                                <span class="badge bg-primary " title="Issued">{{ $chequebook->issued_count ?? 0 }} Issued</span>
                                                                <span class="badge bg-success" title="Cleared">{{ $chequebook->cleared_count ?? 0 }} Cleared</span>
                                                                <span class="badge bg-warning " title="Missed">{{ $chequebook->missed_count ?? 0 }} Missed</span>
                                                                <span class="badge bg-danger" title="Cancelled">{{ $chequebook->cancelled_count ?? 0 }} Cancelled</span>
                                                                <span class="badge bg-info " title="Used">{{ $chequebook->used_count ?? 0 }} Used</span>
                                                                <span class="badge bg-secondary" title="Remaining">{{ $chequebook->remaining_count ?? max(0, ($chequebook->no_of_cheques ?? 0) - ($chequebook->used_count ?? 0)) }} Remaining</span>
                                                            </div>
                                                        </td>
                                                          <td class="text-start text-nowrap" title="{{ $chequebook->remarks }}">{{ $chequebook->remarks }}</td>
                                                        <td class="text-center" style="width: 100px; min-width: 100px; max-width: 100px;">

                                            <div class="d-flex justify-content-end align-items-center">
                                                @if ($chequebook->attachment)
                                                    <a class="btn btn-sm btn-light" href="{{ asset('storage/' . $chequebook->attachment) }}" target="_blank" title="View attachment">
                                                        <i class="ico icon-bold-paperclip" style="font-size:16px" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <span class="btn btn-sm btn-light disabled" title="No attachment">
                                                        <i class="ico icon-bold-paperclip" style="font-size:16px; opacity:0.35;" aria-hidden="true"></i>
                                                    </span>
                                                @endif

                                                @if ($chequebook->trashed())
                                                    <form action="{{ url('chequebook/restore/' . $chequebook->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Restore this cheque book?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light" title="Restore chequebook">
                                                            <i class="ico icon-outline-refresh text-success" style="font-size: 16px;"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="#" data-id="{{ $chequebook->id }}" data-account_id="{{ $chequebook->bank_id }}" data-no_of_cheques="{{ $chequebook->no_of_cheques }}" data-start_no="{{ $chequebook->start_no }}" data-remarks="{{ $chequebook->remarks }}" data-attachment="{{ $chequebook->attachment }}" class="btn btn-sm btn-light edit-lead-btn" title="Edit chequebook"><i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>

                                                    <form action="{{ url('chequebook/delete/' . $chequebook->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this cheque book?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light" title="Delete chequebook">
                                                            <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>


                                        </td>


                                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No cheque books found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>



                </div>
            </div>
        </div>

        <div class="modal fade" id="usedChequesModal" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-labelledby="usedChequesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usedChequesModalLabel">Used Cheques</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-md-8">
                                <div class="input-group">
                                
                                    <input id="usedChequesSearch" type="text" class="form-control" placeholder="Search" />
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <a class="btn-sm btn-light" id="usedChequeListLink" target="_blank" rel="noopener">View in Cheque Payment List</a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div id="usedChequesMeta" class="fw-semibold">Loading cheque data...</div>
                            <div id="usedChequesTotal" class="text-muted"></div>
                        </div>
                        <div class="position-relative">
                            <div id="usedChequesLoadingOverlay" class="position-absolute w-100 h-100 top-0 start-0 d-none" style="background: rgba(255,255,255,0.75); z-index: 20; display:flex; align-items:center; justify-content:center;">
                                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                            </div>
                            <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                                <table class="table table-hover table-sm data-table" id="long-list" style="table-layout: fixed; width: 100%;">
                                    <thead class="position-sticky top-0 bg-white" style="z-index: 10;">
                                        <tr>
                                            <th style="width:20px">#</th>
                                            <th class="text-center">Cheque Number</th>
                                            <th class="text-center">Doc Number</th>
                                            <th class="text-center">Doc Date</th>
                                            <th>Bank Name</th>
                                            <th class="text-center">Cheque Date</th>
                                            <th>Supplier Name</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Payment Doc</th>
                                            <th class="text-center">Deal ID</th>
                                            <th class="text-center"><i class="ico icon-outline-paperclip" style="font-size: 16px;"></i></th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usedChequesModalBody">
                                        <tr><td colspan="12" class="text-center py-4">Loading…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add cheque book modal -->

        <div class="modal side-panel fade" id="addchequebook" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="top:25%;max-width:1000px!important;left: 37%;">

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Add Cheque Book</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <form id="chequebook-form" action="{{ url('chequebook/store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                <input type="hidden" id="chequebook_id" name="chequebook_id" value="">
                                    <div class="row gap-rows row-cols-5">


                                    <div class="col">
                                        <label for="" class="form-label">Bank</label>
                                        <select class="form-control js-example-basic-single" name="account_id" id="modal_account_id"
                                            required>
                                            @foreach ($accounts as $val)
                                                <option value="{{ @$val->id }}">{{ @$val->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                    <div class="col">
                                        <label for="" class="form-label">No. of Cheques</label>
                                        <input class="form-control" type="text" autocomplete="off" name="no_of_cheques"
                                            required>
                                    </div>

                                    <div class="col">
                                        <label for="" class="form-label">Start No</label>
                                        <input class="form-control" type="text" autocomplete="off" name="start_no" required>
                                    </div>

                                    <div class="col">
                                        <label for="" class="form-label">End No</label>
                                        <input class="form-control" type="text" autocomplete="off" name="end_no" id="end_no" required readonly>
                                    </div>

                                     <div class="col">
                                        <label for="" class="form-label">Attachment</label>
                                        <input class="form-control" type="file" name="attachment">
                                    </div>

                                    <div class="col-12">
                                        <label for="" class="form-label">Remarks</label>
                                        <input class="form-control" type="text" name="remarks">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function updateEndNo() {
                            var start = parseInt(document.querySelector('input[name="start_no"]').value, 10);
                            var count = parseInt(document.querySelector('input[name="no_of_cheques"]').value, 10);

                            if (Number.isInteger(start) && Number.isInteger(count) && count > 0) {
                                document.querySelector('input[name="end_no"]').value = start + count - 1;
                            } else {
                                document.querySelector('input[name="end_no"]').value = '';
                            }
                        }

                        document.querySelector('input[name="start_no"]').addEventListener('input', updateEndNo);
                        document.querySelector('input[name="no_of_cheques"]').addEventListener('input', updateEndNo);
                    </script>

                    <script>
                        $(document).ready(function () {
                            var chequebookStoreUrl = "{{ url('chequebook/store') }}";
                            var chequebookUpdateBaseUrl = "{{ url('chequebook/update') }}";

                            // Add mode: reset form whenever add button is clicked
                            $("a[data-bs-target='#addchequebook']").on('click', function () {
                                $('#chequebook-form').attr('action', chequebookStoreUrl);
                                $('#addchequebook .modal-title').text('Add Cheque Book');
                                $('#chequebook_id').val('');
                                $('#modal_account_id').val('').trigger('change');
                                $('input[name="no_of_cheques"]').val('');
                                $('input[name="start_no"]').val('');
                                $('input[name="end_no"]').val('');
                                $('input[name="remarks"]').val('');
                            });

                            $(document).on('click', '.edit-lead-btn', function (e) {
                                e.preventDefault();
                                var id = $(this).data('id');
                                var accountId = $(this).data('account_id');
                                var noOfCheques = $(this).data('no_of_cheques');
                                var startNo = $(this).data('start_no');
                                var remarks = $(this).data('remarks');

                                $('#chequebook-form').attr('action', chequebookUpdateBaseUrl + '/' + id);
                                $('#addchequebook .modal-title').text('Edit Cheque Book');
                                $('#chequebook_id').val(id);
                                $('#modal_account_id').val(accountId).trigger('change');
                                $('input[name="no_of_cheques"]').val(noOfCheques);
                                $('input[name="start_no"]').val(startNo);
                                $('input[name="end_no"]').val(parseInt(startNo, 10) + parseInt(noOfCheques, 10) - 1);
                                $('input[name="remarks"]').val(remarks);

                                var editModal = new bootstrap.Modal(document.getElementById('addchequebook'));
                                editModal.show();
                            });
                        });
                    </script>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                        </button>
                        </form>
                    </div>
                </div>


            </div>
        </div>





        <script>
            $(document).on('click', '.chequebook-doc-link', function (e) {
                e.preventDefault();
                var chequebookId = $(this).data('id');
                var bankId = $(this).data('bank');
                var doc = $(this).data('doc');

                $('#usedChequesModalLabel').text('Used cheques in ' + doc);
                $('#usedChequesMeta').text('Loading cheques...');
                $('#usedChequesModalBody').html('<tr><td colspan="2" class="text-center py-4">Loading...</td></tr>');
                $('#usedChequeListLink').attr('href', '{{ url("payment-cheque-list") }}?bank_name=' + bankId);

                var usedChequesModal = new bootstrap.Modal(document.getElementById('usedChequesModal'));
                usedChequesModal.show();

                $('#usedChequesLoadingOverlay').removeClass('d-none');
                $.ajax({
                    url: '{{ url("api/chequebook-used-numbers") }}/' + chequebookId,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && Array.isArray(response.used) && response.used.length) {
                            var usedData = response.used;
                            var chequeAttachmentBaseUrl = '{{ url("public/uploads/payment_cheque") }}';

                            function renderRows(data) {
                                if (!data.length) {
                                    $('#usedChequesModalBody').html('<tr><td colspan="12" class="text-center py-3">No records match your search.</td></tr>');
                                    return;
                                }
                                var rows = '';
                                data.forEach(function (item, idx) {
                                    var attachmentHtml = '<span class="text-muted">-</span>';

                                    if (item.payment_id) {
                                        attachmentHtml = '<button type="button" class="btn btn-sm btn-light cheque-payment-attachments-btn" data-payment-id="' + item.payment_id + '" title="Payment Attachments"><i class="ico icon-outline-paperclip" style="font-size: 16px;"></i></button>';
                                    }

                                    if (item.cheque_attachment) {
                                        attachmentHtml += ' <a class="btn btn-sm btn-light" href="' + chequeAttachmentBaseUrl + '/' + encodeURIComponent(item.cheque_attachment) + '" target="_blank" title="Cheque Attachment"><i class="ico icon-bold-download-minimalistic" style="font-size: 16px;"></i></a>';
                                    }

                                    rows += '<tr>' +
                                        '<td>' + (idx + 1) + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.cheque_number || '').html() + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.doc_number || '').html() + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.doc_date || '').html() + '</td>' +
                                        '<td>' + $('<div>').text(item.bank_name || '').html() + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.cheque_date || '').html() + '</td>' +
                                        '<td>' + $('<div>').text(item.supplier_name || '').html() + '</td>' +
                                        '<td class="text-end">' + $('<div>').text(item.amount || '').html() + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.payment_doc || '').html() + '</td>' +
                                        '<td class="text-center">' + $('<div>').text(item.deal_id || '').html() + '</td>' +
                                        '<td class="text-center d-flex justify-content-center">' + attachmentHtml + '</td>' +
                                        '<td>' + $('<div>').text(item.created_by || '').html() + '</td>' +
                                        '</tr>';
                                });
                                $('#usedChequesModalBody').html(rows);
                            }

                            renderRows(usedData);
                            $('#usedChequesMeta').html('');
                            $('#usedChequesTotal').text('Showing ' + usedData.length + ' records');

                            function applyUsedChequesFilter() {
                                var term = $('#usedChequesSearch').val().trim().toLowerCase();
                                if (!term) {
                                    renderRows(usedData);
                                    $('#usedChequesTotal').text('Showing ' + usedData.length + ' records');
                                    return;
                                }

                                var filtered = usedData.filter(function (item) {
                                    return [item.doc_number, item.cheque_number, item.supplier_name, item.bank_name, item.payment_doc, item.deal_id]
                                        .join(' ').toLowerCase().indexOf(term) !== -1;
                                });

                                renderRows(filtered);
                                $('#usedChequesTotal').text('Showing ' + filtered.length + ' / ' + usedData.length + ' records');
                            }

                            $('#usedChequesSearch').off('input').on('input', applyUsedChequesFilter);
                            // optional: trigger once on load
                            applyUsedChequesFilter();
                        } else {
                            $('#usedChequesModalBody').html('<tr><td colspan="12" class="text-center py-3">No used cheques found for this cheque book.</td></tr>');
                            $('#usedChequesMeta').html('No used cheques.');
                            $('#usedChequesTotal').text('');
                        }
                    },
                    error: function () {
                        $('#usedChequesModalBody').html('<tr><td colspan="11" class="text-center py-3 text-danger">Failed to load used cheques.</td></tr>');
                        $('#usedChequesMeta').html('Error loading data.');
                        $('#usedChequesTotal').text('');
                    },
                    complete: function () {
                        $('#usedChequesLoadingOverlay').addClass('d-none');
                    }
                });
            });
        </script>

        <div class="modal fade" id="chequePaymentAttachmentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="chequePaymentAttachmentsLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="chequePaymentAttachmentsLabel">Payment Attachments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label class="form-label">Upload files</label>
                                <input type="file" id="chequePaymentAttachmentsFiles" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" id="uploadChequePaymentAttachmentsBtn" class="btn btn-light">Upload</button>
                            </div>
                        </div>
                        <div id="chequePaymentAttachmentsMessage" class="mb-2"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>File Name</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="chequePaymentAttachmentsList">
                                    <tr><td colspan="4" class="text-center">No attachments yet.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var currentChequePaymentId = 0;

            function formatChequeAttachmentDMY(dateString) {
                var d = new Date(dateString);
                if (isNaN(d.getTime())) return '';
                var dd = String(d.getDate()).padStart(2, '0');
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var yyyy = d.getFullYear();
                return dd + '/' + mm + '/' + yyyy;
            }

            function renderChequePaymentAttachments(attachments) {
                var $tbody = $('#chequePaymentAttachmentsList').empty();
                if (!attachments || attachments.length === 0) {
                    $tbody.html('<tr><td colspan="4" class="text-center">No attachments found.</td></tr>');
                    return;
                }
                attachments.forEach(function (att, index) {
                    var viewUrl = '{{ url("payment/attachments") }}/' + att.id + '/download';
                    var attachedDate = att.created_at ? formatChequeAttachmentDMY(att.created_at) : '';
                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + $('<div>').text(att.file_name || '').html() + '</td>' +
                        '<td>' + attachedDate + '</td>' +
                        '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                            '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                                '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                            '</a>' +
                            '<button type="button" class="btn btn-sm btn-light text-danger delete-cheque-payment-attachment-btn" data-id="' + att.id + '" title="Delete">' +
                                '<i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i>' +
                            '</button>' +
                        '</div></td>' +
                        '</tr>';
                    $tbody.append(row);
                });
            }

            function fetchAndRenderChequePaymentAttachments(paymentId) {
                if (!paymentId || paymentId <= 0) {
                    $('#chequePaymentAttachmentsList').html('<tr><td colspan="4" class="text-center">No attachments available.</td></tr>');
                    return;
                }
                $('#chequePaymentAttachmentsList').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                $.get('{{ url("payment") }}/' + paymentId + '/attachments', function (response) {
                    if (response.success) {
                        renderChequePaymentAttachments(response.attachments);
                    } else {
                        toastr.error('Unable to load attachments.');
                    }
                }).fail(function () {
                    toastr.error('Unable to fetch attachments.');
                });
            }

            $(document).on('click', '.cheque-payment-attachments-btn', function () {
                currentChequePaymentId = parseInt($(this).data('payment-id') || 0, 10);
                $('#chequePaymentAttachmentsMessage').html('');
                $('#chequePaymentAttachmentsFiles').val('');
                $('#chequePaymentAttachmentsModal').modal('show');
                fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
            });

            $('#uploadChequePaymentAttachmentsBtn').on('click', function () {
                if (!currentChequePaymentId || currentChequePaymentId <= 0) {
                    toastr.error('No payment linked.');
                    return;
                }
                var files = $('#chequePaymentAttachmentsFiles')[0].files;
                if (!files.length) {
                    toastr.warning('Please choose at least one file.');
                    return;
                }
                var formData = new FormData();
                formData.append('sys_payment_id', currentChequePaymentId);
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
                var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
                $.ajax({
                    url: '{{ url("payment/attachments/upload") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        if (response.success) {
                            toastr.success('Attachments uploaded successfully.');
                            $('#chequePaymentAttachmentsFiles').val('');
                            fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
                        } else {
                            toastr.error(response.message || 'Upload failed.');
                        }
                    },
                    error: function (xhr) {
                        var err = 'Upload failed.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            err = Object.values(xhr.responseJSON.errors).map(function (v) { return v.join(', '); }).join(' | ');
                        } else if (xhr.responseText) {
                            err = xhr.status + ' ' + xhr.statusText;
                        }
                        toastr.error(err);
                    }
                });
            });

            $(document).on('click', '.delete-cheque-payment-attachment-btn', function () {
                var id = $(this).data('id');
                if (!confirm('Delete this attachment?')) return;
                var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
                $.ajax({
                    url: '{{ url("payment/attachments") }}/' + id + '/delete',
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        if (response.success) {
                            toastr.success('Attachment deleted.');
                            fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
                        } else {
                            toastr.error('Unable to delete attachment.');
                        }
                    },
                    error: function () {
                        toastr.error('Unable to delete attachment.');
                    }
                });
            });
        </script>

        <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
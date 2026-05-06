<?php try { ?>


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'packing-list', 'method' => 'POST', 'id' => 'packing-list']) }}


<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New ({{ @App\SysHelper::get_new_code('sys_packing_list', 'PK', 'doc_number') }})

        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
           

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

             <div class="dropdown">
                <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#ModalExcelPackingList"><i class="ico icon-outline-download text-primary"></i> Import</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">


                <div class="col-3">
                    <label class="form-label">Account (Customer / Supplier)</label>
                    <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                        <option data-display="@lang('Customer')" value="">@lang('Select Account')</option>
                        @foreach ($account as $value)
                            <option value="{{ @$value->id }}"
                                {{ isset($account_id) ? (!empty(@$account_id) ? (@$account_id == @$value->id ? 'selected' : '') : '') : '' }}>
                               {{ @$value->account_name }}

                                

                               @if (Illuminate\Support\Str::startsWith($value->account_code, 'CU'))
                                   @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ $value->account_code }})
                                   @endif
                                @elseif (Illuminate\Support\Str::startsWith($value->account_code, 'SU'))
                                    @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                        ({{ $value->account_code }})
                                    @endif
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>



                <div class="col-1-5">
                    <label class="form-label">Date</label>
                    @php
                        $value = \Carbon\Carbon::parse(old('date') ?? now())->format('d/m/Y');
                    @endphp

                    <div class="form-group">
                        <input type="text" id="date" name="date" class="form-control date-picker"
                            value="{{ @$value }}" />
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">Doc Number</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number"
                            value="{{ @App\SysHelper::get_new_code('sys_packing_list', 'PK', 'doc_number') }}" />
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}">
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>

                        
                    </div>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-2">
                    <label class="form-label">Ref No</label>
                    <input type="text" class="form-control" value="" name="refno" id="refno" required>
                </div>


                <div class="col-1-5">
                    <label class="form-label">Ref Date</label>
                    @php
                        $value = \Carbon\Carbon::parse(old('date') ?? now())->format('d/m/Y');
                    @endphp

                    <div class="form-group">
                        <input type="text" id="refdate" name="refdate" class="form-control date-picker"
                            value="{{ @$value }}" />
                    </div>
                </div>


                <div class="col-3">
                    <label class="form-label">Created By</label>
                    <input type="text" readonly class="form-control"
                        value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
                        name="createdby" id="createdby">
                </div>

                <div class="col">
                    <label class="form-label">Remarks</label>
                    <input type="text" class="form-control" value="" name="remarks" id="remarks" required>
                </div>

            </div>
        </div>
    </div>


    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="40px">@lang('No')<div class="resizer"></div>
                    </th>

                     <th class="resizable text-center" width="200px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="90px">@lang('Box No')<div class="resizer"></div>
                    </th>

                   


                    <th class="resizable text-center" width="80px">@lang('Qty')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('COO')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('H.S Code')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="100px">@lang('Weight')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="120px">@lang('Dimension (L x W x H)')
                        <div class="resizer"></div>
                    </th>

                </tr>
            </thead>
            <tbody>

                @php
                    $i = 1;
                @endphp

                {{-- @if (count($cart) > 0)

                    @foreach ($cart as $dt)
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="{{ $i }}" /></td>

                            <td class="noborder">
                                <select class="form-control noborder " name="part_number[]">
                                    <option value="{{ $dt->part_number }}">
                                        {{ $dt->partno ?? 0 }}</option>
                                </select>
                            </td>
                            <td>
                                <input class="form-control" type="text" value="{{ $dt->description }}"
                                    name="description[]" autocomplete="off">
                                <input class="form-control" type="text" name="part_number_txt[]"
                                    autocomplete="off" readonly="true" hidden>
                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                    readonly="true" hidden>
                                <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                                    readonly="true" hidden>
                                <input class="form-control" type="text" name="product_type_part_number_text[]"
                                    autocomplete="off" readonly="true" hidden>
                            </td>

                            <td><input class="form-control text-center" value="{{ $dt->qty }}" type="text"
                                    name="qty[]" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" value="{{ $dt->unitprice }}" type="text"
                                    name="unitprice[]" step="any" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" value="{{ $dt->value }}" type="number"
                                    name="value[]" autocomplete="off" min="0" readonly>
                            </td>


                            <td><input class="form-control serial-no-column" value="{{ $dt->serialno }}"
                                    type="text" name="serial_no[]"></td>
                            <td><input class="form-control s" type="text" value="{{ $dt->narration }}"
                                    name="narration[]"></td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach



                @endif --}}



                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="{{ $i }}" />
                    </td>

                  

                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td><input class="form-control text-end" type="text" name="box_no[]"></td>


                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>

                    <td><input class="form-control text-end" type="text" name="coo[]"></td>
                    <td><input class="form-control text-end" type="text" name="hscode[]"></td>
                    <td><input class="form-control text-end " type="text" name="weight[]"></td>
                    <td><input class="form-control text-end" type="text" name="dimension[]"></td>

                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"></th>
                    <th class="text-end" scope="col"></th>
                    <th class="text-end"><label id="lbl_total_weight">0</label></th>
                    <th class="text-end" scope="col"></th>
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
</div>

{{ Form::close() }}










{{-- Models --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

@include('backEnd.inventory.itemAddModal')

<div class="modal fade" data-bs-backdrop="false" id="ModalExcelPackingList" tabindex="-1" aria-labelledby="ModalExcelPackingListLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalExcelPackingListLabel">Packing List Excel Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-auto">
                        <label for="packinglist-excel-file" class="col-form-label">Select Excel / CSV file</label>
                    </div>
                    <div class="col-auto">
                        <input class="form-control" type="file" id="packinglist-excel-file" accept=".xlsx,.xls,.csv" />
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-light" onclick="previewPackingListExcel()">Preview</button>
                      
                    </div>
                    <div class="col-auto">
                        <a href="{{ url('public/uploads/product_upload/packing_list_sample_format.csv') }}" target="_blank">(Sample File)</a>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 320px; overflow:auto;">
                    <table id="packinglist-excel-table" class="table table-bordered table-sm mb-0" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Part No</th>
                                <th>Box No</th>
                                <th>Qty</th>
                                <th>COO</th>
                                <th>H.S. Code</th>
                                <th>Weight</th>
                                <th>Dimension</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" id="packinglist-import-btn" class="btn btn-light" onclick="applyPackingListExcelImport()">Import</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script>
    let packingListImportedRows = [];
    let packingListMasterItems = @json($items ?? []);

    function getPackingListFallback(partNo) {
        if (!partNo) {
            return { part_id: '', coo: '', hscode: '', weight: '' };
        }
        var key = partNo.toString().trim().toLowerCase();
        var item = packingListMasterItems.find(function (it) {
            return it.part_number && it.part_number.toString().trim().toLowerCase() === key;
        });
        if (!item) {
            return { part_id: '', coo: '', hscode: '', weight: '' };
        }
        return {
            part_id: item.id || '',
            coo: item.coo || '',
            hscode: item.hscode || '',
            weight: item.weight || ''
        };
    }

    function previewPackingListExcel() {
        var file = document.getElementById('packinglist-excel-file').files[0];
        if (!file) {
            alert('Please select a file first.');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            var data = e.target.result;
            var workbook = XLSX.read(data, { type: 'binary' });
            var sheet = workbook.Sheets[workbook.SheetNames[0]];
            var rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            var tableBody = document.querySelector('#packinglist-excel-table tbody');
            tableBody.innerHTML = '';
            packingListImportedRows = [];

            for (var i = 1; i < rows.length; i++) {
                var row = rows[i];

                if (!row || !row[0] || !row[0].toString().trim()) {
                    continue;
                }

                var partNo = row[0].toString().trim();
                var boxNo = (row[1] || '').toString().trim();
                var qty = row[2] !== undefined ? row[2] : '';
                var coo = (row[3] || '').toString().trim();
                var hscode = (row[4] || '').toString().trim();
                var weight = row[5] !== undefined ? row[5] : '';
                var dimension = (row[6] || '').toString().trim();

                var fallback = getPackingListFallback(partNo);
                if (!coo) coo = fallback.coo;
                if (!hscode) hscode = fallback.hscode;
                if (!weight) weight = fallback.weight;

                packingListImportedRows.push({
                    part_no: partNo,
                    part_id: fallback.part_id,
                    box_no: boxNo,
                    qty: qty,
                    coo: coo,
                    hscode: hscode,
                    weight: weight,
                    dimension: dimension
                });

                var tr = document.createElement('tr');
                if (!fallback.part_id) tr.style.backgroundColor = '#ffe4e1';
                tr.innerHTML = '<td>' + partNo + '</td>' +
                    '<td>' + boxNo + '</td>' +
                    '<td>' + qty + '</td>' +
                    '<td>' + coo + '</td>' +
                    '<td>' + hscode + '</td>' +
                    '<td>' + weight + '</td>' +
                    '<td>' + dimension + '</td>' +
                    '<td><button type="button" class="btn btn-sm btn-light" onclick="removePreviewRow(this)"><i class="ico icon-bold-trash-bin-2" style="font-size: 14px;"></i></button></td>';

                tableBody.appendChild(tr);
            }

            if (packingListImportedRows.length === 0) {
                alert('No valid rows found in file. Ensure Part No is in first column.');
            }
        };
        reader.readAsBinaryString(file);
    }

    function removePreviewRow(button) {
        var row = button.closest('tr');
        var index = Array.from(row.parentNode.children).indexOf(row);
        if (index > -1 && index < packingListImportedRows.length) {
            packingListImportedRows.splice(index, 1);
        }
        row.parentNode.removeChild(row);
    }

    function applyPackingListExcelImport() {
        if (!packingListImportedRows.length) {
            alert('No rows to import. Please preview an Excel file first.');
            return;
        }

        // Build payload arrays
        var payload = {
            part_number: [],
            box_no: [],
            qty: [],
            coo: [],
            hscode: [],
            weight: [],
            dimension: [],
            _token: '{{ csrf_token() }}'
        };

        packingListImportedRows.forEach(function (item) {
            payload.part_number.push(item.part_no);
            payload.box_no.push(item.box_no);
            payload.qty.push(item.qty);
            payload.coo.push(item.coo);
            payload.hscode.push(item.hscode);
            payload.weight.push(item.weight);
            payload.dimension.push(item.dimension);
        });

        var importBtn = document.getElementById('packinglist-import-btn');
        var originalBtnText = importBtn ? importBtn.innerHTML : 'Import';
        if (importBtn) {
            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing';
        }

        $.ajax({
            url: '{{ url("packing-list-cart-excel-add") }}',
            type: 'POST',
            data: payload,
            success: function (response) {
                if (!response.success) {
                    alert('Import failed: ' + (response.message || 'Unknown error'));
                    return;
                }

                var tbody = document.querySelector('#myTable tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));
                var lastDataIndex = -1;

                rows.forEach(function (row, i) {
                    var partVal = row.querySelector('select[name="part_number[]"]')?.value || '';
                    var qtyVal = row.querySelector('input[name="qty[]"]')?.value || '';
                    if (partVal.toString().trim() !== '' || qtyVal.toString().trim() !== '') {
                        lastDataIndex = i;
                    }
                });

                var existingFilledCount = lastDataIndex + 1;
                var insertBefore = rows[lastDataIndex + 1] || null;

                response.data.forEach(function (item, idx) {
                    var row = document.createElement('tr');
                    row.innerHTML = '<td><input type="text" class="form-control text-center" name="sort_id[]" value="' + (existingFilledCount + idx + 1) + '" /></td>' +
                        '<td class="noborder"><select class="form-control noborder" name="part_number[]"><option value="' + (item.part_number || '') + '" selected>' + (item.partno || '') + '</option></select></td>' +
                        '<td><input class="form-control text-end" type="text" name="box_no[]" value="' + (item.boxno || '') + '" /></td>' +
                        '<td><input class="form-control text-center" type="text" name="qty[]" value="' + (item.qty || '') + '" onchange="calc_change_new(this)" /></td>' +
                        '<td><input class="form-control text-end" type="text" name="coo[]" value="' + (item.coo || '') + '" /></td>' +
                        '<td><input class="form-control text-end" type="text" name="hscode[]" value="' + (item.hscode || '') + '" /></td>' +
                        '<td><input class="form-control text-end" type="text" name="weight[]" value="' + (item.weight || '') + '" /></td>' +
                        '<td><input class="form-control text-start" type="text" name="dimension[]" value="' + (item.dimension || '') + '" /></td>';
                    if (insertBefore) {
                        tbody.insertBefore(row, insertBefore);
                    } else {
                        tbody.appendChild(row);
                    }
                });

                $('#ModalExcelPackingList').modal('hide');
                update_totals();

                // Fire the select2 focus initializer on the new row's part selector.
                $('select[name="part_number[]"]').each(function () {
                    if (!$(this).hasClass('js-product-select')) {
                        $(this).addClass('js-product-select');
                    }
                });

                if (typeof toastr !== 'undefined') {
                    toastr.success('Packing list data imported into cart successfully', 'Success');
                } else {
                    alert('Import successful! Cart table updated.');
                }

                if (importBtn) {
                    importBtn.disabled = false;
                    importBtn.innerHTML = originalBtnText;
                }
            },
            error: function (xhr) {
                alert('Import failed: ' + xhr.responseText);
                if (importBtn) {
                    importBtn.disabled = false;
                    importBtn.innerHTML = originalBtnText;
                }
            }
        });
    }
</script>





{{-- Models --}}










<script>
    function splitAmount(modalInputId, targetFieldName) {
        const amount = parseFloat(document.getElementById(modalInputId).value);
        if (isNaN(amount) || amount <= 0) {
            alert("Please enter a valid amount.");
            return;
        }

        const valueFields = document.querySelectorAll('input[name="value[]"]');
        const targetFields = document.querySelectorAll(`input[name="${targetFieldName}[]"]`);

        let totalValue = 0;
        let validRows = [];

        valueFields.forEach((input, index) => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val > 0) {
                totalValue += val;
                validRows.push({
                    index,
                    input
                });
            }
        });

        if (totalValue === 0) {
            alert("All rows have empty or zero 'Value'. Nothing to split.");
            return;
        }

        validRows.forEach(({
            index,
            input
        }) => {
            const rowVal = parseFloat(input.value);
            const share = (rowVal / totalValue) * amount;

            const targetInput = targetFields[index];
            targetInput.value = share.toFixed(2);

            const row = targetInput.closest('tr');
            calc_change_new(row);
        });

        if (typeof update_totals === 'function') {
            update_totals();
        }
    }

    
</script>




<script>
    update_totals();

    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        var unitprice = $row.find('input[name="unitprice[]"]').val() || '0';
        var discount = $row.find('input[name="discount[]"]').val() || '0';
        var fright = $row.find('input[name="fright[]"]').val() || '0';
        var customcharges = $row.find('input[name="customcharges[]"]').val() || '0';

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        // Calculate value
        var fin_value = parseFloat(unitprice) * parseFloat(qty);
        $row.find('input[name="value[]"]').val(fin_value.toFixed(decimal_point));

        // Calculate taxable amount
        var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
        $row.find('input[name="taxableamount[]"]').val(fin_taxableamount.toFixed(decimal_point));

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
        $row.find('input[name="vatamount[]"]').val(fin_vatamount.toFixed(decimal_point));

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
        $row.find('input[name="totalamount[]"]').val(total_amount.toFixed(decimal_point));

        $("#loading_bg").css("display", "none");
        update_totals();
    }

    function update_totals() {
        let total_qty = 0,
            total_price = 0,
            total_value = 0,
            total_discount = 0,
            total_fright = 0,
            total_customcharges = 0,
            total_taxableamount = 0,
            total_vatamount = 0,
            total_totalamount = 0,
            total_weight = 0;

        const decimal_point = @json(session('logged_session_data.decimal_point'));

        $('#myTable tbody tr').each(function() {
            const $row = $(this);

            total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
            total_price += parseFloat($row.find('input[name="unitprice[]"]').val()) || 0;
            total_value += parseFloat($row.find('input[name="value[]"]').val()) || 0;
            total_discount += parseFloat($row.find('input[name="discount[]"]').val()) || 0;
            total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
            total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val()) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val()) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val()) || 0;
            total_weight += parseFloat($row.find('input[name="weight[]"]').val()) || 0;
        });

        $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
        $('#lbl_total_price').text(total_price.toFixed(decimal_point));
        $('#lbl_total_value').text(total_value.toFixed(decimal_point));
        $('#lbl_total_discount').text(total_discount.toFixed(decimal_point));
        $('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
        $('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(total_taxableamount.toFixed(decimal_point));
        $('#lbl_total_vatamount').text(total_vatamount.toFixed(decimal_point));
        $('#lbl_total_totalamount').text(total_totalamount.toFixed(decimal_point));
        $('#lbl_total_weight').text(total_weight.toFixed(decimal_point));
    }
</script>
<script>
    $(document).on('focus', 'select[name="part_number[]"]', function() {
        const $select = $(this);

        // Add the class if not present
        if (!$select.hasClass('js-product-select')) {
            $select.addClass('js-product-select');
            //$select.remove('select2-hidden-accessible');

            // Initialize Select2
            initAccountSelect2(this); // your existing function
        }
    });
</script>

<script>
    $(document).ready(function() {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route('autocomplete.get_product_list_ajax') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search_text: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.part_number,
                                    description: item.description,
                                    coo: item.coo,
                                    hscode: item.hscode,
                                    weight: item.weight,
                                    product_type: item.product_type
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: '',
                minimumInputLength: 2,
                dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
            });

            $(selector).on('select2:select', function(e) {
                var selectedData = e.params.data;
                var $row = $(this).closest('tr'); // find the closest row

                // Populate fields in this row
                $row.find('input[name="coo[]"]').val(selectedData.coo || '');
                $row.find('input[name="hscode[]"]').val(selectedData.hscode || '');
                $row.find('input[name="weight[]"]').val(selectedData.weight || '');

                // Optional: set related metadata fields if present
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="description[]"]').val(selectedData.description || '');

                // after selecting part number, go to qty (or box_no as configured)
                setTimeout(function() {
                    $row.find('input[name="box_no[]"]').focus();
                }, 50);

            });


        }

        initAccountSelect2('.js-product-select');

        // Re-initialize on focus if needed
         // Re-initialize on focus if needed
            $(document).on('focus', '.js-product-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
            $(this).select2('open');

                }
            });


        // On click, open dropdown and focus on search field
        $(document).on('click', '.js-product-select', function() {
            $(this).select2('open');
        });

        // Recalculate totals whenever weight is edited directly
        $(document).on('input', 'input[name="weight[]"]', function() {
            update_totals();
        });

              // some rows may not yet have the js-account-select class, so initialise it manually
    (function openFirstAccount() {
        var $first = $('select[name="part_number[]"]').first();
        if ($first.length) {
            // add class and initialize if not already done
            if (!$first.hasClass('js-product-select')) {
                $first.addClass('js-product-select');
                initAccountSelect2($first);
            }
            // give select2 a moment to render then open dropdown
            setTimeout(function() {
                try { $first.select2('open'); } catch (e) { /* ignore */ }
            }, 50);
        }
    })();

        // Optional: Auto focus on search input when dropdown opens
        $(document).on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
            }, 0);
        });
    });
</script>

<script>
    /*table row fill based on layout height*/
    window.onload = function() {
        const table = document.getElementById('myTable');
        const tbody = table.querySelector('tbody');

        // If there are no rows, do nothing
        if (tbody.rows.length === 0) return;

        const rowHeight = tbody.rows[0].offsetHeight;
        const pageHeight = window.innerHeight - 65;
        const tableTop = table.getBoundingClientRect().top;
        const availableHeight = pageHeight - tableTop;

        let existingRows = tbody.rows.length;
        let totalRows = Math.floor(availableHeight / rowHeight);

        const lastRow = tbody.rows[tbody.rows.length - 1];

        for (let i = existingRows + 1; i <= totalRows; i++) {
            const newRow = lastRow.cloneNode(true); // clone entire row

            const firstCellInput = newRow.cells[0].querySelector('input');
            if (firstCellInput) {
                firstCellInput.value = i;
            }
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach((input, index) => {
                if (index !== 0) input.value = "";
            });

            tbody.appendChild(newRow);
        }
    };
    /*table row fill based on layout height*/
</script>


<script>
    $(document).ready(function() {
        $('#add-btn-modal').on('click', function(e) {
            e.preventDefault();

            var formData = $('#productForm').serialize();

            $.ajax({
                url: "{{ route('product.modalsave') }}", // Update with your route name
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Product saved successfully.');
                        $('#addproductModal').modal('hide'); // optional
                        // Optionally reload table or clear form
                    } else {
                        alert('Something went wrong.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred. Please check console.');
                }
            });
        });

    });
</script>





 <script>

        $(document).on("keydown", 'input[name="box_no[]"], input[name="qty[]"], input[name="coo[]"], input[name="hscode[]"], input[name="weight[]"], input[name="dimension[]"]', function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // prevent form submit

                let row = $(this).closest("tr"); // current row
                let name = $(this).attr("name");

            
               
                if (name === "box_no[]") {
                    row.find('input[name="qty[]"]').focus();
                } 
                else if (name === "qty[]") {
                    row.find('input[name="coo[]"]').focus();
                } 
                 else if (name === "coo[]") {
                    row.find('input[name="hscode[]"]').focus();
                } 
                else if (name === "hscode[]") {
                    row.find('input[name="weight[]"]').focus();
                } 
                else if (name === "weight[]") {
                    row.find('input[name="dimension[]"]').focus();
                } 
                else if (name === "dimension[]") {
                    // Jump to next row's part_number[] and open Select2 dropdown
                    let nextRow = row.next("tr");
                    if (nextRow.length) {
                        let partNumberSelect = nextRow.find('select[name="part_number[]"]');
                        if (partNumberSelect.length) {
                            // Add the js-product-select class so the focus handler can initialize Select2
                            if (!partNumberSelect.hasClass('js-product-select')) {
                                partNumberSelect.addClass('js-product-select');
                            }
                            
                            // Trigger focus - the existing focus handler for .js-product-select 
                            // will initialize Select2 and open the dropdown automatically
                            partNumberSelect.trigger('focus');
                        }
                    }
                }
                
            }
        });

        
    </script>



<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

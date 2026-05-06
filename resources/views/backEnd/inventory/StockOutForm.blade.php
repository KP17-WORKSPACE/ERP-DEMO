<?php try { ?>


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-out', 'method' => 'POST', 'id' => 'tender-create-form']) }}

<input type="hidden" name="stock_out_id" id="stock_out_id" value="0">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New

            <span id="SH_DOC" class="d-none font-weight-600">
                ({{ @App\SysHelper::get_new_code('sys_stock_out', 'SH', 'doc_number') }})</span>
            <span id="DO_DOC" class="d-none font-weight-600">
                ({{ @App\SysHelper::get_new_code('sys_stock_out', 'DO', 'doc_number') }})</span>
            <span id="RO_DOC" class="d-none font-weight-600">
                ({{ @App\SysHelper::get_new_code('sys_stock_out', 'RO', 'doc_number') }})</span>


        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">
                    <li data-bs-toggle="modal" data-bs-target="#addpoexcelimport"><a href="#"
                            class="dropdown-item">
                            <i class="ico icon-outline-import text-success"></i>
                            Import</a></li>


                </ul>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
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



                <div class="col-1-5">
                    <label class="form-label">Mode</label>

                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="mode" id="mode">
                            <option value="SH" selected>Shortage Stock</option>
                            <option value="DO">Demo Out</option>
                            <option value="RO">RMA Out</option>

                        </select>

                        
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        function toggleRMAFields() {
                            let mode = $('#mode').val();

                            // Always reset first
                            $('#RO_DOC, #DO_DOC, #SH_DOC').addClass('d-none');
                            $('.rmaFields').addClass('d-none').find('input').val('');

                            if (mode === 'RO') {
                                $('.rmaFields').removeClass('d-none');
                                $('#RO_DOC').removeClass('d-none');
                                $('#doc_number').val($('#RO_DOC').text().trim().replace(/[()]/g, ''));
                            } else if (mode === 'DO') {

                                $('#DO_DOC').removeClass('d-none');
                                $('#doc_number').val($('#DO_DOC').text().trim().replace(/[()]/g, ''));
                            } else if (mode === 'SH') {
                                $('#SH_DOC').removeClass('d-none');
                                $('#doc_number').val($('#SH_DOC').text().trim().replace(/[()]/g, ''));
                            }
                        }

                        // Run on page load (for edit forms)
                        toggleRMAFields();

                        // Listen for changes
                        $('#mode').on('change', function() {
                            toggleRMAFields();
                        });
                    });
                </script>




                <div id="" class="col-3 d-none rmaFields">

                    <label class="form-label">Customer
                    </label>
                    <select class="form-control js-example-basic-single" name="customer_id" id="customer_id">
                        <option value="">-Select-</option>
                        @foreach ($customers as $value)
                            <option value="{{ @$value->id }}">
                                {{ @$value->account_code }} - {{ @$value->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="" class="col-3 d-none rmaFields">

                    <label class="form-label">Supplier
                    </label>
                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="supplier_id" id="supplier_id">
                            <option value="">-Select-</option>
                            @foreach ($suppliers as $value)
                                <option value="{{ @$value->id }}">
                                    {{ @$value->account_code }} - {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-1-5">
                    <label class="form-label">Doc Number</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number"
                            value="{{ @App\SysHelper::get_new_code('sys_stock_out', 'SH', 'doc_number') }}" />
                    </div>
                </div>

                <div class="col-1-5">
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
                    <label class="form-label">Created By</label>
                    <input type="text" readonly class="form-control"
                        value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
                        name="createdby" id="createdby">
                </div>

                <div class="col-4">
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
                    <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="300px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="350px">@lang('Description')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="100px">@lang('Qty')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Price')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Value')
                        <div class="resizer"></div>
                    </th>


                    <th class="resizable text-center serial-no-column" width="200px">@lang('Serial No')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center serial-no-column" width="200px">@lang('Narration')
                        <div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>

                @php
                    $i = 1;
                @endphp

                @if (count($cart) > 0)

                    @foreach ($cart as $dt)
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="{{ $i }}" /></td>

                            <td class="noborder">
                                <select class="form-control noborder " name="part_number[]">
                                    <option value="{{ $dt->part_number }}">
                                        {{ $dt->partno ?? 0 }}</option>
                                </select>
                                {{-- on focus add this class and its funcanalities js-product-select --}}
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
                                    onchange="calc_change_new(this)" onkeydown="return set_license_key_normal(event, this)"></td>
                            <td><input class="form-control text-end" value="{{ $dt->unitprice }}" type="text"
                                    name="unitprice[]" step="any" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" value="{{ $dt->value }}" type="text"
                                    name="value[]" autocomplete="off" min="0" readonly>
                            </td>


                            <td><input class="form-control serial-no-column" value="{{ $dt->serialno }}"
                                    type="text" name="serial_no[]"></td>
                            <td><input class="form-control text-start" type="text" value="{{ $dt->narration }}"
                                    name="narration[]"></td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach



                @endif



                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="{{ $i }}" />
                    </td>

                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td>
                        <input class="form-control" type="text" name="description[]" autocomplete="off">
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>

                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeydown="return set_license_key_normal(event, this)"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly>
                    </td>


                    <td><input class="form-control serial-no-column" type="text" name="serial_no[]"></td>
                    <td><input class="form-control text-start" type="text" name="narration[]"></td>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>

                    <th class="text-end" scope="col"></th>
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

<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Narration</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea" rows="6"
                            placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="AddNarration()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="height: 300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Serial No</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Serial No:</label>
                                <div class="form-group">
                                    <textarea type="text" class="form-control" id="add_serial_no" style="height: 150px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="addSerialNo()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal side-panel fade" id="descriptionModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="height: 300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Description</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Description:</label>
                                <div class="form-group">
                                    <textarea type="text" class="form-control" id="add_description" style="height: 150px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="addDescription()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Models --}}

<!-- Modal License Key-->
<button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>
<div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="ModalLicenseKey" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select License Key <label style="margin-left: 68px"
                        id="ModalLabelHeading"></label> <span style="margin-left: 116px">Available Qty</span> -
                    <label id="total_key">0</label>
                </h5>
                <input type="hidden" id="part_no" />
                <input type="hidden" id="update_id" />
                <input type="hidden" id="license_qty_limit" value="0" />
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="popup_close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="edit_license_id" value="" />
                        <input type="number" class="form-control" name="license_qty" id="license_qty"
                            value="1" readonly />
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Selected: <label id="selected_key">0</label> of <label
                                id="license_qty_cap">0</label></label>
                        <input type="text" id="license_key_search" placeholder="Search license key..."
                            class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <table id="lk-table" class="table table-hover long-list" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 5%;">Select</th>
                                    <th style="width: 30%;" class="text-start">Licence Key</th>
                                    <th style="width: 15%;">Expiry Date</th>
                                    <th style="width: 12%;">Doc No</th>
                                    <th style="width: 10%;">Doc Date</th>
                                    <th style="width: 13%;">Name</th>
                                    <th style="width: 15%;">Bill Number</th>
                                    <th style="width: 15%;">Deal ID</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="set_license_key()" type="button" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add Selected
                </button>
            </div>
        </div>
    </div>
</div>





<div class="modal  fade" id="addpoexcelimport" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-out-cart-excel-add', 'method' => 'POST', 'id' => 'stock-out-cart-excel-add']) }}

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Items Excel Import</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <label for="excel-file" class="form-label mb-0">Select File (.csv)</label>
                            </div>

                            <div class="col-auto">
                                <input class="form-control" type="file" id="excel-file"
                                    accept=".xlsx, .xls, .csv">
                            </div>

                            <div class="col-auto">
                                <button type="button" onclick="readExcel()" class="btn btn-light">Preview</button>
                            </div>

                            <div class="col-auto">
                                <small>(<a
                                        href="{{ url('public/uploads/product_upload/stock_out_sample_format.csv') }}"
                                        target="_blank">Sample File</a>)</small>
                            </div>

                            <div class="col-md-12 mt-2">
                                <table id="excel-table" class="table table-bordered table-striped"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:220px;">Part No</th>
                                            <th>Description</th>
                                            <th style="width:70px;">Qty</th>
                                            <th style="width:100px;" class="text-right">Unit Price</th>
                                            <th style="width:100px;" class="text-left">Serial No</th>
                                            <th style="width:100px;" class="text-left">Narration</th>
                                            <th style="width:50px;" class="text-right"></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>


                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
        {{ Form::close() }}


    </div>
</div>

<script>
    let narrationModal;
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('narrationModal');
        narrationModal = new bootstrap.Modal(modalElement);

        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function() {
                const textarea = document.getElementById('narrationTextarea');
                if (textarea) {
                    textarea.focus();
                }
            });
        }
    });
    let currentSerialInput2 = null;

    $(document).on('click', 'input[name="narration[]"]', function() {
        currentSerialInput2 = $(this);
        $('#narrationTextarea').val(currentSerialInput2.val());
        narrationModal.show();
    });

    function AddNarration() {
        if (currentSerialInput2) {
            const val = $('#narrationTextarea').val();
            currentSerialInput2.val(val);
            narrationModal.hide();
            currentSerialInput2 = null;
        }
    }
</script>


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

    document.getElementById("discount_add_btn").addEventListener("click", function() {
        splitAmount('discountInput', 'discount');
        $('#discountModal').modal('hide');
    });

    document.getElementById("freight_add_btn").addEventListener("click", function() {
        splitAmount('freightInput', 'fright');
        $('#freightModal').modal('hide');
    });

    document.getElementById("custom_add_btn").addEventListener("click", function() {
        splitAmount('customCharges', 'customcharges');
        $('#customModal').modal('hide');
    });
</script>

<script>
    let serialNoModal;
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);

        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function() {
                const txt = document.getElementById('add_serial_no');
                if (txt) {
                    txt.focus();
                   
                }
            });
        }
    });
    let currentSerialInput = null;

    $(document).on('click', 'input[name="serial_no[]"]', function() {
        currentSerialInput = $(this);
        $('#add_serial_no').val(currentSerialInput.val());
        serialNoModal.show();
    });

    function addSerialNo() {
        if (currentSerialInput) {
            const val = $('#add_serial_no').val();
            currentSerialInput.val(val);
            serialNoModal.hide();
            currentSerialInput = null;
        }
    }
</script>
<script>
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function() {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);

        if (descriptionElement) {
            descriptionElement.addEventListener('shown.bs.modal', function() {
                const txt = document.getElementById('add_description');
                if (txt) {
                    txt.focus();
                    
                }
            });
        }
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'input[name="description[]"]', function() {
        currentDescriptionInput = $(this);
        $('#add_description').val(currentDescriptionInput.val());
        descriptionModal.show();
    });

    function addDescription() {
        if (currentDescriptionInput) {
            const val = $('#add_description').val();
            currentDescriptionInput.val(val);
            descriptionModal.hide();
            currentDescriptionInput = null;
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
        var unitpriceRaw = ($row.find('input[name="unitprice[]"]').val() || '').toString().replace(/,/g, '');
        var discountRaw = ($row.find('input[name="discount[]"]').val() || '0').toString().replace(/,/g, '');
        var frightRaw = ($row.find('input[name="fright[]"]').val() || '0').toString().replace(/,/g, '');
        var customchargesRaw = ($row.find('input[name="customcharges[]"]').val() || '0').toString().replace(/,/g, '');

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        var unitprice = unitpriceRaw.trim() === '' ? null : parseFloat(unitpriceRaw);
        var discount = discountRaw.trim() === '' ? 0 : parseFloat(discountRaw);
        var fright = frightRaw.trim() === '' ? 0 : parseFloat(frightRaw);
        var customcharges = customchargesRaw.trim() === '' ? 0 : parseFloat(customchargesRaw);

        // Format unit price (leave blank when no valid unitprice)
        if (unitprice === null || !Number.isFinite(unitprice)) {
            $row.find('input[name="unitprice[]"]').val('');
            unitprice = 0;
        } else {
            if (typeof formatAmount === 'function') {
                $row.find('input[name="unitprice[]"]').val(formatAmount(unitprice));
            } else {
                $row.find('input[name="unitprice[]"]').val(unitprice.toFixed(decimal_point));
            }
        }

        // Calculate value
        var fin_value = unitprice * parseFloat(qty);
        $row.find('input[name="value[]"]').val(typeof formatAmount === 'function' ? formatAmount(fin_value) : fin_value.toFixed(decimal_point));

        // Calculate taxable amount
        var fin_taxableamount = fin_value + customcharges + fright - discount;
        $row.find('input[name="taxableamount[]"]').val(typeof formatAmount === 'function' ? formatAmount(fin_taxableamount) : fin_taxableamount.toFixed(decimal_point));

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
        $row.find('input[name="vatamount[]"]').val(typeof formatAmount === 'function' ? formatAmount(fin_vatamount) : fin_vatamount.toFixed(decimal_point));

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
        $row.find('input[name="totalamount[]"]').val(typeof formatAmount === 'function' ? formatAmount(total_amount) : total_amount.toFixed(decimal_point));

        $("#loading_bg").css("display", "none");
        update_totals();
    }

    function update_totals() {
        function parseRowNumber($elem) {
            if (!$elem || !$elem.length) return 0;
            let val = $elem.val();
            if (val === undefined || val === null || val === '') return 0;
            val = val.toString().replace(/,/g, '');
            let num = parseFloat(val);
            return Number.isFinite(num) ? num : 0;
        }

        let total_qty = 0,
            total_price = 0,
            total_value = 0,
            total_discount = 0,
            total_fright = 0,
            total_customcharges = 0,
            total_taxableamount = 0,
            total_vatamount = 0,
            total_totalamount = 0;

        const decimal_point = @json(session('logged_session_data.decimal_point'));

        $('#myTable tbody tr').each(function() {
            const $row = $(this);

            total_qty += parseRowNumber($row.find('input[name="qty[]"]')) || 0;
            total_price += parseRowNumber($row.find('input[name="unitprice[]"]')) || 0;
            total_value += parseRowNumber($row.find('input[name="value[]"]')) || 0;
            total_discount += parseRowNumber($row.find('input[name="discount[]"]')) || 0;
            total_fright += parseRowNumber($row.find('input[name="fright[]"]')) || 0;
            total_customcharges += parseRowNumber($row.find('input[name="customcharges[]"]')) || 0;
            total_taxableamount += parseRowNumber($row.find('input[name="taxableamount[]"]')) || 0;
            total_vatamount += parseRowNumber($row.find('input[name="vatamount[]"]')) || 0;
            total_totalamount += parseRowNumber($row.find('input[name="totalamount[]"]')) || 0;
        });

        $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
        $('#lbl_total_price').text(typeof formatAmount === 'function' ? formatAmount(total_price) : total_price.toFixed(decimal_point));
        $('#lbl_total_value').text(typeof formatAmount === 'function' ? formatAmount(total_value) : total_value.toFixed(decimal_point));
        $('#lbl_total_discount').text(typeof formatAmount === 'function' ? formatAmount(total_discount) : total_discount.toFixed(decimal_point));
        $('#lbl_total_fright').text(typeof formatAmount === 'function' ? formatAmount(total_fright) : total_fright.toFixed(decimal_point));
        $('#lbl_total_customcharges').text(typeof formatAmount === 'function' ? formatAmount(total_customcharges) : total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(typeof formatAmount === 'function' ? formatAmount(total_taxableamount) : total_taxableamount.toFixed(decimal_point));
        $('#lbl_total_vatamount').text(typeof formatAmount === 'function' ? formatAmount(total_vatamount) : total_vatamount.toFixed(decimal_point));
        $('#lbl_total_totalamount').text(typeof formatAmount === 'function' ? formatAmount(total_totalamount) : total_totalamount.toFixed(decimal_point));
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
                                    hscode: item.hscode,
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

                // Set values using "name" attribute selectors inside the same row
                $row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                    .description || '');
                $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="fright[]"]').val(0);
                $row.find('input[name="customcharges[]"]').val(0);
                $row.find('input[name="tax[]"]').val($('#net_vat').val());

                   // after selecting part number, go to qty
                    setTimeout(function() {
                        $row.find('input[name="qty[]"]').focus();
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


        // On click, open dropdown and focus on search field
        $(document).on('click', '.js-product-select', function() {
            $(this).select2('open');
        });

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





<?php
$part_number = $items->pluck('part_number');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script>
    function readExcel() {
        var file = document.getElementById('excel-file').files[0];
        if (!file) {
            alert("Please select an Excel file.");
            return;
        }

        var reader = new FileReader();
        reader.onload = function(event) {
            var data = event.target.result;
            var workbook = XLSX.read(data, {
                type: 'binary'
            });

            // Assuming the data is in the first sheet
            var sheet = workbook.Sheets[workbook.SheetNames[0]];
            var rows = XLSX.utils.sheet_to_json(sheet, {
                header: 1
            });

            var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ""; // Clear any previous data

            // Loop through each row and add data to the table
            for (var i = 1; i < rows.length; i++) { // Skip header row
                var row = rows[i];
                if (row.length < 6) continue; // Skip invalid rows



                var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                var lowercase_part_number = part_number.map(function(value) {
                    return value.toLowerCase();
                });

                var json_output = JSON.stringify(lowercase_part_number);

                var newRow = tableBody.insertRow(tableBody.rows.length);

                var rowVal = String(row[0] ?? '');
                var trimmedValue = rowVal.trim();

                if (json_output.includes(trimmedValue.toLowerCase())) { // Use .includes() for array checking

                } else {
                    newRow.style.backgroundColor = "#ffbebe";
                }

                // Part No
                var partNoCell = newRow.insertCell(0);
                var partNoInput = document.createElement('input');
                partNoInput.type = 'text'; // Change to text input
                partNoInput.name = 'excel_part_no[]';
                partNoInput.value = rowVal.trim();
                partNoInput.classList.add('form-control');
                partNoCell.appendChild(partNoInput);

                // Description
                var descriptionCell = newRow.insertCell(1);
                var descriptionInput = document.createElement('input');
                descriptionInput.type = 'text'; // Change to text input
                descriptionInput.name = 'excel_description[]';
                descriptionInput.value = (row[1] || '').toString().trim();
                descriptionInput.classList.add('form-control');
                descriptionCell.appendChild(descriptionInput);

                // Qty
                var qtyCell = newRow.insertCell(2);
                var qtyInput = document.createElement('input');
                qtyInput.type = 'text'; // Change to text input
                qtyInput.name = 'excel_qty[]';
                qtyInput.value = row[2];
                qtyInput.classList.add('form-control');
                qtyCell.appendChild(qtyInput);

                // Unit Price (Right-aligned)
                var unitPriceCell = newRow.insertCell(3);
                var unitPriceInput = document.createElement('input');
                unitPriceInput.type = 'text'; // Change to text input
                unitPriceInput.name = 'excel_unit_price[]';
                unitPriceInput.value = row[3];
                unitPriceInput.classList.add('text-right');
                unitPriceInput.classList.add('form-control');
                unitPriceCell.appendChild(unitPriceInput);

                // Discount (Right-aligned)
                var serialnoCell = newRow.insertCell(4);
                var serialnoInput = document.createElement('input');
                serialnoInput.type = 'text'; // Change to text input
                serialnoInput.name = 'excel_serial_no[]';
                serialnoInput.value = row[4];
                serialnoInput.classList.add('text-left');
                serialnoInput.classList.add('form-control');
                serialnoCell.appendChild(serialnoInput);

                // VAT (Right-aligned)
                var narrationCell = newRow.insertCell(5);
                var narrationInput = document.createElement('input');
                narrationInput.type = 'text'; // Change to text input
                narrationInput.name = 'excel_narration[]';
                narrationInput.value = row[5];
                narrationInput.classList.add('text-left');
                narrationInput.classList.add('form-control');
                narrationCell.appendChild(narrationInput);

                var deleteCell = newRow.insertCell(6); // Last cell for delete button
                var deleteButton = document.createElement('button');
                deleteButton.type = 'button'; // Make sure the button doesn't submit a form
                deleteButton.textContent = 'Delete';
                           deleteButton.classList.add('btn-sm', 'btn-light');
                                                deleteButton.innerHTML = '<i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>';

                deleteButton.onclick = function() {
                    // Delete the row when the button is clicked
                    var rowToDelete = this.parentNode.parentNode;
                    rowToDelete.remove();
                };
                deleteCell.appendChild(deleteButton);

            }
        };
        reader.readAsBinaryString(file);
    }
</script>


<script>
    function filterLicenseRows() {
        var query = ($('#license_key_search').val() || '').toString().toLowerCase().trim();
        $('#lk-table tbody tr').each(function() {
            var rowText = ($(this).text() || '').toLowerCase();
            $(this).toggle(query === '' || rowText.indexOf(query) !== -1);
        });
    }

    function stockOutLicenseSetSerialTargetFromRow($row) {
        window.stockOutLicenseSerialInput = null;
        if ($row && $row.length) {
            var $el = $row.find('input[name="serial_no[]"]').first();
            if ($el.length) {
                window.stockOutLicenseSerialInput = $el;
            }
        }
    }

    function stockOutLicenseResolveSerialInput() {
        var $inp = window.stockOutLicenseSerialInput;
        if ($inp && $inp.length) {
            return $inp;
        }
        var partId = ($('#part_no').val() || '').toString().trim();
        if (!partId) {
            return $();
        }
        var $found = $();
        $('#myTable tbody tr').each(function() {
            var $sel = $(this).find('select[name="part_number[]"]').first();
            if (!$sel.length) return;
            if (($sel.val() || '').toString().trim() !== partId) return;
            $found = $(this).find('input[name="serial_no[]"]').first();
            if ($found.length) return false;
        });
        return $found;
    }

    function stockOutLicenseAppendSelectedKeysToSerial() {
        var keys = [];
        $('#lk-table tbody tr').each(function() {
            var $tr = $(this);
            if (!$tr.find('.chk_key').is(':checked')) return;
            var $cells = $tr.find('td');
            if ($cells.length < 2) return;
            var t = $cells.eq(1).text().replace(/\s+/g, ' ').trim();
            if (t) keys.push(t);
        });
        var $inp = stockOutLicenseResolveSerialInput();
        if ($inp && $inp.length) {
            $inp.val(keys.join(', '));
        }
    }

    function set_license_key_normal(e, el) {
        e = e || window.event;
        var key = e.which || e.keyCode;
        if (key !== 13) return true;

        var $row = $(el).closest("tr");
        var partId = $row.find('select[name="part_number[]"] option:selected').val();
        var pt = $row.find('input[name="product_type[]"]').val();
        var isLicenseType = parseInt(pt, 10) === 2;
        var hasValidPart = partId !== undefined && partId !== null && String(partId).trim() !== '';
        if (!isLicenseType && !hasValidPart) return true;
        if (!hasValidPart) {
            toastr.warning('Select a part number before assigning license keys.');
            e.preventDefault();
            return false;
        }

        $('#part_no').val(partId);
        stockOutLicenseSetSerialTargetFromRow($row);
        var rowQty = parseInt($(el).val(), 10) || 0;
        $('#license_qty_limit').val(rowQty);
        $('#license_qty').val(rowQty);
        $('#license_qty_cap').text(rowQty);
        $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected').text());
        $("#btn_ModalLicenseKey").click();
        get_license_key(String(partId));
        e.preventDefault();
        return false;
    }

    function get_license_key(part_id) {
        $("#loading_bg").css("display", "block");
        var stockOutId = parseInt($('#stock_out_id').val() || 0, 10);
        var action = "{{ URL::to('stock-out-get-dn-license-key') }}";
        var requestData = {
            _token: '{{ csrf_token() }}',
            item_id: part_id
        };
        var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
        $('#license_qty').val(qtyLimit);
        $('#license_qty_cap').text(qtyLimit);
        if (stockOutId > 0) {
            requestData.stock_out_id = stockOutId;
        }

        $.ajax({
            url: action,
            type: "POST",
            data: requestData,
            cache: false,
            success: function(dataResult) {
                try {
                    dataResult = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                } catch (err) {
                    toastr.error('Could not load license keys.');
                    $('#lk-table tbody').empty();
                    $('#selected_key').text(0);
                    $('#total_key').text(0);
                    return;
                }

                var rows = dataResult.data || [];
                var selectedCount = 0;
                var rowsHtml = "";
                $('#total_key').text(rows.length);

                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var isSelected = stockOutId > 0 ? (Number(row.status) === 2 && Number(row.stock_out_id) === stockOutId) : (Number(row.stock_out_id) === -1);
                    if (isSelected) selectedCount++;
                    var isSalesReturn = parseInt(row.sales_return_id, 10) > 0;
                    var isStockIn = !isSalesReturn && parseInt(row.type, 10) === 3;
                    var isOpeningStock = !isSalesReturn && !isStockIn && parseInt(row.opening_stock_id, 10) > 0;
                    var docNo = isSalesReturn ? (row.sr_doc_number || '') : (isStockIn ? (row.stkin_doc_number || '') : (isOpeningStock ? (row.ops_doc_number || '') : (row.grn_no || '')));
                    var docDate = isSalesReturn ? (row.sr_doc_date ? get_format_date(row.sr_doc_date) : '') :
                        (isStockIn ? (row.stkin_doc_date ? get_format_date(row.stkin_doc_date) : '') : (isOpeningStock ? (row.ops_doc_date ? get_format_date(row.ops_doc_date) : '') : (row.grn_date ? get_format_date(row.grn_date) : '')));
                    var partyName = isSalesReturn ? (row.sr_customer_name || '') : (isStockIn ? 'Stock In' : (isOpeningStock ? 'Opening Stock' : (row.supplier_name || '')));
                    var billNumber = isSalesReturn ? (row.sr_lpo_number || '') : ((isOpeningStock || isStockIn) ? '' : (row.grn_bill_number || ''));
                    var dealId = isSalesReturn ? (row.sr_deal_code || row.sr_deal_id || '') : ((isOpeningStock || isStockIn) ? '' : (row.grn_deal_code || row.grn_deal_id || ''));
                    var grnDocUrlBase = "{{ URL::to('get-url-purchase-grn') }}";
                    var srDocUrlBase = "{{ URL::to('get-url-sales-return') }}";
                    var opsEditUrlBase = "{{ URL::to('item-store') }}";
                    var stkinEditUrlBase = "{{ URL::to('get-url-stock-in') }}";
                    var docUrl = '';
                    if (docNo) {
                        if (isSalesReturn) {
                            docUrl = srDocUrlBase + "/" + encodeURIComponent(docNo);
                        } else if (isStockIn) {
                            docUrl = stkinEditUrlBase + "/" + encodeURIComponent(docNo);
                        } else if (isOpeningStock) {
                            var opsId = parseInt(row.opening_stock_id, 10) || 0;
                            if (opsId > 0) docUrl = opsEditUrlBase + "/" + opsId + "/edit";
                        } else {
                            docUrl = grnDocUrlBase + "/" + encodeURIComponent(docNo);
                        }
                    }
                    var safeDocNo = $('<div>').text(docNo || '').html();
                    var docNoHtml = docNo ? (docUrl ? ("<a href='" + docUrl + "' target='_blank' rel='noopener noreferrer'>" + safeDocNo + "</a>") : safeDocNo) : '';
                    rowsHtml += "<tr class='text-center' data-lk-status=\"" + (row.status != null ? row.status : '') + "\">\
                        <td><input class='chk_key' type='checkbox' id='select_key_" + Number(i + 1) + "' onclick='key_select_change(" + Number(i + 1) + ")'" + (isSelected ? ' checked' : '') + " /><input type='hidden' id='item_key_id_" + Number(i + 1) + "' value='" + row.id + "' /></td>\
                        <td class='text-start'>" + (row.license_key || "") + "</td>\
                        <td>" + (row.exp_date ? get_format_date(row.exp_date) : "") + "</td>\
                        <td>" + docNoHtml + "</td>\
                        <td>" + docDate + "</td>\
                        <td class='text-start'>" + partyName + "</td>\
                        <td>" + billNumber + "</td>\
                        <td>" + dealId + "</td>\
                    </tr>";
                }
                $('#lk-table tbody').empty().append(rowsHtml);
                filterLicenseRows();
                $('#selected_key').text(selectedCount);
                key_select_change(0);
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function key_select_change(id) {
        var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
        if (id > 0) {
            var nowSelected = $('.chk_key:checked').length;
            if (qtyLimit > 0 && nowSelected > qtyLimit) {
                $('#select_key_' + id).prop('checked', false);
                toastr.error('Only ' + qtyLimit + ' license keys can be selected for this item quantity.');
            }
        }
        var selected = 0;
        var b = 1;
        var itm_id = 0;
        $(".chk_key").each(function() {
            if (this.checked) {
                selected = Number(selected + 1);
                if (itm_id == 0) {
                    itm_id = $('#item_key_id_' + b).val();
                } else {
                    itm_id += ',' + $('#item_key_id_' + b).val();
                }
            }
            b++;
        });
        $('#update_id').val(itm_id);
        $('#selected_key').text(selected);
    }

    function set_license_key() {
        $("#loading_bg").css("display", "block");
        var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
        var selectedCount = $('.chk_key:checked').length;
        if (qtyLimit > 0 && selectedCount > qtyLimit) {
            toastr.error('Only ' + qtyLimit + ' license keys can be selected for this item quantity.');
            $("#loading_bg").css("display", "none");
            return false;
        }
        var stagingIds = [];
        var keepStockOutKeyIds = [];
        $('.chk_key:checked').each(function() {
            var st = parseInt($(this).closest('tr').attr('data-lk-status'), 10);
            var hid = $(this).closest('td').find('input[type="hidden"]').val();
            if (!hid) return;
            if (st === 1) stagingIds.push(hid);
            else if (st === 2) keepStockOutKeyIds.push(hid);
        });

        var requestData = {
            _token: '{{ csrf_token() }}',
            id: stagingIds.join(','),
            item_id: $('#part_no').val(),
            qty_limit: qtyLimit
        };
        var stockOutId = parseInt($('#stock_out_id').val() || 0, 10);
        if (stockOutId > 0) {
            requestData.stock_out_id = stockOutId;
            requestData.keep_stock_out_key_ids = keepStockOutKeyIds.join(',');
        }

        $.ajax({
            url: "{{ URL::to('stock-out-update-dn-license-key') }}",
            type: "POST",
            data: requestData,
            cache: false,
            success: function(dataResult) {
                try {
                    dataResult = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                } catch (err) {
                    toastr.error('Unexpected response from server.');
                    return;
                }
                if (dataResult.error) {
                    toastr.error(dataResult.error);
                    return;
                }
                stockOutLicenseAppendSelectedKeysToSerial();
                $('#popup_close').click();
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    $(document).ready(function() {
        $(document).on('click', '#lk-table > tbody > tr', function(e) {
            if ($(e.target).closest('table').attr('id') !== 'lk-table') return;
            if ($(e.target).closest('td').hasClass('no-toggle')) return;
            $(this).toggleClass('expand');
        });
        $(document).on('input keyup change', '#license_key_search', function() {
            filterLicenseRows();
        });
        $(document).on('shown.bs.modal', '#ModalLicenseKey', function() {
            $('#license_key_search').val('');
            filterLicenseRows();
            setTimeout(function() {
                $('#license_key_search').focus();
            }, 50);
        });
    });
</script>


  <script>

        $(document).on("keydown", 'input[name="description[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="serial_no[]"], input[name="narration[]"]', function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // prevent form submit

                let row = $(this).closest("tr"); // current row
                let name = $(this).attr("name");

            
               
                if (name === "qty[]") {
                    if (set_license_key_normal(e, this) === false) {
                        return;
                    }
                    row.find('input[name="unitprice[]"]').focus();
                } 
                else if (name === "unitprice[]") {
                    row.find('input[name="serial_no[]"]').focus();
                } 
                 else if (name === "serial_no[]") {
                    row.find('input[name="narration[]"]').focus();
                } 
                else if (name === "narration[]") {
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

<script>
    $(document).ready(function() {
        $('#tender-create-form').on('keypress', function(e) {
            if (e.which === 13 && !$(e.target).is('textarea')) {
                e.preventDefault();
            }
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

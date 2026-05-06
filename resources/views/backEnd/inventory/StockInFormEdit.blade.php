<?php try { ?>


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in/' . $edit->id, 'method' => 'PUT', 'id' => 'tender-create-form']) }}

<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
<input type="hidden" name="stock_in_id" id="stock_in_id" value="{{ $edit->id }}">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit ({{ $edit->doc_number }})

        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <a class="btn btn-light" href="{{ url('stock-in/' . @$edit->id . '?stockin_action=add') }}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
            </button>



        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-1-5">
                    <label class="form-label">Date</label>
                    @php
                        $value = \Carbon\Carbon::parse($edit->date ?? now())->format('d/m/Y');
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

                            <option value="ES" @if ($mode == 'ES') selected @endif>Excess Stock
                            </option>
                            <option value="DI" @if ($mode == 'DI') selected @endif>Demo In</option>
                            <option value="RI" @if ($mode == 'RI') selected @endif>RMA In</option>

                        </select>

                     
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        function toggleRMAFields() {
                            let mode = $('#mode').val();
                            // Always reset first
                            $('#RI_DOC, #DI_DOC, #ES_DOC').addClass('d-none');
                            $('.rmaFields').addClass('d-none').find('input').val('');
                            if (mode === 'RI') {
                                $('.rmaFields').removeClass('d-none');
                                $('#RI_DOC').removeClass('d-none');
                            } else if (mode === 'DI') {

                                $('#DI_DOC').removeClass('d-none');
                            } else if (mode === 'ES') {
                                $('#ES_DOC').removeClass('d-none');
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
                            <option @if ($edit->customer_id == $value->id) selected @endif value="{{ @$value->id }}">
                           {{ @$value->account_name }}

                             @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                    ({{ @$value->account_code }})
                                     
                                 @endif
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
                                <option @if ($edit->supplier_id == $value->id) selected @endif value="{{ @$value->id }}">
                                    {{ @$value->account_name }} 
                                     @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                        ({{ @$value->account_code }})
                                         @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-1-5">
                    <label class="form-label">Doc Number</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number" value="{{ $edit->doc_number }}" />
                    </div>
                    <input type="hidden" name="current_mode" value="{{ $mode }}">
                </div>

                
                <div class="col-1-5">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($edit->currancy == $value->id) selected @endif>
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
                    <input type="text" class="form-control" value="{{ $edit->remarks }}" name="remarks"
                        id="remarks">
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
                @if (count($edit_items) > 0)
                    @foreach ($edit_items as $dt)
                        <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->partno }}" />
                        <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" />
                        <input type="hidden" id="deal_ref_id_{{ $dt->id }}" value="{{ $dt->refid }}" />
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
                            <td><input class="form-control text-center" data-enter-skip type="text" name="qty[]"
                                    autocomplete="off" value="{{ $dt->qty }}" min="0"
                                    onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>
                            <td><input value="{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }}" class="form-control text-end" type="text"
                                    name="unitprice[]" step="any" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" type="text" name="value[]"
                                    autocomplete="off" value="{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }}" min="0" readonly>
                            </td>                                        
                            <td><input class="form-control text-start serial-no-column" value="{{ $dt->serialno }}"
                                    type="text" name="serial_no[]"></td>
                            <td><input class="form-control text-start" id="reference" type="text"
                                    value="{{ $dt->narration }}" name="narration[]"></td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                @endif

                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="{{ $i }}" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
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
                    <td><input class="form-control text-center" data-enter-skip type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly>
                    </td>
                    <td><input class="form-control serial-no-column text-start" type="text" name="serial_no[]"></td>
                    <td><input class="form-control text-start" id="reference" type="text" name="narration[]"></td>
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

<div class="modal fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add License Key (<label id="ModalLabelHeading"></label>)</h4>
                <button class="btn btn-sm btn-light ms-auto" data-bs-toggle="modal" data-bs-target="#ModalExcelQuote"
                    data-backdrop="static" data-keyboard="false"><i class="ico icon-outline-import text-success"></i>
                    Import</button>
                <input type="hidden" id="part_number_id" />
                <input type="hidden" id="license_row_index" value="" />
                <input type="hidden" id="edit_license_id" value="" />
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="license_qty" id="license_qty" value="1" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">License Key (<span id="licenseCountSummary"
                                        class="text-muted small mt-2">Selected: 0 of 0</span>)</label>
                                <input type="text" class="form-control" name="license_key" id="license_key" />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Exp Date</label>
                                <input type="text" class="form-control date-picker" name="exp_date" id="exp_date"
                                    autocomplete="off" />
                            </div>
                            <div class="col-md-2"><br />
                                <button type="button" id="license_add" class="btn btn-light btn-sm"
                                    onclick="return add_license_key()"><i
                                        class="ico icon-outline-add-square text-success me-1"></i>Add</button>
                                <button type="button" id="license_cancel_edit" class="btn btn-sm btn-outline-secondary ms-1"
                                    onclick="cancel_license_edit()" style="display:none;" title="Cancel edit">&#x2715;</button>
                            </div>
                        </div>
                        <div id="licenseKeyMessage" class="text-danger small mb-2 mt-2" style="display:none;"></div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <table id="lk-table" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Sr.No</th>
                                            <th style="width: 60%;">Licence Key</th>
                                            <th style="width: 20%;">Expiry Date</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" data-bs-dismiss="modal">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">License Excel Import</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select File (.csv)</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="import_file" id="import_file"
                                        class="form-control me-2 w-25" accept=".csv, .xls, .xlsx" />
                                    <a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}"
                                        target="_blank">(Sample File)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="return excel_license_key()" type="button" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>


{{-- <div class="modal  fade" id="addpoexcelimport" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-excel-cart', 'method' => 'POST', 'id' => 'add-purchase-order-items-excel-cart']) }}

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">PO Items Excel Import</h4>
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
                                <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                            </div>

                            <div class="col-auto">
                                <small>(<a
                                        href="{{ url('public/uploads/product_upload/po_items_sample_format.csv') }}"
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
                                            <th style="width:100px;" class="text-end">Unit Price</th>
                                            <th style="width:100px;" class="text-end">Discount</th>
                                            <th style="width:100px;" class="text-end">VAT</th>

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

<div class="modal  fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Attachments - <label id="att_cust_name"></label></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Attach File') <span>*</span> </label>
                                    <input class="form-control" type="file" id="att_file" name="att_file"
                                        onchange="updateDocName()" />
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Date') <span>*</span> </label>
                                    <input class="form-control" type="date" id="att_date" name="att_date"
                                        value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('File Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="doc_name" name="doc_name"
                                        value="" />
                                </div>
                            </div>
                            <script>
                                function updateDocName() {
                                    var fileInput = document.getElementById('att_file');
                                    var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                    var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                    document.getElementById('doc_name').value = fileNameWithoutExtension;
                                }
                            </script>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">No</th>
                                            <th style="width: 30%;">Date</th>
                                            <th style="width: 50%;">Attachment</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <br />


                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="srl_id" />

                <button type="button" onclick="add_attachment()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div> --}}


<script>
    let narrationModal;
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('narrationModal');
        narrationModal = new bootstrap.Modal(modalElement);
    });
    let currentSerialInput = null;

    $(document).on('click', 'input[name="narration[]"]', function() {
        currentSerialInput = $(this);
        $('#narrationTextarea').val(currentSerialInput.val());
        narrationModal.show();
    });

    function AddNarration() {
        if (currentSerialInput) {
            const val = $('#narrationTextarea').val();
            currentSerialInput.val(val);
            narrationModal.hide();
            currentSerialInput = null;
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




    $(document).ready(function() {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
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
                                    text: item.account_code + ' - ' + item.account_name
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Select Account',
                minimumInputLength: 2
            });
        }

        // Initial init
        initAccountSelect2('.js-account-select');

        // Re-initialize on focus (if needed for dynamically added fields)
        $(document).on('focus', '.js-account-select', function() {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
            }
        });

        // Open dropdown and focus search box on click
        $(document).on('click', '.js-account-select', function() {
            $(this).select2('open');
        });

        // Focus the search input inside the opened Select2 dropdown
        $(document).on('select2:open', function() {
            setTimeout(function() {
                const searchInput = document.querySelector(
                    '.select2-container--open .select2-search__field');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 0);
        });
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
    $(window).ready(function() {
        $("#tender-create-form").on("keypress", function(event) {
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>

<script>
    let stockInLicenseRows = [];

    function set_license_key(el, e) {
        var key = e.which || e.keyCode;
        if (key !== 13) {
            return true;
        }

        var $row = $(el).closest("tr");
        var pt = $row.find('input[name="product_type[]"]').first().val();
        var partId = $row.find('select[name="part_number[]"]').val();
        var hasValidPart = partId !== undefined && partId !== null && String(partId).trim() !== '';
        var isLicenseType = parseInt(String(pt == null ? '' : pt).trim(), 10) === 2;

        // Existing edit rows may not have product_type populated, so allow modal
        // when a valid part number is selected as a fallback.
        if (isLicenseType || hasValidPart) {
            $('#part_number_id').val(partId);
            $('#license_row_index').val($('#myTable > tbody > tr').index($row));
            $('#license_qty').val($(el).val());
            $('#ModalLabelHeading').text($row.find('select[name="part_number[]"] option:selected').text());
            showLicenseKeyMessage('');
            cancel_license_edit();
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('ModalLicenseKey')).show();
            } else if ($.fn.modal) {
                $('#ModalLicenseKey').modal('show');
            }
            view_license_key();
            e.preventDefault();
            return false;
        }

        return true;
    }

    function showLicenseKeyMessage(message, type = 'danger') {
        var $msg = $('#licenseKeyMessage');
        $msg.removeClass('text-danger text-warning text-success');
        if (!message) {
            $msg.hide();
            return;
        }
        $msg.text(message).addClass(type === 'success' ? 'text-success' : (type === 'warning' ? 'text-warning' : 'text-danger')).show();
    }

    function getLicenseQty() {
        var qty = parseInt($('#license_qty').val(), 10);
        return isNaN(qty) ? 0 : qty;
    }

    function normalizeLicenseDateForStore(value) {
        var raw = (value || '').toString().trim();
        if (!raw || raw === '0000-00-00') return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;
        var parts = raw.replace(/\./g, '/').replace(/-/g, '/').split('/');
        if (parts.length !== 3) return '';
        var day = parts[0].padStart(2, '0');
        var month = parts[1].padStart(2, '0');
        var year = parts[2];
        if (year.length === 2) year = '20' + year;
        if (!/^\d{4}$/.test(year)) return '';
        return year + '-' + month + '-' + day;
    }

    function formatLicenseDateForDisplay(value) {
        var ymd = normalizeLicenseDateForStore(value);
        if (!ymd) return '';
        var parts = ymd.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function setLicenseAddButtonMode(mode) {
        if (mode === 'update') {
            $('#license_add').html('<i class="ico icon-outline-pen-2 me-1"></i>Update');
            return;
        }
        $('#license_add').html('<i class="ico icon-outline-add-square text-success me-1"></i>Add');
    }

    function cancel_license_edit() {
        $('#edit_license_id').val('');
        $('#license_key').val('');
        $('#exp_date').val('');
        setLicenseAddButtonMode('add');
        $('#license_cancel_edit').hide();
        $('#lk-table tbody tr').removeClass('table-warning');
    }

    function edit_license_key_mode(id, btn) {
        var targetId = parseInt(id, 10);
        var row = stockInLicenseRows.find(function(item) {
            return parseInt(item.id, 10) === targetId;
        });
        if (!row) {
            showLicenseKeyMessage('Unable to find selected key for update.', 'danger');
            return;
        }
        $('#edit_license_id').val(targetId);
        $('#license_key').val((row.license_key || '').toString().trim()).focus();
        $('#exp_date').val(formatLicenseDateForDisplay(row.exp_date));
        setLicenseAddButtonMode('update');
        $('#license_cancel_edit').show();
        $('#lk-table tbody tr').removeClass('table-warning');
        $(btn).closest('tr').addClass('table-warning');
    }

    function getExistingLicenseKeys() {
        return stockInLicenseRows.map(function(row) {
            return (row.license_key || '').toString().trim().toLowerCase();
        }).filter(Boolean);
    }

    function getActiveLicenseTargetRow(itemId) {
        var $rows = $('#myTable > tbody > tr');
        var rowIndex = parseInt($('#license_row_index').val(), 10);
        if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
            var $byIdx = $rows.eq(rowIndex);
            if ($byIdx.length && (!itemId || String($byIdx.find('select[name="part_number[]"]').val()) === String(itemId))) {
                return $byIdx;
            }
        }
        var $matches = $rows.filter(function() {
            return String($(this).find('select[name="part_number[]"]').val()) === String(itemId);
        });
        return $matches.length ? $matches.first() : $();
    }

    function getCommaSeparatedLicenseKeys(rows) {
        var seen = {};
        return (rows || []).map(function(row) {
                return (row.license_key || '').toString().trim();
            })
            .filter(function(key) {
                if (!key) return false;
                var normalized = key.toLowerCase();
                if (seen[normalized]) return false;
                seen[normalized] = true;
                return true;
            });
    }

    function applyLicenseKeysToSerialInput(itemId, rows) {
        var $targetRow = getActiveLicenseTargetRow(itemId);
        if (!$targetRow.length) return;
        $targetRow.find('input[name="serial_no[]"]').val(getCommaSeparatedLicenseKeys(rows).join(', '));
    }

    function updateLicenseAddState() {
        var maxQty = getLicenseQty();
        var currentCount = getExistingLicenseKeys().length;
        var isEditMode = ($('#edit_license_id').val() || '').toString().trim() !== '';
        $('#license_add').prop('disabled', maxQty <= 0 || (!isEditMode && currentCount >= maxQty));
        $('#licenseCountSummary').text('Selected: ' + currentCount + ' of ' + maxQty);
    }

    function renderLicenseRows(rows) {
        var seen = {};
        var duplicates = [];
        var tr = '';
        var uniqueRows = [];
        var serial = 0;
        (rows || []).forEach(function(row) {
            var licenseKey = (row.license_key || '').toString().trim();
            if (!licenseKey) return;
            var normalized = licenseKey.toLowerCase();
            if (seen[normalized]) {
                duplicates.push(licenseKey);
                return;
            }
            seen[normalized] = true;
            uniqueRows.push(row);
            serial += 1;
            var rowId = parseInt(row.id, 10);
            tr += '<tr data-id="' + rowId + '">' +
                '<td class="text-center">' + serial + '</td>' +
                '<td>' + $('<div>').text(licenseKey).html() + '</td>' +
                '<td>' + formatLicenseDateForDisplay(row.exp_date) + '</td>' +
                '<td class="text-center" style="white-space:nowrap;">' +
                '<a onclick="edit_license_key_mode(' + rowId + ', this)" class="btn-sm btn-light me-1" title="Edit"><i class="ico icon-outline-pen-2"></i></a>' +
                '<a onclick="delete_license_key(' + rowId + ')" class="btn-sm btn-light" title="Delete"><i class="ico icon-outline-trash-bin-trash"></i></a>' +
                '</td></tr>';
        });
        stockInLicenseRows = uniqueRows;
        applyLicenseKeysToSerialInput($('#part_number_id').val(), uniqueRows);
        if (!serial) {
            tr = '<tr><td colspan="4" class="text-center text-muted">No keys added.</td></tr>';
        }
        $('#lk-table tbody').html(tr);
        updateLicenseAddState();
        if (duplicates.length) {
            showLicenseKeyMessage('Duplicate license keys were ignored: ' + duplicates.join(', '), 'warning');
        } else {
            showLicenseKeyMessage('');
        }
    }

    function parseAjaxResponse(dataResult) {
        if (typeof dataResult === 'string') return JSON.parse(dataResult);
        return dataResult || {};
    }

    function excel_license_key() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        var maxQty = getLicenseQty();
        var itemId = $('#part_number_id').val();
        var fileInput = $('#import_file')[0];
        if (!itemId) {
            showLicenseKeyMessage('Select a product before importing license keys.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (maxQty <= 0) {
            showLicenseKeyMessage('License quantity must be greater than zero before importing.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            $('#import_file').focus();
            showLicenseKeyMessage('Select a valid CSV or Excel file to import.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        var fileName = fileInput.files[0].name.toLowerCase();
        var allowedExtensions = ['csv', 'xls', 'xlsx'];
        var extension = fileName.split('.').pop();
        if ($.inArray(extension, allowedExtensions) === -1) {
            showLicenseKeyMessage('Unsupported file type. Use .csv, .xls, or .xlsx.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('item_id', itemId);
        formData.append('stock_in_id', $('#stock_in_id').val());
        formData.append('license_qty', maxQty);
        formData.append('import_file', fileInput.files[0]);

        $.ajax({
            url: "{{ URL::to('add-stkin-license-key-excel') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                try {
                    var response = parseAjaxResponse(dataResult);
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }
                    renderLicenseRows(response.data || []);
                    $('#license_key').val('');
                    $('#exp_date').val('');
                    $('#import_file').val('');
                    if (response.duplicate || (response.duplicate_keys && response.duplicate_keys.length)) {
                        showLicenseKeyMessage(response.message || ('Duplicate license keys were skipped: ' + (response.duplicate_keys || []).join(', ')), 'warning');
                    } else {
                        showLicenseKeyMessage('Imported license keys loaded successfully.', 'success');
                    }
                } catch (err) {
                    showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
        return false;
    }

    function add_license_key() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        var itemId = $('#part_number_id').val();
        var licenseKey = ($('#license_key').val() || '').toString().trim();
        var expDate = normalizeLicenseDateForStore($('#exp_date').val());
        var maxQty = getLicenseQty();
        var existingKeys = getExistingLicenseKeys();
        var editId = $('#edit_license_id').val();
        if (!itemId) {
            showLicenseKeyMessage('Select a product before adding license keys.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (maxQty <= 0) {
            showLicenseKeyMessage('License quantity must be greater than zero.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (!licenseKey) {
            $('#license_key').focus();
            showLicenseKeyMessage('Enter a license key.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (!editId && existingKeys.indexOf(licenseKey.toLowerCase()) !== -1) {
            showLicenseKeyMessage('This license key has already been added.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (!editId && existingKeys.length >= maxQty) {
            showLicenseKeyMessage('Cannot add more than ' + maxQty + ' license keys.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        var payload = {
            _token: '{{ csrf_token() }}',
            item_id: itemId,
            license_key: licenseKey,
            exp_date: expDate,
            license_qty: maxQty,
            stock_in_id: $('#stock_in_id').val()
        };
        var action = editId ? "{{ URL::to('update-stkin-license-key') }}" : "{{ URL::to('add-stkin-license-key') }}";
        if (editId) payload.id = editId;

        $.ajax({
            url: action,
            type: "POST",
            data: payload,
            cache: false,
            success: function(dataResult) {
                try {
                    var response = parseAjaxResponse(dataResult);
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }
                    renderLicenseRows(response.data || []);
                    cancel_license_edit();
                    if (response.duplicate || (response.duplicate_keys && response.duplicate_keys.length)) {
                        showLicenseKeyMessage(response.message || ('Duplicate license keys were skipped: ' + (response.duplicate_keys || []).join(', ')), 'warning');
                    }
                } catch (err) {
                    showLicenseKeyMessage('Unable to add license key. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to add license key. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
        return false;
    }

    function view_license_key() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        $.ajax({
            url: "{{ URL::to('view-stkin-license-key') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                item_id: $('#part_number_id').val(),
                stock_in_id: $('#stock_in_id').val()
            },
            cache: false,
            success: function(dataResult) {
                try {
                    var response = parseAjaxResponse(dataResult);
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }
                    renderLicenseRows(response.data || []);
                } catch (err) {
                    showLicenseKeyMessage('Unable to load license keys. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to load license keys. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function delete_license_key(id) {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        $.ajax({
            url: "{{ URL::to('delete-stkin-license-key') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                item_id: $('#part_number_id').val(),
                stock_in_id: $('#stock_in_id').val()
            },
            cache: false,
            success: function(dataResult) {
                try {
                    var response = parseAjaxResponse(dataResult);
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }
                    renderLicenseRows(response.data || []);
                } catch (err) {
                    showLicenseKeyMessage('Unable to delete license key. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to delete license key. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }
</script>



  <script>

        $(document).on("keydown", 'input[name="description[]"], input[name="unitprice[]"], input[name="serial_no[]"], input[name="narration[]"]', function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // prevent form submit

                let row = $(this).closest("tr"); // current row
                let name = $(this).attr("name");

            
               
               if (name === "unitprice[]") {
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





<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

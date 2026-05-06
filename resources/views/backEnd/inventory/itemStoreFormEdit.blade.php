@extends('backEnd.newmasterpage')
@section('mainContent')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <?php try { ?>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>


    <aside class="left-nav col-12" id="leftSidebar">

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store-update', 'method' => 'POST', 'id' => 'item-store-update']) }}

        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
        <input type="hidden" name="id" value="{{ isset($openingstock) ? $openingstock->id : '' }}">
        <input type="hidden" id="opening_stock_id" value="{{ isset($openingstock) ? $openingstock->id : '' }}">

        <div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
            <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
                <h4 class="purchase-order-content-header-left">
                    Edit ({{ $openingstock->doc_number }})

                </h4>
                <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

                    <button type="submit" name="btnSubmit" class="btn btn-light">
                        <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
                    </button>


                        <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                        
                          <li><a href="{{ url('item-store/show') }}" class="dropdown-item">Opening Stock</a></li>
                            <li><a href="{{ url('item-store-import') }}" class="dropdown-item">Import Opening Stock</a></li>
                            <li><button type="button" id="exportExcelOpeningStock" class="dropdown-item">Export</button></li>


                   




                        </ul>
                    </div>


                </div>
            </div>
            <div class="card mb-3 mt-2">
                <div class="card-body">
                    <div class="row gap-rows">

                        <div class="col-1-5">
                            <label class="form-label">@lang('Doc') @lang('Date')<span>*</span></label>

                            @php
                                $value = date('d/m/Y');
                                if (isset($openingstock) && !empty($openingstock->doc_date)) {
                                    $value = date('d/m/Y', strtotime($openingstock->doc_date));
                                }
                            @endphp


                            <div class="form-group">
                                <input type="text" id="doc_date" name="doc_date" class="form-control date-picker"
                                    value="{{ @$value }}" />
                            </div>
                        </div>

                        <div class="col-1-5">
                            <label class="form-label">@lang('Doc') @lang('Number')<span>*</span></label>
                            <div class="form-group">
                                <input type="text" id="doc_number" name="doc_number" class="form-control" readonly
                                    value="{{ $openingstock->doc_number }}" />
                            </div>
                        </div>


                        <div class="col-1-5">
                            <label class="form-label">@lang('Bill') @lang('Date')<span>*</span></label>

                            @php
                                $value = date('d/m/Y');
                                if (isset($openingstock) && !empty($openingstock->bill_date)) {
                                    $value = date('d/m/Y', strtotime($openingstock->bill_date));
                                }
                            @endphp

                            <div class="form-group">
                                <input type="text" id="bill_date" name="bill_date" class="form-control date-picker"
                                    value="{{ @$value }}" />
                            </div>
                        </div>

                        <div class="col-1-5">
                            <label class="form-label">Currency</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($openingstock) ? (!empty(@$openingstock->currency) ? (@$openingstock->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i> -->
                            </div>
                            @if ($errors->has('currency'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('currency') }}</strong>
                                </span>
                            @endif
                        </div>


                             <div class="col-2">
                            <label class="form-label">Created By</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="createdby" autocomplete="off"
                                    id="createdby" readonly value="{{ $openingstock->createdby->full_name }}" />
                            </div>
                            @if ($errors->has('createdby'))
                                <span class="invalid-feedback"
                                    role="alert"><strong>{{ $errors->first('createdby') }}</strong></span>
                            @endif
                        </div>

                        <div class="col-4">
                            <div class="input-effect">
                                <label class="dynamicslbl form-label">@lang('Narration')</label>
                                <input class="form-control" name="remarks" data-bs-toggle="modal" data-bs-target="#narrationModal"
                                    id="narration" type="text"
                                    value="{{ isset($openingstock) ? (!empty(@$openingstock->narration) ? @$openingstock->narration : old('narration')) : old('narration') }}"
                                    name="narration">
                            </div>
                        </div>



                   


                    </div>
                </div>
            </div>


            <div class="table-container">
                <table class="table table-hover form-item-table" id="myTable"
                    style="table-layout: fixed; width: 100%;margin: 0 auto;">
                    <thead>
                        <tr>
                           <th class="resizable text-center" width="25px">@lang('No')<div class="resizer"></div>
                            </th>

                            <th class="resizable text-center" width="150px">@lang('Part No') <a
                                    class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                    data-bs-target="#addproductModal"></a>
                                <div class="resizer"></div>
                            </th>

                            <th class="resizable text-center" width="200px">@lang('Description')
                                <div class="resizer"></div>
                            </th>

                            <th class="resizable text-center" width="50px">@lang('Qty')
                                <div class="resizer"></div>
                            </th>
                            <th class="resizable text-center" width="100px">@lang('Price')
                                <div class="resizer"></div>
                            </th>
                            <th class="resizable text-center" width="100px">@lang('Value')
                                <div class="resizer"></div>
                            </th>         
                            <th class="resizable text-center" width="140px">@lang('Serial No')
                                <div class="resizer"></div>
                            </th>

                            <th class="resizable text-center" width="200px">@lang('Narration')
                                <div class="resizer"></div>
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php $qty = 0;
                        $i = 1;
                        $price = 0.0;
                        $total = 0.0; ?>
                        @if (count($stocklist) > 0)
                            @foreach ($stocklist as $dt)
                                <tr>

 <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{$i}}" />
                            </td>

                                    <td class="noborder">
                                        <select class="form-control noborder " name="part_number[]">
                                            <option value="{{ $dt->productdet->id }}">
                                                {{ $dt->productdet->part_number ?? 0 }}</option>
                                        </select>
                                        {{-- on focus add this class and its funcanalities js-product-select --}}
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" name="description[]"
                                            autocomplete="off" value="{{ $dt->description }}">
                                        <input class="form-control" type="text" name="part_number_txt[]"
                                            autocomplete="off" readonly="true" hidden>
                                        <input class="form-control" type="text" name="hscode_txt[]"
                                            autocomplete="off" readonly="true" hidden>
                                        <input class="form-control" type="text" name="product_type[]"
                                            autocomplete="off" readonly="true" hidden>
                                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                                            autocomplete="off" readonly="true" hidden>
                                    </td>
                                    <input type="hidden" id="pid_{{ $dt->id }}"
                                        value="{{ $dt->productdet->id }}">
                                    <input type="hidden" id="txt_doc_number_{{ $dt->id }}"
                                        value="{{ $dt->doc_number }}">
                                    <input type="hidden" id="product_type_{{ $dt->id }}"
                                        value="{{ @$dt->productdet->product_type }}">
                                    <input type="hidden" id="partno_{{ $dt->id }}"
                                        value="{{ @$dt->productdet->part_number }}">

                                    <td><input class="form-control text-center" id="txt_uqty_{{ $dt->id }}"
                                            value="{{ $dt->qty_in }}"  type="text" name="qty[]"
                                            autocomplete="off" data-enter-skip min="0" onchange="calc_change_new(this)"
                                            onkeypress="return set_license_key_po(this, event, {{ $dt->id }})"></td>
                                    <td><input class="form-control text-end" type="text" name="unitprice[]"
                                            value="{{ @App\SysHelper::com_curr_format($dt->price_in,2,'.',',') }}" step="any" autocomplete="off"
                                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)" ></td>
                                    <td><input class="form-control text-end" onblur="formatCurrency(this)"  type="text" name="value[]"
                                            autocomplete="off" value="{{@App\SysHelper::com_curr_format($dt->price_in * $dt->qty_in,2,'.',',') }}" min="0"
                                            readonly>
                                    </td>



                                    <td><input class="form-control text-end" type="text" name="refno[]"
                                            autocomplete="off" value="{{ $dt->refno }}">
                                    </td>

                                    <td><input class="form-control text-start" type="text" name="narration[]" 
                                            autocomplete="off" value="{{ $dt->remarks }}">
                                    </td>

                                </tr>

                                <?php
                                $i++;
                                $qty += $dt->qty_in;
                                $price += $dt->price_in;
                                $total += $dt->price_in * $dt->qty_in;
                                ?>
                            @endforeach
                        @endif

                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="{{$i}}" /></td>
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

                            <td><input class="form-control text-center" type="text" data-enter-skip name="qty[]" autocomplete="off"
                                    min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>
                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                    autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                                    min="0" readonly>
                            </td>



                            <td><input class="form-control text-end" type="text" name="refno[]" autocomplete="off">
                            </td>

                            <td><input class="form-control text-start" type="text" name="narration[]" autocomplete="off"></td>

                        </tr>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" scope="col"></th>
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

    </aside>








    <script>

        $(document).on("keydown", 'input[name="description[]"], input[name="unitprice[]"], input[name="refno[]"], input[name="narration[]"]', function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // prevent form submit

                let row = $(this).closest("tr"); // current row
                let name = $(this).attr("name");

            
               
              
                else if (name === "unitprice[]") {
                    row.find('input[name="refno[]"]').focus();
                } 
                 else if (name === "refno[]") {
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

        document.addEventListener('DOMContentLoaded', function() {
            const referenceInput = document.getElementById('narration');
            const narrationTextarea = document.getElementById('narrationTextarea');
            const insertButton = document.getElementById('insertNarration');
            const narrationModal = document.getElementById('narrationModal');

            // Pre-fill textarea when modal opens
            narrationModal.addEventListener('show.bs.modal', () => {
                narrationTextarea.value = referenceInput.value;
            });

            // Focus textarea when modal is fully open
            narrationModal.addEventListener('shown.bs.modal', () => {
                setTimeout(function() {
                    narrationTextarea.focus();
                }, 50);
            });

            // On insert button click, update input and close modal
            insertButton.addEventListener('click', () => {
                referenceInput.value = narrationTextarea.value;
                bootstrap.Modal.getInstance(narrationModal).hide();
            });
        });
    </script>


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
                    <button type="button" id="insertNarration" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>




    {{-- model --}}
    <div class="modal  fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Add License  <label style="margin-left: 117px" id="ModalLabelHeading"></label>
                    </h4>
                    <button class="btn btn-sm btn-light ms-auto" data-bs-toggle="modal" data-bs-target="#ModalExcelQuote"
                        data-backdrop="static" data-keyboard="false"><i
                            class="ico icon-outline-import  text-success"></i> Import</button>
                    <input type="hidden" id="part_number_id" />
                    <input type="hidden" id="license_row_index" value="" />
                    <input type="hidden" id="edit_license_id" value="" />
                    <button type="button" class="btn-close" style="    margin: -.5rem -.5rem -.5rem 3rem;"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="" class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="license_qty" id="license_qty"
                                        value="1" />
                                </div>
                                <div class="col-md-6">
                                    <label for="" class="form-label">License Key (<span id="licenseCountSummary"
                                            class="text-muted small mt-2">Selected: 0 of 0</span>)</label>
                                    <input type="text" class="form-control" name="license_key" id="license_key" />
                                </div>
                                <div class="col-md-2">
                                    <label for="" class="form-label">Exp Date</label>
                                    <input type="text" class="form-control date-picker" name="exp_date" id="exp_date" autocomplete="off" />
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
                                        <tbody>

                                        </tbody>
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

    <div class="modal  fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">License Excel Import
                    </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">

                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Select File (.csv)</label>
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


    {{-- model --}}




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referenceInput = document.getElementById('narration');
            const narrationTextarea = document.getElementById('narrationTextarea');
            const insertButton = document.getElementById('insertNarration');
            const narrationModal = document.getElementById('narrationModal');

            // Pre-fill textarea when modal opens
            narrationModal.addEventListener('show.bs.modal', () => {
                narrationTextarea.value = referenceInput.value;
            });

            // On insert button click, update input and close modal
            insertButton.addEventListener('click', () => {
                referenceInput.value = narrationTextarea.value;
                bootstrap.Modal.getInstance(narrationModal).hide();
            });
        });
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
            var unitpriceRaw = ($row.find('input[name="unitprice[]"]').val() || '').replace(/,/g, '');
            var discount = ($row.find('input[name="discount[]"]').val() || '0').replace(/,/g, '');
            var fright = ($row.find('input[name="fright[]"]').val() || '0').replace(/,/g, '');
            var customcharges = ($row.find('input[name="customcharges[]"]').val() || '0').replace(/,/g, '');

            var decimal_point = @json(session('logged_session_data.decimal_point'));

            // Calculate unit price formatting
            var parsedUnitprice = unitpriceRaw === '' ? null : parseFloat(unitpriceRaw);
            if (parsedUnitprice !== null && Number.isFinite(parsedUnitprice)) {
                $row.find('input[name="unitprice[]"]').val(formatAmount(parsedUnitprice));
            } else {
                $row.find('input[name="unitprice[]"]').val('');
            }

            var valueUnitprice = (parsedUnitprice !== null && Number.isFinite(parsedUnitprice)) ? parsedUnitprice : 0;

            // Calculate value
            var fin_value = valueUnitprice * parseFloat(qty);
            $row.find('input[name="value[]"]').val(formatAmount(fin_value));

            // Calculate taxable amount
            var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
            $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));

            // Calculate VAT
            var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
            $row.find('input[name="vatamount[]"]').val(formatAmount(fin_vatamount));

            // Calculate total amount
            var total_amount = fin_taxableamount + fin_vatamount;
            $row.find('input[name="totalamount[]"]').val(formatAmount(total_amount));

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

                total_qty += parseRowNumber($row.find('input[name="qty[]"]'));
                total_price += parseRowNumber($row.find('input[name="unitprice[]"]'));
                total_value += parseRowNumber($row.find('input[name="value[]"]'));
                total_discount += parseRowNumber($row.find('input[name="discount[]"]'));
                total_fright += parseRowNumber($row.find('input[name="fright[]"]'));
                total_customcharges += parseRowNumber($row.find('input[name="customcharges[]"]'));
                total_taxableamount += parseRowNumber($row.find('input[name="taxableamount[]"]'));
                total_vatamount += parseRowNumber($row.find('input[name="vatamount[]"]'));
                total_totalamount += parseRowNumber($row.find('input[name="totalamount[]"]'));
            });

            $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
            $('#lbl_total_price').text(formatAmount(total_price));
            $('#lbl_total_value').text(formatAmount(total_value));
            $('#lbl_total_discount').text(formatAmount(total_discount));
            $('#lbl_total_fright').text(formatAmount(total_fright));
            $('#lbl_total_customcharges').text(formatAmount(total_customcharges));
            $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
            $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
            $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
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



    <!-- Modal License Key-->
    <script>
        let openingStockLicenseRows = [];

        function set_license_key(el, e) {
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }

            var $row = $(el).closest("tr");
            var pt = $row.find('input[name="product_type[]"]').first().val();

            if (parseInt(String(pt == null ? '' : pt).trim(), 10) === 2) {
                $('#part_number_id').val($row.find('select[name="part_number[]"]').val());
                $('#license_row_index').val($('#myTable > tbody > tr').index($row));
                $('#license_qty').val($(el).val());
                $('#ModalLabelHeading').text($row.find('select[name="part_number[]"] option:selected').text());
                showLicenseKeyMessage('');
                cancel_license_edit();
                var modalEl = document.getElementById('ModalLicenseKey');
                if (modalEl) {
                    if (window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else if ($.fn.modal) {
                        $('#ModalLicenseKey').modal('show');
                    }
                }
                view_license_key();
                e.preventDefault();
                return false;
            }

            return true;
        }

        function set_license_key_po(el, e, rowid) {
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }
            var pt = $('#product_type_' + rowid).val();
            if (parseInt(String(pt == null ? '' : pt).trim(), 10) === 2) {
                var $row = $(el).closest("tr");
                $('#part_number_id').val($('#pid_' + rowid).val());
                $('#license_row_index').val($('#myTable > tbody > tr').index($row));
                $('#license_qty').val($(el).val());
                $('#ModalLabelHeading').text($('#partno_' + rowid).val());
                showLicenseKeyMessage('');
                cancel_license_edit();
                var modalEl = document.getElementById('ModalLicenseKey');
                if (modalEl) {
                    if (window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else if ($.fn.modal) {
                        $('#ModalLicenseKey').modal('show');
                    }
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
            $msg
                .text(message)
                .addClass(type === 'success' ? 'text-success' : type === 'warning' ? 'text-warning' : 'text-danger')
                .show();
        }

        function getLicenseQty() {
            var qty = parseInt($('#license_qty').val(), 10);
            return isNaN(qty) ? 0 : qty;
        }

        function normalizeLicenseDateForStore(value) {
            var raw = (value || '').toString().trim();
            if (!raw || raw === '0000-00-00') {
                return '';
            }
            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                return raw;
            }
            var normalized = raw.replace(/\./g, '/').replace(/-/g, '/');
            var parts = normalized.split('/');
            if (parts.length !== 3) {
                return '';
            }
            var day = parts[0].padStart(2, '0');
            var month = parts[1].padStart(2, '0');
            var year = parts[2];
            if (year.length === 2) {
                year = '20' + year;
            }
            if (!/^\d{4}$/.test(year) || !/^\d{2}$/.test(month) || !/^\d{2}$/.test(day)) {
                return '';
            }
            return year + '-' + month + '-' + day;
        }

        function formatLicenseDateForDisplay(value) {
            var ymd = normalizeLicenseDateForStore(value);
            if (!ymd) {
                return '';
            }
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
            var row = openingStockLicenseRows.find(function(item) {
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
            return openingStockLicenseRows
                .map(function(row) {
                    return (row.license_key || '').toString().trim().toLowerCase();
                })
                .filter(Boolean);
        }

        function getActiveLicenseTargetRow(itemId) {
            var $rows = $('#myTable > tbody > tr');
            var rowIndex = parseInt($('#license_row_index').val(), 10);
            if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
                var $byIdx = $rows.eq(rowIndex);
                if ($byIdx.length) {
                    if (!itemId || String($byIdx.find('select[name="part_number[]"]').val()) === String(itemId)) {
                        return $byIdx;
                    }
                }
            }
            var $matches = $rows.filter(function() {
                return String($(this).find('select[name="part_number[]"]').val()) === String(itemId);
            });
            if ($matches.length === 0) {
                return $();
            }
            if ($matches.length === 1) {
                return $matches.first();
            }
            if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
                var $candidate = $rows.eq(rowIndex);
                if ($candidate.length && $matches.toArray().indexOf($candidate[0]) !== -1) {
                    return $candidate;
                }
            }
            return $matches.first();
        }

        function getCommaSeparatedLicenseKeys(rows) {
            var seen = {};
            return (rows || []).map(function(row) {
                    return (row.license_key || '').toString().trim();
                })
                .filter(function(key) {
                    if (!key) {
                        return false;
                    }
                    var normalized = key.toLowerCase();
                    if (seen[normalized]) {
                        return false;
                    }
                    seen[normalized] = true;
                    return true;
                });
        }

        function applyLicenseKeysToRefInput(itemId, rows) {
            var $targetRow = getActiveLicenseTargetRow(itemId);
            if (!$targetRow.length) {
                return;
            }
            var serialText = getCommaSeparatedLicenseKeys(rows).join(', ');
            $targetRow.find('input[name="refno[]"]').val(serialText);
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
            var getSelectedRows = '';
            var uniqueRows = [];
            var uniqueCount = 0;

            rows = rows || [];
            rows.forEach(function(row) {
                var licenseKey = (row.license_key || '').toString().trim();
                if (!licenseKey) {
                    return;
                }
                var normalized = licenseKey.toLowerCase();
                if (seen[normalized]) {
                    duplicates.push(licenseKey);
                    return;
                }
                seen[normalized] = true;
                uniqueRows.push(row);
                uniqueCount += 1;

                var rowId = parseInt(row.id, 10);
                getSelectedRows += '<tr data-id="' + rowId + '">' +
                    '<td class="text-center">' + uniqueCount + '</td>' +
                    '<td>' + $('<div>').text(licenseKey).html() + '</td>' +
                    '<td>' + formatLicenseDateForDisplay(row.exp_date) + '</td>' +
                    '<td class="text-center" style="white-space:nowrap;">' +
                    '<a onclick="edit_license_key_mode(' + rowId + ', this)" class="btn-sm btn-light me-1" title="Edit"><i class="ico icon-outline-pen-2"></i></a>' +
                    '<a onclick="delete_license_key(' + rowId + ')" class="btn-sm btn-light" title="Delete"><i class="ico icon-outline-trash-bin-trash"></i></a>' +
                    '</td>' +
                    '</tr>';
            });

            openingStockLicenseRows = uniqueRows;
            applyLicenseKeysToRefInput($('#part_number_id').val(), uniqueRows);
            if ($('#edit_license_id').val()) {
                var editId = parseInt($('#edit_license_id').val(), 10);
                var stillExists = openingStockLicenseRows.some(function(item) {
                    return parseInt(item.id, 10) === editId;
                });
                if (!stillExists) {
                    cancel_license_edit();
                }
            }

            if (uniqueCount === 0) {
                getSelectedRows = '<tr><td colspan="4" class="text-center text-muted">No keys added.</td></tr>';
            }

            $('#lk-table tbody').empty().append(getSelectedRows);
            updateLicenseAddState();

            if (duplicates.length) {
                showLicenseKeyMessage('Duplicate license keys were ignored: ' + duplicates.join(', '), 'warning');
            } else {
                showLicenseKeyMessage('');
            }
        }

        function parseAjaxResponse(dataResult) {
            if (typeof dataResult === 'string') {
                return JSON.parse(dataResult);
            }
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

            var action = "{{ URL::to('add-ops-license-key-excel') }}";
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('item_id', itemId);
            formData.append('opening_stock_id', $('#opening_stock_id').val());
            formData.append('license_qty', maxQty);
            formData.append('import_file', fileInput.files[0]);

            $.ajax({
                url: action,
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

            var action = editId ? "{{ URL::to('update-ops-license-key') }}" : "{{ URL::to('add-ops-license-key') }}";
            var payload = {
                _token: '{{ csrf_token() }}',
                item_id: itemId,
                license_key: licenseKey,
                exp_date: expDate,
                license_qty: maxQty,
                opening_stock_id: $('#opening_stock_id').val(),
            };
            if (editId) {
                payload.id = editId;
            }
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

            var action = "{{ URL::to('view-ops-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#part_number_id').val(),
                    opening_stock_id: $('#opening_stock_id').val(),
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

            var action = "{{ URL::to('delete-ops-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    item_id: $('#part_number_id').val(),
                    opening_stock_id: $('#opening_stock_id').val(),
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
    <!-- Modal License Key-->

    <?php
    $part_number = $items->pluck('part_number');
    ?>

    <script>
        $(document).ready(function () {
            $('#exportExcelOpeningStock').on('click', function (e) {
                e.preventDefault();

                var companyName     = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var docNumber       = $('#doc_number').val() || '';
                var docDate         = $('#doc_date').val() || '';
                var billDate        = $('#bill_date').val() || '';
                var currency        = $('#currency option:selected').text().trim() || '';
                var createdBy       = $('#createdby').val() || '';
                var headerNarration = $('#narration').val() || '';

                var workbook  = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('Opening Stock');
                var N = 8;

                worksheet.columns = [
                    { width: 6  },
                    { width: 22 },
                    { width: 38 },
                    { width: 10 },
                    { width: 16 },
                    { width: 16 },
                    { width: 22 },
                    { width: 32 },
                ];

                // Row 1 — Company Name
                var r1 = worksheet.addRow([]);
                r1.getCell(1).value     = companyName;
                r1.getCell(1).font      = { bold: true, size: 14 };
                r1.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                r1.height = 26;
                worksheet.mergeCells(1, 1, 1, N);

                // Row 2 — Page Title
                var r2 = worksheet.addRow([]);
                r2.getCell(1).value     = 'Opening Stock';
                r2.getCell(1).font      = { bold: true, size: 12 };
                r2.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                r2.height = 20;
                worksheet.mergeCells(2, 1, 2, N);

                // Row 3 — Doc No / Doc Date / Bill Date
                var r3 = worksheet.addRow([]);
                r3.getCell(1).value = 'Doc No: ' + docNumber;
                r3.getCell(3).value = 'Doc Date: ' + docDate;
                r3.getCell(6).value = 'Bill Date: ' + billDate;
                r3.height = 16;
                worksheet.mergeCells(3, 1, 3, 2);
                worksheet.mergeCells(3, 3, 3, 5);
                worksheet.mergeCells(3, 6, 3, N);

                // Row 4 — Currency / Created By / Narration
                var r4 = worksheet.addRow([]);
                r4.getCell(1).value = 'Currency: ' + currency;
                r4.getCell(3).value = 'Created By: ' + createdBy;
                r4.getCell(6).value = 'Narration: ' + headerNarration;
                r4.height = 16;
                worksheet.mergeCells(4, 1, 4, 2);
                worksheet.mergeCells(4, 3, 4, 5);
                worksheet.mergeCells(4, 6, 4, N);

                // Row 5 — Blank separator
                worksheet.addRow([]);

                // Row 6 — Table Headers
                var headerRow = worksheet.addRow(['No', 'Part No', 'Description', 'Qty', 'Price', 'Value', 'Serial No', 'Narration']);
                headerRow.height = 20;
                headerRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                    cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border    = {
                        top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
                    };
                });

                // Data rows
                var hasData = false;
                $('#myTable tbody tr').each(function () {
                    var $row        = $(this);
                    var no          = $row.find('input[name="sort_id[]"]').val() || '';
                    var partNo      = $row.find('select[name="part_number[]"] option:selected').text().trim() || '';
                    var description = $row.find('input[name="description[]"]').val() || '';
                    var qty         = $row.find('input[name="qty[]"]').val() || '';
                    var price       = $row.find('input[name="unitprice[]"]').val() || '';
                    var value       = $row.find('input[name="value[]"]').val() || '';
                    var serialNo    = $row.find('input[name="refno[]"]').val() || '';
                    var rowNarr     = $row.find('input[name="narration[]"]').val() || '';

                    var isEmpty = [partNo, description, qty].every(function (v) { return v.trim() === ''; });
                    if (isEmpty) return;

                    hasData = true;
                    var dr = worksheet.addRow([no, partNo, description, qty, price, value, serialNo, rowNarr]);
                    dr.eachCell({ includeEmpty: true }, function (cell) {
                        cell.border = {
                            top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                    dr.getCell(1).alignment = { horizontal: 'center' };
                    dr.getCell(4).alignment = { horizontal: 'center' };
                    dr.getCell(5).alignment = { horizontal: 'right' };
                    dr.getCell(6).alignment = { horizontal: 'right' };
                });

                if (!hasData) {
                    alert('No data available for export');
                    return;
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    function pad(n) { return n < 10 ? '0' + n : n; }
                    var d = new Date();
                    var filename = 'opening_stock_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
            });
        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

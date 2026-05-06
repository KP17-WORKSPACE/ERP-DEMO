<?php try { ?>


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'clearance-update', 'method' => 'POST', 'id' => 'clearance-create-form']) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($clearance) ? $clearance->id : '' }}">
<input type="hidden" id="clearance_id" value="{{ $clearance->id }}">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ $clearance->doc_no }}


        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">


            <a class="btn btn-light" href="{{ url('clearance/' . @$clearance->id . '?clr_action=add') }}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">

                    <li data-bs-toggle="modal" data-bs-target="#attachment_popup_win" onclick="view_attachment()"><a
                            href="#" class="dropdown-item">
                            <i class="ico icon-bold-file-text text-success"></i>
                            Attachment</a></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">



            <div class="row">
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Doc') @lang('Number')<span>*</span></label>
                        <input class="form-control" type="text" name="doc_no" autocomplete="off" id="doc_no"
                            value="{{ $clearance->doc_no }}" readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('doc_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('doc_no') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="form-label">@lang('Invoice') @lang('lang.date')</label>
                                <input class="form-control date-picker" id="invoice_date" type="text" autocomplete="off"
                                    name="invoice_date" value="{{@App\SysHelper::normalizeToDmy($clearance->invoice_date)}}">
                                <span class="focus-border"></span>
                                @if ($errors->has('invoice_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Invoice') @lang('Number')<span>*</span></label>
                        <input class="form-control" type="text" name="invoice_no" autocomplete="off" id="invoice_no"
                            value="{{ $clearance->invoice_no }}" readonly required>
                        <span class="focus-border"></span>
                        @if ($errors->has('invoice_no'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('invoice_no') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">@lang('Currency')<span>*</span></label>
                    <select class="form-control" name="currency" id="currency">
                        @foreach ($currency as $value)
                            <option value="{{ @$value->id }}" <?php if($clearance->currency==$value->id) { ?> Selected <?php } ?>>
                                {{ @$value->code }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-4 mb-3">
                    <div class="input-effect">
                        <label class="form-label">Created By</label>
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby"
                            value="{{ Auth::user()->full_name }}" readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('createdby'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('createdby') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="col-4 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Bill To') <span></span></label>
                        <input type="text" class="form-control" id="bill_to" name="bill_to" required
                            value="{{ $clearance->bill_to }}"></input>


                    </div>
                </div>
                <div class="col-4 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Ship To') <span></span></label>
                        <input type="text" class="form-control" id="ship_to" name="ship_to"
                            value="{{ $clearance->ship_to }}" required></input>

                    </div>
                </div>
                <div class="col-4 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Ship To Address') <span></span></label>
                        <input type="text" class="form-control" id="ship_to_address" name="ship_to_address"
                            value="{{ $clearance->ship_to_address }}"></input>

                    </div>
                </div>
                <div class="col-lg-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Payment Method') <span></span></label>
                        <select class="form-control" name="payment_method[]" id="payment_method">
                            <option value=""></option>
                            <option value="CDR Cash" <?php if($clearance->payment_method=='CDR Cash') { ?> Selected <?php } ?>>CDR Cash</option>
                            <option value="CDR Bank" <?php if($clearance->payment_method=='CDR Bank') { ?> Selected <?php } ?>>CDR Bank</option>
                            <option value="Deposit" <?php if($clearance->payment_method=='Deposit') { ?> Selected <?php } ?>>Deposit</option>
                            <option value="Credit A/C*" <?php if($clearance->payment_method=='Credit A/C*') { ?> Selected <?php } ?>>Credit A/C*
                            </option>
                            <option value="Stan. G*" <?php if($clearance->payment_method=='Stan. G*') { ?> Selected <?php } ?>>Stan. G*
                            </option>
                            <option value="Bank G*" <?php if($clearance->payment_method=='Bank G*') { ?> Selected <?php } ?>>Bank G*</option>
                            <option value="FTT" <?php if($clearance->payment_method=='FTT') { ?> Selected <?php } ?>>FTT</option>
                            <option value="Alcohol" <?php if($clearance->payment_method=='Alcohol') { ?> Selected <?php } ?>>Alcohol</option>
                            <option value="Other" <?php if($clearance->payment_method=='Other') { ?> Selected <?php } ?>>Other</option>
                        </select>


                    </div>
                </div>
                <div class="col-lg-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer Bill Type') <span></span></label>
                        <select class="form-control" name="customer_bill_type[]" id="customer_bill_type">
                            <option value=""></option>
                            <option value="Import" <?php if($clearance->customer_bill_type=='Import') { ?> Selected <?php } ?>>Import</option>
                            <option value="Import for Re-Export" <?php if($clearance->customer_bill_type=='Import for Re-Export') { ?> Selected <?php } ?>>
                                Import for Re-Export</option>
                            <option value="Temporary Exit" <?php if($clearance->customer_bill_type=='Temporary Exit') { ?> Selected <?php } ?>>Temporary
                                Exit</option>
                            <option value="Free Zone Internal Transfer"<?php if($clearance->customer_bill_type=='Free Zone Internal Transfer') { ?> Selected
                                <?php } ?>>Free Zone Internal Transfer</option>
                            <option value="Bill of Entry" <?php if($clearance->customer_bill_type=='Bill of Entry') { ?> Selected <?php } ?>>Bill of
                                Entry</option>
                            <option value="Export" <?php if($clearance->customer_bill_type=='Export') { ?> Selected <?php } ?>>Export</option>
                        </select>


                    </div>
                </div>

                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Free Zone Bill of Entry No') <span></span></label>
                        <input type="text" class="form-control" id="free_zone_bill_no" name="free_zone_bill_no"
                            required value="{{ $clearance->free_zone_bill_no }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Description of Goods') <span></span></label>
                        <input type="text" class="form-control" id="goods_description" name="goods_description"
                            required value="{{ $clearance->goods_description }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('BOE No') <span></span></label>
                        <input type="text" class="form-control" id="boe_no" name="boe_no"
                            value="{{ $clearance->boe_no }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Exit Point') <span></span></label>
                        <select class="form-control" name="exit_point" id="exit_point">
                            <option value="Jebel Ali Free Zone" <?php if($clearance->exit_point=='Jebel Ali Free Zone') { ?> Selected <?php } ?>>
                            </option>
                            <option value="Jebel Ali Free Zone / Dubai Airport Free Zone" <?php if($clearance->exit_point=='Jebel Ali Free Zone / Dubai Airport Free Zone') { ?> Selected
                                <?php } ?>>Jebel Ali Free Zone / Dubai Airport Free Zone</option>
                        </select>
                    </div>
                </div>

                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Box Type') <span></span></label>
                        <select class="form-control" name="box_type" id="box_type">
                            <option value=""></option>
                            <option value="PCS" <?php if($clearance->box_type=='PCS') { ?> Selected <?php } ?>>PCS</option>
                            <option value="Box" <?php if($clearance->box_type=='Box') { ?> Selected <?php } ?>>Box</option>
                            <option value="Pallet" <?php if($clearance->box_type=='Pallet') { ?> Selected <?php } ?>>Pallet</option>
                        </select>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Box Qty') <span></span></label>
                        <input type="text" class="form-control" id="box_qty" name="box_qty"
                            value="{{ $clearance->box_qty }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Volume CBM') <span></span></label>
                        <input type="text" class="form-control" id="cbm" name="cbm"
                            value="{{ $clearance->cbm }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <div class="input-effect">
                        <label class="form-label">@lang('Destination') <span></span></label>
                        <select class="form-control" name="destination" id="destination">
                            <option value="Jebel Ali Free Zone" <?php if($clearance->destination=='Jebel Ali Free Zone') { ?> Selected <?php } ?>>
                                Jebel Ali Free Zone</option>
                            <option value="UAE" <?php if($clearance->destination=='UAE') { ?> Selected <?php } ?>>UAE</option>
                            <option value="Qatar" <?php if($clearance->destination=='Qatar') { ?> Selected <?php } ?>>Qatar</option>
                            <option value="Oman" <?php if($clearance->destination=='Oman') { ?> Selected <?php } ?>>Oman</option>
                            <option value="Saudi Arabia" <?php if($clearance->destination=='Saudi Arabia') { ?> Selected <?php } ?>>Saudi
                                Arabia</option>
                            <option value="Kuwait" <?php if($clearance->destination=='Kuwait') { ?> Selected <?php } ?>>Kuwait</option>
                            <option value="Jordan" <?php if($clearance->destination=='Jordan') { ?> Selected <?php } ?>>Jordan</option>
                            <option value="Dubai Silicon Oasis" <?php if($clearance->destination=='Dubai Silicon Oasis') { ?> Selected <?php } ?>>
                                Dubai Silicon Oasis</option>
                            <option value="Egypt" <?php if($clearance->destination=='Egypt') { ?> Selected <?php } ?>>Egypt</option>
                            <option value="Africa" <?php if($clearance->destination=='Africa') { ?> Selected <?php } ?>>Africa</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="50px">@lang('No')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('Description')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('COO')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('H.S Code')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('Weight')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="50px">@lang('Qty')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Unit Price')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Amount')
                        <div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>


                @php
                    $i = 1;

                @endphp
                @if (count($clearanceitems) > 0)
                    @foreach ($clearanceitems as $dt)
                        <tr>
                            <td><input name="sort_id[]" type="text" class="form-control text-center"
                                    id="inputPONumber" value="{{ $i++ }}" />
                                </td>

                            <td> <select class="form-control noborder " name="part_number[]">
                                <option value="{{$dt->pid}}">{{$dt->partno}}</option>
                                </select>
                              <input class="form-control" type="text" value="{{$dt->partno}}" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                                
                            </td>
                            {{-- on focus add this class and its funcanalities js-product-select --}}

                            <td>
                                <input class="form-control" type="text" name="description[]"
                                    value="{{ $dt->description }}" autocomplete="off">

                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                    readonly="true" hidden>
                                <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                                    readonly="true" hidden>
                                <input class="form-control" type="text" name="product_type_part_number_text[]"
                                    autocomplete="off" readonly="true" hidden>
                            </td>

                            <td>
                                <input class="form-control text-center" type="text" name="coo[]"
                                    value="{{ $dt->coo }}" autocomplete="off">
                            </td>

                            <td>
                                <input class="form-control text-center" type="text" name="hscode[]"
                                    value="{{ $dt->hscode }}" autocomplete="off">
                            </td>


                            <td>
                                <input class="form-control text-center" type="text" name="weight[]"
                                    value="{{ $dt->weight }}" autocomplete="off" onchange="calc_change_new(this)">
                            </td>


                            <td><input class="form-control text-center" type="text" name="qty[]"
                                    value="{{ $dt->qty }}" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                            <td><input class="form-control text-end" type="text" name="unitprice[]"
                                    value="{{ $dt->price }}" step="any" autocomplete="off" min="0"
                                    onchange="calc_change_new(this)"></td>
                            <td><input class="form-control text-end" type="number" name="value[]"
                                    value="{{ $dt->totalprice }}" autocomplete="off" min="0" readonly>
                            </td>
                        </tr>
                    @endforeach
                @endif


                <tr>
                    <td><input name="sort_id[]" type="text" class="form-control text-center" id="inputPONumber"
                            value="{{ $i }}" /></td>
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

                    <td>
                        <input class="form-control text-center" type="text" name="coo[]" autocomplete="off">
                    </td>

                    <td>
                        <input class="form-control text-center" type="text" name="hscode[]" autocomplete="off">
                    </td>


                    <td>
                        <input onchange="calc_change_new(this)" class="form-control text-center" type="text"
                            name="weight[]" autocomplete="off">
                    </td>


                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off"
                            min="0" readonly>
                    </td>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_weight">0</label></th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>

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
                                    <input class="form-control date-picker" type="text" id="att_date" name="att_date"
                                        value="{{ date('d/m/Y') }}" />
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
</div>




<script>
    update_totals();

    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        var weight = $row.find('input[name="weight[]"]').val() || '0';
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
            total_weight = 0,
            total_totalamount = 0;

        const decimal_point = @json(session('logged_session_data.decimal_point'));

        $('#myTable tbody tr').each(function() {
            const $row = $(this);

            total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
            total_weight += parseFloat($row.find('input[name="weight[]"]').val()) || 0;
            total_price += parseFloat($row.find('input[name="unitprice[]"]').val()) || 0;
            total_value += parseFloat($row.find('input[name="value[]"]').val()) || 0;
            total_discount += parseFloat($row.find('input[name="discount[]"]').val()) || 0;
            total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
            total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val()) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val()) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val()) || 0;
        });

        $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
        $('#lbl_total_weight').text(total_weight.toFixed(decimal_point));
        $('#lbl_total_price').text(total_price.toFixed(decimal_point));
        $('#lbl_total_value').text(total_value.toFixed(decimal_point));
        $('#lbl_total_discount').text(total_discount.toFixed(decimal_point));
        $('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
        $('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(total_taxableamount.toFixed(decimal_point));
        $('#lbl_total_vatamount').text(total_vatamount.toFixed(decimal_point));
        $('#lbl_total_totalamount').text(total_totalamount.toFixed(decimal_point));
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
                placeholder: 'Select Product',
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
            });


        }

        initAccountSelect2('.js-product-select');

        // Re-initialize on focus if needed
        $(document).on('focus', '.js-product-select', function() {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
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
        $("#clearance-create-form").on("keypress", function(event) {
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>



<script>
    function add_attachment() {
        $("#loading_bg").css("display", "block");

        if ($('#att_file').val() == "") {
            $('#att_file').focus();
            $("#loading_bg").css("display", "none");
            return false;
        }

        var action = "{{ URL::to('add-clearance-attachment') }}";

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
        formData.append('doc_id', 0);
        formData.append('att_date', $('#att_date').val());
        formData.append('att_file', $('#att_file')[0].files[0]);
        formData.append('doc_name', $('#doc_name').val());


        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                <td>" + Number(i + 1) + "</td>\
                                <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-light d-inline-block'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a></td>\
                                </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    function view_attachment() {
        $("#loading_bg").css("display", "block");

        console.log($('#doc_no').val())

        $('#att_cust_name').text("Syscom FZE " + $('#doc_no').val());

        var action = "{{ URL::to('view-clearance-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: 0,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                <td>" + Number(i + 1) + "</td>\
                                <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-light d-inline-block'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a></td>\
                                </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    function delete_attachment(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-clearance-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                doc_id: 0,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                <td>" + Number(i + 1) + "</td>\
                                <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-light d-inline-block'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a></td>\
                                </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
</script>

{{-- attachment end --}}

<script>
    function validateAttachForm() {
        var val1 = $("#shipping_name_add").val();
        var val2 = $("#contact_name_add").val();
        var val3 = $("#contact_no_add").val();
        var val4 = $("#address1_add").val();
        var val5 = $("#address2_add").val();

        if (val1 === "") {
            $('.modal_input_validation_1').show();
            $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
            $("span.modal_input_validation_1").addClass("red_alert");
            return false;
        }
        if (val2 === "") {
            $('.modal_input_validation_2').show();
            $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
            $("span.modal_input_validation_2").addClass("red_alert");
            return false;
        }
        if (val3 === "") {
            $('.modal_input_validation_3').show();
            $(".modal_input_validation_3").html("<font style='color:red;'>Must be Fill Up</font>");
            $("span.modal_input_validation_3").addClass("red_alert");
            return false;
        }
        if (val4 === "") {
            $('.modal_input_validation_4').show();
            $(".modal_input_validation_4").html("<font style='color:red;'>Must be Fill Up</font>");
            $("span.modal_input_validation_4").addClass("red_alert");
            return false;
        }
        if (val5 === "") {
            $('.modal_input_validation_5').show();
            $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
            $("span.modal_input_validation_5").addClass("red_alert");
            return false;
        }
        //return true;

        var url = $('#url').val();
        $.ajax({
            type: "POST",
            data: {
                shipping_name: val1,
                contact_name: val2,
                contact_no: val3,
                address1: val4,
                address2: val5
            },

            //url: 'http://syscom.company/venus-erp/shipping-store2',
            //url: 'http://localhost:81/venus-erp/shipping-store2',
            url: url + '/' + 'shipping-store2',
            cache: false,
            success: function(response) {
                var response = JSON.parse(response);
                var len = 0;
                if (response['data'] == "ERROR") {
                    alert("Error found in something!!");
                } else {
                    if (response['data'] != null) {
                        len = response['data'].length;
                    }
                    if (len > 0) {

                        //$('#shipping_name').find('option').not(':first').remove();

                        for (var i = 0; i < len; i++) {
                            var id = response['data'][i].id;
                            var name = response['data'][i].shipping_name;
                            var option = "<option value='" + id + "'>" + name + "</option>";
                            //$("#shipping_name").append($(option));
                            //$('#shipping_name').append(new Option(name, id));
                            $("#shipping_name").append(option);
                            $("#vendor").append(option);
                        }

                        alert('Shipping Added Successfully!!');
                        $('#btn_close2').click();
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });


        //preventDefault();
    }

    function cfc_amount_change(id) {
        var amt = $("#cfc_amount_" + id).val();
        $("#cfc_cal_amount_" + id).val(amt);
    }
</script>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

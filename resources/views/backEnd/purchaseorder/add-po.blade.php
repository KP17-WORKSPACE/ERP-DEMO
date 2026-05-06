<?php try { ?>
<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New Purchase Order
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <button class="btn btn-light">
                <i class="ico icon-outline-archive-down-minimlistic text-warning"></i> Save &
                Download
            </button>
            <button class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label for="" class="form-check-label">Vendor</label>
                    <select class=" js-account-select" name="vendors" id="vendors" required style="width: 100%;">
                        <option value=""></option>

                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Number:</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_order','PO' ,'doc_number') }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">PO date:</label>
                    @php
                        $value = date('Y-m-d');
                        if (isset($edit) && !empty($edit->date)) {
                            @$value = date('Y-m-d', strtotime(@$edit->date));
                        } else {
                            if (!empty(old('po_date'))) {
                                @$value = old('po_date');
                            } else {
                                @$value = date('Y-m-d');
                            }
                        }
                    @endphp
                    <div class="form-group">
                        <input type="date" id="po_date" type="date" name="po_date" class="form-control date-picker"
                            value="{{ @$value }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency:</label>
                    <div class="form-group">
                        <select class="form-control select2" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>

                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                    </div>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-2">
                    <label class="form-label">Created By:</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="createdby" autocomplete="off" id="createdby"
                            readonly
                            value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" />
                    </div>
                    @if ($errors->has('createdby'))
                        <span class="invalid-feedback"
                            role="alert"><strong>{{ $errors->first('createdby') }}</strong></span>
                    @endif
                </div>

                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Bill to Name') <span></span></label>
                        <input type="text" class="form-control" value="{{ @$company->company_name }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                        <input type="text" class="form-control" value="{{ @$company->company_address }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab"
                    data-bs-target="#shipping-details-info" type="button" role="tab"
                    aria-controls="shipping-details-info" aria-selected="false">Shipping
                    Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="false">VAT
                    Details</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">
                    <div class="col-2">
                        <label class="form-label">Delivery Date:</label>
                        @php
                            $value = date('Y-m-d');
                            if (isset($edit) && !empty($edit->date)) {
                                @$value = date('Y-m-d', strtotime(@$edit->date));
                            } else {
                                if (!empty(old('delivery_date'))) {
                                    @$value = old('delivery_date');
                                } else {
                                    @$value = date('Y-m-d');
                                }
                            }
                        @endphp
                        <div class="form-group">
                            <input type="date" class="form-control date-picker" id="delivery_date" type="date"
                                name="delivery_date" value="{{ @$value }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Payment Terms:*</label>
                        <div class="form-group">
                            <select class="form-control" required name="payment_terms" id="payment_terms"
                                onchange="fn_payment_terms()">
                                <option value=""></option>
                                @foreach ($paymentterms as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <div class="col-2" id="div_payment_terms" style="display: none; padding-top: px;">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                            <input class="txtbx primary-input form-control" type="text" name="payment_terms2"
                                autocomplete="off" id="payment_terms2" value="{{ @$edit->payment_terms2 }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Customer Reference:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="narration" type="text" name="narration" value=""
                                required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Salesman Name:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                @foreach ($salesman as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Name:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_name" type="text"
                                name="contact_person_name" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Email:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_email" type="text"
                                name="contact_person_email" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Telephone:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_telephone" type="text"
                                name="contact_person_telephone" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Internal Transfer:</label>
                        <div class="form-group">
                            <select class="form-control select2" id="internal_transfer" name="internal_transfer"
                                required>
                                <option value="" selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-lg-2 mb-2" id="div_deal_id" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Deal ID')*</label>
                            <input class="form-control" id="deal_id" type="text" name="deal_id" value="Without Deal"
                                required>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Create Deal:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_deal" id="create_deal" required
                                onchange="create_deal_change()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <script>
                        function create_deal_change() {
                            if ($('#create_deal').val() == 1) {
                                $('#div_deal_id').css('display', 'none');

                            } else {
                                $('#div_deal_id').css('display', '');
                            }
                        }

                        function create_deal_change() {
                            if ($('#internal_transfer').val() == 1) {
                                $('#div_deal_id').css('display', '');
                                $('#create_deal').val('0');
                                $('#create_deal').change();
                                $('#deal_id').val('');
                                $('#deal_id').prop('required', true);
                            }
                        }
                    </script>

                    <div class="col-2">
                        <label class="form-label">Create Goods Receipt Note:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_grn" id="create_grn"
                                onchange="fn_create_grn_pi()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Create Purchase Invoice:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_pi" id="create_pi"
                                onchange="fn_create_grn_pi()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <script>
                        function fn_create_grn_pi() {
                            var create_grn = $("#create_grn").val();
                            var create_pi = $("#create_pi").val();

                            if (create_grn == "1" || create_pi == "1") {
                                $(".create_grn_pi").show();
                            } else {
                                $(".create_grn_pi").hide();
                            }
                        }

                        $(document).ready(function () {
                            fn_create_grn_pi(); // Ensure correct initial state
                            $("#create_grn, #create_pi").change(fn_create_grn_pi);
                        });
                    </script>

                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Bill Number')</label>
                            <input class="form-control" id="bill_number" type="text" name="bill_number">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Bill Date')</label>
                            <input class="form-control" id="bill_date" type="date" name="bill_date">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('AWB No')</label>
                            <input class="form-control" id="awbno" type="text" name="awbno">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('BOE No')</label>
                            <input class="form-control" id="boeno" type="text" name="boeno">
                        </div>
                    </div>
                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Narration')</label>
                            <input class="form-control" id="reference" type="text" name="reference">
                        </div>
                    </div>


                </div>
            </div>
            <div class="tab-pane fade" id="shipping-details-info" role="tabpanel"
                aria-labelledby="shipping-details-info-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Company Name:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required>
                                <option value=""></option>
                                @foreach ($customer as $value)
                                    @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp
                                    <option value="{{ @$value->id }}" {{ $s }}>{{ @$value->account_code }} -
                                        {{ @$value->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            $(function () { $("#shipping_supplier").change(); });
                        </script>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ session('logged_session_data.full_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Email:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ session('logged_session_data.email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                                value="{{ session('logged_session_data.mobile') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1" id="shipping_address_1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Supplier Type:</label>
                        <div class="form-group">
                            <select class="form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                @foreach ($suppliertype as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Purchase Type:</label>
                        <div class="form-group">
                            <select name="purchase_type" id="purchase_type"
                                class="form-control  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                id="inputVendorName">

                                @foreach ($purchasetype as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Supplier Country:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="supplier_country" id="country"
                                required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                                            <option value="{{ @$value->id }}" <?php        try {?> @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif @endif <?php        } catch (\Throwable $th) {
                                    } ?>>{{ @$value->name }} </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
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
                    <th class="resizable text-center" width="150px">@lang('Part No')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('Description')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="50px">@lang('Tax')
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
                    <th class="resizable text-center" width="100px" scope="col">Discount <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px" scope="col">Freight <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px" scope="col">Custom <a class="icon icon-outline-book"
                            data-bs-toggle="modal" data-bs-target="#customModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Taxable')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('VAT')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')
                        <div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control" id="inputPONumber" value="1" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td>
                        <input class="form-control" type="text" name="description[]" autocomplete="off">
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="number" class="form-control" name="tax[]" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="qty[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly>
                    </td>
                    <td><input class="form-control" type="number" name="discount[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="fright[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0"
                            readonly></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="col">Total</th>
                    <th class="text-center">0</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                    <th class="text-end" scope="col">0.00</th>
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.js-example-basic-single').select2({
            width: '100%' // or 'resolve' or any specific value like '300px'
        });
    });
</script>
<script>


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
    }
</script>

<script>
    /*table row fill based on layout height*/
    window.onload = function () {
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
    function add_attachment() {
        $("#loading_bg").css("display", "block");

        if ($('#att_file').val() == "") { $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-purchase-order-attachment') }}";

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', 0);
        formData.append('att_date', $('#att_date').val()); // Append other form data
        formData.append('att_file', $('#att_file')[0].files[0]);
        formData.append('doc_name', $('#doc_name').val());


        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                }
                else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function view_attachment() {
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#vendors :selected').text() + " " + $('#doc_number').val());

        var action = "{{ URL::to('view-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: 0,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                }
                else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function delete_attachment(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                doc_id: 0,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                }
                else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
</script>

{{-- attachment end--}}

<script>
    function srlno_add() {
        var hdtxt = $("#description_new").val();
        var srl = $("#serialno").val();
        $("#srlno_textarea").val(srl);
        $("#div_serialno_title").html(hdtxt);
        document.getElementById('add_srlno_popup').click();
        $("#srlno_textarea").focus();
    }
    function srlno_add_item() {
        var srltxt = $("#srlno_textarea").val();
        $("#serialno").val(srltxt);
        document.getElementById('add_srl_cls').click();
    }
</script>

<script>

    $(document).on("change", "#vendors", function () {
        var id = $("#vendors").val();
        get_vendors_detail(id);
    });
    function get_vendors_detail(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-chartofaccounts-info') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        $("#payment_terms").val(dataResult['data'][i].payment_terms);
                        $("#contact_person_name").val(dataResult['data'][i].contcat_person);
                        $("#contact_person_email").val(dataResult['data'][i].email);
                        //$("#shipping_address_2").val(dataResult['data'][i].address2);
                        $("#contact_person_telephone").val(dataResult['data'][i].contcat_number);

                        $("#supplier_type").val(dataResult['data'][i].supplier_type);
                        $("#purchase_type").val(dataResult['data'][i].purchase_type);

                        //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                        //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                        $("#tax").val(dataResult['data'][i].vat_percentage);

                        $("#country").val(dataResult['data'][i].vat_country);
                        $("#state").val(dataResult['data'][i].vat_state);
                    }
                }
                else {
                    $("#payment_terms").val("");
                    $("#contact_person_name").val("");
                    $("#contact_person_email").val("");
                    //$("#shipping_address_2").val("");
                    $("#contact_person_telephone").val("");
                    $("#country").val("");
                    $("#state").val("");
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#shipping_supplier", function () {
        var id = $("#shipping_supplier").val();
        get_shipping_supplier_detail2(id);
    });
    function get_shipping_supplier_detail2(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-chartofaccounts-info') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        //$("#shipping_name").val(dataResult['data'][i].customer_salutation+'. '+dataResult['data'][i].first_name+' '+dataResult['data'][i].last_name);
                        //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                        $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                        //$("#shipping_email").val(dataResult['data'][i].email);
                        //$("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                    }
                }
                else {
                    //$("#shipping_name").val("");
                    $("#shipping_address_1").val("");
                    //$("#shipping_email").val("");
                    //$("#shipping_contact_no").val("");    
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function get_shipping_supplier_detail(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-chartofaccounts-info') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        $("#shipping_name").val(dataResult['data'][i].customer_salutation + '. ' + dataResult['data'][i].first_name + ' ' + dataResult['data'][i].last_name);
                        //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                        $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                        $("#shipping_email").val(dataResult['data'][i].email);
                        $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                    }
                }
                else {
                    $("#shipping_name").val("");
                    $("#shipping_address_1").val("");
                    $("#shipping_email").val("");
                    $("#shipping_contact_no").val("");
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }


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
            success: function (response) {
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
            error: function (XMLHttpRequest, textStatus, errorThrown) { }
        });

        //preventDefault();
    }


    jQuery(document).ready(function () {
        jQuery('input').keypress(function (event) {
            var enterOkClass = jQuery(this).attr('class');
            if (event.which == 13 && enterOkClass != 'enterSubmit') {
                event.preventDefault();
                return false;
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route("autocomplete.get_supp_account_list_ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search_text: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
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
        $(document).on('focus', '.js-account-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
            }
        });

        // Open dropdown and focus search box on click
        $(document).on('click', '.js-account-select', function () {
            $(this).select2('open');
        });

        // Focus the search input inside the opened Select2 dropdown
        $(document).on('select2:open', function () {
            setTimeout(function () {
                const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 0);
        });
    });
</script>
<script>
    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route("autocomplete.get_product_list_ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search_text: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
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

            $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            var $row = $(this).closest('tr'); // find the closest row

            // Set values using "name" attribute selectors inside the same row
            $row.find('input[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
            $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
            $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
            $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
            $row.find('input[name="discount[]"]').val(0);
            $row.find('input[name="fright[]"]').val(0);
            $row.find('input[name="customcharges[]"]').val(0);
            $row.find('input[name="tax[]"]').val($('#net_vat').val());
            
        });
        }

        initAccountSelect2('.js-product-select');

        // Re-initialize on focus if needed
        $(document).on('focus', '.js-product-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
            }
        });

        // On click, open dropdown and focus on search field
        $(document).on('click', '.js-product-select', function () {
            $(this).select2('open');
        });

        // Optional: Auto focus on search input when dropdown opens
        $(document).on('select2:open', function () {
            setTimeout(function () {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 0);
        });
    });
</script>

<script>
    $(document).on('focus', 'select[name="part_number[]"]', function () {
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


@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                //setTimeout(function () { disableButton(); }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });

        function validate_form_submission() {
            if ($("#vendors").val() == "") {
                alert("Please Fill Vendors");
                $("#vendors").focus();
                return false;
            }
            if ($("#payment_terms").val() == "") {
                alert("Please Fill Payment Terms");
                $("#payment_terms").focus();
                return false;
            }
            if ($("#shipping_name").val() == "") {
                alert("Please Fill Shipping Name");
                $("#shipping_name").focus();
                return false;
            }
            if ($("#shipping_contact_no").val() == "") {
                alert("Please Fill Shipping Contact No");
                $("#shipping_contact_no").focus();
                return false;
            }
            if ($("#supplier_type").val() == "") {
                alert("Please Fill Supplier Type");
                $("#supplier_type").focus();
                return false;
            }
            if ($("#purchase_type").val() == "") {
                alert("Please Fill Purchase Type");
                $("#purchase_type").focus();
                return false;
            }
            if ($("#part_number_1").val() == "none") {
                alert("Please Fill Part Number");
                $("#part_number_1").focus();
                return false;
            }
            if ($("#description_1").val() == "") {
                alert("Please Fill Description");
                $("#description_1").focus();
                return false;
            }
            if ($("#qty_1").val() == "") {
                alert("Please Fill Qty");
                $("#qty_1").focus();
                return false;
            }
            if ($("#unitprice_1").val() == "") {
                alert("Please Fill Unit Price");
                $("#unitprice_1").focus();
                return false;
            }
            if ($("#taxableamount_1").val() == "") {
                alert("Please Fill Taxable Amount");
                $("#taxableamount_1").focus();
                return false;
            }
        }
    </script>

@endsection


{{-- //table --}}





<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
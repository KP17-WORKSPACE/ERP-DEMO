    <?php try { ?>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-store', 'method' => 'POST', 'id' => 'goods-receipt-note-store']) }}
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
    <input type="hidden" name="net_vat" id="net_vat">
    <input type="hidden" id="hd_pending_po_id" name="hd_pending_po_id" />
    <input type="hidden" id="company_id" value="{{ session('logged_session_data.company_id') }}" />




    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @APP\SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number') }})
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i>
                            Save & Download</button></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label class="form-label">Vendor</label>
                    <div class="form-group">
                        <select class=" js-account-select" name="vendors" id="vendors"
                            onchange="get_pending_po_list()">
                            <option value=""></option>
                            {{-- @foreach ($vendors as $value)
                                                    <option value="{{ @$value->id }}"
                                                        {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                        {{ @$value->account_name }}
                                                    </option>
                                                @endforeach --}}
                        </select>



                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Number</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @APP\SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Date</label>
                    <div class="form-group">
                        @php  $value_date = \Carbon\Carbon::parse( now())->format('d/m/Y'); @endphp
                        <input class="form-control date-picker" id="grn_date" type="text" autocomplete="off"
                            name="grn_date" value="{{ @$value_date }}">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group"><select class="form-control" name="currency" id="currency">
                            {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Created By</label>
                    <div class="form-group">

                        <input readonly value="{{Auth::user()->full_name}}" type="text" class="form-control" name="createdby" id="createdby" >
                       
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
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">


                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="txtlbl">Pending list</label>
                            <div id="plist"
                                style="width: 100%; height: 80px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                            </div>
                            <a data-modal-size="modal-md" data-target="#po_pending_popup_win" id="addPoPending"
                                data-toggle="modal"></a>
                            <input type="hidden" id="po_id" name="po_id">
                            <input type="hidden" id="vat_percentage" name="vat_percentage">
                        </div>
                    </div>
                    <div class="col-10 mb-2">
                        <div class="row gap-rows">

                            <div class="col-2">
                                <label class="form-label">LPO Number</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                        type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                        value="{{ isset($edit) ? (!empty(@$edit->lpo_number) ? @$edit->lpo_number : old('lpo_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">LPO Date</label>
                                @php
                                    $raw_date =
                                        old('lpo_date') ??
                                        (isset($edit) && !empty($edit->lpo_date) ? $edit->lpo_date : now());
                                    $value_date = \Carbon\Carbon::parse($raw_date)->format('d/m/Y');
                                @endphp

                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control date-picker {{ $errors->has('lpo_date') ? ' is-invalid' : '' }}"
                                        type="text" name="lpo_date" autocomplete="off" id="lpo_date"
                                        value="{{ $value_date }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Payment Terms</label>
                                <div class="form-group">
                                    <select class="form-control" name="payment_terms" id="payment_terms"
                                        onchange="fn_payment_terms()" required>
                                        <option value=""></option>
                                        @foreach ($paymentterms as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="div_payment_terms" style="display: none; padding-top: px;">
                                    <div class="input-effect">
                                        <label class="form-label">Other Payment Terms</label>
                                        <input
                                            class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                            type="text" name="payment_terms2" autocomplete="off"
                                            id="payment_terms2"
                                            value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Number</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="bill_number" autocomplete="off"
                                        id="bill_number"
                                        value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Date</label>
                                <div class="form-group">

                                    @php
                                        $raw_date =
                                            old('bill_date') ??
                                            (isset($edit) && !empty($edit->bill_date) ? $edit->bill_date : now());
                                        $value_date = \Carbon\Carbon::parse($raw_date)->format('d/m/Y');
                                    @endphp
                                 
                                    <input class="form-control date-picker" id="bill_date" type="text" autocomplete="off"
                                        name="bill_date" value="{{ @$value_date }}" style="margin-top: 0px;">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">AWB No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                        type="text" name="awbno" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                        id="awbno">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">BOE No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                        type="text" name="boeno" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->boeno) ? @$edit->boeno : old('boeno')) : old('boeno') }}"
                                        id="boeno">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Reference</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="reference" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->reference) ? @$edit->reference : old('reference')) : old('reference') }}"
                                        id="reference">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Salesman Name</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                        <option value=""></option>
                                        @foreach ($salesman as $value)
                                            <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Narration</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="narration" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : old('narration') }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#narrationModal" type="text" name="narration" id="narration">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Warehouse</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="warehouse" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                        id="warehouse">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Deal Id</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : old('deal_id') }}"
                                        id="deal_id">
                                </div>
                            </div>

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
                    <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('Description')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="50px">@lang('Tax')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="50px">@lang('Qty')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Price')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Value')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Dis <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Freight <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Custom <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#customModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Serial No')<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td>
                        <input class="form-control" type="text" name="description[]" autocomplete="off"
                            >
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)">
                    </td>
                    <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="number" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control text-end" type="number" name="discount[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="fright[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="customcharges[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="taxableamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="number" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="number" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="text" name="serial_no[]"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_fright">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_customcharges">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_taxableamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_vatamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_totalamount">0</label></th>
                    <th class="text-end" scope="col"></th>
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
    {{ Form::close() }}


    {{-- Models  --}}
    <!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

    @include('backEnd.inventory.itemAddModal')

    <div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Discount</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Discount Amount:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="discountInput" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Split Discount
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal side-panel fade" id="freightModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Freight</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Freight Amount:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="freightInput" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="freight_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Split Freight
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal side-panel fade" id="customModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Custom</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Custom Charges:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="customCharges" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="custom_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Split Custom
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="height: 279px !important;">
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
    {{-- Models  --}}

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
                total_totalamount = 0;

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
                    $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
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
        function get_pending_po_list() {
            var id = $("#vendors").val();
            get_vat(id);
            get_po_list(id);
        }


        function get_vat(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-vat-by-ca') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#loading_bg").css("display", "none");
                    } else {
                        $('#net_vat').val(dataResult['data'].vat_percentage);
                        //$("select[id=tax] option:first").text(dataResult['data'].vat_percentage +'%');
                        //$("select[id=tax] option:first").val(dataResult['data'].vat_percentage);
                        $("#tax").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
        }

        function get_po_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('goods-receipt-note-pending') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#plist").empty();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var doc_number = dataResult['data'][i].doc_number;
                            var option = "<option value='" + id + "'>" + doc_number +
                                "</option>";
                            var innerHtml =
                                "<input type='checkbox' onclick='popup_po_pending(" + id +
                                ")' id='pending_po_" + (i + 1) +
                                "' name='pending_po' value='" + id +
                                "'/> <label for='pending_po_" + (i + 1) + "'> " + doc_number +
                                "</label><br />";

                            $("#plist").append(innerHtml);


                        }
                    } else {
                        $("#plist").empty();
                    }
                    var innerHtml =
                        "<input type='radio' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without PO</label><br />";
                    $("#plist").append(innerHtml);

                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function popup_po_pending(id) {
            var selectedValues = [];
            $('input[name="pending_po"]:checked').each(function() {
                selectedValues.push($(this).val());
            });
            $("#loading_bg").css("display", "block");
            $("#hd_pending_po_id").val(selectedValues);
            $("#po_id").val(id);
            if (selectedValues != "") {
                document.getElementById('addPoPending').click();
            }

            if (id != 0) {
                //$("#table_id2").css("display", "none");    
            }

            $("#loading_bg").css("display", "none");
        }
    </script>



    <!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static"
        data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add License Key - <label
                            id="ModalLabelHeading"></label></h5>
                    <a class="btn-sm btn-danger float-right" data-toggle="modal"
                        data-target="#ModalExcelQuote">License Excel Import</a>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Qty</label><input type="hidden"
                                id="item_id" />
                            <input type="number" class="form-control" name="license_qty" id="license_qty"
                                value="1" readonly />
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">License Key</label>
                            <input type="text" class="form-control" name="license_key" id="license_key" />
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Exp Date</label>
                            <input type="date" class="form-control" name="exp_date" id="exp_date" />
                        </div>
                        <div class="col-md-1"><br />
                            <button type="button" id="license_add" class="btn btn-primary"
                                onclick="return add_license_key()">Add</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-bordered table-striped" width="100%"
                                cellspacing="0">
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
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">Save</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">License Excel Import</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select File (.csv)</label>
                                <input type="file" name="import_file" id="import_file" class="btn-danger" />
                                (<a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}"
                                    target="_blank">Sample File</a>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        onclick="return excel_license_key()">Upload</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->

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
        function set_license_key() {
            $('#qty').keypress(function(e) {
                var key = e.which;
                if (key === 13) { //the enter key code
                    var pt = $('#product_type').val();
                    if (pt == 2) {
                        $('#item_id').val($('#part_number_new').val());
                        $('#ModalLabelHeading').text($('#part_number_new option:selected').text());
                        $('#license_qty').val($('#qty').val());
                        $('#btn_ModalLicenseKey').click();
                        view_license_key();
                    }
                    return true;
                }
            });
        }

        function set_license_key_po(rowid, producttype) {
            $('#qty_' + rowid).keypress(function(e) {
                var key = e.which;
                if (key === 13) { //the enter key code
                    var pt = producttype;
                    if (pt == 2) {
                        $('#item_id').val($('#part_id_' + rowid).val());
                        $('#ModalLabelHeading').text($('#part_number_' + rowid).val());
                        $('#license_qty').val($('#qty_' + rowid).val())
                        $('#btn_ModalLicenseKey').click();
                        view_license_key();
                    }
                    return true;
                }
            });
        }


        function add_license_key() {
            $("#loading_bg").css("display", "block");

            if ($('#license_key').val() == "") {
                $('#license_key').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            if ($('#exp_date').val() == "") {
                $('#exp_date').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            if ($('#license_qty').val() == "") {
                $('#license_qty').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#item_id').val(),
                    license_key: $('#license_key').val(),
                    exp_date: $('#exp_date').val(),
                    license_qty: $('#license_qty').val(),

                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                <td>" + Number(i + 1) + "</td>\
                                                <td>" + dataResult['data'][i].license_key + "</td>\
                                                <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function excel_license_key() {
            $("#loading_bg").css("display", "block");

            if ($('#import_file').val() == "") {
                $('#import_file').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart-excel') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
            formData.append('item_id', $('#part_number_new').val()); // Append other form data
            formData.append('license_qty', $('#license_qty').val()); // Append other form data            
            formData.append('import_file', $('#import_file')[0].files[0]);


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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                <td>" + Number(i + 1) + "</td>\
                                                <td>" + dataResult['data'][i].license_key + "</td>\
                                                <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function view_license_key() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('view-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#part_number_new').val(),
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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                <td>" + Number(i + 1) + "</td>\
                                                <td>" + dataResult['data'][i].license_key + "</td>\
                                                <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function delete_license_key(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    item_id: $('#part_number_new').val(),
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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                <td>" + Number(i + 1) + "</td>\
                                                <td>" + dataResult['data'][i].license_key + "</td>\
                                                <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
    </script>
    <!-- Modal License Key-->

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

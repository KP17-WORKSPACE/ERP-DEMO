    <?php try { ?>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update', 'method' => 'POST', 'id' => 'goods-receipt-note-update']) }}
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" id="grn_id" name="id" value="{{ isset($grn) ? $grn->id : '' }}">
    <input type="hidden" name="grn_po_id" id="grn_po_id" value="{{ $grn->po_id }}">
    <input type="hidden" id="company_id" value="{{ session('logged_session_data.company_id') }}" />
    <input type="hidden" name="doc_number_main" id="doc_number_main" value="{{ $grn->doc_number }}">
    <input type="hidden" name="net_vat" id="net_vat" value="{{ @$grn_items[0]->tax }}">





    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ @$grn->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a type="submit" class="btn btn-light" href="{{ url('goods-receipt-note/create') }}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('goods-receipt-note/' . $grn->id . '/delete') }}"><i
                                class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel GRN</a></li>
                    <li><a class="dropdown-item" href="{{ url('goods-receipt-note/' . $grn->id . '/download') }}"><i
                                class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label class="form-label">Vendor Name:</label>
                    <div class="form-group">
                        <select class="form-control " name="vendors" id="vendors" onchange="get_pending_po_list()">
                            <option value=""></option>
                            @foreach ($vendors as $value)
                                <option value="{{ @$value->id }}" @if (isset($grn) && $grn->vendors == $value->id) selected @endif>
                                    {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select>



                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Number:</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ @$grn->doc_number }}" readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Date:</label>
                    <div class="form-group">
                        @php $value = @$grn->grn_date; @endphp
                        <input class="form-control date-picker" id="grn_date" type="date" autocomplete="off"
                            name="grn_date" value="{{ @$value }}">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency:</label>
                    <div class="form-group"><select class="form-control" name="currency" id="currency">
                            {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($grn->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Created By:</label>
                    <div class="form-group"><select class="form-control" name="createdby" id="createdby">
                            <option value=""></option>
                            @foreach ($staff as $value)
                                <option disabled value="{{ @$value->user_id }}"
                                    @if ($value->user_id == @$grn->created_by) selected @endif>{{ @$value->full_name }}</option>
                            @endforeach
                        </select>
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
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                aria-labelledby="extra-fields-tab">
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
                                <label class="form-label">LPO Number:</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                        type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                        value="{{ isset($grn) ? (!empty(@$grn->lpo_number) ? @$grn->lpo_number : old('lpo_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">LPO Date:</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('lpo_date') ? ' is-invalid' : '' }}"
                                        type="date" name="lpo_date" autocomplete="off" id="lpo_date"
                                        value="{{ isset($grn) ? (!empty(@$grn->lpo_date) ? @$grn->lpo_date : old('lpo_date')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Payment Terms:</label>
                                <div class="form-group">
                                    <select class="form-control" name="payment_terms" id="payment_terms"
                                        onchange="fn_payment_terms()" required>
                                        <option value=""></option>
                                        @foreach ($paymentterms as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($grn) ? (!empty(@$grn->payment_terms) ? (@$grn->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                            value="{{ isset($grn) ? (!empty(@$grn->payment_terms2) ? @$grn->payment_terms2 : old('payment_terms2')) : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Number</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="bill_number" autocomplete="off"
                                        id="bill_number"
                                        value="{{ isset($grn) ? (!empty(@$grn->bill_number) ? @$grn->bill_number : old('bill_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Date</label>
                                <div class="form-group">
                                    @php
                                        $value = date('Y-m-d');
                                        if (isset($grn) && !empty($grn->bill_date)) {
                                            @$value = date('Y-m-d', strtotime(@$grn->bill_date));
                                        } else {
                                            if (!empty(old('bill_date'))) {
                                                @$value = old('bill_date');
                                            } else {
                                                @$value = date('Y-m-d');
                                            }
                                        }
                                    @endphp
                                    <input class="form-control" id="bill_date" type="date" autocomplete="off"
                                        name="bill_date" value="{{ @$value }}" style="margin-top: 0px;">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">AWB No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                        type="text" name="awbno" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->awbno) ? @$grn->awbno : old('awbno')) : old('awbno') }}"
                                        id="awbno">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">BOE No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                        type="text" name="boeno" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->boeno) ? @$grn->boeno : old('boeno')) : old('boeno') }}"
                                        id="boeno">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Reference</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="reference" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->reference) ? @$grn->reference : old('reference')) : old('reference') }}"
                                        id="reference">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Salesman Name</label>
                                <div class="form-group">
                                    <select class="form-control" required name="sales_person" id="sales_person">
                                        <option value=""></option>
                                        @foreach ($salesman as $value)
                                            <option value="{{ @$value->user_id }}"
                                                @if (@$grn->sales_person == @$value->user_id) selected @endif>
                                                {{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Narration</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="narration" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->narration) ? @$grn->narration : old('narration')) : old('narration') }}"
                                        id="narration">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Warehouse</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="warehouse" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->warehouse) ? @$grn->warehouse : old('warehouse')) : old('warehouse') }}"
                                        id="warehouse">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Deal Id</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->deal_id) ? @$grn->deal_id : old('deal_id')) : old('deal_id') }}"
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
                    <th class="resizable text-center" width="150px">@lang('Part No')<div class="resizer"></div>
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
                            class="icon icon-outline-book" data-bs-toggle="modal"
                            data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Freight <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Custom <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#customModal"></a>
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
                @if (isset($grn_items) && count($grn_items) > 0)
                    @php
                        $i = 1;
                        $po_qty = 0;
                        $qty = 0;
                        $executed_qty = 0;
                        $balance_qty = 0;
                        $unitprice = 0;
                        $value = 0;
                        $discount = 0;
                        $fright = 0;
                        $custom = 0;
                        $taxableamount = 0;
                        $vatamount = 0;
                        $total = 0;
                        $grn_qty = 0;
                    @endphp
                    @if (count($grn_items) > 0)
                        @foreach ($grn_items as $items)
                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="{{ $items->sort_id }}" />
                                    <input type="hidden" id="product_type[]" value="{{ $items->product_type }}" />
                                    <input type="hidden" name="item_po_id[]" value="{{ $items->po_id }}" />
                                </td>
                                <td><input type="text" class="form-control" name="part_number_txt[]"
                                        value="{{ $items->part_number ?? 0 }}" readonly />
                                    <input type="hidden" name="part_number[]" value="{{ $items->part_no }}" />
                                </td>
                                <td><input type="text" class="form-control" name="description[]"
                                        value="{{ $items->description ?? 0 }}" readonly /></td>

                                @if (session('logged_session_data.company_id') == 2)
                                    <td>{{ $items->hscode }}</td>
                                @endif

                                <td style="display: none;"><input type="text" class="form-control text-center"
                                        id="po_qty_{{ $i }}" name="po_qty[]"
                                        value="{{ $items->po_qty }}" /></td>
                                <td><input type="text" class="form-control" name="tax[]"
                                        value="{{ number_format($items->tax ?? 0, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-center" name="qty[]"
                                        value="{{ $items->qty }}"
                                        onkeypress="set_license_key_po({{ $i }})"
                                        onchange="calc_change_new(this)" /></td>
                                <td style="display: none;"><input type="text" class="form-control"
                                        name="grn_qty[]" value="{{ $items->grn_qty }}" /></td>
                                <td style="display: none;"><input type="text" class="form-control"
                                        name="balance_qty[]" value="{{ abs($items->po_qty - $items->grn_qty) }}"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" step="Any"
                                        id="unitprice_{{ $i }}" name="unitprice[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->unitprice, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="value[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->value, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="discount[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->discount, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="fright[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->fright, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="customcharges[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->customcharges, 2, '.', '') }}"
                                        onchange="calc_change_new(this)" /></td>

                                <td><input type="text" class="form-control text-end" name="taxableamount[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->taxableamount, 2, '.', '') }}"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" name="vatamount[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->vatamount, 2, '.', '') }}"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" name="totalamount[]"
                                        value="{{ @App\SysHelper::com_curr_format($items->taxableamount + $items->vatamount, 2, '.', '') }}"
                                        readonly /></td>
                                <td>

                                    <?php
                                    $srno = $edit_list_srl->where('part_no', $items->part_no)->where('item_id', $items->id)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);
                                    
                                    if ($string != '') {
                                        $string = str_replace('"', '', $string);
                                    }
                                    ?>
                                    <input type="text" class="form-control" name="serial_no[]"
                                        value="{{ $string }}" />
                                </td>

                            </tr>

                            @php
                                $po_qty += $items->po_qty;
                                $qty += $items->qty;
                                $grn_qty += $items->grn_qty;
                                $balance_qty += abs($items->po_qty - $items->grn_qty);
                                $unitprice += $items->unitprice;
                                $value += $items->value;
                                $discount += $items->discount;
                                $fright += $items->fright;
                                $custom += $items->customcharges;
                                $taxableamount += $items->taxableamount;
                                $vatamount += $items->vatamount;
                                $total += $items->taxableamount + $items->vatamount;
                                $i++;
                            @endphp
                        @endforeach
                    @endif
                @endif
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="{{ count($grn_items) + 1 }}" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}

                        <input type="hidden" name="item_po_id[]" value="{{ $grn_items[0]->po_id }}" />
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
                    <td><input type="text" class="form-control text-center" name="tax[]"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off"
                            min="0" readonly>
                    </td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]"
                            autocomplete="off" step="0.01" min="0" value=""
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="taxableamount[]"
                            autocomplete="off" step="0.01" min="0" readonly></td>
                    <td><input class="form-control text-end" type="number" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="number" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control serial-no-column" type="text" name="serial_no[]"></td>
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <?php try { ?>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'proforma-invoice', 'method' => 'POST', 'id' => 'proforma-invoice']) }}


    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
    <input type="hidden" name="net_vat" id="net_vat">

    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New
            ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number') }})
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label class="form-label">Customer Name</label>
                    <div class="form-group">
                        <select onchange="get_pending_quote_list()" class="form-control js-example-basic-single"
                            name="customer" id="customer" required>
                            <option value=""></option>
                            @foreach ($customer as $value)
                                <option value="{{ @$value->id }}"
                                    {{ isset($editData) ? (!empty($editData->customer) ? (@$editData->customer == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">@lang('Proforma') @lang('Number')<span>*</span></label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_proforma_invoice', 'PF', 'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Proforma Date</label>
                    <div class="form-group">
                        @php $value_date = \Carbon\Carbon::parse( now())->format('d/m/Y'); @endphp
                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                            name="doc_date" value="{{ @$value_date }}" style="margin-top: 0px">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control" name="currency" id="currency" required>
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}"
                                    {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">LPO Number<span>*</span></label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                            value="{{ isset($editData) ? (!empty(@$editData->lpo_number) ? @$editData->lpo_number : old('lpo_number')) : old('lpo_number') }}">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">LPO Date</label>
                    <div class="form-group">
                        @php

                            if (isset($editData) && !empty($editData->lpo_date)) {
                                $value_date = Carbon\Carbon::parse($editData->lpo_date)->format('d/m/Y');
                            } elseif (!empty(old('lpo_date'))) {
                                $value_date = Carbon\Carbon::parse(old('lpo_date'))->format('d/m/Y');
                            } else {
                                $value_date = Carbon\Carbon::now()->format('d/m/Y');
                            }
                        @endphp
                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                            name="doc_date" value="{{ @$value_date }}" style="margin-top: 0px">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Payment Terms</label>
                    <div class="form-group">
                        <select class="form-control" name="payment_terms" id="payment_terms" required>
                            <option value=""></option>
                            @foreach ($paymentterms as $value)
                                <option value="{{ @$value->id }}"
                                    {{ isset($editData) ? (!empty(@$editData->payment_terms) ? (@$editData->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->title }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">Delivery Terms</label>
                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off"
                        id="delivery_terms"
                        value="{{ isset($editData) ? (!empty(@$editData->delivery_terms) ? @$editData->delivery_terms : old('delivery_terms')) : old('delivery_terms') }}">
                </div>

                <div class="col-2">
                    <label class="form-label">Sales Man<span>*</span></label>

                    <select class="form-control js-example-basic-single rounded-0" name="sales_man" id="sales_man"
                        required>
                        <option value=""></option>
                        @foreach ($sales_man as $value)
                            <option value="{{ @$value->user_id }}"
                                {{ isset($editData) ? (!empty(@$editData->sales_man) ? (@$editData->sales_man == @$value->user_id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->full_name }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div class="col-2">
                    <label class="form-label">Deal ID<span>*</span></label>
                    <input class="form-control" type="number" name="deal_id" autocomplete="off" id="deal_id"
                        value="{{ isset($editData) ? (!empty(@$editData->deal_id) ? @$editData->deal_id : old('deal_id')) : '' }}">
                </div>
                <div class="col-2">
                    <label class="form-label">Narration</label>
                    <input class="form-control" data-bs-toggle="modal" data-bs-target="#narrationModal"
                        type="text" name="narration" autocomplete="off" id="narration"
                        value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('number')) : '' }}">
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="hd_pending_qt_id" />



    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="shipping-details-tab" data-bs-toggle="tab"
                    data-bs-target="#shipping-details" type="button" role="tab"
                    aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT
                    Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="end-details-tab" data-bs-toggle="tab" data-bs-target="#end-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="true">End User
                    Details</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="shipping-details" role="tabpanel"
                aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">


                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="txtlbl">Pending list</label>
                            <div id="plist"
                                style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                            </div>
                            <a data-modal-size="modal-md" data-target="#qt_pending_popup_win" id="addQtPending"
                                data-toggle="modal"></a>
                            <input type="hidden" id="qt_id" name="qt_id">

                        </div>
                    </div>
                    <div class="col-10 mb-2">
                        <div class="row gap-rows">

                            <div class="col-3">
                                <label class="form-label">Name</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" value="" id="shipping_name"
                                        name="shipping_name">

                                </div>
                            </div>

                            <div class="col-4">
                                <label class="form-label">Address</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" value="" id="shipping_address"
                                        name="shipping_address">


                                </div>
                            </div>


                        </div>
                    </div>



                </div>
            </div>

            <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">
                    <div class="col-2">
                        <label class="form-label">Customer Type</label>
                        <div class="form-group">
                            <select class="form-control" name="customer_type" id="customer_type" required>
                                <option value="0"></option>
                                @foreach ($customertype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->customer_type) ? (@$edit->customer_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}</option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Sale Type</label>
                        <div class="form-group">
                            <select class="form-control" name="sale_type" id="sale_type">
                                <option value="0"></option>
                                @foreach ($saletype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->sale_type) ? (@$edit->sale_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}</option>
                                @endforeach
                            </select>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Customer Country</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="customer_country"
                                id="country">
                                <option data-display="" value="0"></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}" <?php try{?>
                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif
                                        @endif
                                        <?php } catch (\Throwable $th) {} ?>
                                        >{{ @$value->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Customer State</label>
                        <div class="form-group">
                            <div id="sectionStateDiv">
                                <select class="form-control js-example-basic-single" name="customer_state"
                                    id="state">
                                    <option data-display="" value="0"></option>
                                    <?php try{?>
                                    @foreach ($states as $key => $value)
                                        @if (isset($edit))
                                            <option data-display="{{ $edit->vatstate->name }}"
                                                value="{{ $edit->customer_state }}" selected>
                                                {{ $edit->vatstate->name }}</option>
                                        @else
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endif
                                    @endforeach
                                    <?php } catch (\Throwable $th) {} ?>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="end-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">End User Name</label>
                        <input type="text" class="form-control" name="end_user_name" autocomplete="off"
                            value="{{ isset($edit) ? (!empty(@$edit->end_user_name) ? @$edit->end_user_name : '') : old('end_user_name') }}" />

                    </div>
                    <div class="col-3">
                        <label class="form-label">Contact Person Name</label>
                        <input type="text" class="form-control" name="contact_person_name" autocomplete="off"
                            value="{{ isset($edit) ? (!empty(@$edit->contact_person_name) ? @$edit->contact_person_name : '') : old('contact_person_name') }}">

                    </div>
                    <div class="col-3">
                        <label class="form-label">Contact Person Email</label>
                        <input type="text" class="form-control" name="contact_person_email" autocomplete="off"
                            value="{{ isset($edit) ? (!empty(@$edit->contact_person_email) ? @$edit->contact_person_email : '') : old('contact_person_email') }}">

                    </div>
                    <div class="col-3">
                        <label class="form-label">Contact Person No</label>
                        <input type="text" class="form-control" name="contact_person_no" autocomplete="off"
                            value="{{ isset($edit) ? (!empty(@$edit->contact_person_no) ? @$edit->contact_person_no : '') : old('contact_person_no') }}">

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
                    
                    <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" />
                    </td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                  
                    
                    <td><input class="form-control" type="number text-center" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control" type="number" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="number" name="discount[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    
                    <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
               
                    <th class="text-end" scope="col"><label id="lbl_total_taxableamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_vatamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_totalamount">0</label></th>
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
        function calc_change_new(el) {
            $("#loading_bg").css("display", "block");

            // Get the current row
            var $row = $(el).closest('tr');

            

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
            var fin_vatamount = fin_taxableamount;
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
        //popup_qt_pending(8);

        $(window).ready(function() {
            $("#quotations-form").on("keypress", function(event) {
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });



        function popup_qt_pending(id) {
            $("#hd_pending_qt_id").val(id);
            $("#qt_id").val(id);
            document.getElementById('addQtPending').click();
        }

        function get_pending_quote_list() {
            var id = $("#customer").val();
            get_guote_list(id);
            get_vat(id);
        }

       

        function get_guote_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('quotation-pending') }}";
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
                            if (dataResult['data'] != "ERROR") {
                                var id = dataResult['data'][i].id;
                                var deal_name = dataResult['data'][i].deal_name;
                                var option = "<option value='" + id + "'>" + id + '- ' + deal_name +
                                    "</option>";
                                var innerHtml =
                                    "<input type='radio' onclick='popup_qt_pending(" + id +
                                    ")' id='pending_qt_" + i +
                                    "' name='pending_qt' value='" + deal_name +
                                    "'> <label for='pending_qt_" + i + "'> " + id + '- ' + deal_name +
                                    "</label><br />";

                                $("#plist").append(innerHtml);
                            } else {
                                $("#plist").empty();
                            }
                        }
                    } else {
                        $("#plist").empty();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }



        $(document).ready(function() {

            $("#get_pending_list").click(function() {
                var val = $("#customer_with_vat").val();
                var url = $('#url').val();
                alert(val);
                $.ajax({
                    type: "POST",
                    data: {
                        id: val
                    },
                    url: url + '/' + 'quotation-pending-exe',
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
                                for (var i = 0; i < len; i++) {
                                    var id = response['data'][i].id;
                                    var doc_number = response['data'][i].doc_number;
                                    var option = "<option value='" + id + "'>" + doc_number +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_qt_pending(" + id +
                                        ")' id='pending_qt_" + i +
                                        "' name='pending_qt' value='" + doc_number +
                                        "'> <label for='pending_qt_" + i + "'> " + doc_number +
                                        "</label><br />";

                                    $("#plist").append(innerHtml);

                                }
                                //$('#btn_close2').click();
                            } else {
                                $("#plist").empty();
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {}
                });
            });

        });
    </script>





    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Purchase Invoice</h2>
                <span class="page-label">Home - Purchase Invoice</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('purchase-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>New</a>
                <a href="{{ url('purchase-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i>List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            {{-- @if (isset($edit))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => '/quotations/' . $edit->id, 'method' => 'PUT', 'id' => 'purchase-invoice-create-form']) }}
                @else --}}
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-store', 'method' => 'POST', 'id' => 'purchase-invoice-create-form']) }}
            {{-- @endif --}}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" name="net_vat" id="net_vat">

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <label class="txtlbl">@lang('Vendor') <span>*</span></label>
                    <select class="form-control js-account-select" name="vendors" id="vendors">
                        <option value=""></option>
                        {{-- @foreach ($vendors as $value)
                        <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                            {{ @$value->account_name }}
                        </option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">PIV Number<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_invoice','PI' ,'doc_number') }}"
                                    readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('doc_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('doc_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">PIV Date</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($edit) && !empty($edit->date)) {
                                                @$value = date('Y-m-d', strtotime(@$edit->date));
                                            } else {
                                                if (!empty(old('pi_date'))) {
                                                    @$value = old('pi_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="pi_date" type="date" autocomplete="off" name="pi_date" value="{{ @$value }}" style="margin-top: 0px">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('pi_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('pi_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">Currency<span>*</span></label>
                                <select class="form-control" name="currency" id="currency">
                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            @if($company->currency_id == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist" style="width: 100%; height: 320px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#grn_pending_popup_win" id="addGRNPending" data-toggle="modal"></a>
                        <input type="hidden" id="grn_id" name="grn_id">
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>

                </div>
                
                        
                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-lg-4 mb-0">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Number') <span>*</span></label>
                                <input
                                    class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                    type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->lpo_number) ? @$edit->lpo_number : old('lpo_number')) : '' }}">
                                <span class="focus-border"></span>
                                @if ($errors->has('lpo_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('lpo_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Date') <span>*</span></label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit) && !empty($edit->date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit->date));
                                    } else {
                                        if (!empty(old('lpo_date'))) {
                                            @$value = old('lpo_date');
                                        } else {
                                            @$value = date('Y-m-d');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="lpo_date" type="date" autocomplete="off" name="lpo_date" value="{{ @$value }}" style="margin-top:0px;">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Payment Terms')<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
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
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Number')<span>*</span></label>
                                <input class="form-control" type="text" name="bill_number" autocomplete="off" id="bill_number" value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}" onchange="updateNarration()">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Date')*</label>
                                @php
                                    $value = date('m/d/Y');
                                    if (isset($edit) && !empty($edit->bill_date)) {
                                        @$value = date('m/d/Y', strtotime(@$edit->bill_date));
                                    } else {
                                        if (!empty(old('bill_date'))) {
                                            @$value = old('bill_date');
                                        } else {
                                            @$value = date('m/d/Y');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="bill_date" type="date" autocomplete="off"
                                    name="bill_date" value="{{ @$value }}" required >
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('AWB No') <span>*</span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('BOE No') <span>*</span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                    type="text" name="boeno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->boeno) ? @$edit->boeno : old('boeno')) : old('boeno') }}"
                                    id="boeno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Warehouse') <span>*</span></label>
                                <input class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Reference') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="reference" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->reference) ? @$edit->reference : old('reference')) : old('reference') }}"
                                    id="reference">
                                <span class="focus-border"></span>
                                @if ($errors->has('reference'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('reference') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}"
                                    readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('createdby'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('createdby') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('GRN No')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="grn_no" autocomplete="off" id="grn_no"
                                    value="{{ isset($edit) ? (!empty(@$edit->grn_no) ? @$edit->grn_no : old('grn_no')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('GRN Date')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="date" name="grn_date" autocomplete="off" id="grn_date" required
                                    value="{{ isset($edit) ? (!empty(@$edit->grn_date) ? @$edit->grn_date : old('grn_date')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">@lang('Salesman Name')*</label>
                                <select class="form-control" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Deal Id')<span>*</span></label>
                                <input class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : '' }}">
                            </div>
                        </div>

                    </div>
                    
                </div>

                <div class="col-lg-12 mb-0">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <a class="primary-btn fix-gr-bg" data-modal-size="modal-md" data-target="#add_to_do"
                                            data-toggle="modal"
                                            style="padding: 2px 7px !important; margin:5px 0 0 5px; float: right;"><span
                                                data-toggle="tooltip" title="Add Shipping Details" class="ti-plus"></span></a>
                                        <label class="dynamicslbl">@lang('Shipping Name') <span></span></label>
                                        <input type="text" class="form-control" cols="0"
                                        rows="4" name="shipping_name"
                                        id="shipping_name">{{ isset($edit) ? (!empty(@$edit->shipping_name) ? @$edit->shipping_name : '') : old('shipping_name') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Shipping Address 1') <span></span></label>
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_1"
                                            id="shipping_address_1">{{ isset($edit) ? (!empty(@$edit->shipping_address_1) ? @$edit->shipping_address_1 : '') : old('shipping_address_1') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Ship to Address 2') <span></span></label>
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_2"
                                            id="shipping_address_2">{{ isset($edit) ? (!empty(@$edit->shipping_address_2) ? @$edit->shipping_address_2 : '') : old('shipping_address_2') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact No') <span></span></label>
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_contact_no"
                                            id="shipping_contact_no">{{ isset($edit) ? (!empty(@$edit->shipping_contact_no) ? @$edit->shipping_contact_no : '') : old('shipping_contact_no') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="vat" role="tabpanel" aria-labelledby="vat-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Supplier Type') <span></span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                            name="supplier_type" id="supplier_type">
                                            <option value="0"></option>
                                            @foreach ($suppliertype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Purchase Type') <span></span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                            name="purchase_type" id="purchase_type">
                                            <option value="0"></option>
                                            @foreach ($purchasetype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Supplier Country')<span>*</span></label>
                                        <select class="form-control" name="supplier_country" id="country" required>
                                            <option data-display="" value=""></option>
                                            @foreach ($countries as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    <?php try{?>                                                        
                                                    @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif
                                                    @endif
                                                    <?php } catch (\Throwable $th) {} ?>
                                                    >{{ @$value->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Supplier State')<span>*</span></label>
                                        <div id="sectionStateDiv">
                                            <select class="form-control" name="supplier_state" id="state">
                                                <option data-display="" value=""></option>
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
                    </div>
                </div>
        </div>
        <div class="equipment comon-status row mt-4 d-block">
            <table class="table table-bordered table-striped" id="grn-pi-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>@lang('Part No')</th>
                        <th>@lang('GRN Qty')</th>
                        <th>@lang('VAT')</th>
                        <th>@lang('Qty')</th>
                        <th>@lang('Unit Price')</th>
                        <th>@lang('Value')</th>
                        <th style="width:70px;" class="text-right">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a> --}}
                            Discount
                        </th>
                        <th style="width:70px;" class="text-right">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalFreight">Freight</a> --}}
                            Freight
                        </th>
                        <th style="width:130px;" class="text-right">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalCustom">Custom Charges</a> --}}
                            Custom Charges
                        </th>                        
                        <th>@lang('Taxable Amount')</th>
                        <th>@lang('VAT Amount')</th>
                        <th>@lang('Total Amount')</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <td class="sstablefoot"></td>
                        <td class="sstablefoot"></td>
                        <td class="sstablefoot"></td>
                        <td class="sstablefoot"></td>
                        <td class="sstablefoot text-right"><label id="qty_total">0</label></td>
                        <td class="sstablefoot text-right"><label id="unitprice_total">0.00</label></td>
                        <td class="sstablefoot text-right"><label id="value_total">0.00</label></td>
                        <td class="sstablefoot text-right"><label id="discount_total">0.00</label></td>
                        <td class="sstablefoot text-right">0.00</td>
                        <td class="sstablefoot text-right">0.00</td>
                        <td class="sstablefoot text-right"><label id="taxableamount_total">0.00</label></td>
                        <td class="sstablefoot text-right"><label id="vatamount_total">0.00</label></td>
                        <td class="sstablefoot text-right"><label id="totalamount_total">0.00</label></td>
                    </tr>
                </tfoot>
            </table>

            <div style="display: none;">
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowPO"><span
                        class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>


            <script>
                function fn_addRow(id) {
                    var rownum = document.getElementById('po-row-count').value;
                    if (id == rownum) {
                        document.getElementById('po-row-count').value = (Number(rownum) + Number(1));
                        document.getElementById('addRowPO').click();
                    }
                }

                function ddl_part_change(id) {
                    var selOpt = $('#part_number_' + id + ' :selected').val();
                    $('#part_number_txt_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                    var selOpt2 = $('#part_number_txt_' + id + ' :selected').text();
                    $('#description_' + id + '').val(selOpt2);
                    $('#description_' + id + '').focus();
                }



                function fn_payment_terms() {
                    var val_payment_terms = $('#payment_terms').val();
                    if (val_payment_terms == 22) {
                        $('#div_payment_terms').css('display', 'block');
                    } else {
                        $('#div_payment_terms').css('display', 'none');
                    }
                }

                function fn_shipping_name() {
                    var shipping_id = $('#shipping_name').val();
                    var shipping_data = $('#ship_' + shipping_id).val();
                    var ret = shipping_data.split("#");
                    $('#shipping_address_1').val(ret[0]);
                    $('#shipping_address_1').focus();
                    $('#shipping_address_2').val(ret[1]);
                    $('#shipping_address_2').focus();
                    $('#shipping_contact_no').val(ret[2]);
                    $('#shipping_contact_no').focus();
                }
            </script>



        </div>

        <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
            <div class="col-lg-12 text-right">
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowEquipment">
                    <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>
        </div>





        <div class="equipment comon-status row mt-4 d-block">
            <table class="table table-bordered table-striped" id="pi-table2" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:100px;">@lang('Name')</th>
                        <th style="width:350px;">@lang('Credit Account')</th>
                        <th style="width:70px;">@lang('Amount')</th>
                        <th style="width:80px;">@lang('Remarks')
                            <input type="hidden" value="1" id="fright_row" />
                            <a style="cursor: pointer;" class="btn-md float-right" onclick="add_fright()"><i class="fa fa-plus-square" aria-hidden="true"></i></a></th>
                    </tr>
                    <script>
                        function add_fright()
                        {
                            var id = $('#fright_row').val();
                            id=Number(id)+1;
                            $('#fright_row').val(id);
                            $('#fright_row_'+id).css("display", "");
                        }
                    </script>
                </thead>
                <tbody>
                    <tr id="fright_row_1">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(1)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"  onchange="updateNarration()"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_2">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(2)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"  onchange="updateNarration()"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_3">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(3)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]"  onchange="updateNarration()"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_4">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(4)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]"  onchange="updateNarration()"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_5">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(5)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]"  onchange="updateNarration()"
                                autocomplete="off">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>



        <div class="row mt-40">
            <div class="col-lg-8 text-right font-weight-bold">
                  Total Amount : <label id="total_amount">0.00</label>
            </div>
            <div class="col-lg-4 text-right">
                
                                <a class="btn btn-danger" onclick="get_adjustments()">@lang('Adjustment')</a>

                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <span class="ti-check"></span>
                    @if (isset($edit))
                        @lang('lang.update')
                    @else
                        @lang('lang.save')
                    @endif
                    @lang('Purchase Invoice')

                </button>
            </div>
        </div>

        {{ Form::close() }}

    </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


<script>
    function updateNarration() {
        const billNumber = document.getElementById('bill_number').value.trim();
        const remarks = document.querySelectorAll('input[name="cfc_remarks[]"]');
        let remarkValues = [];

        remarks.forEach(input => {
            if (input.value.trim() !== '') {
                remarkValues.push(input.value.trim());
            }
        });

        const narrationValue = billNumber + ' - ' + remarkValues.join(', ');
        document.getElementById('narration').value = narrationValue;
    }

    // Trigger when bill_number or any cfc_remarks[] is changed
    document.getElementById('bill_number').addEventListener('input', updateNarration);
    document.querySelectorAll('input[name="cfc_remarks[]"]').forEach(input => {
        input.addEventListener('input', updateNarration);
    });
</script>


    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="grn_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Purchase Invoice Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_grn_id" />
                        <input type="hidden" id="hd_pending_po_id" />
                        <div class="container-fluid">
                            {{-- <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Select All') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_new_reference" name="bi_new_reference" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Product Code') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_amount_to_adjust" name="bi_amount_to_adjust" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Contains') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_contains" name="bi_contains" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                        </div> --}}

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('#') </th>
                                                    <th></th>
                                                    <th>@lang('Part No')</th>
                                                    <th>@lang('GRN Qty')</th>
                                                    <th>@lang('VAT')</th>
                                                    <th>@lang('Qty')</th>
                                                    <th>@lang('Unit Price')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
                                                    <th>@lang('Fright')</th>
                                                    <th>@lang('Customs')</th>
                                                    <th>@lang('Taxable Amount')</th>
                                                    <th>@lang('VAT Amount')</th>
                                                    <th>@lang('Total Amount')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>

                                        <button class="btn btn-primary bg-success" type="button" id="addGRNPendingItems">
                                            Add Selected
                                        </button>
                                        {{-- <input class="btn btn-primary fix-gr-bg" type="" value="save" onclick="return validateAttachForm()"> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- popup --}}
    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Add Shipping</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control {{ $errors->has('shipping_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="shipping_name_add" name="shipping_name"
                                        value="{{ isset($editData) ? @$editData->shipping_name : old('shipping_name') }}">
                                    <label> @lang('Shipping Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control {{ $errors->has('contact_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="contact_name_add" name="contact_name"
                                        value="{{ isset($editData) ? @$editData->contact_name : old('contact_name') }}">
                                    <label> @lang('Contact Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        type="number" id="contact_no_add" name="contact_no"
                                        value="{{ isset($editData) ? @$editData->contact_no : old('contact_no') }}">
                                    <label> @lang('Contact No') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}"
                                        type="text" id="address1_add" name="address1"
                                        value="{{ isset($editData) ? @$editData->address1 : old('address1') }}">
                                    <label> @lang('Address 1') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}"
                                        type="text" id="address2_add" name="address2"
                                        value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">
                                    <label> @lang('Address 2') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="btn btn-primary tr-bg" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="btn btn-primary fix-gr-bg" type="submit" value="save"
                                            onclick="return validateAttachForm()">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------------------- --}}
    
    {{-- ModalDiscount --}}
    <div class="modal fade" id="modalDiscount" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Discount</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-invoice-items-cart-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Discount Amount</label>
                                <input type="text" class="form-control" id="discount_amount" name="discount_amount" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="discount_amount_pi_id" value="{{ @$pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split Discount</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalDiscount --}}
    {{-- ModalFreight --}}
    <div class="modal fade" id="modalFreight" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Freight</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-invoice-items-cart-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Freight Amount</label>
                                <input type="text" class="form-control" id="freight_amount" name="freight_amount" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="freight_amount_pi_id" value="{{ @$pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split freight</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalFreight --}}
    {{-- ModalCustom --}}
    <div class="modal fade" id="modalCustom" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Custom Charges</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-invoice-items-cart-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Custom Charges Amount</label>
                                <input type="text" class="form-control" id="custom_amount" name="custom_amount" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="custom_amount_pi_id" value="{{ @$pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split Custom Charges</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalCustom --}}

    
{{-- attachment start--}}
<div class="modal fade admin-query" id="attachment_popup_win" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0 p-3">
                <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
                <button class="close" data-dismiss="modal" type="button">
                    ×
                </button>
            </div>
            <div class="modal-body m-0 p-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('Attach File') <span>*</span> </label>
                                <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('Date') <span>*</span> </label>
                                <input class="form-control" type="date" id="att_date" name="att_date" value="{{ date('Y-m-d') }}"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('File Name') <span>*</span> </label>
                                <input class="form-control" type="text" id="doc_name" name="doc_name" value=""/>
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
                        <table id="att-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
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

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12 text-right">
                                    <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                        @lang('Close')
                                    </button>
                                    <input type="hidden" id="srl_id" />
                                    <button class="btn btn-success" type="button" onclick="add_attachment()">
                                        Add Attachment
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-purchase-invoice-attachment') }}";
        
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
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#vendors :selected').text() + " " + $('#doc_number').val());

        var action = "{{ URL::to('view-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : 0,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function delete_attachment(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : 0,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    </script>

{{-- attachment end--}}

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
            //$("#cfc_cal_amount_" + id).val(amt);
            
            var cfc_total=0;
            var total=0;
            for(var i=1; i <= 5; i++){
                cfc_total = $("#cfc_amount_" + i).val()
                cfc_total = (cfc_total === '') ? '0' : cfc_total;
                total += Number(cfc_total);
                
            }
            var tot = $("#totalamount_total").html();
            tot = (tot === '') ? '0' : tot;
            $("#total_amount").html((Number(total)+Number(tot)).toFixed(@json(session('logged_session_data.decimal_point'))));
        }

        function popup_grn_pending(id,po_id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_grn_id").val(id);
            $("#hd_pending_po_id").val(po_id);
            $("#grn_id").val(id);
            document.getElementById('addGRNPending').click();
            $("#loading_bg").css("display", "none");
        }

        function calc_change(id) {
            //var net_vat = $('#net_vat').val();
            //var net_vat = $('#vat_percentage').val();
            var tax = $('#tax_' + id + '').val();
            var qty = $('#qty_' + id + '').val();
            var unitprice = $('#unitprice_' + id + '').val();
            var value = $('#value_' + id + '').val();
            var discount = $('#discount_' + id + '').val();
            var taxamount = $('#taxamount_' + id + '').val();
            var vatamount = $('#vatamount_' + id + '').val();


            tax = (tax === '') ? '0' : tax;
            qty = (qty === '') ? '0' : qty;
            unitprice = (unitprice === '') ? '0' : unitprice;
            var fin_value = (unitprice * qty);
            $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


            value = (value === '') ? '0' : value;
            discount = (discount === '') ? '0' : discount;
            var fin_taxableamount = ((unitprice * qty) - Number(discount));
            $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(tax)) / 100);
            $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            $('#totalamount_' + id + '').val((Number(fin_taxableamount)+Number(fin_vatableamount)).toFixed(@json(session('logged_session_data.decimal_point'))));

            calc_total();
        }

        function calc_total() {
            var numItems = $('.rno').length;

            //alert(numItems);

            var countrow = document.getElementById('row-count').value;
            var t1 = 0,
                t2 = 0,
                t3 = 0,
                t4 = 0,
                t5 = 0,
                t6 = 0,
                t7 = 0;
            for (var i = 1; i <= countrow; i++) {
                t1 += Number($('#qty_' + i).val());
                t2 += Number($('#unitprice_' + i).val());
                t3 += Number($('#value_' + i).val());
                t4 += Number($('#discount_' + i).val());
                t5 += Number($('#taxamount_' + i).val());
                t6 += Number($('#vatamount_' + i).val());
            }
            $('#qty_total').text(t1);
            $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
        }
    </script>
    
    <script>
                
        $(document).on("change", "#vendors", function () {
            var id = $("#vendors").val();
            get_vat(id);
            get_po_list(id);
        });

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
                            $("#net_vat").val(dataResult['data'].vat_percentage);
                            $("#loading_bg").css("display", "none");     }
                        }
                });
        }
        
        function get_po_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('goods-receipt-note-for-pi') }}";
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
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $("#plist").empty();
                            for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var po_id = dataResult['data'][i].po_id;
                                    var doc_number = dataResult['data'][i].doc_number;
                                    var option = "<option value='" + id + "'>" + doc_number +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_grn_pending(" + id +
                                        ", "+ po_id +")' id='pending_grn_" + i +
                                        "' name='pending_grn' value='" + doc_number +
                                        "'> <label for='pending_grn_" + i + "'> " + doc_number +
                                        "</label><br />";

                                    $("#plist").append(innerHtml);
                                    
                      
                            }                        
                        }
                        else{
                            $("#plist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_deal_code() {
            var action = "{{ URL::to('get-deal-code-from-id') }}";
                $.ajax({
                    url: action,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        deal_id: $('#deal_id').val(),
                    },
                    cache: false,
                    success: function(dataResult) {
                        $("#deal_id").val(dataResult);
                        }
                });
        }


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
                                description: item.description
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

        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#description_new').val(selectedData.description || '');
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

<!-- Modal Adjustment-->
    <script>
        function get_adjustments() {

                    $("#loading_bg").css("display", "block");

                    $('#adj_piv_amount_actual').val($("input[name='totalamount[]']").val());
                    $('#adj_sup_id').val($('#vendors').val());

                    var action = "{{ URL::to('purchase-invoice-get-adjustment') }}";
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            vendors: $("#vendors").val(),
                        },
                        cache: false,
                        success: function(dataResult) {
                            var data = JSON.parse(dataResult);
                            // Handle 'unadjusted'
                            if (data.unadjusted && data.unadjusted.length > 0) {
                            var getSelectedRows="";
                                for (var i = 0; i < data.unadjusted.length; i++) {
                                    var a= (data.unadjusted[i].amount-data.unadjusted[i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    getSelectedRows +="<tr>\
                                         <td class='border'>"+data.unadjusted[i].doc_date+"</td>\
                                         <td class='border'>"+data.unadjusted[i].doc_number+"</td>\
                                         <td class='border'>"+data.unadjusted[i].account_name+"</td>\
                                        <td class='border text-right'>"+a+"</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+data.unadjusted[i].doc_number+"' class='form-control text-right' value='' onclick=\"set_adjust('"+(data.unadjusted[i].amount-data.unadjusted[i].adj_amount)+"','"+data.unadjusted[i].doc_number+"')\" />\
                                            <input type='hidden' name='paymentno[]' value='"+data.unadjusted[i].doc_number+"'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+a+"'/>\
                                        </td>\
                                        </tr>";
                                }
        
                            }

                            // Handle 'unadjusted_pdc'
                            if (data.unadjusted_pdc && data.unadjusted_pdc.length > 0) {
                            var getSelectedRows2="";
                                for (var j = 0; j < data.unadjusted_pdc.length; j++) {
                                    getSelectedRows2 +="<tr>\
                                         <td class='border'>"+data.unadjusted_pdc[i].doc_date+"</td>\
                                         <td class='border'>"+data.unadjusted_pdc[i].doc_number+"</td>\
                                         <td class='border'>"+data.unadjusted_pdc[i].account_name+"</td>\
                                        <td class='border text-right'>"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+data.unadjusted_pdc[i].doc_number+"' class='form-control text-right' value='"+data.unadjusted_pdc[i].adj_amount+"' onclick=\"set_adjust('"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"','"+data.unadjusted[i].doc_number+"')\" />\
                                            <input type='hidden' name='paymentno[]' value='"+data.unadjusted_pdc[i].doc_number+"'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"'/>\
                                        </td>\
                                        </tr>";
                                }
                            }
                            
                                $('#adjustment_table tbody').empty();
                                $("#adjustment_table tbody").append(getSelectedRows);
                                $("#adjustment_table tbody").append(getSelectedRows2);
                        }
                    });
                    $("#btnModalAdjustment").click();
                    $("#loading_bg").css("display", "none");
                }
    </script>
    <script>
$(document).ready(function() {
    $('#adjustmentForm').on('submit', function(e) {
        e.preventDefault();

        // Collect the form data
        let formData = $(this).serialize();

        // Optional: basic validation
        

        // AJAX submission
        $.ajax({
            url: "{{ url('purchase-invoice-add-adjustment-cart') }}", // Replace with your actual route
            type: "POST",
            data: formData,
            success: function(response) {
                // Handle success response
                alert('Adjustment saved successfully.');
                $('#ModalAdjustment').modal('hide'); // Hide modal if using Bootstrap
            },
            error: function(xhr) {
                // Handle errors
                alert('Error occurred while saving. Check console.');
                console.log(xhr.responseText);
            }
        });
    });
});
</script>
    <a id="btnModalAdjustment" data-toggle="modal" data-target="#ModalAdjustment"></a>
    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Unadjusted List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="adjustmentForm" method="POST">
                @csrf
                {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="adjustment_table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    {{-- @if(count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="adj_sup_id" name="adj_sup_id" value=""/>
                    <input type="hidden" id="adj_piv_id" name="adj_piv_id" value=""/>
                    <input type="hidden" id="adj_piv_no" name="adj_piv_no" value=""/>
                    <input type="hidden" id="adj_piv_date" name="adj_piv_date" value=""/>
                    <input type="hidden" id="adj_piv_amount" name="adj_piv_amount" value=""/>
                    <input type="hidden" id="adj_piv_amount_actual" name="adj_piv_amount_actual" value=""/>
                    <input type="hidden" id="adj_piv_amount_adjusted" name="adj_piv_amount_adjusted" value="0"/>
                    <button class="btn btn-success" type="submit" >Adjust</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
                {{-- {{ Form::close() }} --}}
                </form>
            </div>
        </div>
    </div>
    <script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_piv_amount']").val());
    let currentAdjusted = 0;

    // Sum up all currently adjusted values
    $("input[id^='set_amt_']").each(function () {
        let val = parseFloat($(this).val());
        if (!isNaN(val)) {
            currentAdjusted += val;
        }
    });

    let remaining = maxAdjustable - currentAdjusted;

    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    // Check how much is available for this line
    let adjustAmount = parseFloat(amt);
    if (adjustAmount > remaining) {
        adjustAmount = remaining;
    }

    $('#set_amt_' + id).val(adjustAmount);

    // Optional: update hidden adjusted total
    $("input[name='adj_piv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>
    <!-- Modal Adjustment-->
@endsection

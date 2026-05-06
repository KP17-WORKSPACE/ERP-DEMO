    <?php try { ?>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update', 'method' => 'POST', 'id' => 'purchase-invoice-create-form']) }}

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" id="pi_id" value="{{ isset($edit_pi) ? $edit_pi->id : '' }}">
            <input type="hidden" name="net_vat" id="net_vat" value="{{ @$edit_pi_items[0]->tax }}">
            <input type="hidden" name="doc_number_main" id="doc_number_main" value="{{ $edit_pi->doc_number }}">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ @$edit_pi->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a type="submit" class="btn btn-light" href="{{url('purchase-invoice/create')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('purchase-invoice/'.$edit_pi->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel PI</a></li>
                    <li><a class="dropdown-item" href="{{url('purchase-invoice/'.$edit_pi->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adjustmentModal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button></li>
                </ul>
            </div>
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-4">
                                            <label class="form-label">Vendor Name</label>
                                            <div class="form-group">
                                                <select class="form-control " name="vendors" id="vendors" onchange="get_pending_po_list()">
                                                <option value=""></option>
                                                @foreach ($vendors as $value)
                                                    <option value="{{ @$value->id }}" @if(isset($grn) && $edit_pi->vendors == $value->id) selected @endif>
                                                        {{ @$value->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                    


                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">PI Number</label>
                                            <div class="form-group">
                                                <input
                                class="form-control"
                                type="text" name="doc_number" autocomplete="off" id="doc_number"
                                value="{{ @$edit_pi->doc_number }}"
                                readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">PI Date</label>
                                            <div class="form-group">
                                           @php
                                                $rawDate = old('grn_date') ?? ($edit_pi->grn_date ?? null);
                                                $value = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
                                            @endphp
                                            <input class="form-control date-picker" id="grn_date" type="text" autocomplete="off" name="grn_date" value="{{ @$value }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group"><select
                                class="form-control"
                                name="currency" id="currency">
                                {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                                @foreach ($currency as $value)
                                    <option value="{{ @$value->id }}"
                                    @if($edit_pi->currency_id == $value->id) selected @endif>
                                        {{ @$value->code }}
                                    </option>
                                @endforeach
                            </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By</label>
                                          
                                                
                                                <input readonly type="text" class="form-control" name="createdby" id="createdby" value="{{$edit_pi->createdby->full_name}}">

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab" data-bs-target="#shipping-details" type="button" role="tab" aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details" type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT Details</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">


                <div class="col-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist" style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#grn_pending_popup_win" id="addGRNPending" data-toggle="modal"></a>
                        <input type="hidden" id="grn_id" name="grn_id">
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
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
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->lpo_number) ? @$edit_pi->lpo_number : old('lpo_number')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">LPO Date</label>
                                                <div class="form-group">
                                                      @php
                                                $rawDate = old('grn_date') ?? ($edit_pi->lpo_date ?? null);
                                                $value = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
                                            @endphp
                                                    <input class="form-control date-picker" id="lpo_date" type="text" autocomplete="off" name="lpo_date" value="{{ @$value }}" style="margin-top:0px;">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Payment Terms</label>
                                                <div class="form-group">
                                                    <select
                                    class="form-control"
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                    <option value=""></option>
                                    @foreach ($paymentterms as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit_pi) ? (!empty(@$edit_pi->payment_terms) ? (@$edit_pi->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                                </div>
                                                <div id="div_payment_terms" style="display: none; padding-top: px;">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                                        <input
                                                            class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                                            type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                                            value="{{ isset($edit_pi) ? (!empty(@$edit_pi->payment_terms2) ? @$edit_pi->payment_terms2 : old('payment_terms2')) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Bill Number</label>
                                                <div class="form-group">
                                <input class="form-control" type="text" name="bill_number" autocomplete="off" id="bill_number" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->bill_number) ? @$edit_pi->bill_number : old('bill_number')) : '' }}" onchange="updateNarration()">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Bill Date</label>
                                                <div class="form-group">
                                                  @php
                                                    $rawDate = old('bill_date') ?? ($edit_pi->bill_date ?? now());
                                                    $value = \Carbon\Carbon::parse($rawDate)->format('d/m/Y');
                                                @endphp
                                <input class="form-control date-picker" id="bill_date" type="text" autocomplete="off"
                                    name="bill_date" value="{{ @$value }}" required >
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">AWB No</label>
                                                <div class="form-group">
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->awbno) ? @$edit_pi->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">BOE No</label>
                                                <div class="form-group">
                                <input class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                    type="text" name="boeno" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->boeno) ? @$edit_pi->boeno : old('boeno')) : old('boeno') }}"
                                    id="boeno">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Warehouse</label>
                                                <div class="form-group">
                                                <input class="form-control"
                                                    type="text" name="warehouse" autocomplete="off"
                                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->warehouse) ? @$edit_pi->warehouse : old('warehouse')) : old('warehouse') }}"
                                                    id="warehouse">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Reference</label>
                                                <div class="form-group">
                                            <input
                                                class="form-control"
                                                type="text" name="reference" autocomplete="off"
                                                value="{{ isset($edit_pi) ? (!empty(@$edit_pi->reference) ? @$edit_pi->reference : old('reference')) : old('reference') }}"
                                                id="reference">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">GRN No</label>
                                                <div class="form-group">
                                <input
                                    class="form-control"
                                    type="text" name="grn_no" autocomplete="off" id="grn_no"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->grn_no) ? @$edit_pi->grn_no : old('grn_no')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">GRN Date</label>
                                                <div class="form-group">
                                                    @php
    $rawDate = old('grn_date') ?? ($edit_pi->grn_date ?? null);
    $grnDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
@endphp
                                <input
                                    class="form-control date-picker"
                                    type="text" name="grn_date" autocomplete="off" id="grn_date" required
                                    value="{{$grnDate}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Salesman Name</label>
                                                <div class="form-group">
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($value->user_id==$edit_pi->sales_person) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Narration</label>
                                                <div class="form-group">
                                <input
                                    class="form-control" data-bs-toggle="modal"
                                        data-bs-target="#narrationModal" 
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->narration) ? @$edit_pi->narration : old('narration')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Deal Id</label>
                                                <div class="form-group">
                                <input class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->deal_id) ? @$edit_pi->deal_id : old('deal_id')) : '' }}">
                                                </div>
                                            </div>

                    </div>
                </div>


                                            
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="shipping-details" role="tabpanel" aria-labelledby="shipping-details-tab">
                                        <div class="row gap-rows">
                                            <div class="col-2">
                                                <label class="form-label">Shipping Name</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                        rows="4" name="shipping_name"
                                        id="shipping_name" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_name) ? @$edit_pi->shipping_name : '') : old('shipping_name') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Shipping Address 1</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_1"
                                            id="shipping_address_1" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_address_1) ? @$edit_pi->shipping_address_1 : '') : old('shipping_address_1') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Ship to Address 2</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_2"
                                            id="shipping_address_2" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_address_2) ? @$edit_pi->shipping_address_2 : '') : old('shipping_address_2') }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Contact No</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_contact_no"
                                            id="shipping_contact_no" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_contact_no) ? @$edit_pi->shipping_contact_no : '') : old('shipping_contact_no') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row gap-rows">
                                            <div class="col-2">
                                                <label class="form-label">Supplier Type</label>
                                                <div class="form-group"> 
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                            name="supplier_type" id="supplier_type">
                                            <option value="0"></option>
                                            @foreach ($suppliertype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit_pi) ? (!empty(@$edit_pi->supplier_type) ? (@$edit_pi->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Purchase Type</label>
                                                <div class="form-group">
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                            name="purchase_type" id="purchase_type">
                                            <option value="0"></option>
                                            @foreach ($purchasetype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit_pi) ? (!empty(@$edit_pi->purchase_type) ? (@$edit_pi->purchase_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Supplier Country</label>
                                                <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="supplier_country" id="country" required>
                                            <option data-display="" value=""></option>
                                            @foreach ($countries as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    <?php try{?>                                                        
                                                    @if (isset($edit_pi)) @if (@$edit_pi->supplier_country == $value->id) selected @endif
                                                    @endif
                                                    <?php } catch (\Throwable $th) {} ?>
                                                    >{{ @$value->name }} </option>
                                            @endforeach
                                        </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Supplier State</label>
                                                <div class="form-group">
                                                    <div id="sectionStateDiv">
                                            <select class="form-control js-example-basic-single" name="supplier_state" id="state">
                                                <option data-display="" value=""></option>
                                                <?php try{?>
                                                    @foreach ($states as $key => $value)                                                    
                                                        <option value="{{ $value->id }}" @if($edit_pi->supplier_state==$value->id) selected @endif>{{ $value->name }}</option>
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
                            <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Part No') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Description')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Tax')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Qty')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Price')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Value')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Freight <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#freightModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Custom <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#customModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($edit_pi_items) && count($edit_pi_items) > 0)
                                         @php $i=1; $po_qty=0; $qty=0; $executed_qty=0; $balance_qty=0; $unitprice=0; $value=0; $discount=0; $fright=0; $custom=0; $taxableamount = 0; $vatamount = 0; $total = 0; $grn_qty=0; @endphp
                    @if (count($edit_pi_items)>0)
                        @foreach ($edit_pi_items as $items)
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
                                <input type="hidden" ame="product_type[]" value="{{ $items->product_type }}" />
                                <input type="hidden" name="item_po_id[]" value="{{ $items->po_id }}" />
                                <input type="hidden" name="item_id[]" value="{{ $items->id }}" />
                            </td>
                            <td><input type="text" class="form-control" name="part_number_txt[]" value="{{ $items->partno ?? 0 }}" readonly/>                            
                                <input type="hidden"  name="part_number[]" value="{{ $items->part_number }}" /></td>
                            <td><input type="text" class="form-control" name="description[]" value="{{ $items->description ?? 0 }}" readonly/></td>
                            
                        @if (session('logged_session_data.company_id')==2)
                        <td>{{ $items->hscode }}</td>
                        @endif

                            <td style="display: none;"><input type="text" class="form-control" id="po_qty_{{ $i }}" name="po_qty[]" value="{{ $items->po_qty }}" /></td>
                            <td><input type="text" class="form-control text-center" name="tax[]" value="{{ number_format($items->tax ?? 0,0,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-center" name="qty[]" value="{{ $items->qty }}"  onkeypress="set_license_key_po({{ $i }})" onchange="calc_change_new(this)"/></td>
                            <td style="display: none;"><input type="text" class="form-control" name="grn_qty[]" value="{{ $items->grn_qty }}" /></td>
                            <td style="display: none;"><input type="text" class="form-control" name="balance_qty[]" value="{{ abs($items->po_qty - $items->grn_qty) }}" readonly /></td>
                            <td><input type="text" class="form-control text-end" step="Any" id="unitprice_{{ $i }}" name="unitprice[]" value="{{ @App\SysHelper::com_curr_format($items->unitprice,2,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="value[]" value="{{ @App\SysHelper::com_curr_format($items->value,2,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="discount[]" value="{{ @App\SysHelper::com_curr_format($items->discount,2,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="fright[]" value="{{ @App\SysHelper::com_curr_format($items->fright,2,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="customcharges[]" value="{{ @App\SysHelper::com_curr_format($items->customcharges,2,'.','') }}" onchange="calc_change_new(this)"/></td>
                            
                            <td><input type="text" class="form-control text-end" name="taxableamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount,2,'.','') }}" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="vatamount[]" value="{{ @App\SysHelper::com_curr_format($items->vatamount,2,'.','') }}" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="totalamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', '') }}" readonly/></td>
                            {{-- <td >

                                /*
                                    $srno = $edit_list_srl->where('part_no',$items->part_no)->where('item_id',$items->id)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);

                                    if($string!=""){
                                        $string=str_replace('"', '',$string);
                                    }*/
                                
                                <input type="text" class="form-control" name="serial_no[]" value="{{ $string }}" /></td> --}}
                            
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
                        $total += $items->taxableamount+$items->vatamount;
                        $i++;
                        @endphp
                        @endforeach
                    @endif
                    @endif
                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                </select>
                                                {{-- on focus add this class and its funcanalities js-product-select --}}
                                                <input type="hidden" name="item_id[]" value="0" />
                                                <input type="hidden" name="item_po_id[]" value="{{ $edit_pi_items[0]->pi_id }}" />
                                            </td> 
                                            <td>                                                                    
                                                <input class="form-control" type="text" name="description[]" autocomplete="off" readonly="true">
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                                            <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control" type="number" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control" type="number" name="fright[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" scope="col" >Total</th>
                                            <th class="text-center"><label id="lbl_total_qty" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_price" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_value" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_discount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_fright" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_customcharges" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_taxableamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_vatamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_totalamount" >0</label></th>
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

        <div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="freightModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="customModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="adjustmentModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" > 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Unadjusted List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <table class="table" id="adjustment_table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-end">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @if(count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-payment/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-end">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-end"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-end" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="paymentno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-payment/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-end">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-end"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-end" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="paymentno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
                        <input type="hidden" id="adj_sup_id" name="adj_sup_id" value="{{ $edit_pi->vendors }}"/>
                        <input type="hidden" id="adj_piv_id" name="adj_piv_id" value="{{ $edit_pi->id }}"/>
                        <input type="hidden" id="adj_piv_no" name="adj_piv_no" value="{{ $edit_pi->doc_number }}"/>
                        <input type="hidden" id="adj_piv_date" name="adj_piv_date" value="{{ $edit_pi->pi_date }}"/>
                        <input type="hidden" id="adj_piv_amount" name="adj_piv_amount" value="{{ $adjusted_amt }}"/>
                        <input type="hidden" id="adj_piv_amount_actual" name="adj_piv_amount_actual" value="{{ $adjusted_amt_actual }}"/>
                        <input type="hidden" id="adj_piv_amount_adjusted" name="adj_piv_amount_adjusted" value="0"/>
						<button type="submit" class="btn btn-light add-btn ms-2" id="discount_add_btn">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
						</button>
					</div>
                {{ Form::close() }}
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
            validRows.push({ index, input });
        }
    });

    if (totalValue === 0) {
        alert("All rows have empty or zero 'Value'. Nothing to split.");
        return;
    }

    validRows.forEach(({ index, input }) => {
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

document.getElementById("discount_add_btn").addEventListener("click", function () {
    splitAmount('discountInput', 'discount');
    $('#discountModal').modal('hide');
});

document.getElementById("freight_add_btn").addEventListener("click", function () {
    splitAmount('freightInput', 'fright');
    $('#freightModal').modal('hide');
});

document.getElementById("custom_add_btn").addEventListener("click", function () {
    splitAmount('customCharges', 'customcharges');
    $('#customModal').modal('hide');
});
</script>

<script>
    let serialNoModal;
    document.addEventListener("DOMContentLoaded", function () {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);
    });
    let currentSerialInput = null;
    
    $(document).on('click', 'input[name="serial_no[]"]', function () {
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

    $('#myTable tbody tr').each(function () {
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
            placeholder: 'Select Product',
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
            $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
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
    /*table row fill based on layout height*/
 window.onload = function () {
    const table = document.getElementById('myTable');
    const tbody = table.querySelector('tbody');

    // If there are no rows, do nothing
    if (tbody.rows.length === 0) return;

    const rowHeight = tbody.rows[0].offsetHeight;
    const pageHeight = window.innerHeight-65;
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
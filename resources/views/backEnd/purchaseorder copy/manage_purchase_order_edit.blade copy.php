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
                <h2 class="page-heading m-0">Purchase Order Edit</h2>
                <span class="page-label">Home - Purchase Order</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('purchase-order/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>New</a>
                <a href="{{ url('purchase-order/'.$po->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-list"></i> View</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="PO Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-purchase-order') }}";                
                    document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            var val = this.value.trim();
                            if (val !== '') {                                
                                window.location.href = baseUrl + '/' + val;
                            }
                        }
                    });
                </script>
                <!-- Input with Search -->
                <a href="{{ url('purchase-order') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update', 'method' => 'POST', 'id' => 'tender-create-form']) }}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" id="po_id" value="{{ isset($po) ? $po->id : '' }}">
            <input type="hidden" name="net_vat" id="net_vat" value="{{ $net_vat }}">

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Vendor Name')</label>
                        <select
                            class="form-control js-account-select"
                            name="vendors" id="vendors" required    >
                            <option value=""></option>
                            @foreach ($vendors as $value)
                                <option value="{{ @$value->id }}"
                                    {{ isset($po) ? (!empty($po->vendors) ? (@$po->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('PO') @lang('Number')<span>*</span></label>
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ $po->doc_number }}" >
                        <input type="hidden" name="doc_number_main" value="{{ $po->doc_number }}" >
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('PO') @lang('lang.date')</label>
                        @php
                            $value = date('Y-m-d');
                            if (isset($po) && !empty($po->po_date)) {
                                @$value = date('Y-m-d', strtotime(@$po->po_date));
                            } else {
                                if (!empty(old('po_date'))) {
                                    @$value = old('po_date');
                                } else {
                                    @$value = date('Y-m-d');
                                }
                            }
                        @endphp
                        <input class="form-control" id="po_date" type="date" name="po_date"
                            value="{{ @$value }}">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Currency')</label>
                        <a class="text-danger float-right" data-toggle="modal" data-target="#ModalChangeCurrancy">Change Currency</a>
                        <select class="form-control js-example-basic-single" name="currency" id="currency">
                            @foreach ($currency as $value)
                            @if($po->currency == @$value->id)
                                <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                        <input
                            class="form-control"
                            type="text" name="createdby" autocomplete="off" id="createdby" readonly
                            value="{{ isset($po) ? (!empty(@$po->created_by) ? @$po->createdby->full_name : old('createdby')) : Auth::user()->full_name }}">

                        @if ($errors->has('createdby'))
                            <span class="invalid-feedback"
                                role="alert"><strong>{{ $errors->first('createdby') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 mb-2"></div>
                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Bill to Name') <span></span></label>
                        <input type="text" class="form-control"
                            value="{{ @$company->company_name }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                        <input type="text" class="form-control"
                            value="{{ @$company->company_address }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="extra-tab" data-toggle="tab" href="#extra" role="tab" aria-controls="extra" aria-selected="true">Extra Fields</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                    </li>
                  </ul>
                  
                  <div class="tab-content">
                    <div class="tab-pane active" id="extra" role="tabpanel" aria-labelledby="extra-tab">
                        <div class="row mt-2">
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Delivery Date')</label>
                                    @php
                                        $value = "";
                                        if (isset($po) && !empty($po->date)) {
                                            @$value = date('Y-m-d', strtotime(@$po->date));
                                        }
                                    @endphp
                                    <input class="form-control" id="delivery_date" type="date"
                                        name="delivery_date" value="{{ @$value }}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Payment Terms')*</label>
                                    <select
                                        class="form-control" required
                                        name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                        <option value=""></option>
                                        @foreach ($paymentterms as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($po) ? (!empty(@$po->payment_terms) ? (@$po->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    <div id="div_payment_terms" style="display: none; padding-top: px;">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                            <input class="txtbx primary-input form-control" type="text" name="payment_terms2" autocomplete="off" id="payment_terms2" value="{{ @$po->payment_terms2 }}">
                                        </div>
                                    </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Customer Reference')*</label>
                                    <input class="form-control" id="narration" type="text" name="narration" value="{{ $po->narration }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Salesman Name')*</label>
                                    <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                        <option value=""></option>
                                        @foreach ($salesman as $value)
                                            <option value="{{ @$value->user_id }}" @if($po->sales_person==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Deal ID')*</label>
                                    <input class="form-control" id="deal_id" type="text" name="deal_id" value="{{ App\SysHelper::get_code_from_dealid($po->deal_id) }}" required>
                                </div>
                            </div>

                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Contact Person Name')*</label>
                                    <input class="form-control" id="contact_person_name" type="text" name="contact_person_name" value="{{ $po->contact_person_name }}" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Contact Person Email')*</label>
                                    <input class="form-control" id="contact_person_email" type="text" name="contact_person_email" value="{{ $po->contact_person_email }}" required>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Contact Person Telephone')*</label>
                                    <input class="form-control" id="contact_person_telephone" type="text" name="contact_person_telephone" value="{{ $po->contact_person_telephone }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Internal Transfer')*</label>
                                    <select class="form-control" id="internal_transfer" name="internal_transfer" required>
                                        <option value="">Select</option>
                                        <option value="1" @if($po->internal_transfer==1) selected @endif>Yes</option>
                                        <option value="2" @if($po->internal_transfer==2) selected @endif>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Narration')</label>
                                    <input class="form-control" id="reference" type="text" name="reference" value="{{ $po->reference }}" >
                                </div>
                            </div> 

                        </div>
                    </div>
                    <div class="tab-pane" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                        <div class="row mt-2">
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Company Name') <span></span></label>
                                    <select class="form-control js-example-basic-single" name="shipping_supplier" id="shipping_supplier" required>
                                        <option value=""></option>
                                        @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}"
                                                @if(isset($po))
                                                    @if(!empty($po->shipping_supplier))
                                                        @if ($po->shipping_supplier == @$value->id)
                                                            selected
                                                        @endif
                                                    @else
                                                        @if (session('logged_session_data.company_id')==2) //SYSCOM FZE
                                                            @if($value->id==6262) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==3) //SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1
                                                            @if($value->id==3864) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==4) //SYSCOM DISTRIBUTION LTD
                                                            @if($value->id==6259) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==5) //SYSCOM IT SOLUTIONS LLC
                                                            @if($value->id==9364) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==6) //SYSCOM DISTRIBUTIONS LLC
                                                            @if($value->id==208) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==7) //STACK LINK UK LTD
                                                            @if($value->id==6217) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==8) //SUPREME SYSTEM TRADING ESTABLISHMENT
                                                            @if($value->id==6250) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==9) //SYSCOM DISTRIBUTION WLL
                                                            @if($value->id==6260) selected @endif
                                                        @elseif (session('logged_session_data.company_id')==10) //SUPREME SYSTEM DISTRIBUTORS SPC
                                                            @if($value->id==6251) selected @endif
                                                        @endif     
                                                    @endif
                                                @endif                                                                                                
                                                >{{ @$value->account_name }}</option>
                                        @endforeach
                                        </select>
                                </div>
                            </div>

                            <div class="col-lg-2 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Contact Name') <span></span></label>
                                    <input type="text" class="form-control"  name="shipping_name" id="shipping_name" value="{{ isset($po) ? (!empty(@$po->shipping_name) ? @$po->shipping_name : '') : old('shipping_name') }}" />

                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Shipping Address') <span></span></label>
                                    <textarea type="text" class="form-control" cols="0"
                                        rows="4" name="shipping_address_1"
                                        id="shipping_address_1">{{ isset($po) ? (!empty(@$po->shipping_address_1) ? @$po->shipping_address_1 : '') : old('shipping_address_1') }}</textarea>
                                    
                                </div>
                            </div>
                            <div class="col-lg-2 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Email') <span></span></label>
                                    <input type="text" class="form-control" name="shipping_email" id="shipping_email" value="{{ isset($po) ? (!empty(@$po->shipping_email) ? @$po->shipping_email : '') : old('shipping_email') }}" />
                                </div>
                            </div>
                            <div class="col-lg-2 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Contact No') <span></span></label>
                                    <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no" value="{{ isset($po) ? (!empty(@$po->shipping_contact_no) ? @$po->shipping_contact_no : '') : old('shipping_contact_no') }}" />
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
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('Supplier Country') <span></span></label>
                                    <select class="form-control" name="supplier_country" id="country" required>
                                        <option data-display="" value=""></option>
                                        @foreach ($countries as $key => $value)
                                            <option value="{{ @$value->id }}"
                                                <?php try{?>                                                        
                                                @if (isset($po)) @if (@$po->supplier_country == $value->id) selected @endif
                                                @endif
                                                <?php } catch (\Throwable $th) {} ?>
                                                >{{ @$value->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mt-2">
                    <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:50px;"></th>
                                <th style="width:150px;">@lang('Part No')</th>
                                <th>@lang('Description')</th>
                                <th style="width:100px;">@lang('Tax')</th>
                                <th style="width:100px;">@lang('Qty')</th>
                                <th style="width:120px;">@lang('Unit Price')</th>
                                <th style="width:120px;">@lang('Value')</th>
                                <th style="width:100px;">@lang('Discount')</th>
                                <th style="width:100px;">@lang('Freight')</th>
                                <th style="width:100px;">@lang('Custom Charges')</th>
                                <th style="width:130px;">@lang('Taxable Amount')</th>
                                <th style="width:130px;">@lang('VAT Amount')</th>
                                <th style="width:130px;">@lang('Total')</th>
                                <th style="width:20px;"></th>
                            </tr>
                            <tr>
                                <td><input class="form-control" type="number" id="sort_id" name="sort_id[]" /></td>
                                <td><input type="checkbox" checked hidden>
                                    <select class="form-control js-product-select" name="part_number[]" id="part_number_new">
                                        <option value="none"></option>
                                        @foreach ($items as $key => $value)
                                            <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="description_new" name="description[]" autocomplete="off">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="tax" name="tax[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="unitprice" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                    <script>
                                        $("#unitprice").on('keyup', function (e) {
                                            if (e.key === 'Enter' || e.keyCode === 13) {
                                                calc_change_new();
                                                if($('#btn_add_row').css('display') == 'none'){
                                                    $('#update_add_row').click();
                                                }
                                                if($('#update_add_row').css('display') == 'none'){
                                                    $('#btn_add_row').click();
                                                }
                                            }
                                        });
                                    </script>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="discount" name="discount[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="fright" name="fright[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="customcharges" name="customcharges[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="taxableamount" name="taxableamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="vatamount" name="vatamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="totalamount" name="totalamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td><input type="hidden" id="item_id" />
                                    <a onclick="return add_rows()" id="btn_add_row" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    <a onclick="return update_rows()" style="display: none;" id="update_add_row" class="btn btn-warning"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                </td>
                            </tr>
<script>
$(document).ready(function () {
    set_sort_id();
});
function set_sort_id() {
    let $sortInputs = $("input[name='sortid[]']");
    let firstVal = $sortInputs.first().val();
    if (!firstVal || firstVal == 0) {
        $sortInputs.each(function (index) {
            $(this).val(index + 1);
        });
    }
    let lastVal = parseInt($sortInputs.last().val()) || 0;
    $("#sort_id").val(lastVal + 1);
}
</script>
                            <script>
                            function calc_change_new(id) {
                                //var net_vat = $('#net_vat').val();
                                var net_vat = $('#tax').val();
        
                                var qty = $('#qty').val();
                                var unitprice = $('#unitprice').val();
                                var value = $('#value').val();
                                var discount = $('#discount').val();
                                var fright = $('#fright').val();
                                var customcharges = $('#customcharges').val();
        
                                qty = (qty === '') ? '0' : qty;
                                unitprice = (unitprice === '') ? '0' : unitprice;
                                var fin_value = (unitprice * qty);
                                $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
        
        
                                value = (value === '') ? '0' : value;
                                discount = (discount === '') ? '0' : discount;
                                fright = (fright === '') ? '0' : fright;
                                customcharges = (customcharges === '') ? '0' : customcharges;
                                var fin_taxableamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount));
                                $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                                var fin_vatamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat)) / 100);
                                var vatamount = $('#vatamount').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                                
                                $('#totalamount').val((Number(fin_taxableamount) + Number(fin_vatamount)).toFixed(@json(session('logged_session_data.decimal_point'))));
        
                            }
                            function add_rows() {

                                if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                                if($("#qty").val()==""){$("#qty").focus(); return false;}
                                if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                                if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                                if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}

                                $("#loading_bg").css("display", "block");
                                var action = "{{ URL::to('add-purchase-order-items') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        part_number: $("#part_number_new").val(),
                                        description: $("#description_new").val(),
                                        tax: $("#tax").val(),
                                        qty: $("#qty").val(),
                                        unitprice: $("#unitprice").val(),
                                        value: $("#value").val(),
                                        discount: $("#discount").val(),
                                        fright: $("#fright").val(),
                                        customcharges: $("#customcharges").val(),
                                        taxableamount: $("#taxableamount").val(),
                                        vatamount: $("#vatamount").val(),
                                        po_id: $("#po_id").val(),
                                        sort_id: $("#sort_id").val(),
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

                                                var qty_total=0; var value_total=0; var discount_total=0; var fright_total=0; var customcharges_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;

                                                for(var i=0; i<len; i++){


                                                    getSelectedRows +="<tr>\
                                                        <td class='text-right'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                        <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                        <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                        <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                        <td class='text-right'>"+formatAmount((Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                        <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                        </tr>";
                                                        
                                                        qty_total += Number(dataResult['data'][i].qty);
                                                        value_total += Number(dataResult['data'][i].value);
                                                        discount_total += Number(dataResult['data'][i].discount);
                                                        fright_total += Number(dataResult['data'][i].fright);
                                                        customcharges_total += Number(dataResult['data'][i].customcharges);
                                                        taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                        vatamount_total += Number(dataResult['data'][i].vatamount);

                                                        taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                        vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                        amount_total += Number(taxableamount_total1 + vatamount_total1);

                                                    /*$("#payment_terms").val(dataResult['data'][i].part_number_new);
                                                    $("#shipping_name").val(dataResult['data'][i].contcat_person);
                                                    $("#shipping_address_1").val(dataResult['data'][i].address);
                                                    $("#shipping_address_2").val(dataResult['data'][i].address2);
                                                    $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                                                    $("#country").val(dataResult['data'][i].vat_country);
                                                    $("#state").val(dataResult['data'][i].vat_state);*/
                                                }

                                                $("#part_number_new").val("none");
                                                $("#description_new").val("");
                                                //$("#tax").val("");
                                                $("#qty_total").text(formatAmount(qty_total));
                                                $("#value_total").text(formatAmount(value_total));
                                                $("#discount_total").text(formatAmount(discount_total));
                                                $("#fright_total").text(formatAmount(fright_total));
                                                $("#customcharges_total").text(formatAmount(customcharges_total));
                                                $("#taxableamount_total").text(formatAmount(taxableamount_total));
                                                $("#vatamount_total").text(formatAmount(vatamount_total));
                                                $("#amount_total").text(formatAmount(amount_total));

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows);
                                                row_clear();
                                            }
                                            else{
                                                
                                            }
                                    }
                                });
                                set_sort_id();
                                $("#loading_bg").css("display", "none");
                            }
                            
                            function row_edit(id) {
                                $('#btn_add_row').css("display",'none');
                                $('#update_add_row').css("display",'block');

                                var partno = $('#partno_'+id).val();
                                var pid = $('#pid_'+id).val();
                                //alert(partno);
                                //alert(pid);
                                
                                $("#item_id").val($('#item_'+id).val());
                                const targetSelect1 = $('#part_number_new');
                                        const option = new Option(partno, pid, true, true);
                                        targetSelect1.append(option).trigger('change');
                                //$('#part_number_new').addClass('js-example-basic-single');
                                $('#description_new').val($('#description_'+id).val());
                                $('#tax').val($('#tax_'+id).val());
                                $('#qty').val($('#qty_'+id).val());
                                $('#unitprice').val($('#unitprice_'+id).val());
                                $('#value').val($('#value_'+id).val());
                                $('#discount').val($('#discount_'+id).val());
                                $('#taxableamount').val($('#taxableamount_'+id).val());
                                $('#vatamount').val($('#vatamount_'+id).val());
                                $('#taxableamount').val($('#taxableamount_'+id).val());
                                $('#sort_id').val($('#sort_id_'+id).val());
                                //$('#totalamount').val($('#totalamount_'+id).val());
                                calc_change_new();
                                
                                $('#fright').val($('#fright_'+id).val());
                                $('#customcharges').val($('#customcharges_'+id).val());

                            }

                            function row_clear() {
                                $("#part_number_new").val('');
                                $("#select2-part_number_new-container").html('');
                                $('#description_new').val('');
                                $('#tax').val('');
                                $('#qty').val('');
                                $('#unitprice').val('');
                                $('#value').val('');
                                $('#discount').val('');
                                $('#fright').val('');
                                $('#customcharges').val('');
                                $('#taxableamount').val('');
                                $('#vatamount').val('');
                                $('#taxableamount').val('');
                                $('#totalamount').val('');
                                $("#sort_id").val(''),
                                
                                $('#btn_add_row').css("display",'block');
                                $('#update_add_row').css("display",'none');
                            }
                            
                            function update_rows() {
                                $("#loading_bg").css("display", "block");
                                var action = "{{ URL::to('update-purchase-order-items') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id : $("#item_id").val(),
                                        part_number: $("#part_number_new").val(),
                                        description: $("#description_new").val(),
                                        tax: $("#tax").val(),
                                        qty: $("#qty").val(),
                                        unitprice: $("#unitprice").val(),
                                        value: $("#value").val(),
                                        discount: $("#discount").val(),
                                        fright: $("#fright").val(),
                                        customcharges: $("#customcharges").val(),
                                        taxableamount: $("#taxableamount").val(),
                                        vatamount: $("#vatamount").val(),
                                        po_id: $("#po_id").val(),
                                        sort_id: $("#sort_id").val(),
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
                                                
                                            var qty_total=0; var value_total=0; var discount_total=0; var fright_total=0; var customcharges_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;
                                                
                                                for(var i=0; i<len; i++){

                                                    getSelectedRows +="<tr>\
                                                        <td class='text-right'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                        <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                        <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                        <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                        <td class='text-right'>"+formatAmount((Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                        <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                        </tr>";
                                                        qty_total += Number(dataResult['data'][i].qty);
                                                        value_total += Number(dataResult['data'][i].value);
                                                        discount_total += Number(dataResult['data'][i].discount);
                                                        fright_total += Number(dataResult['data'][i].fright);
                                                        customcharges_total += Number(dataResult['data'][i].customcharges);
                                                        taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                        vatamount_total += Number(dataResult['data'][i].vatamount);
                                                        
                                                        taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                        vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                        amount_total += Number(taxableamount_total1 + vatamount_total1);
                                                }

                                                $("#part_number_new").val("none");
                                                $("#description_new").val("");
                                                //$("#tax").val("");
                                                $("#qty_total").text(formatAmount(qty_total));
                                                $("#value_total").text(formatAmount(value_total));
                                                $("#discount_total").text(formatAmount(discount_total));
                                                $("#fright_total").text(formatAmount(fright_total));
                                                $("#customcharges_total").text(formatAmount(customcharges_total));
                                                $("#taxableamount_total").text(formatAmount(taxableamount_total));
                                                $("#vatamount_total").text(formatAmount(vatamount_total));
                                                $("#amount_total").text(formatAmount(amount_total));

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows);
                                                row_clear();
                                            }
                                            else{
                                                
                                            }
                                    }
                                });
                                set_sort_id();
                                $("#loading_bg").css("display", "none");
                            }

                            function row_delete(id,po_id) {
                                if (confirm("Are you sure you want to delete this item?") == false) {
                                    return false;
                                }
                                $("#loading_bg").css("display", "block");
                                var action = "{{ URL::to('delete-purchase-order-items') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: id,
                                        po_id: po_id,
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

                                                var qty_total=0; var value_total=0; var discount_total=0; var fright_total=0; var customcharges_total=0; var taxableamount_total=0; var vatamount_total=0; var amount_total=0;

                                                for(var i=0; i<len; i++){


                                                    getSelectedRows +="<tr>\
                                                        <td class='text-right'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                        <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                        <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                        <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                        <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                        <td class='text-right'>"+formatAmount((Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                        <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                        </tr>";
                                                        qty_total += Number(dataResult['data'][i].qty);
                                                        value_total += Number(dataResult['data'][i].value);
                                                        discount_total += Number(dataResult['data'][i].discount);
                                                        fright_total += Number(dataResult['data'][i].fright);
                                                        customcharges_total += Number(dataResult['data'][i].customcharges);
                                                        taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                        vatamount_total += Number(dataResult['data'][i].vatamount);
                                                        amount_total += Number(dataResult['data'][i].taxableamount + dataResult['data'][i].vatamount); 

                                                    /*$("#payment_terms").val(dataResult['data'][i].part_number_new);
                                                    $("#shipping_name").val(dataResult['data'][i].contcat_person);
                                                    $("#shipping_address_1").val(dataResult['data'][i].address);
                                                    $("#shipping_address_2").val(dataResult['data'][i].address2);
                                                    $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                                                    $("#country").val(dataResult['data'][i].vat_country);
                                                    $("#state").val(dataResult['data'][i].vat_state);*/
                                                }

                                                $("#part_number_new").val("none");
                                                $("#description_new").val("");
                                                //$("#tax").val("");
                                                $("#qty_total").text(formatAmount(qty_total));
                                                $("#value_total").text(formatAmount(value_total));
                                                $("#discount_total").text(formatAmount(discount_total));
                                                $("#fright_total").text(formatAmount(fright_total));
                                                $("#customcharges_total").text(formatAmount(customcharges_total));
                                                $("#taxableamount_total").text(formatAmount(taxableamount_total));
                                                $("#vatamount_total").text(formatAmount(vatamount_total));
                                                $("#amount_total").text(formatAmount(amount_total));

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows); 
                                            }
                                            else{
                                                
                                            }
                                    }
                                });
                                set_sort_id();
                                $("#loading_bg").css("display", "none");
                            }
                        </script>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mt-2">
                <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:30px;"></th>
                            <th style="width:100px;">@lang('Part No')</th>
                            <th style="width:350px;">@lang('Description')</th>
                            <th style="width:70px;" class="text-right">@lang('Tax')</th>
                            <th style="width:70px;" class="text-center">@lang('Qty')</th>
                            <th style="width:80px;" class="text-right">@lang('Unit Price')</th>
                            <th style="width:70px;" class="text-right">@lang('Value')</th>
                            <th style="width:70px;" class="text-right">
                                <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a>
                            </th>
                            <th style="width:70px;" class="text-right">
                                <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalFreight">Freight</a>
                            </th>
                            <th style="width:130px;" class="text-right">
                                <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalCustom">Custom Charges</a>
                            </th>
                            <th style="width:120px;" class="text-right">@lang('Taxable Amount')</th>
                            <th style="width:100px;" class="text-right">@lang('VAT Amount')</th>
                            <th style="width:100px;" class="text-right">@lang('Total Amount')</th>
                            <th style="width:70px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; $qty_total=0; $value_total=0; $discount_total=0; $fright_total=0; $customcharges_total=0; $taxableamount_total=0; $vatamount_total=0; $amount_total=0; @endphp
                        @if (count($po_items)>0)
                        @foreach ($po_items as $dt)
                        <tr>
                            <td>{{ $dt->sort_id }}<input type="hidden" name="sortid[]" id="sort_id_{{ $i }}" value="{{ $dt->sort_id }}" /></td>
                            <td>{{ $dt->partno }}<input type="hidden" id="partno_{{ $i }}" value="{{ $dt->partno }}" /><input type="hidden" id="pid_{{ $i }}" value="{{ $dt->part_number }}" /></td>
                            <td>{{ $dt->description }}<input type="hidden" id="description_{{ $i }}" value="{{ $dt->description }}" /></td>
                            <td class="text-right">{{ $dt->tax }}<input type="hidden" id="tax_{{ $i }}" value="{{ $dt->tax }}" /></td>
                            <td class="text-center">{{ $dt->qty }}<input type="hidden" id="qty_{{ $i }}" value="{{ $dt->qty }}" /></td>
                            <td class="text-right">{{ $dt->unitprice }}<input type="hidden" id="unitprice_{{ $i }}" value="{{ $dt->unitprice }}" /></td>
                            <td class="text-right">{{ $dt->value }}<input type="hidden" id="value_{{ $i }}" value="{{ $dt->value }}" /></td>
                            <td class="text-right">{{ $dt->discount }}<input type="hidden" id="discount_{{ $i }}" value="{{ $dt->discount }}" /></td>
                            <td class="text-right">{{ $dt->fright }}<input type="hidden" id="fright_{{ $i }}" value="{{ $dt->fright }}" /></td>
                            <td class="text-right">{{ $dt->customcharges }}<input type="hidden" id="customcharges_{{ $i }}" value="{{ $dt->customcharges }}" /></td>
                            <td class="text-right">{{ $dt->taxableamount }}<input type="hidden" id="taxableamount_{{ $i }}" value="{{ $dt->taxableamount }}" /></td>
                            <td class="text-right">{{ $dt->vatamount }}<input type="hidden" id="vatamount_{{ $i }}" value="{{ $dt->vatamount }}" /></td>
                            <td class="text-right">{{ @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }}<input type="hidden" id="totalamount_{{ $i }}" value="{{ $dt->taxableamount+$dt->vatamount }}" /></td>
                            <td>
                                <input type="hidden" id="item_{{ $i }}" value="{{ $dt->id }}" />
                                <a onclick="row_edit({{ $i }})" class="btn-sm btn-info"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a onclick="row_delete({{ $dt->id }},{{ $dt->po_id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            
                        @php $qty_total += $dt->qty; $value_total += $dt->value; $discount_total += $dt->discount; $fright_total += $dt->fright; $customcharges_total += $dt->customcharges; $taxableamount_total += $dt->taxableamount; $vatamount_total += $dt->vatamount; $amount_total += ($dt->taxableamount+$dt->vatamount); $i++; @endphp
                        @endforeach                            
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="font-weight-bold text-center"><label id="qty_total">{{ $qty_total }}</label></td>
                            <td></td>
                            <td class="text-right font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($value_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="discount_total">{{ @App\SysHelper::com_curr_format($discount_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="fright_total">{{ @App\SysHelper::com_curr_format($fright_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="customcharges_total">{{ @App\SysHelper::com_curr_format($customcharges_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($taxableamount_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($vatamount_total,2,'.',',') }}</label></td>
                            <td class="text-right font-weight-bold"><label id="amount_total">{{ @App\SysHelper::com_curr_format($amount_total,2,'.',',') }}</label></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>

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
                        $('#description_' + id + '').val(selOpt2.trim());
                        $('#description_' + id + '').focus();
                    }

                    function calc_change(id) {
                        //var net_vat = $('#net_vat').val();
                        var net_vat = $('#net_vat').val();

                        var qty = $('#qty_' + id + '').val();
                        var unitprice = $('#unitprice_' + id + '').val();
                        var value = $('#value_' + id + '').val();
                        var discount = $('#discount_' + id + '').val();
                        var fright = $('#fright_' + id + '').val();
                        var customcharges = $('#customcharges_' + id + '').val();

                        qty = (qty === '') ? '0' : qty;
                        unitprice = (unitprice === '') ? '0' : unitprice;
                        var fin_value = (unitprice * qty);
                        $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


                        value = (value === '') ? '0' : value;
                        discount = (discount === '') ? '0' : discount;
                        fright = (fright === '') ? '0' : fright;
                        customcharges = (customcharges === '') ? '0' : customcharges;
                        var fin_taxableamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat) +
                            100) / 100);
                        $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                        var fin_vatamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat)) / 100);
                        var vatamount = $('#vatamount_' + id + '').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                        calc_total();
                    }

                    function calc_total() {
                        var countrow = document.getElementById('po-row-count').value;
                        var t1 = 0,
                            t2 = 0,
                            t3 = 0,
                            t4 = 0,
                            t5 = 0,
                            t6 = 0,
                            t7 = 0;
                            t8 = 0;
                        for (var i = 1; i <= countrow; i++) {
                            t1 += Number($('#qty_' + i).val());
                            t2 += Number($('#unitprice_' + i).val());
                            t3 += Number($('#value_' + i).val());
                            t4 += Number($('#discount_' + i).val());
                            t8 += Number($('#fright_' + i).val());
                            t5 += Number($('#customcharges_' + i).val());
                            t6 += Number($('#taxableamount_' + i).val());
                            t7 += Number($('#vatamount_' + i).val());
                        }
                        $('#qty_total').text(t1);
                        $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#fright_total').text(t8.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
                    }

                    function fn_payment_terms() {
                        var val_payment_terms = $('#payment_terms').val();
                        if (val_payment_terms == 22) {
                            $('#div_payment_terms').css('display', 'block');
                        } else {
                            $('#div_payment_terms').css('display', 'none');
                        }
                    }
                    $('#payment_terms').change();

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



            <div class="row mt-40" style="display: none;">
                <div class="col-lg-12">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('lang.note') <span></span></label>
                        <textarea class="txtbx primary-input form-control" cols="0" rows="4" name="note">{{ isset($edit) ? (!empty(@$edit->note) ? @$edit->note : '') : old('description') }}</textarea>
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-info" value="1" name="btnSubmit" id="btnSubmit" onclick="return validate_form_submission()">
                        <span class="ti-check"></span>
                        Save & Print Purchase Order
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnSubmit" onclick="return validate_form_submission()">
                        <span class="ti-check"></span>
                        @if (isset($edit))
                            @lang('lang.update')
                        @else
                            @lang('lang.save')
                        @endif
                        @lang('Purchase Order')

                    </button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


    <!-- Modal Change Currancy-->
    <div class="modal fade" id="ModalChangeCurrancy" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Currancy</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($po->currency == $value->id)
                                            <option value="{{ @$value->id }}" >{{ @$value->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy To</label>
                                <select class="form-control" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
                                    <option value="">Select</option>
                                    @foreach ($currencylist2 as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                                @foreach ($currencylist2 as $value)
                                    <input type="hidden" id="rate_{{ @$value->id }}" name="rate_{{ @$value->id }}" value="{{ @$value->rate }}" />
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Default Currency Conversion Rate</label>
                                <input type="text" class="form-control" id="to_currency_rate" name="to_currency_rate" required />
                            </div>
                        </div>
                        <script>
                            function set_rate(){
                                var id = $('#to_currency_id').val();
                                var rate = $('#rate_'+id).val();

                                $('#to_currency_rate').val(rate);
                            }

                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="cur_po_id" value="{{ @$po->id }}"/>
                    <input type="hidden" name="cur_po_doc_no" value="{{ @$po->doc_number }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->



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
                                    <label class="dynamicslbl">@lang('Shipping Name') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control {{ $errors->has('shipping_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="shipping_name_add" name="shipping_name"
                                        value="{{ isset($editData) ? @$editData->shipping_name : old('shipping_name') }}">


                                    <span class="modal_input_validation_1 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Contact Name') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control {{ $errors->has('contact_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="contact_name_add" name="contact_name"
                                        value="{{ isset($editData) ? @$editData->contact_name : old('contact_name') }}">


                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Contact No') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        type="number" id="contact_no_add" name="contact_no"
                                        value="{{ isset($editData) ? @$editData->contact_no : old('contact_no') }}">


                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Address 1') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}"
                                        type="text" id="address1_add" name="address1"
                                        value="{{ isset($editData) ? @$editData->address1 : old('address1') }}">


                                    <span class="modal_input_validation_4 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Address 2') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}"
                                        type="text" id="address2_add" name="address2"
                                        value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">


                                    <span class="modal_input_validation_5 red_alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save"
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                    <input type="hidden" name="discount_amount_po_id" value="{{ @$po->id }}"/>                    
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                    <input type="hidden" name="freight_amount_po_id" value="{{ @$po->id }}"/>                    
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                    <input type="hidden" name="custom_amount_po_id" value="{{ @$po->id }}"/>                    
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

        var action = "{{ URL::to('add-purchase-order-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', $('#po_id').val());
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

        var action = "{{ URL::to('view-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#po_id').val(),
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
        var action = "{{ URL::to('delete-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : $('#po_id').val(),
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
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
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
                        else{
                            
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        $(document).on("change", "#shipping_supplier", function () {
            var id = $("#shipping_supplier").val();
            get_shipping_supplier_detail(id);
        });
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
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                $("#shipping_name").val(dataResult['data'][i].customer_salutation+'. '+dataResult['data'][i].first_name+' '+dataResult['data'][i].last_name);
                                $("#shipping_address_1").val(dataResult['data'][i].address+'\n'+dataResult['data'][i].address2);
                                $("#shipping_email").val(dataResult['data'][i].email);
                                $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                            }                        
                        }
                        else{
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


        jQuery(document).ready(function(){
            jQuery('input').keypress(function(event){
                var enterOkClass =  jQuery(this).attr('class');
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
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $("#btnSubmit").click(function() {
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

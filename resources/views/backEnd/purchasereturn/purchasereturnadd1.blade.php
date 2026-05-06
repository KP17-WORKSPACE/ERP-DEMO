@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Purchase Return</h2>
                <span class="page-label">Home - Purchase Return</span>
            </div>
            <div>
                <a href="{{ url('purchase-return-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>
                    New</a>
                <a href="{{url('purchase-return')}}" type="button" class="btn btn-info"><i class="fa fa-list"></i>
                    List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-update']) }}
            <input type="hidden" value="{{@$editData->id}}" name="cust_id">
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-store']) }}
            @endif
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="po_id" id="po_id">
            <input type="hidden" name="grn_id" id="grn_id">
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

                    <script>    
                        $(document).on("change", "#vendors", function () {
                            var id = $("#vendors").val();
                            get_pi_list(id);
                            get_vendors_detail(id);

                        });
                    </script>
                </div>
                <div class="col-lg-8">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">Purchase Return Number<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_return','PR' ,'doc_number') }}"
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
                                        <label class="txtlbl">Purchase Return Date</label>
                                        <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                            name="doc_date" value="{{ date('Y-m-d') }}" style="margin-top: 0px">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('doc_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('doc_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">Currency<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="currency" id="currency">
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
                        <a data-modal-size="modal-md" data-target="#pi_pending_popup_win" id="addPIPending" data-toggle="modal"></a>
                        <input type="hidden" id="pi_id" name="pi_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>

                </div>
                
                        
                <div class="col-lg-8 mb-2">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">PIV Number<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="pi_number" autocomplete="off" id="pi_number" value="" readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('doc_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pi_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">PIV Date</label>
                                        <input class="form-control" id="pi_date" type="date" autocomplete="off"
                                            name="pi_date" value="{{ date('Y-m-d') }}" style="margin-top: 0px" readonly>
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
                                <input class="form-control" id="lpo_date" type="date" autocomplete="off" name="lpo_date" value="{{ date('Y-m-d') }}" style="margin-top:0px;">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Payment Terms')<span>*</span></label>
                                <select class="form-control" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
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
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
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
                                <label class="txtlbl">@lang('Warehouse') <span>*</span></label>
                                <input
                                    class="form-control"
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
                                <label class="txtlbl">@lang('Salesman Name')<span>*</span></label>
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input
                                    class="form-control"
                                    type="text" name="salesman_name" autocomplete="off" id="salesman_name"
                                    value="{{ isset($edit) ? (!empty(@$edit->salesman_name) ? @$edit->salesman_name : old('salesman_name')) : '' }}"> --}}
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
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="">
                            </div>
                        </div>

                    </div>
                    
                </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="equipment comon-status row mt-4 d-block">
                    <hr />
                            <h6 class="primary-color">@lang('Item Details'):</h6>

                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('SL')</th>
                                        <th style="width:150px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
                                        <th style="width:100px;">@lang('VAT')</th>
                                        <th style="width:100px;">@lang('Qty')</th>
                                        <th style="width:120px;">@lang('Unit Price')</th>
                                        <th style="width:120px;">@lang('Value')</th>
                                        <th style="width:100px;">@lang('Discount')</th>
                                        <th style="width:130px;">@lang('Taxable Amount')</th>
                                        <th style="width:130px;">@lang('VAT Amount')</th>
                                        <th style="width:130px;">@lang('Total')</th>
                                        <th style="width:130px;">@lang('Serial No')</th>
                                        <th style="width:20px;"></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input class="form-control" type="number" id="sort_id" autocomplete="off" />
                                        </td>
                                        <td><input type="checkbox" checked hidden>
                                            <select class="form-control js-product-select" id="part_number_new">
                                                <option value="none"></option>
                                                {{-- @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                                @endforeach --}}
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="description_new" autocomplete="off" readonly="true">
                                        </td>
                                        <td>
                                            <input class="form-control vat" type="number" id="tax" autocomplete="off" min="0" value="0">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="qty" autocomplete="off" min="0" onchange="calc_change_new()" onkeypress="set_license_key_normal()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="unitprice" autocomplete="off" min="0" onchange="calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="value" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="discount" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="taxableamount" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="vatamount" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="totalamount" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="serial_no" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="hidden" id="cart_item_id" />
                                            <input type="hidden" id="deal_ref_id" />
                                            <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                            <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                        </td>
                                    </tr>
                    <script>
                    $(document).ready(function () {
                        let $sortInputs = $("input[name='sortid[]']");
                        let firstVal = $sortInputs.first().val();
                        if (!firstVal || firstVal == 0) {
                            $sortInputs.each(function (index) {
                                $(this).val(index + 1);
                            });
                        }
                        let lastVal = parseInt($sortInputs.last().val()) || 0;
                        $("#sort_id").val(lastVal + 1);
                    });
                    </script>
                                    <script>
                                    function calc_change_new(id) {
                                        var net_vat = $('#tax').val();
                
                                        var qty = $('#qty').val();
                                        var unitprice = $('#unitprice').val();
                                        var value = $('#value').val();
                                        var discount = $('#discount').val();
                
                                        qty = (qty === '') ? '0' : qty;
                                        unitprice = (unitprice === '') ? '0' : unitprice;
                                        var fin_value = (unitprice * qty);
                                        $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
                
                
                                        value = (value === '') ? '0' : value;
                                        discount = (discount === '') ? '0' : discount;
                                        var fin_taxableamount = ((unitprice * qty) - Number(discount));
                                        $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                
                                        var fin_vatamount = ((unitprice * qty) - Number(discount)) * ((Number(net_vat)) / 100);
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
                                        var action = "{{ URL::to('add-purchase-return-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                part_number: $("#part_number_new").val(),
                                                tax: $("#tax").val(),
                                                qty: $("#qty").val(),
                                                unitprice: $("#unitprice").val(),
                                                value: $("#value").val(),
                                                discount: $("#discount").val(),
                                                taxableamount: $("#taxableamount").val(),
                                                vatamount: $("#vatamount").val(),
                                                serial_no: $("#serial_no").val(),
                                                sort_id: $("#sort_id").val(),
                                            },
                                            cache: false,
                                            success: function(dataResult) {
                                                var dataResult = JSON.parse(dataResult);
                                                var len = 0;
                                                var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;

                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
        
                                                            t_qty += Number(dataResult['data'][i].qty);
                                                            t_value += Number(dataResult['data'][i].value);
                                                            t_discount += Number(dataResult['data'][i].discount);
                                                            t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                            t_vatamount += Number(dataResult['data'][i].vatamount);
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                                <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].vat+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vat+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serial_no_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                                
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        //$("#tax").val("");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#fright").val("0");
                                                        $("#customcharges").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#sort_id").val("");

                                                        $("#qty_total").text(t_qty);
                                                        $("#value_total").text(t_value);
                                                        $("#discount_total").text(t_discount);
                                                        $("#taxableamount_total").text(t_taxableamount);
                                                        $("#vatamount_total").text(t_vatamount);
                                                        $("#amount_total").text(t_taxableamount + t_vatamount);
        
                                                        $('#pi-ret-table tbody').empty();
                                                        $("#pi-ret-table tbody").append(getSelectedRows); 
                                                    }
                                                    else{
                                                        
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                    }                            
                                    function row_edit(id) {
                                        $('#btn_add_row').css("display",'none');
                                        $('#update_add_row').css("display",'block');
        
                                        var partno = $('#partno_'+id).val();
                                        var pid = $('#pid_'+id).val();
                                        //alert(partno);
                                        //alert(pid);
                                        const targetSelect1 = $('#part_number_new');
                                        const option = new Option(partno, pid, true, true);
                                        targetSelect1.append(option).trigger('change');
                                        //$('#part_number_new').addClass('js-example-basic-single');
                                        $('#description_new').val($('#description_'+id).val());
                                        $('#qty').val($('#qty_'+id).val());
                                        $('#unitprice').val($('#unitprice_'+id).val());
                                        $('#value').val($('#value_'+id).val());
                                        $('#discount').val($('#discount_'+id).val());
                                        $('#taxableamount').val($('#taxableamount_'+id).val());
                                        $('#vatamount').val($('#vatamount_'+id).val());
                                        $('#taxableamount').val($('#taxableamount_'+id).val());
                                        $('#totalamount').val($('#totalamount_'+id).val());
                                        $('#serial_no').val($('#serial_no_'+id).val());
        
                                        $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                        $('#deal_ref_id').val($('#deal_ref_id_'+id).val());
                                        $('#sort_id').val($('#sort_id_'+id).val());
                                    }
                                    
                                    function row_update() {
                                        $("#loading_bg").css("display", "block");
                                        var itm_id = $('#cart_item_id').val();
                                        if($('#deal_ref_id').val() != ""){
                                            var deal_ref_id = $('#deal_ref_id').val();
                                        } else { var deal_ref_id = 0; }
                                        var part_number = $('#part_number_new').val();
                                        //var description = $('#description_new').val();
                                        var tax = $("#tax").val();
                                        var qty = $('#qty').val();
                                        var unitprice = $('#unitprice').val();
                                        var value = $('#value').val();
                                        var discount = $('#discount').val();
                                        var taxableamount = $('#taxableamount').val();
                                        var vatamount = $('#vatamount').val();
                                        var serial_no = $('#serial_no').val();
        
                                        var action = "{{ URL::to('update-purchase-return-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                itm_id: itm_id,
                                                deal_ref_id: deal_ref_id,
                                                part_number: part_number,
                                                tax: tax,
                                                qty: qty,
                                                unitprice: unitprice,
                                                value: value,
                                                discount: discount,
                                                taxableamount: taxableamount,
                                                vatamount: vatamount,
                                                serial_no: serial_no,
                                                sort_id: $("#sort_id").val(),
                                            },
                                            cache: false,
                                            success: function(dataResult) {
                                                var dataResult = JSON.parse(dataResult);
                                                var len = 0;
                                                var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;
                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
                                                            t_qty += Number(dataResult['data'][i].qty);
                                                            t_value += Number(dataResult['data'][i].value);
                                                            t_discount += Number(dataResult['data'][i].discount);
                                                            t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                            t_vatamount += Number(dataResult['data'][i].vatamount);
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                                <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].vat+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vat+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serial_no_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        //$("#tax").val("");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#totalamount").val(""); 
                                                        $("#sort_id").val("");
                                                        $("#select2-part_number_new-container").html('');  

                                                        $("#qty_total").text(t_qty);
                                                        $("#value_total").text(t_value);
                                                        $("#discount_total").text(t_discount);
                                                        $("#taxableamount_total").text(t_taxableamount);
                                                        $("#vatamount_total").text(t_vatamount);
                                                        $("#amount_total").text(t_taxableamount + t_vatamount);                                             
        
                                                        $('#pi-ret-table tbody').empty();
                                                        $("#pi-ret-table tbody").append(getSelectedRows); 
                                                        
                                                        $('#btn_add_row').css("display",'block');
                                                        $('#update_add_row').css("display",'none');
        
                                                    }
                                                    else{
                                                        $('#pi-ret-table tbody').empty();
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                        $("#edit_cart_close").click();
                                    }
        
                                    function row_delete(id) {
                                        if (confirm("Are you sure you want to delete this item?") == false) {
                                            return false;
                                        }
                                        $("#loading_bg").css("display", "block");
                                        var action = "{{ URL::to('delete-purchase-return-items-cart') }}";
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
                                                var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;
                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
                                                            t_qty += Number(dataResult['data'][i].qty);
                                                            t_value += Number(dataResult['data'][i].value);
                                                            t_discount += Number(dataResult['data'][i].discount);
                                                            t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                            t_vatamount += Number(dataResult['data'][i].vatamount);
        
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                                <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].vat+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vat+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serial_no_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        //$("#tax").val("");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#sort_id").val("");

                                                        $("#qty_total").text(t_qty);
                                                        $("#value_total").text(t_value);
                                                        $("#discount_total").text(t_discount);
                                                        $("#taxableamount_total").text(t_taxableamount);
                                                        $("#vatamount_total").text(t_vatamount);
                                                        $("#amount_total").text(t_taxableamount + t_vatamount);
        
                                                        $('#pi-ret-table tbody').empty();
                                                        $("#pi-ret-table tbody").append(getSelectedRows); 
                                                    }
                                                    else{
                                                        $('#pi-ret-table tbody').empty();
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                    }
                                </script>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                    <table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>@lang('SL')</th>
                                <th>@lang('Part No')</th>
                                <th>@lang('PI Qty')</th>
                                <th>@lang('TAX')</th>
                                <th>@lang('Qty')</th>
                                <th>@lang('Unit Price')</th>
                                <th>@lang('Value')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Taxable Amount')</th>
                                <th>@lang('VAT Amount')</th>
                                <th>@lang('Total Amount')</th>
                                <th class="text-right"style="width:150px;">@lang('SRL No')</th>
                                <th class="text-right"style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($cart)>0)
                                    @foreach ($cart as $dt)
                                    <tr>
                                        <td>{{ $dt->sort_id }} <input type="hidden" name='sortid[]' id="sort_id_{{ $dt->id }}" value="{{ $dt->sort_id }}" /></td>
                                        <td>{{ $dt->partno }} <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->partno }}" />
                                            <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" /></td>
                                        <td>{{ $dt->description }} <input type="hidden" id="description_{{ $dt->id }}" value="{{ $dt->description }}" /></td>
                                        <td>{{ $dt->vat }} <input type="hidden" id="tax_{{ $dt->id }}" value="{{ intval($dt->vat) }}" /></td>
                                        <td>{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}" value="{{ $dt->qty }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }} <input type="hidden" id="unitprice_{{ $dt->id }}" value="{{ $dt->unitprice }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }} <input type="hidden" id="value_{{ $dt->id }}" value="{{ $dt->value }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }} <input type="hidden" id="discount_{{ $dt->id }}" value="{{ $dt->discount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',') }} <input type="hidden" id="taxableamount_{{ $dt->id }}" value="{{ $dt->taxableamount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->vatamount,2,'.',',') }} <input type="hidden" id="vatamount_{{ $dt->id }}" value="{{ $dt->vatamount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }} <input type="hidden" id="totalamount_{{ $dt->id }}" value="{{ $dt->taxableamount+$dt->vatamount }}" /></td>
                                        <td align="right">{{ $dt->serialno }}</td>
                                        <td>
                                            <input type="hidden" id="cart_item_id_{{ $dt->id }}" value="{{ $dt->id }}" />
                                            <input type="hidden" id="deal_ref_id_{{ $dt->id }}" value="{{ $dt->refid }}" />
                                            <a onclick="row_edit({{ $dt->id }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a onclick="row_delete({{ $dt->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </td>
                                        </tr>
                                    @endforeach
                                        
                                    @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-left"><label id="qty_total">0</label></td>
                                <td class="text-right"></td>
                                <td class="text-right"><label id="value_total">0.00</label></td>
                                <td class="text-right"><label id="discount_total">0.00</label></td>
                                <td class="text-right"><label id="taxableamount_total">0.00</label></td>
                                <td class="text-right"><label id="vatamount_total">0.00</label></td>
                                <td class="text-right"><label id="totalamount_total">0.00</label></td>
                            </tr>
                        </tfoot>
                    </table>                        
                </div>
                
            </div>
            <div class="col-lg-12 text-right">
                <a class="btn btn-info" onclick="add_set_adjestment()">Adjustment</a>
                <button class="btn btn-primary" type="submit">Generate Purchase Return</button>
            </div>
        </div>






            
    </div>
    </div>
    {{ Form::close() }}

        </div>
    </div>


    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="pi_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Purchase Invoice Item List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_pi_id" />
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
                                                    <th>@lang('SL')</th>
                                                    <th>@lang('Part No')</th>
                                                    <th>@lang('PI Qty')</th>
                                                    <th>@lang('TAX')</th>
                                                    <th>@lang('Qty')</th>
                                                    <th>@lang('Unit Price')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
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

                                        <button class="btn btn-primary bg-success" type="button" id="addPIPendingItems">
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

    {{-- popup --}}
    
    <script>
        function add_set_adjestment() {
            
            var csid = $('#vendors').val();
            $('#adj_pri_no').val($('#doc_number').val());

            var amt = $('#totalamount_total').text();
            
            $('#act_pri_adj_amount').val(amt);
            $('#pri_adj_amount').val(amt);

            get_adjestments_add(csid);
    
            $('#btn_adj_popup_win').click();
        }
        function get_set_amount(id)
        {
            set_adjestment(id);
            var adj_total = Number($('#adj_total_'+id).val());
            var adj_paid = Number($('#adj_paid_'+id).val());
            $('#adj_balance_'+id).val(adj_total - adj_paid);
        }

        function set_adjestment(id){
            var sum = $('#act_pri_adj_amount').val();
            var numItems = $('.class_adj_paid').length;
            var adj=0;
            for(i=0; i < numItems; i++){
                if(i!=id){

                    if($('#adj_paid_'+i).prop('readonly')){

                    } else {
                    adj +=  Number($('#adj_paid_'+i).val()); }
                }
            }                                
            
            var adj2 = sum - adj;
            

            if(adj2 > 0){
                $('#pri_adj_amount').val(adj2);
            }
            else { $('#pri_adj_amount').val(0); }

            var adj3 = $('#pri_adj_amount').val();

            if(adj3 > 0){
                var adj_total = Number($('#adj_balance_'+id).val());
                if(adj3 >= adj_total){
                    $('#adj_paid_'+id).val(adj_total);
                }
                else{
                    $('#adj_paid_'+id).val(adj3);
                }
            }
        }
    </script>

    <a id="btn_adj_popup_win" data-modal-size="modal-md" data-target="#adj_popup_win" data-toggle="modal"></a>
    <div class="modal fade admin-query" id="adj_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Adjestments</h4>
                    <button class="close" data-dismiss="modal" type="button" id="btn_adjestments_close">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" name="adj_pri_no">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <input type="text" id="act_pri_adj_amount" hidden/>
                                    <input type="text" id="pri_adj_amount" hidden/>
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%" id="table_adjestment">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('PIV No')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th><label id="footer_total" /></th>
                                                <th><label id="footer_paid" /></th>
                                                <th><label id="footer_balance" /></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-right">
                                    <div class="">
                                        <button class="btn btn-success fix-gr-bg" type="button" onclick="save_adjestments()">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- popup --}}

    {{-- popup srl --}}
    <a data-modal-size="modal-md" data-target="#srl_pending_popup_win" id="btnsrlpopup" data-toggle="modal"></a>
    <form id="srl">
        <div class="modal fade admin-query" id="srl_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Add Serial Number</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="srl_row" />
                        <div class="container-fluid">
                            <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Part Number') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="srl_part_number" name="srl_part_number" value="" readonly>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-2 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Qty') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="srl_qty" name="srl_qty" value="" readonly>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Serial No') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="srl_number" name="srl_number" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-2 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"><span></span> </label><br />
                                    <button class="btn btn-primary bg-primary mt-2" onclick="add_serial_no()" type="button" id="btn_close2"> @lang('Add') </button>
                                </div>
                            </div>
                            
                            

                            </div>
                                <br style="clear: both;" />
                            <div class="row">
                                <div class="col-lg-12" id="srllist">
                                    
                                    <span class="bg-info m-2 p-2"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- popup srl --}}

    
    
{{-- popup --}}
<div class="modal fade admin-query" id="dn_srlno_popup_win" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header m-0 p-3">
                <h4 class="modal-title"><div id="div_serialno_title"></div></h4>
                <button class="close" data-dismiss="modal" type="button">
                    ×
                </button>
            </div>
            <div class="modal-body m-0 p-3">
                <input type="hidden" id="hd_pending_dn_id"/>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('Serial No') <span>*</span> </label>
                                <textarea class="dynamicstxt primary-input form-control" id="srlno_textarea" name="srlno_textarea"></textarea>
                                <span class="focus-border"></span>
                                <span class="modal_input_validation_2 red_alert"></span>
                            </div>
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
                                    <button class="btn btn-success" type="button" onclick="srlno_add_item()">
                                        Add Selected
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- popup --}}
<a data-modal-size="modal-md" data-target="#dn_srlno_popup_win" id="add_srlno_popup" data-toggle="modal"></a>
    <script>
    function srlno_add(id){
        var hdtxt = $("#part_number_"+id).val();
        var srl = $("#srl_"+id).val();
        $("#srl_id").val(id);
        $("#srlno_textarea").val(srl);
        $("#div_serialno_title").html(hdtxt);
        document.getElementById('add_srlno_popup').click();
    }
    function srlno_add_item(){
        var id = $("#srl_id").val();
        var srltxt = $("#srlno_textarea").val();
        $("#srl_"+id).val(srltxt);
        document.getElementById('add_srl_cls').click();
    }
</script>

    
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <script>
        function get_pi_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-pi-list') }}";
            $.ajax({
                url: action,
                type: "get",
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
                                    var doc_number = dataResult['data'][i].doc_number;
                                    var option = "<option value='" + id + "'>" + doc_number +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_pi_pending(" + id + ")' id='pending_pi_" + i + "' name='pending_pi' value='" + doc_number + "'><label for='pending_pi_" + i + "'> " + doc_number +"</label><br />";

                                    $("#plist").append(innerHtml);
                                    
                      
                            }                        
                        }
                        else{
                            $("#plist").empty();
                        }
                        var innerHtml ="<input type='radio' onclick='without_pi(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without PI</label><br />";
                        $("#plist").append(innerHtml);
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        function popup_pi_pending(id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_pi_id").val(id);
            $("#pi_id").val(id);
            document.getElementById('addPIPending').click();
            get_adjestments(id);
            $("#loading_bg").css("display", "none");
        }

        function without_pi(id) {
            $("#loading_bg").css("display", "block");
    
            $("#pi_id").val(id);
            $("#table_id").css("display", "");
    
            $("#loading_bg").css("display", "none");
        }

        function get_adjestments(id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-purchase-return-adjestment-list') }}";
            $.ajax({
                url: action,
                type: "get",
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
                                $('#adj_pri_no').val(dataResult['data'][i].pri_no);
                                $('#adj_pi_no').val(dataResult['data'][i].piv_no);
                                $('#adj_lpo_no').val(dataResult['data'][i].lpo_no);
                                $('#adj_total').val(dataResult['data'][i].total_amount);
                                $('#adj_paid').val(dataResult['data'][i].paid_amount);
                                $('#adj_balance').val(dataResult['data'][i].balance_amount);
                            }                        
                        }
                        else{

                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        function get_adjestments_add(id){
            $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-purchase-return-adjestment-list-add') }}";
        $.ajax({
            url: action,
            type: "get",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                var tblrow="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){

                            var rdonly="";
                            var paid_amount = dataResult['data'][i].paid_amount;
                            if(paid_amount == null){paid_amount=0;}
                            if(paid_amount != 0){ rdonly="readonly"; }
                            var balance_amount = dataResult['data'][i].total_amount-Number(paid_amount);

                            tblrow += "<tr>";
                            tblrow += "<td><input type='text' class='form-control' name='adj_doc_date[]' id='adj_doc_date_"+ i +"' value='"+ get_format_date(dataResult['data'][i].doc_date) +"' readonly /></td>";
                            
                            tblrow += "<td><input type='text' class='form-control' name='adj_pi_no[]' id='adj_pi_no_"+ i +"' value='"+ dataResult['data'][i].doc_number +"' readonly /></td>";
                            tblrow += "<td><input type='text' class='form-control' name='adj_total[]' id='adj_total_"+ i +"' value='"+ dataResult['data'][i].total_amount +"' readonly /></td>";
                            
                            if(paid_amount == 0){
                            tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required/></td>";
                            } else {
                                if(dataResult['data'][i].adj_status == 5){                                            
                                    tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required/></td>";
                                } else {
                                tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid2[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required readonly /></td><input type='hidden' name='adj_paid[]' value='0'/>";
                                }
                            }

                            
                            tblrow += "<td><input type='text' class='form-control' name='adj_balance[]' id='adj_balance_"+ i +"' value='"+ balance_amount +"' readonly /></td>";
                            tblrow += "</tr>";

                        }
                        
                        $('#table_adjestment tbody').empty();
                        $("#table_adjestment tbody").append(tblrow); 

                    }
                    else{
                        $('#table_adjestment tbody').empty();

                    }
                    $("#loading_bg").css("display", "none");
            }
        });
        }

        function save_adjestments() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('purchase-return-add-adjestment3') }}";
    
            var id = $('#vendors').val();
            var adj_pri_no = $('#doc_number').val();
            var adj_lpo_no = $('#lpo_number').val();
    
            var adj_doc_date = [];
            $('input[name="adj_doc_date[]"]').each(function() { adj_doc_date.push(get_format_date($(this).val())); });
    
            var adj_pi_no = [];
            $('input[name="adj_pi_no[]"]').each(function() { adj_pi_no.push($(this).val()); });
    
            var adj_total = [];
            $('input[name="adj_total[]"]').each(function() { adj_total.push($(this).val()); });
    
            var adj_paid = [];
            $('input[name="adj_paid[]"]').each(function() { adj_paid.push($(this).val()); });
    
            var adj_balance = [];
            $('input[name="adj_balance[]"]').each(function() { adj_balance.push($(this).val()); });
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id:id,
                    adj_pri_no: adj_pri_no,
                    adj_lpo_no: adj_lpo_no,
                    adj_doc_date: adj_doc_date,
                    doc_date: $('#doc_date').val(),
                    adj_pi_no: adj_pi_no,
                    adj_total: adj_total,
                    adj_paid: adj_paid,
                    adj_balance: adj_balance,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    var tblrow="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
    
                                var paid_amount = dataResult['data'][i].paid_amount;
                                if(paid_amount == null){paid_amount=0;}
                                var balance_amount = dataResult['data'][i].total_amount-Number(paid_amount);
    
                                tblrow += "<tr>";
                                tblrow += "<td><input type='text' class='form-control' name='adj_doc_date[]' id='adj_doc_date_"+ i +"' value='"+ dataResult['data'][i].doc_date +"' readonly /></td>";
                                
                                tblrow += "<td><input type='text' class='form-control' name='adj_pi_no[]' id='adj_pi_no_"+ i +"' value='"+ dataResult['data'][i].doc_number +"' readonly /></td>";
                                tblrow += "<td><input type='text' class='form-control' name='adj_total[]' id='adj_total_"+ i +"' value='"+ dataResult['data'][i].total_amount +"' readonly /></td>";
                                tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required /></td>";
                                tblrow += "<td><input type='text' class='form-control' name='adj_balance[]' id='adj_balance_"+ i +"' value='"+ balance_amount +"' readonly /></td>";
                                tblrow += "</tr>";
    
                            }
                            
                            $('#table_adjestment tbody').empty();
                            $("#table_adjestment tbody").append(tblrow); 
                            alert('Adjustment Added Successfully');
                            $('#btn_adjestments_close').click();
    
                        }
                        else{
                            $('#table_adjestment tbody').empty();
    
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        function add_adjestments(){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('purchase-return-add-adjestment2') }}";
            $.ajax({
                url: action,
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    pri_no: $('#adj_pri_no').val(),
                    piv_no: $('#adj_pi_no').val(),
                    lpo_no: $('#adj_lpo_no').val(),
                    doc_date: $('#adj_doc_date').val(),
                    total_amount: $('#adj_total').val(),
                    paid_amount: $('#adj_paid').val(),
                    balance_amount: $('#adj_balance').val(),
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
                                $('#adj_pri_no').val(dataResult['data'][i].pri_no);
                                $('#adj_pi_no').val(dataResult['data'][i].piv_no);
                                $('#adj_lpo_no').val(dataResult['data'][i].lpo_no);
                                $('#adj_total').val(dataResult['data'][i].total_amount);
                                $('#adj_paid').val(dataResult['data'][i].paid_amount);
                                $('#adj_balance').val(dataResult['data'][i].balance_amount);
                            }
                            alert("Adjestment added successfully");
                            $('#btn_adjestments_close').click();
                        }
                        else{

                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

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
                                $("#tax").val(dataResult['data'][i].vat_percentage);
                                /*$("#shipping_name").val(dataResult['data'][i].contcat_person);
                                $("#shipping_address_1").val(dataResult['data'][i].address);
                                $("#shipping_address_2").val(dataResult['data'][i].address2);
                                $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);

                                $("#supplier_type").val(dataResult['data'][i].supplier_type);
                                $("#purchase_type").val(dataResult['data'][i].purchase_type);

                                $("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                                $("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);*/
                                //$("#tax").val(dataResult['data'][i].vat_percentage);

                                /*$("#country").val(dataResult['data'][i].vat_country);
                                $("#state").val(dataResult['data'][i].vat_state);*/
                            }                        
                        }
                        else{
                            $("#payment_terms").val("");
                            $("#tax").val("0");
                            /*$("#shipping_name").val("");
                            $("#shipping_address_1").val("");
                            $("#shipping_address_2").val("");
                            $("#shipping_contact_no").val("");
                            $("#country").val("");
                            $("#state").val("");*/
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function calc_change(id) {
            
            var tax = $('#tax_' + id + '').val();
            var qty = $('#qty_' + id + '').val();
            var unitprice = $('#unitprice_' + id + '').val();
            var discount = $('#discount_' + id + '').val();

            tax = (tax === '') ? '0' : tax;
            qty = (qty === '') ? '0' : qty;
            unitprice = (unitprice === '') ? '0' : unitprice;
            discount = (discount === '') ? '0' : discount;

            var fin_value = (unitprice * qty);
            $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));

            var fin_taxableamount = ((unitprice * qty) - Number(discount));
            $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(tax)) / 100);
            $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            var fin_totalamount = Number(fin_taxableamount) + Number(fin_vatableamount)

            $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            calc_total();

        }
        function calc_total() {
            var rowCount = $('#table_id tbody tr').length;    
            $('#table_id tbody').empty();
            var t1 = 0,
                t3 = 0,
                t4 = 0,
                t5 = 0,
                t6 = 0,
                t7 = 0;
            for (var i = 0; i < rowCount; i++) {
                try {
                    t1 += Number($('#qty_' + i).val());
                    t3 += Number($('#value_' + i).val());
                    t4 += Number($('#discount_' + i).val());
                    t5 += Number($('#taxamount_' + i).val());
                    t6 += Number($('#vatamount_' + i).val());
                    t7 += Number($('#totalamount_' + i).val());
                }
                catch(err) {
                    
                }
            }
            $('#qty_total').text(t1);
            
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#totalamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
        }

        function popup_po_srlno(id) {
            $("#loading_bg").css("display", "block");
            $("#srl_row").val(id);
            var part_number = $('#part_number_' + id + '').val();
            var qty = $('#qty_' + id + '').val();
            $("#srl_part_number").val(part_number);
            $("#srl_qty").val(qty);
            document.getElementById('btnsrlpopup').click();
            var part_no = $('#partno_' + id + '').val();
            var action = "{{ URL::to('purchase-return-get-serialno') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    part_no: part_no,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $("#srllist").empty();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var srl_no = dataResult['data'][i].srl_no;
                                var innerHtml = "<span class='bg-info m-1 pt-1 pr-2 pb-1 pl-2'>" + srl_no +"</span>";
                                $("#srllist").append(innerHtml);
                            }                        
                        }
                        else{
                            $("#srllist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function add_serial_no(){
            $("#loading_bg").css("display", "block");
            var id=$("#srl_row").val();
            var srl_no = $("#srl_number").val();
            if(srl_no==''){$("#srl_number").focus(); $("#loading_bg").css("display", "none"); return false;}
            var part_number = $('#part_number_' + id + '').val();
            var part_no = $('#partno_' + id + '').val();
            var piv_id = $('#hd_pending_pi_id').val();
            var qty = $('#qty_' + id + '').val();
            var action = "{{ URL::to('purchase-return-add-serialno') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    srl_no: srl_no,
                    part_number: part_number,
                    part_no: part_no,
                    piv_id: piv_id,
                    qty:qty,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "QTYERROR") {
                        alert("Error. Qty Miss Match found!!");
                        $("#loading_bg").css("display", "none");
                        return false;
                    }
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $("#srllist").empty();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var srl_no = dataResult['data'][i].srl_no;
                                var innerHtml = "<span class='bg-info m-1 pt-1 pr-2 pb-1 pl-2'>" + srl_no +"</span>";
                                $("#srllist").append(innerHtml);
                            }                        
                        }
                        else{
                            $("#srllist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
            $("#srl_number").val('');
        }


    </script>

    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
        
        $(document).ready(function () {
            $("#btnSubmit2").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit2").prop('disabled', true);
            }
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


<!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select License Key (<label id="ModalLabelHeading" ></label> )</h5>
                    <input type="hidden" id="part_no" />
                    <input type="hidden" id="update_id" />
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Select</th>
                                        <th style="width: 15%;">Expiry Date</th>
                                        <th style="width: 50%;">Licence Key</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label id="selected_key">0</label> Keys Selected out of <label id="total_key">0</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="set_license_key()" type="button">Add Selected</button>
                    <button class="btn btn-secondary" id="popup_close" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function set_license_key_normal(){
            $('#qty').keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = 2;
                    if(pt == 2) {
                        var part_id =$('#part_number_new').val();
                        $('#ModalLabelHeading').text($('#part_number_new').val());    
                        $('#part_no').val(part_id);
                        $('#btn_ModalLicenseKey').click();
                        get_license_key(part_id);
                    }
                    return true;
                }
            });
        }
        function set_license_key_po(rowid,producttype){
            $('#qty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = producttype;
                    if(pt == 2) {
                        var part_id =$('#partno_'+rowid).val();
                        $('#ModalLabelHeading').text($('#part_number_'+rowid).val());    
                        $('#part_no').val(part_id);
                        $('#btn_ModalLicenseKey').click();
                        get_license_key(part_id);
                    }
                    return true;
                }
            });
        }
        function get_license_key(part_id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('purchase-return-get-dn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : part_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            $('#total_key').text(len);
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td><input class='chk_key' type='checkbox' id='select_key_"+ Number(i+1) +"' onclick='key_select_change("+ Number(i+1) +")'  /><input type='hidden' id='item_key_id_"+ Number(i+1) +"' value='"+dataResult['data'][i].id+"' /></td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    </tr>";                                    
                            }
                            $('#license_key').val('');
                            $('#exp_date').val('');
                            $('#lk-table tbody').empty();
                            $("#lk-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#lk-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function key_select_change(id){
            $('#select_key_'+id).on('change', function() { 
                if (this.checked) {
                }
            });

            var a = 0;
            var b = 1;
            var itm_id = 0;
            $(".chk_key").each(function() {
                if(this.checked){
                    a = Number(a+1);
                    if(itm_id == 0){
                        itm_id = $('#item_key_id_'+b).val();
                    }
                    else{
                        itm_id += ','+$('#item_key_id_'+b).val();
                    }
                }
                b++;
            });
            $('#update_id').val(itm_id);
            $('#selected_key').text(a);
        }
        function set_license_key(){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('purchase-return-update-dn-license-key') }}";
            var myArray = $('#update_id').val(); 
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : myArray,
                    item_id : $('#part_no').val(),
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    $('#popup_close').click();
                }
            });
            $("#loading_bg").css("display", "none");
        }

        $(window).ready(function() {
            $("#purchase-return-store").on("keypress", function (event) {
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

@endsection
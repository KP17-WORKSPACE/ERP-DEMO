@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>


                                @include('backEnd.grn.grn_add')


    <div class="container-fluid" style="display: none">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Goods Receipt Note (GRN)</h2>
                <span class="page-label">Home - Goods Receipt Note (GRN)</span>
            </div>
            <div>
                <a href="{{ url('goods-receipt-note/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('goods-receipt-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-store', 'method' => 'POST', 'id' => 'goods-receipt-note-store']) }}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
            <input type="hidden" name="net_vat" id="net_vat">
            <input type="hidden" id="hd_pending_po_id" name="hd_pending_po_id" />
            <input type="hidden" id="company_id" value="{{ session('logged_session_data.company_id') }}" />

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <label class="txtlbl">@lang('Vendor') <span>*</span></label>
                    <select class="form-control js-account-select" name="vendors" id="vendors">
                        <option value=""></option>
                        {{-- @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}"
                                {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->account_name }}
                            </option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-lg-4">
                        <div class="input-effect">
                            <label class="txtlbl">GRN Number<span>*</span></label>
                            <input
                                class="form-control"
                                type="text" name="doc_number" autocomplete="off" id="doc_number"
                                value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @APP\SysHelper::get_new_code('sys_purchase_grn','GR' ,'doc_number') }}"
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
                                <div class="input-effect">
                                    <label class="txtlbl">GRN Date</label>
                                    @php
                                        $value = date('Y-m-d');
                                    @endphp
                                    <input class="form-control" id="grn_date" type="date" autocomplete="off"
                                        name="grn_date" value="{{ @$value }}" style="margin-top: 0px">
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
                        <div id="plist" style="width: 100%; height: 180px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#po_pending_popup_win" id="addPoPending" data-toggle="modal"></a>
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage">
                    </div>
                </div>
                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-lg-4 mb-0">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Number') <span></span></label>
                                <input
                                    class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                    type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->lpo_number) ? @$edit->lpo_number : old('lpo_number')) : '' }}">
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
                                <label class="txtlbl">@lang('')<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
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
                                <label class="txtlbl">@lang('Bill Number')<span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Date')</label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit) && !empty($edit->bill_date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit->bill_date));
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
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('AWB No.') <span></span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('BOE No.') <span></span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                    type="text" name="boeno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->boeno) ? @$edit->boeno : old('boeno')) : old('boeno') }}"
                                    id="boeno">
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
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="">@lang('Salesman Name')*</label>
                                <select class="form-control" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>                                
                                <select class="form-control" name="createdby" id="createdby" >
                                <option value=""></option>
                                @foreach ($staff as $value)
                                    <option disabled value="{{ @$value->user_id }}" @if($value->user_id == Auth::user()->id) selected @endif>{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
        
                                {{--  <input class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}"
                                    readonly>  --}}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : old('narration') }}"
                                    id="narration">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Warehouse') <span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Deal Id') <span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : old('deal_id') }}"
                                    id="deal_id">
                            </div>
                        </div>
                    </div>
                </div>

        </div>
        <div class="equipment comon-status row mt-4 d-block">
            <table class="table table-bordered table-striped" id="table_id2" style="display: none; width:100%;" cellspacing="0">
                <thead>
                    <tr>
                        <th width="200px">@lang('Part No')</th>
                        <th>@lang('Description')</th>
                        <th width="100px">@lang('Tax')</th>
                        <th width="100px">@lang('Qty')</th>
                        <th width="120px">@lang('Unit Price')</th>
                        <th width="120px">@lang('Value')</th>
                        <th width="100px">@lang('Discount')</th>
                        <th width="100px">@lang('Fright')</th>
                        <th width="100px">@lang('Customs')</th>
                        <th width="100px">@lang('Taxable Amount	')</th>
                        <th width="130px">@lang('VAT Amount	')</th>
                        <th width="130px">@lang('Total Amount')</th>
                        <th width="20px"></th>
                    </tr>
                    <tr>
                        <td><input type="checkbox" checked hidden>
                            <select class="form-control js-product-select" name="part_number1[]" id="part_number_new">
                                <option value="none"></option>
                                {{-- @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                @endforeach --}}
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" id="description_new" name="description1[]" autocomplete="off" readonly="true">

                            <select class="form-control" name="hscode_txt_new[]" id="hscode_txt_new" readonly="true" hidden>
                                <option value="none"></option>
                                @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->hscode }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" type="text" id="hscode_txt" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                            <input class="form-control" type="text" id="product_type" autocomplete="off" readonly="true" hidden>
                            <input class="form-control" type="text" id="product_type_part_number_text" autocomplete="off" readonly="true" hidden>
                            
                        </td>
                        <td>
                            <input type="number" class="form-control" name="tax1[]" id="tax" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="qty" name="qty1[]" autocomplete="off" min="0" onchange="calc_change_new()" onkeypress="set_license_key()">
                            
                        </td>
                        <td>
                            <input class="form-control" type="number" id="unitprice" name="unitprice1[]" step="any" autocomplete="off" min="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="value" name="value1[]" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="discount" name="discount1[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="fright" name="fright1[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="customcharges" name="customcharges1[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="taxableamount" name="taxableamount1[]" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="vatamount" name="vatamount1[]" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="totalamount" name="totalamount1[]" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input type="hidden" value="1" id="row_id" />
                            <a onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <script>
                   


                    function calc_change_new(id) {
                        $("#loading_bg").css("display", "block");
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
                        
                        $('#totalamount').val((fin_vatamount+fin_taxableamount).toFixed(@json(session('logged_session_data.decimal_point'))));
                        $("#loading_bg").css("display", "none");
                    
                    }

                    function add_rows() {

                        if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                        if($("#qty").val()==""){$("#qty").focus(); return false;}
                        if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                        if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                        if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}

                        var company_id = $('#company_id').val();
                        var hscode = $('#hscode_txt').val();

                        var part_number = $("#part_number_new option:selected").text();
                        var part_id = $('#part_number_new').val();
                        var description_new = $('#description_new').val();
                        var qty = $('#qty').val();
                        var unitprice = $('#unitprice').val();
                        var value = $('#value').val();
                        var discount = $('#discount').val();
                        var vatamount = $('#vatamount').val();
                        var tax = $('#tax').val();
                        
                        var fright =$("#fright").val();
                        var customcharges = $("#customcharges").val();

                        var sumamount = (Number(value) - Number(discount)) + Number(vatamount);

                        var i = $('#row_id').val();

                        $("#loading_bg").css("display", "block");

                        var tr =  "<tr><td class='jshide'><input type=checkbox checked id=id_"+ i +" value="+ i +"></td>\
                            <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='"+ part_number +"' readonly>\
                            <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+ part_id +"'/>\
                            <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='"+ qty +"'/></td>\
                            <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+ description_new +"' readonly></td>";

                            if(company_id==2){
                                tr +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode[]' id='hscode_" + i + "' value='"+hscode+"' readonly></td>";
                            } else{
                                tr +=  "<input type='hidden' id='hscode_" + i + "' name='hscode[]' value='0' readonly></td>";
                            }
                            tr +=  "<td><input class='form-control' type='number' autocomplete='off' min='0' value='"+ qty +"' readonly></td>\
                            <td><input class='form-control' type='number' id='qty_" + i + "' name='qty[]' value='"+ qty +"' autocomplete='off' min='0'onchange='calc_change(" + i + ")'></td>\
                            <td><input class='form-control' type='number' id='tax_" + i + "' name='tax[]' value='"+tax+"' autocomplete='off' min='0'onchange='calc_change(" + i + ")'></td>\
                            <td><input class='form-control' type='number' autocomplete='off' min='0' value='"+ qty +"' readonly></td>\
                            <td><input class='form-control' type='number' autocomplete='off' min='0' value='"+ qty +"' readonly></td>\
                            <td><input class='form-control' type='number' id='unitprice_" + i + "' name='unitprice[]' step='any' value='"+ unitprice +"' autocomplete='off' min='0' onchange='calc_change(" + i + ")'></td>\
                            <td><input class='form-control' type='number' id='value_" + i + "' name='value[]' step='any' value='"+ value +"' autocomplete='off' min='0'></td>\
                            <td><input class='form-control' type='number' id='discount_" + i + "' name='discount[]' step='any' value='"+ discount +"' autocomplete='off' min='0' onchange='calc_change(" + i + ")' step='any'></td>\
                            <td><input class='form-control' type='number' id='fright_" + i + "' name='fright[]' step='any' value='"+ fright +"' readonly></td>\
                            <td><input class='form-control' type='number' id='customcharges_" + i + "' name='customcharges[]' step='any' value='"+ customcharges +"' readonly></td>\
                            <td><input class='form-control' type='number' id='taxamount_" + i + "' name='taxamount[]' step='any' value='"+ sumamount +"' readonly></td>\
                            <td><input class='form-control' type='number' id='vatamount_" + i + "' name='vatamount[]' step='any' value='"+ vatamount +"' readonly></td>\
                            <td><input class='form-control' type='number' id='sumamount_" + i + "' name='sumamount[]' step='any' value='"+ sumamount +"' readonly></td>\
                            <td><input class='form-control' type='test' id='srl_"+ i +"' name='srl[]' onclick='srlno_add("+ i +")' ></td>\
                            </tr>";
                        //$('#table_id tbody').empty();
                        $("#pi-table tbody").append(tr);
                        $(".jshide").show();
                        $(".jshide1").hide();
                        $(".jshide2").hide();

                        
                        $("#part_number_new").val("none");
                        $("#description_new").val("");
                        //$("#tax").val("");
                        $("#qty").val("");
                        $("#unitprice").val("");
                        $("#value").val("");
                        $("#discount").val("");
                        $("#taxableamount").val("");
                        $("#vatamount").val("");
                        $("#totalamount").val("");
                        
                        $("#fright").val('');
                        $("#customcharges").val('');

                        $('#row_id').val(i+1);

                        $("#loading_bg").css("display", "none");
                    }
                    function row_delete(id) {
                        if (confirm("Are you sure you want to delete this item?") == false) {
                            return false;
                        }
                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('delete-grn-items-cart') }}";
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
                                var getSelectedRows="";
                                    if(dataResult['data'] != null){
                                        len = dataResult['data'].length;
                                    }
                                    if(len > 0){
                                        for(var i=0; i<len; i++){


                                            getSelectedRows +="<tr>\
                                                <td>"+dataResult['data'][i].partno+"</td>\
                                                <td>"+dataResult['data'][i].description+"</td>\
                                                <td>"+dataResult['data'][i].tax+"</td>\
                                                <td>"+dataResult['data'][i].qty+"</td>\
                                                <td>"+dataResult['data'][i].unitprice+"</td>\
                                                <td>"+dataResult['data'][i].value+"</td>\
                                                <td>"+dataResult['data'][i].discount+"</td>\
                                                <td>"+dataResult['data'][i].fright+"</td>\
                                                <td>"+dataResult['data'][i].customcharges+"</td>\
                                                <td>"+dataResult['data'][i].taxableamount+"</td>\
                                                <td>"+dataResult['data'][i].vatamount+"</td>\
                                                <td>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"</td>\
                                                <td><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                
                                        }

                                        $("#part_number_new").val("none");
                                        $("#description_new").val("");
                                        //$("#tax").val("");
                                        $("#qty").val("");
                                        $("#unitprice").val("");
                                        $("#value").val("");
                                        $("#discount").val("0");
                                        $("#customcharges").val("0");
                                        $("#fright").val("0");
                                        $("#taxableamount").val("");
                                        $("#vatamount").val("");

                                        $('#pi-table tbody').empty();
                                        $("#pi-table tbody").append(getSelectedRows); 
                                    }
                                    else{
                                        $('#pi-table tbody').empty();
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
            

    <!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add License Key - <label id="ModalLabelHeading" ></label></h5>
                    <a class="btn-sm btn-danger float-right" data-toggle="modal" data-target="#ModalExcelQuote">License Excel Import</a>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                                <label for="" class="form-label">Qty</label><input type="hidden" id="item_id" />
                                <input type="number" class="form-control" name="license_qty" id="license_qty" value="1" readonly/>
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
                                <button type="button" id="license_add" class="btn btn-primary" onclick="return add_license_key()">Add</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
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
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                (<a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}" target="_blank">Sample File</a>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="return excel_license_key()">Upload</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->


    <script>

        
        function set_license_key(){
            $('#qty').keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = $('#product_type').val();
                    if(pt == 2) {
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
        function set_license_key_po(rowid,producttype){
            $('#qty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = producttype;
                    if(pt == 2) {
                        $('#item_id').val($('#part_id_'+rowid).val());
                        $('#ModalLabelHeading').text($('#part_number_'+rowid).val());
                        $('#license_qty').val($('#qty_'+rowid).val())
                        $('#btn_ModalLicenseKey').click();
                        view_license_key();
                    }
                    return true;
                }
            });
        }


        function add_license_key(){
            $("#loading_bg").css("display", "block");

            if($('#license_key').val()==""){ $('#license_key').focus(); $("#loading_bg").css("display", "none"); return false; }
            if($('#exp_date').val()==""){ $('#exp_date').focus(); $("#loading_bg").css("display", "none"); return false; }
            if($('#license_qty').val()==""){ $('#license_qty').focus(); $("#loading_bg").css("display", "none"); return false; }

            var action = "{{ URL::to('add-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#item_id').val(),
                    license_key : $('#license_key').val(),
                    exp_date : $('#exp_date').val(),
                    license_qty : $('#license_qty').val(),

                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                if(Number(i+1)>=$('#license_qty').val()){
                                    $('#license_add').prop('disabled', true);
                                } else { $('#license_add').prop('disabled', false); }
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
        function excel_license_key(){
            $("#loading_bg").css("display", "block");

            if($('#import_file').val()==""){ $('#import_file').focus(); $("#loading_bg").css("display", "none"); return false; }

            var action = "{{ URL::to('add-grn-license-key-cart-excel') }}";
            
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
            formData.append('item_id', $('#part_number_new').val());  // Append other form data
            formData.append('license_qty', $('#license_qty').val());  // Append other form data            
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
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                if(Number(i+1)>=$('#license_qty').val()){
                                    $('#license_add').prop('disabled', true);
                                } else { $('#license_add').prop('disabled', false); }
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
        function view_license_key(){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('view-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#part_number_new').val(),
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
                                if(Number(i+1)>=$('#license_qty').val()){
                                    $('#license_add').prop('disabled', true);
                                } else { $('#license_add').prop('disabled', false); }
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
        function delete_license_key(id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : id,
                    item_id : $('#part_number_new').val(),
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
                                if(Number(i+1)>=$('#license_qty').val()){
                                    $('#license_add').prop('disabled', true);
                                } else { $('#license_add').prop('disabled', false); }
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
    </script>
    <!-- Modal License Key-->

            <table class="table table-bordered table-striped" id="pi-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        
                        <th width="30px">@lang('#') </th>
                        <th width="30px">SL</th>
                        <th width="250px">@lang('Part Number')</th>
                        <th width="200px">@lang('Description')</th>
                        @if (session('logged_session_data.company_id')==2)
                        <th width="100px">@lang('HS Code')</th>
                        @endif
                        {{-- <th width="50px">@lang('PO Qty')</th>
                        <th width="30px">@lang('Already Executed')</th> --}}
                        <th width="70px">@lang('Tax')</th>
                        <th width="100px">@lang('Qty')</th>
                        {{-- <th width="50px">@lang('Balance Qty')</th> --}}
                        <th width="100px">@lang('Unitprice')</th>
                        <th width="100px">@lang('Value')</th>
                        <th style="width:70px;">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a> --}}
                            Discount
                        </th>
                        <th style="width:70px;">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalFreight">Freight</a> --}}
                            Freight
                        </th>
                        <th style="width:70px;">
                            {{-- <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalCustom">Custom Charges</a> --}}
                            Custom Charges
                        </th>
                        <th width="100px">@lang('Taxable Amount')</th>
                        <th width="100px">@lang('Vat Amount')</th>
                        <th width="100px">@lang('Total')</th>
                        <th width="100px">@lang('Serial No')</th>
                                                    
                                                    

                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                    <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              @if (session('logged_session_data.company_id')==2)
                              <td></td>
                              @endif
                              {{-- <td></td>
                              <td></td> --}}
                              <td></td>
                              <td></td>
                              <td><label id="qty_total">0</label></td>
                              {{-- <td></td> --}}
                              <td></td>
                              <td class="text-right"><label id="value_total">0.00</label></td>
                              <td class="text-right"><label id="discount_total">0.00</label></td>
                              <td class="text-right"><label id="fright_total">0.00</label></td>
                              <td class="text-right"><label id="customs_total">0.00</label></td>
                              <td class="text-right"><label id="taxableamount_total">0.00</label></td>
                              <td class="text-right"><label id="vatamount_total">0.00</label></td>
                              <td class="text-right"><label id="totalamount_total">0.00</label></td>
                              <td></td>
                            </tr>
                    {{-- <tr>
            <td></td>
            <td class="sstablefoot"><label id="qty_total">0</label></td>
            <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
            <td class="sstablefoot"><label id="value_total">0.00</label></td>
            <td class="sstablefoot"><label id="discount_total">0.00</label></td>
            <td class="sstablefoot"><label id="taxableamount_total">0.00</label></td>
            <td class="sstablefoot"><label id="vatamount_total">0.00</label></td>
        </tr> --}}
                </tfoot>
            </table>

            <div style="display: none;">
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowPO"><span
                        class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>


            <script>




                function fn_payment_terms() {
                    var val_payment_terms = $('#payment_terms').val();
                    if (val_payment_terms == 22) {
                        $('#div_payment_terms').css('display', 'block');
                    } else {
                        $('#div_payment_terms').css('display', 'none');
                    }
                }

            </script>



        </div>

        <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
            <div class="col-lg-12 text-right">
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowEquipment">
                    <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>
        </div>




        <div class="row mt-4">
            <div class="col-lg-12 text-right">
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <span class="ti-check"></span>
                    @if (isset($edit))
                        @lang('lang.update')
                    @else
                        @lang('lang.save')
                    @endif
                    @lang('Goods Receipt Note (GRN)')

                </button>
            </div>
        </div>

        {{ Form::close() }}

    </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="po_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Goods Receipt Note (GRN) Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
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
                                                    <th><input type="checkbox" id="check_all" checked onchange="chk()" /></th>
                                                    <th style="width: 30px;">@lang('SL') </th>
                                                    <th>@lang('Part Number')</th>
                                                    <th>@lang('Description')</th>
                                                    @if (session('logged_session_data.company_id')==2)
                                                    <th>@lang('HS Code')</th>
                                                    @endif
                                                    {{-- <th>@lang('PO Qty')</th>
                                                    <th>@lang('Already Executed')</th> --}}
                                                    <th>@lang('Tax')</th>
                                                    <th>@lang('Qty')</th>
                                                    {{-- <th>@lang('Balance Qty')</th> --}}
                                                    <th>@lang('Unitprice')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    @if (session('logged_session_data.company_id')==2)
                                                    <td></td>
                                                    @endif
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

                            <script>
                                function chk() {
                                  const isChecked = document.getElementById("check_all").checked;
                                  const checkboxes = document.querySelectorAll(".rowcheck");
                                  checkboxes.forEach(cb => cb.checked = isChecked);
                                }
                              
                                // Optional: trigger once on load
                                window.onload = chk;
                              </script>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>

                                        <button class="btn btn-primary bg-success" type="button" id="addPoPendingItems">
                                            Add Selected
                                        </button>
                                        {{-- <input class="primary-btn fix-gr-bg" type="" value="save" onclick="return validateAttachForm()"> --}}
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-grn-items-cart-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="discount_amount_grn_id" value="{{ @$po->id }}"/>                    
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-grn-items-cart-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="freight_amount_grn_id" value="{{ @$po->id }}"/>                    
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-grn-items-cart-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="custom_amount_grn_id" value="{{ @$po->id }}"/>                    
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Split Custom Charges</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
{{-- ModalCustom --}}

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
                                    
                                    <span class="bg-info m-2 p-2">dzxvgfxzvg</span>
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

    {{-- -------------------------------------------------------- --}}

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

        

        function popup_po_pending(id) {
            var selectedValues = [];
            $('input[name="pending_po"]:checked').each(function() {
                selectedValues.push($(this).val());
            });
            $("#loading_bg").css("display", "block");
            $("#hd_pending_po_id").val(selectedValues);
            $("#po_id").val(id);
            if(selectedValues != ""){
                document.getElementById('addPoPending').click();
            }

            if(id != 0){
                $("#table_id2").css("display", "none");    
            }

            $("#loading_bg").css("display", "none");
        }
        function without_po(id) {
            $("#loading_bg").css("display", "block");

            $("#po_id").val(id);
            $("#table_id2").css("display", "");

            $("#loading_bg").css("display", "none");
        }

        function popup_po_srlno(id) {
            $("#loading_bg").css("display", "block");
            $("#srl_row").val(id);
            var part_number = $('#part_number_' + id + '').val();
            var qty = $('#qty_' + id + '').val();
            $("#srl_part_number").val(part_number);
            $("#srl_qty").val(qty);
            document.getElementById('btnsrlpopup').click();
            var part_no = $('#part_id_' + id + '').val();
            var action = "{{ URL::to('goods-receipt-note-get-serialno') }}";
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
            var part_no = $('#part_id_' + id + '').val();
            var po_id = $('#hd_pending_po_id').val();
            var qty = $('#qty_' + id + '').val();
            var action = "{{ URL::to('goods-receipt-note-add-serialno') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    srl_no: srl_no,
                    part_number: part_number,
                    part_no: part_no,
                    po_id: po_id,
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


        function calc_change(id) {
            var net_vat = $('#net_vat').val();
            //var net_vat = $('#vat_percentage').val();

            var qty = $('#qty_' + id + '').val();
            
            var po_qty = $('#po_qty_' + id + '').val();
            var exe_qty = $('#exe_qty_' + id + '').val();

            var bal = Number(po_qty) - Number(exe_qty) - Number(qty);
            $('#bal_qty_' + id + '').val(bal);

            var unitprice = $('#unitprice_' + id + '').val();
            var value = $('#value_' + id + '').val();
            var discount = $('#discount_' + id + '').val();
            var taxamount = $('#taxamount_' + id + '').val();
            var vatamount = $('#vatamount_' + id + '').val();


            qty = (qty === '') ? '0' : qty;
            unitprice = (unitprice === '') ? '0' : unitprice;
            var fin_value = (unitprice * qty);
            $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


            value = (value === '') ? '0' : value;
            discount = (discount === '') ? '0' : discount;
            var fin_taxableamount = ((unitprice * qty) - Number(discount));
            $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(net_vat)) / 100);
            $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

            //calc_total();            
        }

        function calc_total() {
            var numItems = $('.rno').length;

            alert(numItems);

            var countrow = document.getElementById('row-count').value;
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
                t5 += Number($('#taxamount_' + i).val());
                t6 += Number($('#vatamount_' + i).val());
                t7 += Number($('#fright_' + i).val());
                t8 += Number($('#customcharges_' + i).val());
            }
            $('#qty_total').text(t1);
            $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#fright_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#customs_total').text(t8.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
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
            $('#description_new').val(selectedData.description || '');
        });
        
        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#hscode_txt').val(selectedData.hscode || '');
        });
        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#product_type').val(selectedData.product_type || '');
        });
        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#product_type_part_number_text').val(selectedData.description || '');
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
                            $('#net_vat').val(dataResult['data'].vat_percentage);
                            //$("select[id=tax] option:first").text(dataResult['data'].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'].vat_percentage);
                            $("#tax").val(dataResult['data'].vat_percentage);
                            $("#loading_bg").css("display", "none");     }
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
                                        "<input type='checkbox' onclick='popup_po_pending(" + id +
                                        ")' id='pending_po_" + (i+1) +
                                        "' name='pending_po' value='" + id +
                                        "'/> <label for='pending_po_" + (i+1) + "'> " + doc_number +
                                        "</label><br />";

                                    $("#plist").append(innerHtml);
                                    
                      
                            }                        
                        }
                        else{
                            $("#plist").empty();
                        }
                        var innerHtml ="<input type='radio' onclick='without_po(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without PO</label><br />";
                        $("#plist").append(innerHtml);

                        $("#loading_bg").css("display", "none");
                }
            });
        }



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
            if ($("#awbno").val() == "") {
                alert("Please Fill AWB No");
                $("#awbno").focus();
                return false;
            }
            if ($("#boeno").val() == "") {
                alert("Please Fill BOE No");
                $("#boeno").focus();
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
            if ($("#part_number_0").val() == "") {
                alert("Please Fill Part Number");
                $("#part_number_0").focus();
                return false;
            }
            if ($("#qty_0").val() == "") {
                alert("Please Fill Qty");
                $("#qty_0").focus();
                return false;
            }
            if ($("#unitprice_0").val() == "") {
                alert("Please Fill Unit Price");
                $("#unitprice_0").focus();
                return false;
            }
            if ($("#value_0").val() == "") {
                alert("Please Fill Taxable Amount");
                $("#value_0").focus();
                return false;
            }
            if ($("#taxamount_0").val() == "") {
                alert("Please Fill Taxable Amount");
                $("#taxamount_0").focus();
                return false;
            }
        }
        
    $('#goods-receipt-note-store').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
    });

    </script>
@endsection

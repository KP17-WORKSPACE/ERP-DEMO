@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    
<?php try { ?>

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Opening Stock Edit</h2>
                <span class="page-label">Home - Opening Stock Edit</span>
            </div>
            <div>
                <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>
                <a href="{{ url('item-store/show') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> View Stock</a>
                {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
            </div>
        </div>
        <div class="card shadow mb-4 p-4">
            {{-- @if (isset($edit))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => '/quotations/' . $edit->id, 'method' => 'PUT', 'id' => 'sales-invoice-create-form']) }}
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-store-form']) }}
            @endif --}}

            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="id" value="{{ isset($openingstock) ? $openingstock->id : '' }}">
            <div class="row">
                <div class="col-lg-3 mb-2">
                    <div class="no-gutters input-right-icon">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Doc') @lang('Date')<span>*</span></label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($openingstock) && !empty($openingstock->doc_date) ){
                                    $value = date('Y-m-d', strtotime(@$openingstock->doc_date)); }
                                @endphp
                                <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                    name="doc_date" value="{{ @$value }}">
                            </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">@lang('Doc') @lang('Number')<span>*</span></label>
                        <input
                            class="form-control {{ $errors->has('doc_number') ? 'is-invalid' : ' ' }}"
                            type="text" id="doc_number" name="doc_number"
                            value="{{ isset($openingstock) ? (!empty(@$openingstock->doc_number) ? @$openingstock->doc_number : old('doc_number')) : 'OPS-'.@App\SysHelper::get_new_maxid('sys_item_opening_stock', 'id')  }}">
                        <span class="focus-border"></span>
                        @if ($errors->has('doc_number'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('doc_number') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="no-gutters input-right-icon">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill') @lang('Date')<span>*</span></label>
                                @php $value = date('m/d/Y'); 
                                if(isset($openingstock) && !empty($openingstock->bill_date) ){ @$value = date('m/d/Y', strtotime(@$openingstock->bill_date)); }
                                else{ if(!empty(old('bill_date'))){ @$value = old('bill_date'); }else{ @$value = date('m/d/Y'); } }
                                @endphp
                                <input class="form-control" id="bill_date" type="date" autocomplete="off"
                                    name="bill_date" value="{{ @$value }}">
                            </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <label class="txtlbl">@lang('Currency')<span>*</span></label>
                    <select
                        class="form-control"
                        name="currency" id="currency">
                        {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                        @foreach ($currency as $value)
                            <option value="{{ @$value->id }}"
                                {{ isset($openingstock) ? (!empty(@$openingstock->currency) ? (@$openingstock->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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
                <div class="col-lg-6 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">@lang('Narration') <span>*</span></label>
                        <input
                            class="form-control"
                            type="text" name="narration" autocomplete="off"
                            value="{{ isset($openingstock) ? (!empty(@$openingstock->narration) ? @$openingstock->narration : old('narration')) : old('narration') }}"
                            id="narration">
                        <span class="focus-border"></span>
                        @if ($errors->has('narration'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('narration') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" readonly>                                                        
                        <span class="focus-border"></span>
                        @if ($errors->has('createdby'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('createdby') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        
        <div class="card shadow mb-4 p-4">
            <div class="equipment comon-status row mt-40 d-block">

                <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:220px;">@lang('Product Code')</th>
                            <th>@lang('Product Name')</th>
                            <th style="width:100px;">@lang('Qty')</th>
                            <th style="width:150px;">@lang('Unit Price')</th>
                            <th style="width:150px;">@lang('Value')</th>
                            <th style="width:150px;">@lang('Ref No')</th>
                            <th style="width:30px;"></th>
                        </tr>
                        <tr>
                            <td><input type="checkbox" checked hidden>
                                <select class="form-control js-example-basic-single" name="part_number[]" id="part_number_new" onchange="ddl_part_change_new()">
                                    <option value="none"></option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" type="hidden" id="part_number_val" name="part_number_val[]" autocomplete="off" readonly="true">
                            </td>
                            <td>
                                <select class="form-control" name="part_number_txt[]" id="part_number_txt_new" readonly="true" hidden>
                                    <option value="none"></option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->description }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" type="text" id="description_new" name="description[]" autocomplete="off" readonly="true">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="unitprice" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" id="refno" name="refno[]" autocomplete="off" min="0" >
                            </td>
                            <td>
                                <a onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <script>
                        function ddl_part_change_new() {
                            var selOpt = $('#part_number_new :selected').val();
                            $('#part_number_txt_new option[value=' + selOpt + ']').attr('selected', 'selected');
                            var selOpt2 = $('#part_number_txt_new :selected').text();
                            $('#description_new').val(selOpt2.trim());
                            $('#description_new').focus();
                            var pno = $('#part_number_new :selected').text();
                            $('#part_number_val').val(pno);
                        }
                        function calc_change_new(id) {
                            //var net_vat = $('#net_vat').val();
                            var net_vat = $('#tax').val();
    
                            var qty = $('#qty').val();
                            var unitprice = $('#unitprice').val();
                            var value = $('#value').val();
                            var discount = $('#discount').val();
                            var customcharges = $('#customcharges').val();
    
                            qty = (qty === '') ? '0' : qty;
                            unitprice = (unitprice === '') ? '0' : unitprice;
                            var fin_value = (unitprice * qty);
                            $('#value').val(fin_value.toFixed(2));
    
    
                            value = (value === '') ? '0' : value;
                            discount = (discount === '') ? '0' : discount;
                            customcharges = (customcharges === '') ? '0' : customcharges;
                            var fin_taxableamount = ((unitprice * qty) + Number(customcharges) - Number(discount));
                            $('#taxableamount').val(fin_taxableamount.toFixed(2));
    
                            var fin_vatamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat)) / 100);
                            var vatamount = $('#vatamount').val(fin_vatamount.toFixed(2));
    
                        }
                        function add_rows() {

                            if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                            if($("#qty").val()==""){$("#qty").focus(); return false;}
                            if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                            if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                            if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}

                            $("#loading_bg").css("display", "block");
                            var action = "{{ URL::to('add-stock-items-cart') }}";
                            $.ajax({
                                url: action,
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    part_number: $("#part_number_new").val(),
                                    part_number_txt: $("#part_number_val").val(),
                                    description: $("#description_new").val(),
                                    qty: $("#qty").val(),
                                    unitprice: $("#unitprice").val(),
                                    value: $("#value").val(),
                                    refno: $("#refno").val(),
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
                                                    <td>"+dataResult['data'][i].qty+"</td>\
                                                    <td>"+dataResult['data'][i].unitprice+"</td>\
                                                    <td>"+dataResult['data'][i].value+"</td>\
                                                    <td>"+dataResult['data'][i].refno+"</td>\
                                                    <td><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                    </tr>";
                                                    

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
                                            $("#qty").val("");
                                            $("#unitprice").val("");
                                            $("#value").val("");
                                            //$("#discount").val("0");
                                            //$("#customcharges").val("0");
                                            //$("#taxableamount").val("");
                                            //$("#vatamount").val("");

                                            $('#po-table tbody').empty();
                                            $("#po-table tbody").append(getSelectedRows); 
                                        }
                                        else{
                                            
                                        }
                                }
                            });
                            $("#loading_bg").css("display", "none");
                        }
                        function row_delete(id) {
                            $("#loading_bg").css("display", "block");
                            var action = "{{ URL::to('delete-stock-items-cart') }}";
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
                                                    <td>"+dataResult['data'][i].qty+"</td>\
                                                    <td>"+dataResult['data'][i].unitprice+"</td>\
                                                    <td>"+dataResult['data'][i].value+"</td>\
                                                    <td>"+dataResult['data'][i].refno+"</td>\
                                                    <td><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                    </tr>";
                                                    

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
                                            $("#qty").val("");
                                            $("#unitprice").val("");
                                            $("#value").val("");
                                            //$("#discount").val("0");
                                            //$("#customcharges").val("0");
                                            //$("#taxableamount").val("");
                                            //$("#vatamount").val("");

                                            $('#po-table tbody').empty();
                                            $("#po-table tbody").append(getSelectedRows); 
                                        }
                                        else{
                                            
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

                
                <table class="table table-bordered table-striped" cellspacing="0" width="100%" id="os-table">
                    <thead>
                        <tr>
                            <th style="width:220px;">@lang('Product Code')</th>
                            <th>@lang('Product Name')</th>
                            <th style="width:100px;">@lang('Qty')</th>
                            <th style="width:150px;">@lang('Unit Price')</th>
                            <th style="width:150px;">@lang('Value')</th>
                            <th style="width:150px;">@lang('Ref No')</th>
                            <th style="width:30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $qty=0; $price=0.00; $total=0.00; ?>
                    @if (count($stocklist)>0)
                    @foreach ($stocklist as $dt)
                    <tr>
                        <tr>
                            <td>{{ $dt->productdet->part_number }}</td>
                            <td>{{ $dt->description }}</td>
                            <td>{{ $dt->qty_in }}</td>
                            <td>{{ $dt->price_in }}</td>
                            <td>{{ $dt->qty_in*$dt->price_in }}</td>
                            <td>{{ $dt->refno }}</td>
                            <td><a style="display: none;" onclick="row_delete({{ $dt->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>

                            <?php /*
                        <td>{{ $Item->productdet->part_number }}</td>
                        <td><textarea type="text" class="form-control" id="txt_udescription_{{$Item->id}}">{!! nl2br($Item->description) !!}</textarea></td>
                        <td><input type="number" class="form-control" id="txt_uqty_{{$Item->id}}" onchange="calc_change({{$Item->id}})" value="{{$Item->qty_in}}"></td>
                        <td><input type="number" class="form-control" id="txt_uprice_{{$Item->id}}" onchange="calc_change({{$Item->id}})" value="{{ $Item->price_in }}"></td>
                        <td><input type="number" class="form-control" id="txt_utotal_price_{{$Item->id}}" value="{{ ($Item->price_in * $Item->qty_in) }}" readonly></td>
                        <td style="display: none;"><input type="number" class="form-control" id="txt_uremarks_{{$Item->id}}" value="{{ $Item->remarks }}"></td>
                        <td><input type="number" class="form-control" id="txt_urefno_{{$Item->id}}" value="{{ $Item->refno }}"></td>
                        <td align="right"><input type="hidden" id="txt_upro_id_{{$Item->product_id}}" value="{{$Item->product_id}}">
                        <button class="btn btn-warning btn-xs" title="Update" id="txt_btn_upd_{{$Item->id}}" onclick="upd_tostock({{$Item->id}})"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        <button class="btn btn-danger btn-xs" title="Delete" id="txt_btn_del_{{$Item->id}}" onclick="del_tostock({{$Item->id}})"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                        <?php
                            $qty+=$Item->qty_in;
                            $price+=$Item->price_in;
                            $total+=($Item->price_in * $Item->qty_in);
                        ?>
                        */ ?>
                    </tr>
                    @endforeach
                    @endif

                    

                    <?php /*

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store-additem','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-store-form']) }}
                    <tr>
                        <td>
                            <select class="form-control js-example-basic-single" name="part_number" id="part_number" onchange="ddl_part_change()">
                                <option value="none"></option>
                                @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="part_number_txt" id="part_number_txt" readonly="true" hidden>
                                <option value="none"></option>
                                @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->description }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" type="text" id="description" name="description" autocomplete="off" readonly="true" value="">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="txt_uqty_0" name="qty" autocomplete="off" min="0" onchange="calc_change(0)" value="">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="txt_uprice_0" name="unitprice" autocomplete="off" min="0" onchange="calc_change(0)" value="">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="txt_utotal_price_0" name="value" autocomplete="off" min="0" readonly value="">
                        </td>
                        <td style="display: none;">
                            <input class="form-control" type="text" id="remarks" name="remarks" autocomplete="off" value="">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="refno" name="refno" autocomplete="off" value="">
                        </td>
                        <td>
                            <input type="hidden" name="doc_number" value="{{ $openingstock->doc_number }}" />
                            <input type="hidden" name="doc_date" value="{{ $openingstock->doc_date }}" />
                            <input type="hidden" name="os_id" value="{{ $openingstock->id }}" />
                            <button class="btn btn-success btn-xs" title="Add">+ Add</button>
                        </td>
                    </tr>
                    {{ Form::close() }}
                    <script>
                        function calc_change(id){
                            var qty = $('#txt_uqty_'+id).val();
                            var unitprice = $('#txt_uprice_'+id).val();
                            var value = $('#txt_utotal_price_'+id).val();
                            qty = (qty === '') ? '0' : qty;
                            unitprice = (unitprice === '') ? '0' : unitprice;
                            var fin_value = (unitprice * qty);
                            $('#txt_utotal_price_'+id).val(fin_value.toFixed(2));
                        }
                        function ddl_part_change(){
                            var selOpt = $('#part_number :selected').val();
                            $('#part_number_txt option[value='+selOpt+']').attr('selected','selected');
                            var selOpt2 = $('#part_number_txt :selected').text();
                            $('#description').val(selOpt2);
                            $('#description').focus();
                        }
                    </script>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="sstablefoot"><label id="qty_total">{{ $qty }}</label></td>
                            <td class="sstablefoot"><label id="unitprice_total">{{ $price }}</label></td>
                            <td class="sstablefoot"><label id="value_total">{{ $total }}</label></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                </table>
                <div style="display: none;">
                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowOS"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                </div>

                    <div class="text-right">
                    @if (isset($openingstock))
                    @else
                    <button class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>
                        @if (isset($openingstock)) @lang('lang.update') @else @lang('lang.save') @endif @lang('Opening Stock')
                    </button>
                    @endif

                    */ ?>
                    </div>

                </div>
        </div>

    </div>




    
    <script>
        function upd_tostock(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_uqty_"+id).val();
            var price = $("#txt_uprice_"+id).val();
            var description = $("#txt_udescription_"+id).val();
            var remarks = $("#txt_uremarks_"+id).val();
            var refno = $("#txt_urefno_"+id).val();
            
            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_uqty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_ubtn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('item-store-updateitem') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    remarks: remarks,
                    refno: refno,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function del_tostock(id) {
            
            var result = confirm('Are you sure you want to delete this item?');
            if (!result) {
                return false;
            }

            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_del_"+id).val();
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('item-store-deleteitem') }}";
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
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

    $(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
    });

</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
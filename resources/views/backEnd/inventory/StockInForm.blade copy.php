@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Excess Stock (Stock In)</h2>
            <span class="page-label">Home - Excess Stock (Stock In)</span>
        </div>
        <div>
            <a href="{{ url('stock-in/show') }}" class="btn btn-primary"><i class="fa fa-list"></i> List</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (isset($edit))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in/' . $edit->id, 'method' => 'PUT', 'id' => 'stock-in-form']) }}
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'stock-in-form']) }}
                            @endif
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                            <div class="white-box">

                                <div class="col-lg-12 text-right">
                                    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                        @if (session()->has('message-success'))
                                            <p class="text-success">
                                                {{ session()->get('message-success') }}
                                            </p>
                                        @elseif(session()->has('message-danger'))
                                            <p class="text-danger">
                                                {{ session()->get('message-danger') }}
                                            </p>
                                        @endif
                                    @endif
                                </div>

                                <div class="add-visitor">
                                    <div class="row ">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                
                                                <div class="col-lg-3 mb-10">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="txtlbl">@lang('Date')<span>*</span></label>
                                                                <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ date('Y-m-d') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Doc Number')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('part_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="doc_number" autocomplete="off" id="doc_number" readonly
                                                            value="{{ @App\SysHelper::get_new_code('sys_stock_in','EX' ,'doc_number') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-2">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">Currency<span>*</span></label>
                                                        <select class="form-control" name="currency" id="currency">
                                                            @foreach ($currency as $value)
                                                                <option value="{{ @$value->id }}" >
                                                                    {{ @$value->code }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('createdby') ? ' is-invalid' : '' }}" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                                                        @if ($errors->has('createdby'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('createdby') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-9 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@if(isset($editserialno)) Add New @endif @lang('Remarks') <span>*</span></label>

                                                        <input
                                                            class="form-control {{ $errors->has('serial_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="remarks" autocomplete="off"
                                                            value=""
                                                            id="remarks">
                                                        @if ($errors->has('serial_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('serial_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                

                                            </div>
                                        </div>
                                    </div>


                                    <div class="equipment comon-status row d-block">
                                        <hr />
                                        <a class="btn-sm btn-danger float-right" data-toggle="modal" data-target="#ModalExcelQuote" data-backdrop="static" data-keyboard="false">Items Excel Import</a>
                                        <h6 class="primary-color">@lang('Item Details'):</h6> 
                                        
                                        <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th style="width:150px;">@lang('Part No')</th>
                                                    <th style="width:150px;">@lang('Description')</th>
                                                    <th style="width:100px;">@lang('Qty')</th>
                                                    <th style="width:120px;">@lang('Unit Price')</th>
                                                    <th style="width:120px;">@lang('Value')</th>
                                                    <th style="width:130px;">@lang('Serial No')</th>
                                                    <th style="width:200px;">@lang('Narration')</th>
                                                    <th style="width:20px;"></th>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" checked hidden>
                                                        <select class="form-control js-example-basic-single" name="part_number[]" id="part_number_new" onchange="ddl_part_change_new()">
                                                            <option value="none"></option>
                                                            @foreach ($items as $key => $value)
                                                                <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                                            @endforeach
                                                        </select>
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
                                                        <input class="form-control" type="number" id="unitprice" step="any" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" id="serialno" name="serialno[]" autocomplete="off" onclick="srlno_add()">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" id="narration" name="narration[]" autocomplete="off">
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="cart_item_id" />
                                                        <input type="hidden" id="deal_ref_id" />
                                                        <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                        <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                                    </td>
                                                </tr>
                                                <script>
                                                function ddl_part_change_new() {
                                                    var selOpt = $('#part_number_new :selected').val();
                                                    $('#part_number_txt_new option[value=' + selOpt + ']').attr('selected', 'selected');
                                                    var selOpt2 = $('#part_number_txt_new :selected').text();
                                                    $('#description_new').val(selOpt2.trim());
                                                    $('#description_new').focus();
                                                }
                                                function calc_change_new(id) {
                                                    var net_vat = $('#net_vat').val();
                            
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
                    
                                                    $("#loading_bg").css("display", "block");
                                                    var action = "{{ URL::to('stock-in-cart-add') }}";
                                                    $.ajax({
                                                        url: action,
                                                        type: "POST",
                                                        data: {
                                                            _token: '{{ csrf_token() }}',
                                                            part_number: $("#part_number_new").val(),
                                                            description : $('#description_new').val(),
                                                            qty: $("#qty").val(),
                                                            unitprice: $("#unitprice").val(),
                                                            value: $("#value").val(),
                                                            serialno:$('#serialno').val(),
                                                            narration:$('#narration').val(),
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
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].narration+" <input type='hidden' id='narration_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].narration+"' /></td>\
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
                                                                    $("#serialno").val("");
                                                                    $("#narration").val("");
                    
                                                                    $('#po-table tbody').empty();
                                                                    $("#po-table tbody").append(getSelectedRows); 
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
                                                    
                                                    $("#part_number_new").val(pid);
                                                    $("#select2-part_number_new-container").html(partno);
                                                    //$('#part_number_new').addClass('js-example-basic-single');
                                                    $('#description_new').val($('#description_'+id).val());
                                                    $('#qty').val($('#qty_'+id).val());
                                                    $('#unitprice').val($('#unitprice_'+id).val());
                                                    $('#value').val($('#value_'+id).val());
                                                    $("#serialno").val($('#serialno_'+id).val());
                                                    $("#narration").val($('#narration_'+id).val());
                    
                                                    $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                                    $('#deal_ref_id').val($('#deal_ref_id_'+id).val());
                                                }
                                                
                                                function row_update() {
                                                    $("#loading_bg").css("display", "block");
                                                    var itm_id = $('#cart_item_id').val();
                                                    if($('#deal_ref_id').val() != ""){
                                                        var deal_ref_id = $('#deal_ref_id').val();
                                                    } else { var deal_ref_id = 0; }
                                                    var part_number = $('#part_number_new').val();
                                                    var description = $('#description_new').val();
                                                    var qty = $('#qty').val();
                                                    var unitprice = $('#unitprice').val();
                                                    var value = $('#value').val();
                    
                                                    var action = "{{ URL::to('stock-in-cart-update') }}";
                                                    $.ajax({
                                                        url: action,
                                                        type: "POST",
                                                        data: {
                                                            _token: '{{ csrf_token() }}',
                                                            itm_id: itm_id,
                                                            deal_ref_id: deal_ref_id,
                                                            part_number: part_number,
                                                            description: description,
                                                            qty: qty,
                                                            unitprice: unitprice,
                                                            value: value,
                                                            serialno:$('#serialno').val(),
                                                            narration:$('#narration').val(),
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
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].narration+" <input type='hidden' id='narration_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].narration+"' /></td>\
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
                                                                    $("#serialno").val("");
                                                                    $("#narration").val("");
                                                                    $("#select2-part_number_new-container").html('');                                               
                    
                                                                    $('#po-table tbody').empty();
                                                                    $("#po-table tbody").append(getSelectedRows); 
                                                                    
                                                                    $('#btn_add_row').css("display",'block');
                                                                    $('#update_add_row').css("display",'none');
                    
                                                                }
                                                                else{
                                                                    $('#po-table tbody').empty();
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
                                                    var action = "{{ URL::to('stock-in-cart-delete') }}";
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
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                            <td class='text-right'>"+dataResult['data'][i].narration+" <input type='hidden' id='narration_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].narration+"' /></td>\
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
                                                                    $("#serialno").val("");
                                                                    $("#narration").val("");
                    
                                                                    $('#po-table tbody').empty();
                                                                    $("#po-table tbody").append(getSelectedRows); 
                                                                }
                                                                else{
                                                                    $('#po-table tbody').empty();
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
            
                                        <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px;">@lang('Part No')</th>
                                                    <th style="width:350px;">@lang('Description')</th>
                                                    <th style="width:70px;">@lang('Qty')</th>
                                                    <th class="text-right"style="width:80px;">@lang('Unit Price')</th>
                                                    <th class="text-right"style="width:70px;">@lang('Value')</th>
                                                    <th style="width:100px;">@lang('Serial No')</th>
                                                    <th style="width:200px;">@lang('Remarks')</th>
                                                    <th class="text-right"style="width:65px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($cart)>0)
                                                @foreach ($cart as $dt)
                                                <tr>
                                                    <td>{{ $dt->partno }} <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->partno }}" />
                                                        <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" /></td>
                                                    <td>{{ $dt->description }} <input type="hidden" id="description_{{ $dt->id }}" value="{{ $dt->description }}" /></td>
                                                    <td>{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}" value="{{ $dt->qty }}" /></td>
                                                    <td align="right">{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }} <input type="hidden" id="unitprice_{{ $dt->id }}" value="{{ $dt->unitprice }}" /></td>
                                                    <td align="right">{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }} <input type="hidden" id="value_{{ $dt->id }}" value="{{ $dt->value }}" /></td>
                                                    <td>{{ $dt->serialno }} <input type="hidden" id="serialno_{{ $dt->id }}" value="{{ $dt->serialno }}" /></td>
                                                    <td>{{ $dt->narration }} <input type="hidden" id="narration_{{ $dt->id }}" value="{{ $dt->narration }}" /></td>
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
                                                <tr class="bg-light">
                                                    <td></td>
                                                    <td></td>
                                                    <td class="font-weight-bold"><label id="qty_total">{{ $cart->sum('qty') }}</label></td>
                                                    <td class="text-right font-weight-bold"><label id="unitprice_total"></label></td>
                                                    <td class="text-right font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($cart->sum('value'),2,'.',',') }}</label></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
            
            <script>
            function ddl_part_change(id)
            {
            var selOpt = $('#part_number_'+id+' :selected').val();
            $('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');        
            var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
            $('#description_'+id+'').val(selOpt2);
            $('#description_'+id+'').focus();
            }
            
            function calc_change(id) {
                var net_vat = $('#net_vat').val();
                //var net_vat = $('#vat_percentage').val();
            
                var qty = $('#qty_' + id + '').val();
                var unitprice = $('#unitprice_' + id + '').val();
                var value = $('#value_' + id + '').val();
                var discount = $('#discount_' + id + '').val();
                var taxamount = $('#taxamount_' + id + '').val();
                var vatamount = $('#vatamount_' + id + '').val();
                var totalamount = $('#totalamount_' + id + '').val();
            
            
                qty = (qty === '') ? '0' : qty;
                unitprice = (unitprice === '') ? '0' : unitprice;
                var fin_value = (unitprice * qty);
                $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
            
            
                value = (value === '') ? '0' : value;
                discount = (discount === '') ? '0' : discount;
                var fin_taxableamount = ((unitprice * qty) - Number(discount));
                $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
            
                var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
                $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
            
                var fin_totalamount = (fin_taxableamount + fin_vatableamount);
                $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
            
                calc_total();
            }
            
            $(document).on("change", ".unitprice", function () {
                var tot = 0;
                $(".unitprice").each(function() {
                    var vale = $(this).val();
                    if(!isNaN(parseFloat(vale))){
                        tot = parseInt(tot) + parseInt(vale);
                    }
                });
                alert(tot);
            });
            
            
            function calc_total()
            {
            var countrow = document.getElementById('si-row-count').value;
            
            //var countrow = $('#si-table >tbody >tr').length;
            var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
            for(var i=1; i<=countrow; i++)
            {
            t1 += Number($('#qty_'+i).val());
            t2 += Number($('#unitprice_'+i).val());
            t3 += Number($('#value_'+i).val());
            t4 += Number($('#discount_'+i).val());
            t5 += Number($('#customcharges_'+i).val());
            t6 += Number($('#taxableamount_'+i).val());
            t7 += Number($('#vatamount_'+i).val());
            }
            $('#qty_total').text(t1);
            $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#net_total').text((t6+t7).toFixed(@json(session('logged_session_data.decimal_point'))));
            }
            
            function fn_payment_terms()
            {
            var val_payment_terms = $('#payment_terms').val();
            if(val_payment_terms==22)
            {
            $('#div_payment_terms').css('display','block');
            }
            else
            {
            $('#div_payment_terms').css('display','none');
            }
            }
            function fn_shipping_name()
            {
            var shipping_id = $('#shipping_name').val();
            var shipping_data = $('#ship_'+shipping_id).val();        
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


                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                                <span class="ti-check"></span>
                                                    @lang('lang.save')
                                                @lang('Stock In')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Items Excel Import</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in-cart-excel-add', 'method' => 'POST', 'id' => 'stock-in-cart-excel-add']) }}



                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                                <label for="" class="form-label">Select File (.csv)</label>
                        </div>
                        <div class="col-md-4">
                                <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv" />
                        </div>
                        <div class="col-md-4">
                                <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                                {{-- <input type="file" name="import_file" class="btn-danger" required /> --}}
                                (<a href="{{ url('public/uploads/product_upload/stock_in_sample_format.csv') }}" target="_blank">Sample File</a>)
                        </div>

                        <div class="col-md-12 mt-2">
                            <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width:220px;">Part No</th>
                                        <th>Description</th>
                                        <th style="width:70px;">Qty</th>
                                        <th style="width:100px;" class="text-right">Unit Price</th>
                                        <th style="width:100px;" class="text-left">Serial No</th>
                                        <th style="width:100px;" class="text-left">Narration</th>
                                        <th style="width:50px;" class="text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be inserted here -->
                                </tbody>
                            </table>

                            <?php
                            $part_number = $items->pluck('part_number');
                            ?>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
                            <script>
                                function readExcel() {
                                    var file = document.getElementById('excel-file').files[0];
                                    if (!file) {
                                        alert("Please select an Excel file.");
                                        return;
                                    }
                            
                                    var reader = new FileReader();
                                    reader.onload = function(event) {
                                        var data = event.target.result;
                                        var workbook = XLSX.read(data, { type: 'binary' });
                            
                                        // Assuming the data is in the first sheet
                                        var sheet = workbook.Sheets[workbook.SheetNames[0]];
                                        var rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                            
                                        var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                                        tableBody.innerHTML = "";  // Clear any previous data
                            
                                        // Loop through each row and add data to the table
                                        for (var i = 1; i < rows.length; i++) {  // Skip header row
                                            var row = rows[i];
                                            if (row.length < 6) continue;  // Skip invalid rows


                                            
                                            var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                            var lowercase_part_number = part_number.map(function(value) {
                                                return value.toLowerCase();
                                            });

                                            var json_output = JSON.stringify(lowercase_part_number);

                                            var newRow = tableBody.insertRow(tableBody.rows.length);
                                            
                                            var rowVal = String(row[0] ?? '');
                                            var trimmedValue = rowVal.trim();
                                            
                                            if (json_output.includes(trimmedValue.toLowerCase())) {  // Use .includes() for array checking
    
                                            } else {
                                                newRow.style.backgroundColor = "#ffbebe";
                                            }
                            
                                            // Part No
                                            var partNoCell = newRow.insertCell(0);
                                            var partNoInput = document.createElement('input');
                                            partNoInput.type = 'text';  // Change to text input
                                            partNoInput.name = 'excel_part_no[]';
                                            partNoInput.value = rowVal.trim();
                                            partNoInput.classList.add('form-control');
                                            partNoCell.appendChild(partNoInput);
                            
                                            // Description
                                            var descriptionCell = newRow.insertCell(1);
                                            var descriptionInput = document.createElement('input');
                                            descriptionInput.type = 'text';  // Change to text input
                                            descriptionInput.name = 'excel_description[]';
                                            descriptionInput.value = row[1].trim();
                                            descriptionInput.classList.add('form-control');
                                            descriptionCell.appendChild(descriptionInput);
                                                        
                                            // Qty
                                            var qtyCell = newRow.insertCell(2);
                                            var qtyInput = document.createElement('input');
                                            qtyInput.type = 'text';  // Change to text input
                                            qtyInput.name = 'excel_qty[]';
                                            qtyInput.value = row[2];
                                            qtyInput.classList.add('form-control');
                                            qtyCell.appendChild(qtyInput);
                            
                                            // Unit Price (Right-aligned)
                                            var unitPriceCell = newRow.insertCell(3);
                                            var unitPriceInput = document.createElement('input');
                                            unitPriceInput.type = 'text';  // Change to text input
                                            unitPriceInput.name = 'excel_unit_price[]';
                                            unitPriceInput.value = row[3];
                                            unitPriceInput.classList.add('text-right');
                                            unitPriceInput.classList.add('form-control');
                                            unitPriceCell.appendChild(unitPriceInput);
                            
                                            // Discount (Right-aligned)
                                            var serialnoCell = newRow.insertCell(4);
                                            var serialnoInput = document.createElement('input');
                                            serialnoInput.type = 'text';  // Change to text input
                                            serialnoInput.name = 'excel_serial_no[]';
                                            serialnoInput.value = row[4];
                                            serialnoInput.classList.add('text-left');
                                            serialnoInput.classList.add('form-control');
                                            serialnoCell.appendChild(serialnoInput);
                                            
                                            // VAT (Right-aligned)
                                            var narrationCell = newRow.insertCell(5);
                                            var narrationInput = document.createElement('input');
                                            narrationInput.type = 'text';  // Change to text input
                                            narrationInput.name = 'excel_narration[]';
                                            narrationInput.value = row[5];
                                            narrationInput.classList.add('text-left');
                                            narrationInput.classList.add('form-control');
                                            narrationCell.appendChild(narrationInput);
                                            
                                            var deleteCell = newRow.insertCell(6);  // Last cell for delete button
                                            var deleteButton = document.createElement('button');
                                            deleteButton.type = 'button';  // Make sure the button doesn't submit a form
                                            deleteButton.textContent = 'Delete';
                                            deleteButton.classList.add('btn-sm');
                                            deleteButton.classList.add('btn-danger');
                                            deleteButton.onclick = function() {
                                                // Delete the row when the button is clicked
                                                var rowToDelete = this.parentNode.parentNode;
                                                rowToDelete.remove();
                                            };
                                            deleteCell.appendChild(deleteButton);

                                        }
                                    };
                                    reader.readAsBinaryString(file);
                                }
                            </script>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary excel_model_close" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    {{-- onclick="return add_excel_data()" --}}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->

    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Packing List Edit</h2>
            <span class="page-label">Home - Packing List</span>
        </div>
        <div>
            <a href="{{ url('packing-list/show') }}" class="btn btn-primary"><i class="fa fa-list"></i> List</a>
            <a href="{{ url('packing-list/'.$edit->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-list"></i> View</a>
            <!-- Input with Search -->
            <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                <input type="text" id="quick_search_doc_number" placeholder="Doc Number" class="form-control pr-4" /> 
                <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                <i class="fas fa-search"></i>
                </span>
            </div>
            <script>
                const baseUrl = "{{ url('get-edit-url-packing-list') }}";                
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
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (isset($edit))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'packing-list/' . $edit->id, 'method' => 'PUT', 'id' => 'stock-in-form']) }}
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'packing-list','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'stock-in-form']) }}
                            @endif
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                            <input type="hidden" name="packing_list_id" id="packing_list_id" value="{{ $edit->id }}">
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
                                                <div class="col-lg-6 mb-10">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="txtlbl">Account  (Customer / Supplier)<span>*</span></label>
                                                                <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                                                                    <option data-display="@lang('Customer')" value="">@lang('Select Account')</option>
                                                                    @foreach ($account as $value)
                                                                        <option value="{{ @$value->id }}"
                                                                            {{ isset($edit) ? (!empty(@$edit) ? (@$edit->account_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                                            {{ @$value->account_code }} - {{ @$value->account_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-10">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="txtlbl">@lang('Date')<span>*</span></label>
                                                                <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ $edit->date }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Doc Number')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('part_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="doc_number" autocomplete="off" id="doc_number" readonly
                                                            value="{{ $edit->doc_number }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-2">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">Currency<span>*</span></label>
                                                        <select class="form-control" name="currency" id="currency">
                                                            @foreach ($currency as $value)
                                                                <option value="{{ @$value->id }}" @if($edit->currancy == $value->id) selected @endif>
                                                                    {{ @$value->code }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Ref No')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('part_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="refno" autocomplete="off" id="refno" 
                                                            value="{{ $edit->refno }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Ref Date')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('part_number') ? ' is-invalid' : '' }}"
                                                            type="date" name="refdate" autocomplete="off" id="refdate"  value="{{ $edit->refdate }}"
                                                             required>
                                                    </div>
                                                </div>


                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                                        <input class="form-control {{ $errors->has('createdby') ? ' is-invalid' : '' }}" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@if(isset($editserialno)) Add New @endif @lang('Remarks') <span>*</span></label>

                                                        <input
                                                            class="form-control {{ $errors->has('serial_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="remarks" autocomplete="off"
                                                            value="{{ $edit->remarks }}"
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
                                        <h6 class="primary-color">@lang('Item Details'):</h6> 
                                        
                                        <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px;">@lang('Box No')</th>
                                                    <th style="width:200px;">@lang('Part No')</th>
                                                    <th style="width:100px;">@lang('Qty')</th>
                                                    <th style="width:200px;">@lang('COO')</th>
                                                    <th style="width:150px;">@lang('H.S Code')</th>
                                                    <th style="width:150px;">@lang('Weight')</th>
                                                    <th style="width:150px;">@lang('Dimension (L x W x H)')</th>
                                                    <th style="width:150px;"></th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input class="form-control" type="number" id="boxno" name="boxno" autocomplete="off" min="0">
                                                    </td>
                                                    <td><input type="checkbox" checked hidden>
                                                        <select class="form-control js-example-basic-single" name="part_number" id="part_number_new" onchange="ddl_part_change_new()">
                                                            <option value="none"></option>
                                                            @foreach ($items as $key => $value)
                                                                <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input class="w-100 sstxtbx" type="hidden" id="partno" name="partno">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="number" id="qty" name="qty" autocomplete="off" min="0">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" id="coo" name="coo" autocomplete="off" min="0" >
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" id="hscode" name="hscode" autocomplete="off" min="0" >
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="pro_weight">
                                                        <input class="form-control" type="number" step="Any" id="weight" name="weight" autocomplete="off" min="0" >
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="text" id="dimension" name="dimension" autocomplete="off" min="0" >
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="cart_item_id" />
                                                        <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                        <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                                    </td>
                                                </tr>
                                                <script>
                                                    function ddl_part_change_new() {
                                                        var selOpt = $('#part_number_new :selected').val();
                                                        $('#part_number_txt_new option[value=' + selOpt + ']').attr('selected', 'selected');
                                                        var selOpt2 = $('#part_number_txt_new :selected').text();
                                                        $('#partno').val($("#part_number_new option:selected").text());
                
                                                        $("#loading_bg").css("display", "block");
                                                        var action = "{{ URL::to('get-clearance-items-list') }}";
                                                        $.ajax({
                                                            url: action,
                                                            type: "GET",
                                                            data: {
                                                                _token: '{{ csrf_token() }}',
                                                                pid: $("#part_number_new").val(),
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
                                                                            $("#coo").val(dataResult['data'][i].coo);
                                                                            $("#hscode").val(dataResult['data'][i].hscode);
                                                                            $("#weight").val(dataResult['data'][i].weight);
                                                                            $("#pro_weight").val(dataResult['data'][i].weight);                                                            
                                                                        }
                                                                    }
                                                                }
                                                            });
                                                            $("#loading_bg").css("display", "none");
                                                    }
                                                function add_rows() {
                    
                                                    if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                                                    if($("#qty").val()==""){$("#qty").focus(); return false;}
                    
                                                    $("#loading_bg").css("display", "block");
                                                    var action = "{{ URL::to('packing-list-items-add') }}";
                                                    $.ajax({
                                                        url: action,
                                                        type: "POST",
                                                        data: {
                                                            _token: '{{ csrf_token() }}',
                                                            packing_list_id: $("#packing_list_id").val(),
                                                            boxno: $("#boxno").val(),
                                                            part_number: $("#part_number_new").val(),
                                                            qty: $("#qty").val(),
                                                            coo: $("#coo").val(),
                                                            hscode: $("#hscode").val(),
                                                            weight:$('#weight').val(),
                                                            dimension:$('#dimension').val(),
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
                                                                            <td>"+dataResult['data'][i].boxno+" <input type='hidden' id='boxno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].boxno+"' /></td>\
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' /></td>\
                                                                            <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' /></td>\
                                                                            <td>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' /></td>\
                                                                            <td>"+dataResult['data'][i].dimension+" <input type='hidden' id='dimension_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].dimension+"' /></td>\
                                                                            <td>\
                                                                                <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                                <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                                <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                                <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                            </td>\
                                                                            </tr>";
                                                                            
                                                                    }
                    
                                                                    $("#part_number_new").val("none");
                                                                    //$("#tax").val("");
                                                                    $("#qty").val("");
                                                                    $("#boxno").val("");
                                                                    $("#coo").val("");
                                                                    $("#hscode").val("");
                                                                    $("#weight").val("");
                                                                    $("#dimension").val("");
                    
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
                                                    $('#qty').val($('#qty_'+id).val());
                                                    $('#boxno').val($('#boxno_'+id).val());
                                                    $('#coo').val($('#coo_'+id).val());
                                                    $('#hscode').val($('#hscode_'+id).val());
                                                    $("#weight").val($('#weight_'+id).val());
                                                    $("#dimension").val($('#dimension_'+id).val());
                    
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
                                                    var qty = $('#qty').val();
                                                    var coo = $('#coo').val();
                                                    var hscode = $('#hscode').val();
                    
                                                    var action = "{{ URL::to('packing-list-items-update') }}";
                                                    $.ajax({
                                                        url: action,
                                                        type: "POST",
                                                        data: {
                                                            _token: '{{ csrf_token() }}',
                                                            packing_list_id: $("#packing_list_id").val(),
                                                            boxno: $("#boxno").val(),
                                                            itm_id: itm_id,
                                                            deal_ref_id: deal_ref_id,
                                                            part_number: part_number,
                                                            qty: qty,
                                                            coo: coo,
                                                            hscode: hscode,
                                                            weight:$('#weight').val(),
                                                            dimension:$('#dimension').val(),
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
                                                                            <td>"+dataResult['data'][i].boxno+" <input type='hidden' id='boxno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].boxno+"' /></td>\
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' /></td>\
                                                                            <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' /></td>\
                                                                            <td>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' /></td>\
                                                                            <td>"+dataResult['data'][i].dimension+" <input type='hidden' id='dimension_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].dimension+"' /></td>\
                                                                            <td>\
                                                                                <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                                <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                                <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                                <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                            </td>\
                                                                            </tr>";
                                                                    }
                    
                                                                    $("#part_number_new").val("none");
                                                                    //$("#tax").val("");
                                                                    $("#qty").val("");
                                                                    $("#boxno").val("");
                                                                    $("#coo").val("");
                                                                    $("#hscode").val("");
                                                                    $("#weight").val("");
                                                                    $("#dimension").val("");
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
                                                    var action = "{{ URL::to('packing-list-items-delete') }}";
                                                    $.ajax({
                                                        url: action,
                                                        type: "POST",
                                                        data: {
                                                            _token: '{{ csrf_token() }}',
                                                            packing_list_id: $("#packing_list_id").val(),
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
                                                                            <td>"+dataResult['data'][i].boxno+" <input type='hidden' id='boxno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].boxno+"' /></td>\
                                                                            <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                            <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                            <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' /></td>\
                                                                            <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' /></td>\
                                                                            <td>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' /></td>\
                                                                            <td>"+dataResult['data'][i].dimension+" <input type='hidden' id='dimension_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].dimension+"' /></td>\
                                                                            <td>\
                                                                                <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                                <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                                <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                                <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                            </td>\
                                                                            </tr>";
                                                                    }
                    
                                                                    $("#part_number_new").val("none");
                                                                    //$("#tax").val("");
                                                                    $("#qty").val("");
                                                                    $("#boxno").val("");
                                                                    $("#coo").val("");
                                                                    $("#hscode").val("");
                                                                    $("#weight").val("");
                                                                    $("#dimension").val("");
                    
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
                                                    <th style="width:100px;">@lang('Box No')</th>
                                                    <th style="width:150px;">@lang('Part No')</th>
                                                    <th style="width:70px;">@lang('Qty')</th>
                                                    <th style="width:100px;">@lang('COO')</th>
                                                    <th style="width:100px;">@lang('H.S Code')</th>
                                                    <th style="width:100px;">@lang('Weight')</th>
                                                    <th style="width:200px;">@lang('Dimension')</th>
                                                    <th style="width:65px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($edit_items)>0)
                                                @foreach ($edit_items as $dt)
                                                <tr>
                                                    <td>{{ $dt->boxno }} <input type="hidden" id="boxno_{{ $dt->id }}" value="{{ $dt->boxno }}" /></td>
                                                    <td>{{ $dt->partno }} <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->partno }}" />
                                                        <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" /></td>
                                                    <td>{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}" value="{{ $dt->qty }}" /></td>
                                                    <td>{{ $dt->coo }} <input type="hidden" id="coo_{{ $dt->id }}" value="{{ $dt->coo }}" /></td>
                                                    <td>{{ $dt->hscode }} <input type="hidden" id="hscode_{{ $dt->id }}" value="{{ $dt->hscode }}" /></td>
                                                    <td>{{ $dt->weight }} <input type="hidden" id="weight_{{ $dt->id }}" value="{{ $dt->weight }}" /></td>
                                                    <td>{{ $dt->dimension }} <input type="hidden" id="dimension_{{ $dt->id }}" value="{{ $dt->dimension }}" /></td>
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
                                                @lang('Update')
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
    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
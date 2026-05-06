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
            <h2 class="page-heading m-0">Customs Clearance</h2>
            <span class="page-label">Home - Customs Clearance</span>
        </div>
        <div>
            <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
            <a href="{{ url('clearance/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('clearance') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
		
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'clearance-store', 'method' => 'POST', 'id' => 'clearance-create-form']) }}
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                    <input type="hidden" name="id" value="{{ isset($clearance) ? $clearance->id : '' }}">
                    <input type="hidden" name="deal_id" value="{{ isset($deal_id) ? $deal_id : '0' }}">
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
                                <div class="col-lg-4">
                                    <div class="invoice-details-left">
                                        {{-- <div class="mb-2">
                                                <img src="{{ asset($company->company_logo) }}" class="tender-create-logo">
                                            </div> --}}
                                        <div class="business-info">
                                            <h3>Syscom FZE</h3>
                                            <p>RA08FD03<br />Jebel Ali Freezone, PO Box 124402<br />Dubai, UAE</p>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="txtlbl">@lang('Doc')
                                                    @lang('Number')<span>*</span></label>
                                                    <?php
                                                        $no=@App\SysHelper::get_new_code_normal('sys_clearance','SYZ','invoice_no');
                                                    ?>
                                                    <input class="form-control" type="text" name="doc_no" autocomplete="off" id="doc_no" value="{{ $no }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Invoice')
                                                            @lang('lang.date')</label>
                                                        @php
                                                            $value = date('Y-m-d');
                                                            if (isset($edit) && !empty($edit->date)) {
                                                                @$value = date('m/d/Y', strtotime(@$edit->date));
                                                            }
                                                        @endphp
                                                        <input class="form-control" id="invoice_date" type="date" autocomplete="off" name="invoice_date" value="{{ @$value }}">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="txtlbl">@lang('Created')
                                                    @lang('By')<span>*</span></label>
                                                <input
                                                    class="form-control" type="text" name="createdby" autocomplete="off" id="createdby"
                                                    value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="txtlbl">Invoice Number<span>*</span></label>
                                                <input class="form-control"
                                                    type="text" name="invoice_no" autocomplete="off" id="invoice_no" value="@if(isset($invoice_no)) {{ $invoice_no }} @endif" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <label class="txtlbl">Currency<span>*</span></label>
                                            <select
                                                class="form-control js-example-basic-single"
                                                name="currency" id="currency">
                                                @foreach ($currency as $value)
                                                    <option value="{{ @$value->id }}"
                                                        {{ isset($edit) ? (!empty(@$edit->customer_id) ? (@$edit->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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

                                    </div>
                                </div>
                            </div>



                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Bill To') <span>*</span></label>
                                        <textarea type="text" class="form-control" id="bill_to" name="bill_to" required>@if(isset($customer_address)) {{ $customer_address->customername->name }} @endif</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Ship To') <span>*</span></label>
                                        <textarea type="text" class="form-control" id="ship_to" name="ship_to" required>@if(isset($customer_address)) {{ $customer_address->delivery_company }} @endif</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Ship To Address') <span></span></label>
                                        <textarea type="text" class="form-control" id="ship_to_address" name="ship_to_address">@if(isset($customer_address)) {{ $customer_address->delivery_address }} @endif</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Payment Method') <span></span></label>
                                        <select class="form-control" name="payment_method[]"
                                            id="payment_method">
                                            <option value=""></option>
                                            <option value="CDR Cash">CDR Cash</option>
                                            <option value="CDR Bank" selected>CDR Bank</option>
                                            <option value="Deposit">Deposit</option>
                                            <option value="Credit A/C*">Credit A/C*</option>
                                            <option value="Stan. G*">Stan. G*</option>
                                            <option value="Bank G*">Bank G*</option>
                                            <option value="FTT">FTT</option>
                                            <option value="Alcohol">Alcohol</option>
                                            <option value="Other">Other</option>
                                        </select>


                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Customer Bill Type') <span></span></label>
                                        <select class="form-control" name="customer_bill_type[]"
                                            id="customer_bill_type">
                                            <option value=""></option>
                                            <option value="Import">Import</option>
                                            <option value="Import for Re-Export">Import for Re-Export</option>
                                            <option value="Temporary Exit">Temporary Exit</option>
                                            <option value="Free Zone Internal Transfer" selected>Free Zone Internal Transfer</option>
                                            <option value="Bill of Entry">Bill of Entry</option>
                                            <option value="Export">Export</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Free Zone Bill of Entry No') <span></span></label>
                                        <input type="text" class="form-control"
                                            id="free_zone_bill_no" name="free_zone_bill_no" value="AS PER INV"
                                            required>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Description of Goods') <span></span></label>
                                        <input type="text" class="form-control"
                                            id="goods_description" name="goods_description" value="AS PER INV"
                                            required>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('BOE No') <span>*</span></label>
                                        <input type="text" class="form-control" id="boe_no"
                                            name="boe_no" required>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Exit Point') <span></span></label>
                                        <select class="form-control" name="exit_point" id="exit_point">
                                            <option value="Jebel Ali Free Zone">Jebel Ali Free Zone</option>
                                            <option value="Jebel Ali Free Zone / Dubai Airport Free Zone" selected>Jebel Ali Free Zone / Dubai Airport Free Zone</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-10">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Box Type') <span></span></label>
                                        <select class="form-control" name="box_type"
                                            id="box_type">
                                            <option value=""></option>
                                            <option value="PCS">PCS</option>
                                            <option value="Box" selected>Box</option>
                                            <option value="Pallet">Pallet</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Box Qty') <span></span></label>
                                        <input type="text" class="form-control" id="box_qty"
                                            name="box_qty">
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Volume CBM') <span></span></label>
                                        <input type="text" class="form-control" id="cbm" name="cbm" value="AS PER INV">
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Destination') <span></span></label>
                                        <select class="form-control" name="destination"
                                            id="destination">
                                            <option value="Jebel Ali Free Zone">Jebel Ali Free Zone</option>
                                            <option value="UAE">UAE</option>
                                            <option value="Qatar">Qatar</option>
                                            <option value="Oman">Oman</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                            <option value="Kuwait">Kuwait</option>
                                            <option value="Jordan">Jordan</option>
                                            <option value="Dubai Silicon Oasis">Dubai Silicon Oasis</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="Africa">Africa</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="equipment comon-status row mt-4 d-block">
                                <script>
                                    function ddl_part_change_new() {
                                        var selOpt = $('#part_number_new :selected').val();
                                        $('#part_number_txt_new option[value=' + selOpt + ']').attr('selected', 'selected');
                                        var selOpt2 = $('#part_number_txt_new :selected').text();
                                        $('#partno').val($("#part_number_new option:selected").text());
                                        $('#description_new').val(selOpt2.trim());
                                        $('#description_new').focus();

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
                                                            $("#description_new").val(dataResult['data'][i].description);
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
                                    function calc_change_new(id) {
                
                                        var qty = $('#qty').val();
                                        var price = $('#price').val();
                                        var pro_weight = $('#pro_weight').val();
                
                                        qty = (qty === '') ? '0' : qty;
                                        price = (price === '') ? '0' : price;
                                        pro_weight = (pro_weight === '') ? '0' : pro_weight;

                                        var fin_value = (price * qty);
                                        var fin_value_weight = (pro_weight * qty);

                                        $('#totalprice').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
                                        $('#weight').val(fin_value_weight.toFixed(3));
                                    }
                                    function add_rows() {
        
                                        if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                                        if($("#coo").val()==""){$("#coo").focus(); return false;}
                                        if($("#hscode").val()==""){$("#hscode").focus(); return false;}
                                        if($("#weight").val()==""){$("#weight").focus(); return false;}
                                        if($("#pro_weight").val()==""){$("#pro_weight").focus(); return false;}
                                        if($("#qty").val()==""){$("#qty").focus(); return false;}
                                        if($("#price").val()==""){$("#price").focus(); return false;}
                                        if($("#totalprice").val()==""){$("#totalprice").focus(); return false;}
                                        
                                        $("#loading_bg").css("display", "block");
                                        var action = "{{ URL::to('add-clearance-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                pid: $("#part_number_new").val(),
                                                partno: $("#partno").val(),
                                                description: $("#description_new").val(),
                                                coo: $("#coo").val(),
                                                hscode: $("#hscode").val(),
                                                weight: $("#weight").val(),
                                                qty: $("#qty").val(),
                                                price: $("#price").val(),
                                                totalprice: $("#totalprice").val(),
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
                                                        
                                                    var qty_total=0; var price_total=0; var totalprice_total=0;
        
                                                        for(var i=0; i<len; i++){

                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].pid+"' /></td>\
                                                                <td><div style='width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;'>"+dataResult['data'][i].description+"</div><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' readonly /></td>\
                                                                <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' readonly /></td>";

                                                                
                                                                if(dataResult['data'][i].clearance_id > 45){
                                                                getSelectedRows +="<td class='text-right'>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' readonly /><input type='hidden' id='pro_weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight/dataResult['data'][i].qty+"'></td>";
                                                                } else{
                                                                    getSelectedRows +="<td class='text-right'>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' readonly /><input type='hidden' id='pro_weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight/dataResult['data'][i].qty+"'></td>";
                                                                } 
                                                                
                                                                getSelectedRows +="<td class='text-center'>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].price+" <input type='hidden' id='price_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].price+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].totalprice+" <input type='hidden' id='totalprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].totalprice+"' /></td>\
                                                                <td class='text-right'>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                                
                                                                qty_total += Number(dataResult['data'][i].qty);
                                                                price_total += Number(dataResult['data'][i].price);
                                                                totalprice_total += Number(dataResult['data'][i].totalprice);
        
                                                        }

                                                        $("#part_number_new").val('');
                                                        $("#description_new").val('');
                                                        $("#coo").val('');
                                                        $("#hscode").val('');
                                                        $("#weight").val('');
                                                        $("#pro_weight").val('');
                                                        $("#qty").val('');
                                                        $("#price").val('');
                                                        $("#totalprice").val('');
                                                        
                                                        $("#qty_total").val(qty_total);
                                                        $("#price_total").val(price_total);
                                                        $("#totalprice_total").val(totalprice_total);
                                                        
                                                        $('#clearance-table tbody').empty();
                                                        $("#clearance-table tbody").append(getSelectedRows);
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

                                        $("#coo").attr("readonly", false);
                                        $("#hscode").attr("readonly", false);
                                        $("#weight").attr("readonly", false);

                                        var partno = $('#partno_'+id).val();
                                        var pid = $('#pid_'+id).val();
                                        var description = $('#description_'+id).val();
                                        var coo = $('#coo_'+id).val();
                                        var hscode = $('#hscode_'+id).val();
                                        var weight = $('#weight_'+id).val();
                                        var pro_weight = $('#pro_weight_'+id).val();
                                        var qty = $('#qty_'+id).val();
                                        var price = $('#price_'+id).val();
                                        var totalprice = $('#totalprice_'+id).val();

                                        
                                $("#part_number_new").val(pid);
                                $("#partno").val(partno);
                                $("#select2-part_number_new-container").html(partno);
                                $('#description_new').val(description);
                                        $("#coo").val(coo);
                                        $("#hscode").val(hscode);
                                        $("#weight").val(weight);
                                        $("#pro_weight").val(pro_weight);
                                        $("#qty").val(qty);
                                        $("#price").val(price);
                                        $("#totalprice").val(totalprice);
        
                                        $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                    }
                                    
                                    function row_update() {
                                        $("#loading_bg").css("display", "block");
                                        
                                        $("#coo").attr("readonly", true);
                                        $("#hscode").attr("readonly", true);
                                        $("#weight").attr("readonly", true);

                                        var itm_id = $('#cart_item_id').val();
                                        var pid = $("#partno").val();
                                        var partno = $("#partno").val();
                                        var description = $("#description_new").val();
                                        var coo = $("#coo").val();
                                        var hscode = $("#hscode").val();
                                        var weight = $("#weight").val();
                                        var qty = $("#qty").val();
                                        var price = $("#price").val();
                                        var totalprice = $("#totalprice").val();
        
                                        var action = "{{ URL::to('update-clearance-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                itm_id: itm_id,
                                                pid: $("#part_number_new").val(),
                                                partno: $("#partno").val(),
                                                description: $("#description_new").val(),
                                                coo: coo,
                                                hscode: hscode,
                                                weight: weight,
                                                qty: qty,
                                                price: price,
                                                totalprice: totalprice,
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
                                                        
                                                        var qty_total=0; var price_total=0; var totalprice_total=0;
            
                                                            for(var i=0; i<len; i++){

                                                                getSelectedRows +="<tr>\
                                                                    <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].pid+"' /></td>\
                                                                    <td><div style='width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;'>"+dataResult['data'][i].description+"</div><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                    <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' readonly /></td>\
                                                                    <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' readonly /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' readonly /><input type='hidden' id='pro_weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight/dataResult['data'][i].qty+"'></td>\
                                                                    <td class='text-center'>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].price+" <input type='hidden' id='price_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].price+"' /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].totalprice+" <input type='hidden' id='totalprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].totalprice+"' /></td>\
                                                                    <td class='text-right'>\
                                                                        <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                        <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                        <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                    </td>\
                                                                    </tr>";
                                                                    
                                                                    qty_total += Number(dataResult['data'][i].qty);
                                                                    price_total += Number(dataResult['data'][i].price);
                                                                    totalprice_total += Number(dataResult['data'][i].totalprice);
            
                                                            }

                                                            $("#part_number_new").val('');
                                                            $("#description_new").val('');
                                                            $("#coo").val('');
                                                            $("#hscode").val('');
                                                            $("#weight").val('');
                                                            $("#pro_weight").val('');
                                                            $("#qty").val('');
                                                            $("#price").val('');
                                                            $("#totalprice").val('');
                                                            
                                                            $("#qty_total").val(qty_total);
                                                            $("#price_total").val(price_total);
                                                            $("#totalprice_total").val(totalprice_total);
                                                            
                                                            $('#clearance-table tbody').empty();
                                                            $("#clearance-table tbody").append(getSelectedRows);
                                                        }
                                                    else{
                                                        $('#po-table tbody').empty();
                                                    }
                                            }
                                        });
                                        
                                        $("#part_number_new").val();
                                        $("#coo").val();
                                        $("#hscode").val();
                                        $("#weight").val();
                                        $("#pro_weight").val('');
                                        $("#qty").val();
                                        $("#price").val();
                                        $("#totalprice").val();
                                        $('#btn_add_row').css("display",'block');
                                        $('#update_add_row').css("display",'none'); 

                                        $("#loading_bg").css("display", "none");
                                    }
        
                                    function row_delete(id) {
                                        if (confirm("Are you sure you want to delete this item?") == false) {
                                            return false;
                                        }
                                        $("#loading_bg").css("display", "block");
                                        var action = "{{ URL::to('delete-clearance-items-cart') }}";
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
                                                        
                                                        var qty_total=0; var price_total=0; var totalprice_total=0;
            
                                                            for(var i=0; i<len; i++){

                                                                getSelectedRows +="<tr>\
                                                                    <td>"+dataResult['data'][i].partno+" <input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].pid+"' /></td>\
                                                                    <td><div style='width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;'>"+dataResult['data'][i].description+"</div><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                    <td>"+dataResult['data'][i].coo+" <input type='hidden' id='coo_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].coo+"' readonly /></td>\
                                                                    <td>"+dataResult['data'][i].hscode+" <input type='hidden' id='hscode_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].hscode+"' readonly /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].weight+" <input type='hidden' id='weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight+"' readonly /><input type='hidden' id='pro_weight_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].weight/dataResult['data'][i].qty+"'></td>\
                                                                    <td class='text-center'>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].price+" <input type='hidden' id='price_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].price+"' /></td>\
                                                                    <td class='text-right'>"+dataResult['data'][i].totalprice+" <input type='hidden' id='totalprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].totalprice+"' /></td>\
                                                                    <td class='text-right'>\
                                                                        <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                        <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                        <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                    </td>\
                                                                    </tr>";
                                                                    
                                                                    qty_total += Number(dataResult['data'][i].qty);
                                                                    price_total += Number(dataResult['data'][i].price);
                                                                    totalprice_total += Number(dataResult['data'][i].totalprice);
            
                                                            }

                                                            $("#part_number_new").val('');
                                                            $("#description_new").val('');
                                                            $("#coo").val('');
                                                            $("#hscode").val('');
                                                            $("#weight").val('');
                                                            $("#pro_weight").val('');
                                                            $("#qty").val('');
                                                            $("#price").val('');
                                                            $("#totalprice").val('');
                                                            
                                                            $("#qty_total").val(qty_total);
                                                            $("#price_total").val(price_total);
                                                            $("#totalprice_total").val(totalprice_total);
                                                            
                                                            $('#clearance-table tbody').empty();
                                                            $("#clearance-table tbody").append(getSelectedRows);
                                                        }
                                                    else{
                                                        $('#po-table tbody').empty();
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                    }
                                </script>

                                <table class="table table-bordered table-striped" id="clearance-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width:200px;">@lang('Part No')</th>
                                            <th>@lang('Description')</th>
                                            <th style="width:200px;">@lang('COO')</th>
                                            <th style="width:150px;">@lang('H.S Code')</th>
                                            <th style="width:150px;">@lang('Weight')</th>
                                            <th style="width:100px;">@lang('Qty')</th>
                                            <th style="width:150px;">@lang('Unit Price')</th>
                                            <th style="width:150px;">@lang('Amount')</th>
                                            <th style="width:150px;"></th>
                                        </tr>
                                        <tr>
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
                                                <input class="form-control" type="text" id="description_new" name="description" autocomplete="off" readonly="true">
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" id="coo" name="coo" autocomplete="off" min="0" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" id="hscode" name="hscode" autocomplete="off" min="0" readonly>
                                            </td>
                                            <td>
                                                <input type="hidden" id="pro_weight">
                                                <input class="form-control" type="number" id="weight" name="weight" autocomplete="off" min="0" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" id="qty" name="qty" autocomplete="off" min="0" onchange="calc_change_new()">
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" id="price" name="price" autocomplete="off" min="0" onchange="calc_change_new()">
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" id="totalprice" name="totalprice" autocomplete="off" min="0" readonly>
                                            </td>
                                            <td>
                                                <input type="hidden" id="cart_item_id" />
                                                <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                            </td>
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
                                        @php $i=1; @endphp
                                        @if (count($cart)>0)
                                            @foreach ($cart as $dt)

<tr>
    <td>{{ $dt->partno }}<input type="hidden" id="partno_{{ $i }}" value="{{ $dt->partno }}" /><input type="hidden" id="pid_{{ $i }}" value="{{ $dt->pid }}" /></td>
    <td>
        <div style="width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! $dt->description !!}</div>
        <input type="hidden" id="description_{{ $i }}" value="{{ $dt->description }}" /></td>
    <td>{{ $dt->coo }}<input type="hidden" id="coo_{{ $i }}" value="{{ $dt->coo }}" /></td>
    <td>{{ $dt->hscode }}<input type="hidden" id="hscode_{{ $i }}" value="{{ $dt->hscode }}" /></td>
    
    @if($dt->clearance_id > 45)
    <td class="text-right">{{ @App\SysHelper::com_curr_format($dt->weight,3,'.','') }}<input type="hidden" id="weight_{{ $i }}" value="{{ $dt->weight }}" /><input type="hidden" id="pro_weight_{{ $i }}" value="{{ $dt->weight/$dt->qty }}" /></td>
    @else
    <td class="text-right">{{ @App\SysHelper::com_curr_format($dt->weight,3,'.','') }}<input type="hidden" id="weight_{{ $i }}" value="{{ $dt->weight }}" /><input type="hidden" id="pro_weight_{{ $i }}" value="{{ $dt->weight/$dt->qty }}" /></td>
    @endif

    <td class="text-center">{{ $dt->qty }}<input type="hidden" id="qty_{{ $i }}" value="{{ $dt->qty }}" /></td>
    <td class="text-right">{{ $dt->price }}<input type="hidden" id="price_{{ $i }}" value="{{ $dt->price }}" /></td>
    <td class="text-right">{{ $dt->totalprice }}<input type="hidden" id="totalprice_{{ $i }}" value="{{ $dt->totalprice }}" /></td>
    <td class="text-right">
        <input type="hidden" id="cart_item_id_{{ $i }}" value="{{ $dt->id }}" />
        <a onclick="row_edit({{ $i }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
        <a onclick="row_delete({{ $dt->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </td>
</tr>
                                                

                                                
                                        @php $i++; @endphp

                                            @endforeach                                            
                                        @endif
                                        



                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-center"><label id="qty_total">@if(count($cart)>0) {{ $cart->sum('qty') }} @endif</label></th>
                                            <th class="text-right"><label id="price_total">@if(count($cart)>0) {{ $cart->sum('price') }} @endif</label></th>
                                            <th class="text-right"><label id="totalprice_total">@if(count($cart)>0) {{ $cart->sum('totalprice') }} @endif</label></th>
                                        </tr>
                                    </tfoot>
                                </table>

                                

                                
                                <div style="display: none;">
                                    <button type="button" class="btn btn-primary" id="addRowCL"><span
                                            class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                </div>

                                <script>
                                    function fn_addRow(id) {
                                        var rownum = document.getElementById('cl-row-count').value;
                                        if (id == rownum) {
                                            document.getElementById('cl-row-count').value = (Number(rownum) + Number(1));
                                            document.getElementById('addRowCL').click();
                                        }
                                    }

                                    function ddl_part_change(id) {
                                        var selOpt = $('#part_number_' + id + ' :selected').val();
                                        $('#part_number_txt_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                                        var selOpt2 = $('#part_number_txt_' + id + ' :selected').text();

                                        $('#part_number_coo_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                                        var selOpt3 = $('#part_number_coo_' + id + ' :selected').text();

                                        $('#part_number_hscode_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                                        var selOpt4 = $('#part_number_hscode_' + id + ' :selected').text();

                                        $('#part_number_weight_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                                        var selOpt5 = $('#part_number_weight_' + id + ' :selected').text();

                                        $('#partno_' + id + '').val($("#part_number_" + id + " option:selected").text());
                                        $('#description_' + id + '').val(selOpt2);
                                        $('#coo_' + id + '').val(selOpt3);
                                        $('#hscode_' + id + '').val(selOpt4);
                                        $('#weight_' + id + '').val(selOpt5);
                                        $('#hweight_' + id + '').val(selOpt5);

                                        // $('#description_'+id+'').focus();
                                        // $('#coo_'+id+'').focus();
                                        // $('#hscode_'+id+'').focus();
                                    }

                                    function calc_change(id) {
                                        var weight = $('#weight_' + id + '').val();
                                        var hweight = $('#hweight_' + id + '').val();

                                        var qty = $('#qty_' + id + '').val();
                                        var unitprice = $('#price_' + id + '').val();
                                        var value = $('#totalprice_' + id + '').val();

                                        weight = (weight === '') ? '0' : weight;
                                        qty = (qty === '') ? '0' : qty;
                                        unitprice = (unitprice === '') ? '0' : unitprice;
                                        var fin_value = (unitprice * qty);
                                        $('#totalprice_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));

                                        var fin_weight = (hweight * qty);
                                        $('#weight_' + id + '').val(fin_weight.toFixed(3));


                                        // value = (value === '') ? '0' : value;
                                        // discount = (discount === '') ? '0' : discount;
                                        // customcharges = (customcharges === '') ? '0' : customcharges;
                                        // var fin_taxableamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat) + 100)/100);
                                        // $('#taxableamount_'+id+'').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                        // var fin_vatamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat))/100);
                                        // var vatamount = $('#vatamount_'+id+'').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                        calc_total();
                                    }

                                    function calc_total() {
                                        var countrow = document.getElementById('cl-row-count').value;
                                        var t1 = 0,
                                            t2 = 0,
                                            t3 = 0,
                                            t4 = 0;
                                        for (var i = 1; i <= countrow; i++) {
                                            t1 += Number($('#weight_' + i).val());
                                            t2 += Number($('#qty_' + i).val());
                                            t3 += Number($('#price_' + i).val());
                                            t4 += Number($('#totalprice_' + i).val());
                                        }
                                        $('#weight_total').text(t1.toFixed(3));
                                        $('#qty_total').text(t2);
                                        $('#price_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                                        $('#totalprice_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                                    }
                                </script>



                            </div>

                            <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
                                <div class="col-lg-12 text-right">
                                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowEquipment">
                                        <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                </div>
                            </div>








                            <div class="row mt-40">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <span class="ti-check"></span>
                                        @if (isset($edit))
                                            @lang('lang.update')
                                        @else
                                            @lang('lang.save')
                                        @endif
                                        @lang('Clearance')

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
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>




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

        var action = "{{ URL::to('add-clearance-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', 0);
        formData.append('att_date', $('#att_date').val());
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
        $('#att_cust_name').text("Syscom FZE " + $('#doc_no').val());

        var action = "{{ URL::to('view-clearance-attachment') }}";
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
        var action = "{{ URL::to('delete-clearance-attachment') }}";
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
            $("#cfc_cal_amount_" + id).val(amt);
        }
    </script>
@endsection

@section('script')
    <script>
        $(window).ready(function() {
            $("#clearance-create-form").on("keypress", function(event) {
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });


        // $('input').keypress(function(event) {
        //         if (event.keyCode == 13) {
        //             event.preventDefault();
        //         }
        //     });

        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
@endsection

@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try{ ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Stock</h2>
                <span class="page-label">Home - Stock</span>
            </div>
            <div>
                <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>
                <a href="{{ url('item-store/show') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> View Stock</a>
                {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
            </div>
        </div>
        <div class="card shadow mb-4 p-4">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-store-form']) }}

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
                                    name="doc_date" value="{{ @$value }}" required>
                            </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">@lang('Doc') @lang('Number')<span>*</span></label>
                        <input
                            class="form-control {{ $errors->has('doc_number') ? 'is-invalid' : ' ' }}"
                            type="text" id="doc_number" name="doc_number"
                            value="{{ isset($openingstock) ? (!empty(@$openingstock->doc_number) ? @$openingstock->doc_number : old('doc_number')) : @App\SysHelper::get_new_code('sys_item_opening_stock','OP','doc_number')  }}" readonly>
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
                                @php $value = date('Y-m-d'); 
                                if(isset($openingstock) && !empty($openingstock->bill_date) ){ @$value = date('m/d/Y', strtotime(@$openingstock->bill_date)); }
                                @endphp
                                <input class="form-control" id="bill_date" type="date" autocomplete="off" name="bill_date" value="{{ @$value }}" required>
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
                            id="narration" required>
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
                            $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
    
    
                            value = (value === '') ? '0' : value;
                            discount = (discount === '') ? '0' : discount;
                            customcharges = (customcharges === '') ? '0' : customcharges;
                            var fin_taxableamount = ((unitprice * qty) + Number(customcharges) - Number(discount));
                            $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
                            var fin_vatamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat)) / 100);
                            var vatamount = $('#vatamount').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
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

                <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
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
                        @if (count($cart)>0)
                        @foreach ($cart as $dt)
                        <tr>
                            <td>{{ $dt->partno }}</td>
                            <td>{{ $dt->description }}</td>
                            <td>{{ $dt->qty }}</td>
                            <td>{{ $dt->unitprice }}</td>
                            <td>{{ $dt->value }}</td>
                            <td>{{ $dt->refno }}</td>
                            <td><a onclick="row_delete({{ $dt->id }})" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                        @endforeach                            
                        @endif
                    </tbody>
                    <tfoot style="display: none;">
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="sstablefoot"></td>
                            <td class="sstablefoot"><label id="qty_total">0</label></td>
                            <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
                            <td class="sstablefoot"><label id="value_total">0.00</label></td>
                            <td class="sstablefoot"><label id="discount_total">0.00</label></td>
                            <td class="sstablefoot"><label id="customcharges_total">0.00</label></td>
                            <td class="sstablefoot"><label id="taxableamount_total">0.00</label></td>
                            <td class="sstablefoot"><label id="vatamount_total">0.00</label></td>
                        </tr>
                    </tfoot>
                </table>




                <?php if(1==2) { ?>
                <table class="sstable" cellspacing="0" width="100%" id="os-table">
                    <thead>
                        <tr>
                            <th style="width:220px;">@lang('Product Code')</th>
                            <th>@lang('Product Name')</th>
                            <th style="width:100px;">@lang('Qty')</th>
                            <th style="width:150px;">@lang('Unit Price')</th>
                            <th style="width:150px;">@lang('Value')</th>
                            <th style="width:150px;">@lang('Ref No')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rcount = 15; if(isset($stocklist)){ $rcount = count($stocklist); }?>
                        @for ($roid= 1;  $roid <= $rcount ; $roid++)
                        <tr id="rowone{{$roid}}" onclick="fn_addRow({{$roid}})">
                            <td><select class="form-control js-example-basic-single" name="part_number[]" id="part_number_{{$roid}}" onchange="ddl_part_change({{$roid}})">
                                    <option value="none"></option>
                                    @foreach ($items as $value)
                                        <option value="{{ @$value->id }}" <?php if(isset($stocklist)){if($stocklist[$roid-1]->partno==$value->id) { ?> selected <?php }} ?>>{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="part_number_txt[]" id="part_number_txt_{{$roid}}" readonly="true" hidden>
                                    <option value="none"></option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->description }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" type="text" id="description_{{$roid}}" name="description[]" autocomplete="off" readonly="true" value="<?php if(isset($stocklist)){ ?> {{$stocklist[$roid-1]->description}} <?php } ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="qty_{{$roid}}" name="qty[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})" value="<?php if(isset($stocklist)){ ?> {{ $stocklist[$roid-1]->qty_in }} <?php } ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="unitprice_{{$roid}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})" value="<?php if(isset($stocklist)){ ?> {{$stocklist[$roid-1]->price_in}} <?php } ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="value_{{$roid}}" name="value[]" autocomplete="off" min="0" readonly value="<?php if(isset($stocklist)){ ?> {{ $stocklist[$roid-1]->price_in * $stocklist[$roid-1]->qty_in}} <?php } ?>">
                            </td>
                            <td style="display: none;">
                                <input class="form-control" type="text" id="remarks_{{$roid}}" name="remarks[]" autocomplete="off" value="<?php if(isset($stocklist)){ ?> {{$stocklist[$roid-1]->remarks}} <?php } ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="refno_{{$roid}}" name="refno[]" autocomplete="off" value="<?php if(isset($stocklist)){ ?> {{$stocklist[$roid-1]->refno}} <?php } ?>">
                            </td>
                        </tr>
                        @endfor
                        <?php $roid--;?>
                        <input type="hidden" id="os-row-count" value="{{$roid}}">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="sstablefoot"><label id="qty_total">0</label></td>
                            <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
                            <td class="sstablefoot"><label id="value_total">0.00</label></td>
                            <td></td>
                        </tr>
                        </tfoot>
                </table>

                
                <div style="display: none;">
                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowOS"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                </div>

<script>
function fn_addRow(id)
{
var rownum = document.getElementById('os-row-count').value;        
if(id==rownum)
{
document.getElementById('os-row-count').value = (Number(rownum) + Number(1));
document.getElementById('addRowOS_EXE').click();
}
}
function ddl_part_change(id)
{
var selOpt = $('#part_number_'+id+' :selected').val();
$('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');        
var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
$('#description_'+id+'').val(selOpt2);
$('#description_'+id+'').focus();
}

function calc_change(id)
{
//var net_vat = $('#net_vat').val();
var net_vat = $('#tax_'+id+'').val();

var qty = $('#qty_'+id+'').val();
var unitprice = $('#unitprice_'+id+'').val();
var value = $('#value_'+id+'').val();


qty = (qty === '') ? '0' : qty;
unitprice = (unitprice === '') ? '0' : unitprice;
var fin_value = (unitprice * qty);
$('#value_'+id+'').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


/*value = (value === '') ? '0' : value;
var fin_taxableamount = ((unitprice * qty) * ((Number(net_vat) + 100)/100);
$('#taxableamount_'+id+'').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_vatamount = ((unitprice * qty)) * ((Number(net_vat))/100);
var vatamount = $('#vatamount_'+id+'').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));*/

calc_total();
}

function calc_total()
{
var countrow = document.getElementById('os-row-count').value;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
for(var i=1; i<=countrow; i++)
{
t1 += Number($('#qty_'+i).val());
t2 += Number($('#unitprice_'+i).val());
t3 += Number($('#value_'+i).val());
}
$('#qty_total').text(t1);
$('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
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

<?php } ?>

                    <div class="text-right">
                    @if (isset($openingstock))
                    @else
                    <button class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>
                        @if (isset($openingstock)) @lang('lang.update') @else @lang('lang.save') @endif @lang('Opening Stock')
                    </button>
                    @endif
                    </div>

                </div>
                {{ Form::close() }}
        </div>

    </div>
    
    <?php } catch(Exception $e) { ?> {{ $e }} <?php } ?>


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
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if (response['data'] != null) {
                len = response['data'].length;
                }
                if(len > 0){
                    
                    //$('#shipping_name').find('option').not(':first').remove();

                    for(var i=0; i<len; i++){
                        var id = response['data'][i].id;
                        var name = response['data'][i].shipping_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
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

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    </script>
    
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
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
    </script>
@endsection
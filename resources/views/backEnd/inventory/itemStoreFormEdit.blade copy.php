@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store-update', 'method' => 'POST', 'id' => 'sales-invoice-create-form']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="id" value="{{ isset($openingstock) ? $openingstock->id : '' }}">
            <input type="hidden" id="opening_stock_id" value="{{ isset($openingstock) ? $openingstock->id : '' }}">
            <div class="row">
                <div class="col-lg-3 mb-2">
                    <div class="no-gutters input-right-icon">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Doc') @lang('Date')<span>*</span></label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($openingstock) && !empty($openingstock->doc_date) ){ $value = date('Y-m-d', strtotime(@$openingstock->doc_date)); }
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
                            value="{{ $openingstock->doc_number }}">
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
                                if(isset($openingstock) && !empty($openingstock->bill_date) ){ @$value = date('Y-m-d', strtotime(@$openingstock->bill_date)); }
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
                <div class="col-lg-2 mb-2">
                    <div class="input-effect"><br/>
                        <button class="btn btn-warning mt-2" type="submit">Update</button>
                    </div>
                </div>
            </div>
            {{ Form::close() }}

        </div>
        
        <div class="card shadow mb-4 p-4">
            <div class="equipment comon-status row mt-40 d-block">

    {{-- model --}}
<a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add License Key (<label id="ModalLabelHeading" ></label> )</h5>
                    <button class="btn-sm btn-info ml-5" data-toggle="modal" data-target="#ModalExcelQuote" data-backdrop="static" data-keyboard="false">Import</button>
                    <input type="hidden" id="part_number_id" />
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                                <label for="" class="form-label">Qty</label>
                                <input type="number" class="form-control" name="license_qty" id="license_qty" value="1"/>
                        </div>
                        <div class="col-md-6">
                                <label for="" class="form-label">License Key</label>
                                <input type="text" class="form-control" name="license_key" id="license_key" />
                        </div>
                        <div class="col-md-2">
                                <label for="" class="form-label">Exp Date</label>
                                <input type="date" class="form-control" name="exp_date" id="exp_date" />
                        </div>
                        <div class="col-md-2"><br />
                                <button type="button" id="add_license_key_btn" class="btn btn-primary" onclick="return add_license_key()">Add</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
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
    {{-- model --}}

                
                <table class="table table-bordered table-striped" cellspacing="0" width="100%" id="os-table">
                    <thead>
                        <tr>
                            <th style="width:220px;">@lang('Product Code')</th>
                            <th>@lang('Product Name')</th>
                            <th style="width:100px;">@lang('Qty')</th>
                            <th style="width:150px;">@lang('Unit Price')</th>
                            <th style="width:150px;">@lang('Value')</th>
                            <th style="width:150px;">@lang('Ref No')</th>
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store-additem','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-store-form']) }}
                    <tr style="background: #ffe0e0;">
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
                            <input type="hidden" name="currency_ids" value="{{ $openingstock->currency }}" />
                            <input type="hidden" name="doc_number" value="{{ $openingstock->doc_number }}" />
                            <input type="hidden" name="doc_date" value="{{ $openingstock->doc_date }}" />
                            <input type="hidden" name="os_id" value="{{ $openingstock->id }}" />
                            <button class="btn btn-success btn-xs" title="Add">+ Add</button>
                        </td>
                    </tr>
                    {{ Form::close() }}
                    
                    <tr>
                        <td colspan="7" style="background: #f2f2f2;">&nbsp;</td>
                    </tr>

                        <?php $qty=0; $price=0.00; $total=0.00; ?>
                    @if (count($stocklist)>0)
                    @foreach ($stocklist as $dt)
                    <tr>
                        <tr>
                            <td>{{ @$dt->productdet->part_number }}</td>
                            <td><input type="text" class="form-control" id="txt_udescription_{{$dt->id}}" value="{!! nl2br($dt->productdet->description) !!}"  readonly /></td>
                            <td><input type="number" class="form-control" id="txt_uqty_{{$dt->id}}" onchange="calc_change({{$dt->id}})" value="{{$dt->qty_in}}"  onkeypress="set_license_key_po({{$dt->id}})"></td>
                            <td><input type="number" class="form-control" id="txt_uprice_{{$dt->id}}" onchange="calc_change({{$dt->id}})" value="{{ $dt->price_in }}"></td>
                            <td><input type="number" class="form-control" id="txt_utotal_price_{{$dt->id}}" value="{{ ($dt->price_in * $dt->qty_in) }}" readonly></td>
                            <td style="display: none;"><input type="number" class="form-control" id="txt_uremarks_{{$dt->id}}" value="{{ $dt->remarks }}"></td>
                            <td><input type="text" class="form-control" id="txt_urefno_{{$dt->id}}" value="{{ $dt->refno }}"></td>
                            <td align="right"><input type="hidden" id="pid_{{$dt->id}}" value="{{$dt->productdet->id}}">
                                <input type="hidden" id="txt_doc_number_{{$dt->id}}" value="{{$dt->doc_number}}">
                                <input type="hidden" id="product_type_{{$dt->id}}" value="{{@$dt->productdet->product_type}}">
                                <input type="hidden" id="partno_{{$dt->id}}" value="{{@$dt->productdet->part_number}}">
                                <a onclick="upd_tostock({{ $dt->id }})" class="btn-sm btn-warning" title="Update" style="cursor: pointer;">Update</a>
                                <a onclick="del_tostock({{ $dt->id }})" class="btn-sm btn-danger" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            
                            <?php
                                $qty += $dt->qty_in;
                                $price += $dt->price_in;
                                $total += ($dt->price_in * $dt->qty_in);
                            ?>

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
                        
                        */ ?>
                    </tr>
                    @endforeach
                    <footer>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $qty }}</td>
                            <td align="right">{{ @App\SysHelper::com_curr_format($price, 2, '.', '') }}</td>
                            <td align="right">{{ @App\SysHelper::com_curr_format($total, 2, '.', '') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </footer>
                    @endif

                    <script>
                        function calc_change(id){
                            var qty = $('#txt_uqty_'+id).val();
                            var unitprice = $('#txt_uprice_'+id).val();
                            var value = $('#txt_utotal_price_'+id).val();
                            qty = (qty === '') ? '0' : qty;
                            unitprice = (unitprice === '') ? '0' : unitprice;
                            var fin_value = (unitprice * qty);
                            $('#txt_utotal_price_'+id).val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
                        }
                        function ddl_part_change(){
                            var selOpt = $('#part_number :selected').val();
                            $('#part_number_txt option[value='+selOpt+']').attr('selected','selected');
                            var selOpt2 = $('#part_number_txt :selected').text();
                            $('#description').val(selOpt2);
                            $('#description').focus();
                        }
                    </script>

                    <?php /*

                    
                    
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
            var doc_number = $("#txt_doc_number_"+id).val();

            
            
            if (qty == "") {
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
                    doc_number: doc_number,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        alert("Item has been updated successfully");
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
            var doc_number = $("#txt_doc_number_"+id).val();
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('item-store-deleteitem') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    doc_number: doc_number,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        alert("Item has been deleted successfully");
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


    <!-- Modal License Key-->
        <script>

        function excel_license_key(){
            
            $("#loading_bg").css("display", "block");

            if($('#import_file').val()==""){ $('#import_file').focus(); $("#loading_bg").css("display", "none"); return false; }

            var action = "{{ URL::to('add-grn-license-key-cart-excel') }}";
            
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
            formData.append('item_id', $('#part_number_id').val());  // Append other form data
            formData.append('opening_stock_id', $('#opening_stock_id').val());  // Append other form data
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
                        console.log("length",len)
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                if(Number(i+1)>=$('#license_qty').val()){
                                    $('#license_add').prop('disabled', true);
                                } else { $('#license_add').prop('disabled', false); }
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i + 1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                                    if(Number(i + 1) >= $('#license_key').val()){
                                        $('#add_license_key_btn').prop('disabled', true);
                                    }                                  
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

        function set_license_key_po(rowid){
            $('#txt_uqty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = $('#product_type_'+rowid).val();
                    if(pt == 2) {
                        $('#part_number_id').val($('#pid_'+rowid).val());
                        $('#license_qty').val($('#txt_uqty_'+rowid).val());
                        $('#ModalLabelHeading').text($('#partno_'+rowid).val());                        
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

            var action = "{{ URL::to('add-ops-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#part_number_id').val(),
                    license_key : $('#license_key').val(),
                    exp_date : $('#exp_date').val(),
                    license_qty : $('#license_qty').val(),
                    opening_stock_id : $('#opening_stock_id').val(),

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
                                    <td>"+ Number(i + 1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                                    if(Number(i + 1) >= $('#license_key').val()){
                                        $('#add_license_key_btn').prop('disabled', true);
                                    }                       
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
            $('#add_license_key_btn').prop('disabled', false);
            var action = "{{ URL::to('view-ops-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#part_number_id').val(),
                    opening_stock_id : $('#opening_stock_id').val(),
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
                                    <td>"+ Number(i + 1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                                    if(Number(i + 1) >= $('#license_key').val()){
                                        $('#add_license_key_btn').prop('disabled', true);
                                    }
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
            var action = "{{ URL::to('delete-ops-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : id,
                    item_id : $('#part_number_id').val(),
                    opening_stock_id : $('#opening_stock_id').val(),
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
                                    <td>"+ Number(i + 1) +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
                                    <td><a onclick='delete_license_key("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";  
                                    if(Number(i + 1) >= $('#license_key').val()){
                                        $('#add_license_key_btn').prop('disabled', true);
                                    }                                  
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
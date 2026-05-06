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
                    <h2 class="page-heading m-0">Delivery Note</h2>
                    <span class="page-label">Home - Delivery Note</span>
                </div>
                <div>
                    <a href="{{ url('delivery-note-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="{{ url('delivery-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
            
            @if(isset($select_cart))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' =>  'delivery-note-create-form']) }}
            <input type="hidden" name="store_id" value="cart" />
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'delivery-note-create-form']) }}
            <input type="hidden" name="store_id" value="sales" />
            @endif

            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
            <input type="hidden" id="net_vat" name="net_vat">
            <div class="row">
                                <div class="col-lg-4 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Customer') <span>*</span></label>
                                        <select class="form-control js-account-select" name="customer_id" id="customer_id" required>
                                            <option data-display="@lang('Customer')" value="">@lang('Customer')</option>
                                            @foreach ($customer as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($account_id) ? (!empty(@$account_id) ? (@$account_id == @$value->id ? 'selected' : '') : '') : '' }}
                                                    {{ isset($customer_id) ? (!empty(@$customer_id) ? (@$customer_id == @$value->id ? 'selected' : '') : '') : '' }}                                                    >
                                                    {{ @$value->account_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-8 mb-2">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <label class="txtlbl">DLN Number<span>*</span></label>
                                                <input
                                                    class="form-control"
                                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                                    value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_delivery_note','DN', 'doc_number') }}" readonly>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('doc_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('doc_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">DLN Date</label>
                                                        @php
                                                            $value = date('Y-m-d');
                                                        @endphp
                                                        <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                                            name="doc_date" value="{{ @$value }}" required>
                                                    </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Created By') <span>*</span></label>
                                            <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by" value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Currency<span>*</span></label>
                                                <select class="form-control" name="currency" id="currency">
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
                                        <div id="plist" style="width: 100%; height: 250px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                                        <a data-modal-size="modal-md" data-target="#dn_pending_popup_win" id="addDNPending" data-toggle="modal"></a>
                                        <input type="hidden" id="si_id" name="si_id" value="0" >
                                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                                    </div>
                                </div>
                                <div class="col-lg-8 mb-2">
                                    <div class="row">
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('LPO No') <span>*</span> </label>
                                                @php
    $lpoValue = old('lpo_no');

    if (!empty($lpo_no)) {
        $lpoValue = $lpo_no;
    } elseif (!empty($select_cart[0]->reference_no ?? null)) {
        $lpoValue = $select_cart[0]->reference_no;
    }
@endphp
                                                <input class="form-control" type="text" id="lpo_no" name="lpo_no"
                                                value="{{ $lpoValue }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('LPO Date')</label>
                                                @php $value = date('Y-m-d');
                                                if(isset($select_cart) && !empty($select_cart[0]->reference_date) ){ @$value = date('Y-m-d', strtotime(@$select_cart[0]->reference_date)); }
                                                if(isset($lpo_date) && !empty($lpo_date) ){ @$value = date('Y-m-d', strtotime(@$lpo_date)); }
                                                @endphp
                                                <input class="form-control" id="lpo_date" type="date" name="lpo_date" value="{{ $value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Payment Terms') <span>*</span></label>
                                                <select class="form-control" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
                                                    <option data-display="@lang('Payment Terms') *" value="" >@lang('Payment Terms') *</option>
                                                    @foreach($paymentterms as $value)
                                                         <option value="{{@$value->id}}"
                                                            {{isset($select_cart)? !empty(@$select_cart[0]->payment_terms)? @$select_cart[0]->payment_terms == @$value->id ? 'selected':'':'':''}}
                                                            {{ isset($payment_terms) ? (!empty(@$payment_terms) ? (@$payment_terms == @$value->id ? 'selected' : '') : '') : '' }} 
                                                            >{{@$value->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('SIV No') <span>*</span> </label>
                                                @php
                                                $invoice_no='';
                                                $invoice_date = date('Y-m-d');
                                                if(isset($siv_det)){
                                                    $invoice_no = $siv_det->doc_number;
                                                    $invoice_date=$siv_det->doc_date;
                                                }
                                                if(isset($si_no) && !empty($si_no) ){ @$invoice_no = @$si_no; }
                                                if(isset($si_date) && !empty($si_date) ){ @$invoice_date = @$si_date; }
                                                @endphp
                                                <input class="form-control" type="text" id="invoice_no" name="invoice_no"
                                                value="{{ $invoice_no }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('SIV Date')</label>
                                                <input class="form-control" id="invoice_date" type="date" name="invoice_date" value="{{ @$invoice_date }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Salesman')<span>*</span></label>
                                                <select class="form-control" name="sales_man" id="sales_man" required>
                                                    <option value="">-Select-</option>
                                                    @foreach ($staff as $value)
                                                    <option value="{{ @$value->user_id }}"
                                                        <?php
                                                            if (@$sales_man == $value->user_id) {
                                                                ?> selected <?php
                                                            } elseif (isset($deal_det) && $deal_det->owner == $value->user_id) {
                                                                ?> selected <?php
                                                            } /*elseif ($value->user_id == Auth::id()) {
                                                                ?> selected
                                                            }*/
                                                        ?> >{{ @$value->full_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Warehouse') <span>*</span> </label>
                                                <input class="form-control" type="text" id="warehouse" name="warehouse"
                                                value="{{ isset($editData) ? (!empty(@$editData->warehouse) ? @$edit->warehouse : old('warehouse')) : 'Taken from stock' }}" required>
                                            </div>
                                        </div>
                                        <script>
                                            $('#warehouse').val($('#main_company_id  option:selected').text());
                                        </script>

                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Driver') <span></span> </label>
                                                <input class="form-control" type="text" id="driver" name="driver"
                                                value="{{ isset($editData) ? (!empty(@$editData->driver) ? @$edit->driver : old('driver')) : 'Salman' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Vehicle No') <span>*</span> </label>
                                                <input class="form-control" type="text" id="vehicleno" name="vehicleno"
                                                value="{{ isset($editData) ? (!empty(@$editData->vehicleno) ? @$edit->vehicleno : old('vehicleno')) : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                @php
                                                $supplier_name=@$supp_name;
                                                if(isset($sup_name) && !empty(@$sup_name->supplier_name)){
                                                    $supplier_name=$sup_name->supplier_name;
                                                }
                                                @endphp
                                                <label class="dynamicslbl">  @lang('Supplier Name') <span>*</span> </label>
                                                <input class="form-control" type="text" id="supplier_name" name="supplier_name"
                                                value="@if($supplier_name=="") Taken from stock @else {{ $supplier_name }} @endif" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Deal Id') <span>*</span> </label>
                                                <?php 
                                                    $dealid=0;
                                                    if(isset($deal_id)){
                                                        $dealid = @App\SysHelper::get_code_from_dealid($deal_id);
                                                    }
                                                    else{
                                                        if(isset($select_cart)){
                                                            $dealid = @App\SysHelper::get_code_from_dealid($select_cart[0]->deal_id);
                                                        }
                                                    }
                                                ?>
                                                <input class="form-control" type="text" id="deal_id" name="deal_id"
                                                value="{{ $dealid }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Narration') <span>*</span></label>
                                                <input class="form-control" type="text" name="narration" autocomplete="off" value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}" id="narration">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-lg-4 mb-2" style="display: none;">
                                <div class="input-effect">Pending List
                                    <div class="input-effect" id="sectionDnSINumberDiv">
                                        <select class="niceSelect w-100 bb form-control" name="dn_si_numbers" id="dn_si_numbers">
                                            <option data-display="@lang('Select Sales Invoive Number') *" value="0">@lang('Select Sales Invoive Number') *</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <div class="input-effect">
                                    <a class="primary-btn fix-gr-bg text-white" data-modal-size="modal-md" data-target="#dn_list_popup_win" id="getCtrlDelNote" data-toggle="modal"><span class="ti-search"></span> Findas</a>
                                </div>
                            </div>
                        </div>

                        <div id="item_add_row">
                            <table class="table table-bordered table-striped" id="table_id2" style="display: none; width:100%;" cellspacing="0">
                                <tr>
                                    <th colspan="12"><h6 class="primary-color">@lang('Item Details'):</h6></th>
                                </tr>
                                    <tr>
                                        <th style="width:250px;">@lang('Part No')</th>
                                        <th style="width:100px;">@lang('Description')</th>
                                        <th style="width:100px;">@lang('VAT')</th>
                                        <th style="width:100px;">@lang('Qty')</th>
                                        <th style="width:120px;">@lang('Unit Price')</th>
                                        <th style="width:120px;">@lang('Value')</th>
                                        <th style="width:100px;">@lang('Discount')</th>
                                        <th style="width:130px;">@lang('Taxable Amount')</th>
                                        <th style="width:130px;">@lang('VAT Amount')</th>
                                        <th style="width:130px;">@lang('Total Amount')</th>
                                        <th style="width:130px;">@lang('Srl No')</th>
                                        <th style="width:20px;"></th>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" checked hidden>
                                            <select class="form-control js-product-select"  id="part_number_new">
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
                                            <input class="form-control vat" type="number" id="vat" autocomplete="off" min="0" value="5" onchange="add_calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="qty" autocomplete="off" min="0" onchange="add_calc_change_new()" onkeydown="return set_license_key_normal(event, this)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="unitprice" autocomplete="off" min="0" onchange="add_calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="value" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="discount" autocomplete="off" min="0" value="0" onchange="add_calc_change_new()">
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
                                            <a id="btn_update_row" style="display: none;" onclick="return row_update()" class="btn btn-warning"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a id="btn_add_row" onclick="return row_add()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                            </table>

                            @if(isset($from_deal))
                            <script>
                                $('#table_id2').css('display','');
                            </script>
                            @endif
                                

                            <script>
                                function edit_row(id) {
                                    $('#btn_update_row').css("display",'block');
                                    $('#btn_add_row').css("display",'none');

                                    var partno = $('#part_number_'+id).val();
                                    var pid = $('#part_id_'+id).val();

                                    $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                const targetSelect1 = $('#part_number_new');
                                const option = new Option(partno, pid, true, true);
                                targetSelect1.append(option).trigger('change');
                                    //$('#part_number_new').addClass('js-example-basic-single');
                                    $('#description_new').val($('#description_'+id).val());
                                    $('#qty').val($('#qty_'+id).val());
                                    $('#vat').val($('#tax_'+id).val());
                                    $('#unitprice').val($('#unitprice_'+id).val());
                                    $('#value').val($('#value_'+id).val());
                                    $('#discount').val($('#discount_'+id).val());                                            
                                    $('#taxableamount').val($('#taxableamount_'+id).val());
                                    $('#vatamount').val($('#vatamount_'+id).val());
                                    $('#taxableamount').val($('#taxableamount_'+id).val());
                                    $('#totalamount').val($('#totalamount_'+id).val());
                                    $('#serial_no').val($('#srl_'+id).val());
                                    
                                }
                                function delete_row(id,partno) {
                                    if (confirm("Are you sure you want to delete this item?") == false) {
                                        return false;
                                    }
                                    var dln_id = $('#dln_id').val();
                                    $("#loading_bg").css("display", "block");
                                    var action = "{{ URL::to('delivery-note-item-delete') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            id: id,
                                            partno: partno,
                                            dln_id: dln_id,
                                        },
                                        cache: false,
                                        success: function(dataResult) {
                                            alert("Item Deleted Successfully");
                                            location.reload(true);
                                            var dataResult = JSON.parse(dataResult);
                                        }
                                    });
                                    $("#loading_bg").css("display", "none");
                                }
                                function add_calc_change_new(id) {
                                    var net_vat = $('#vat').val();
            
                                    var qty = $('#qty').val();
                                    var unitprice = $('#unitprice').val();
                                    var value = $('#value').val();
                                    var discount = $('#discount').val();
            
                                    qty = (qty === '') ? '0' : qty;
                                    net_vat = (net_vat === '') ? '0' : net_vat;
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
                                function row_update() {
                                    $("#loading_bg").css("display", "block");
                                    var itm_id = $('#cart_item_id').val();
                                    var dln_id = $('#dln_id').val();
                                    var part_number = $('#part_number_new').val();
                                    //var description = $('#description_new').val();
                                    var tax = $("#vat").val();
                                    var qty = $('#qty').val();
                                    var unitprice = $('#unitprice').val();
                                    var value = $('#value').val();
                                    var discount = $('#discount').val();
                                    var taxableamount = $('#taxableamount').val();
                                    var vatamount = $('#vatamount').val();
                                    var serial_no = $('#serial_no').val();
                                    var action = "{{ URL::to('delivery-note-item-update') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            id: itm_id,
                                            dln_id: dln_id,
                                            part_number: part_number,
                                            tax: tax,
                                            qty: qty,
                                            unitprice: unitprice,
                                            value: value,
                                            discount: discount,
                                            taxableamount: taxableamount,
                                            vatamount: vatamount,
                                            serial_no: serial_no,
                                        },
                                        cache: false,
                                        success: function(dataResult) {;
                                            var dataResult = JSON.parse(dataResult)
                                            var len = 0;
                                            var getSelectedRows="";
                                                if(dataResult['data'] != null){
                                                    alert("Item Updated Successfully");
                                                    location.reload(true);
                                                    len = dataResult['data'].length;
                                                }
                                        }
                                    });
                                    $("#loading_bg").css("display", "none");
                                }
                                function row_add() {
                                    $("#loading_bg").css("display", "block");
                                    //var itm_id = $('#cart_item_id').val();
                                    //var dln_id = $('#dln_id').val();
                                    var part_number = $('#part_number_new').val();
                                    var description = $('#description_new').val();
                                    var tax = $("#vat").val();
                                    var qty = $('#qty').val();
                                    var unitprice = $('#unitprice').val();
                                    var value = $('#value').val();
                                    var discount = $('#discount').val();
                                    var taxableamount = $('#taxableamount').val();
                                    var vatamount = $('#vatamount').val();
                                    var serial_no = $('#serial_no').val();
                                    var action = "{{ URL::to('delivery-note-item-add-cart') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            
                                            account_id: $('#customer_id').val(),
                                            refno: $('#lpo_no').val(),
                                            doc_number: $('#doc_number').val(),
                                            doc_date: $('#doc_date').val(),
                                            deal_id: $('#deal_id').val(),

                                            part_number: part_number,
                                            description :description,
                                            tax: tax,
                                            qty: qty,
                                            unitprice: unitprice,
                                            value: value,
                                            discount: discount,
                                            taxableamount: taxableamount,
                                            vatamount: vatamount,
                                            serial_no: serial_no,
                                        },
                                        cache: false,
                                        success: function(dataResult) {
                                            var dataResult = JSON.parse(dataResult)
                                            var len = 0;
                                            var getSelectedRows="";
                                                if(dataResult['data'] != null){
                                                    alert("Item Added Successfully");
                                                    location.reload(true);
                                                    len = dataResult['data'].length;
                                                }
                                        }
                                    });
                                    $("#loading_bg").css("display", "none");
                                }
                            </script>


                        </div>

                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="DelNoteList_table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:200px;">@lang('Part No')</th>
                                        <th style="width:100px;">@lang('Description')</th>
                                        <th style="width:70px;">@lang('Vat')</th>
                                        <th style="width:70px;">@lang('Qty')</th>
                                        <th style="width:80px;">@lang('Unit Price')</th>
                                        <th style="width:70px;">@lang('Value')</th>
                                        <th style="width:70px;">@lang('Discount')</th>
                                        <th style="width:120px;">@lang('Taxable Amount')</th>
                                        <th style="width:100px;">@lang('VAT Amount')</th>
                                        <th style="width:100px;">@lang('Total Amount')</th>
                                        <th style="width:70px;">@lang('Srl No')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $qty_total=0; $value_total=0; $discount_total=0; $taxableamount_total=0; $vatamount_total=0; $total_amount=0; @endphp
                                    @if (count($select_cart)>0)
                                    @php $i=0; @endphp
                                        @foreach ($select_cart as $cart)
                                        @php                                        
                                        $value = @App\SysHelper::com_curr_format($cart->qty * $cart->unitprice, 2, '.', '');
                                        $taxamount=@App\SysHelper::com_curr_format($value - $cart->discount, 2, '.', '');
                                        $vatamount = @App\SysHelper::com_curr_format(($taxamount)*$cart->tax/100, 2, '.', '');
                                        $totalamount = (($cart->qty * $cart->unitprice) - $cart->discount)+(($cart->qty * $cart->unitprice) - $cart->discount)*$cart->tax/100;
                                        @endphp
                                        <tr>
                                            <td><input class="form-control" type="text" id="part_number_{{ $i }}" name="part_number[]" value="{{ $cart->partno }}"+pin.partnumber+"" readonly>
                                            <input type="hidden" id="part_id_{{ $i }}" name="part_id[]" value="{{ $cart->part_number }}" />
                                            <input type="hidden" name="product_type[]" value="{{ $cart->product_type }}" />
                                            <input type="hidden" name="row_id[]" value="{{ $cart->refid }}" /></td>
                                            <td class="jshide"><input class="form-control" type="text" id="description_{{ $i }}" name="description[]" autocomplete="off" min="0" value="{{ $cart->description }}" ></td>
                                            <td><input class="form-control qty rc" type="number" id="tax_{{ $i }}" name="tax[]" autocomplete="off" min="0" value="{{ $cart->tax }}" onchange="calc_change({{ $i }})"></td>
                                            <td><input class="form-control qty rc" type="number" id="qty_{{ $i }}" name="qty[]" autocomplete="off" min="0" value="{{ $cart->qty }}" onchange="calc_change({{ $i }})" onkeydown="return set_license_key_po(event, {{ $i }}, {{ $cart->product_type }}, this)"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="unitprice_{{ $i }}" value="{{ @App\SysHelper::com_curr_format( $cart->unitprice, 2, '.', '')}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="value_{{ $i }}" value="{{ $value }}" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="discount_{{ $i }}" value=" {{ @App\SysHelper::com_curr_format( $cart->discount , 2, '.', '') }}" name="discount[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="taxableamount_{{ $i }}" value="{{ $taxamount }}" name="taxableamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="vatamount_{{ $i }}" value="{{ $vatamount }}" name="vatamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="totalamount_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($totalamount , 2, '.', '') }}" name="totalamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control srl" type="test" id="srl_{{ $i }}" name="srl[]" onclick="srlno_add({{ $i }})" ></td>
                                            </tr>
                                        @php $i++;
                                        $qty_total += $cart->qty;
                                        $value_total += $value;
                                        $discount_total += $cart->discount;
                                        $taxableamount_total += $taxamount;
                                        $vatamount_total += $vatamount;
                                        $total_amount += $totalamount;
                                        @endphp
                                        @endforeach                                        
                                    @endif

                                </tbody>                                
                                <thead>
                                    <input type="hidden" id="dn_row_count">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th><label id="qty_total">{{ $qty_total }}</label></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"><label id="value_total">{{ @App\SysHelper::com_curr_format($value_total,2,',','') }}</label></th>
                                        <th class="text-right"><label id="discount_total">{{ @App\SysHelper::com_curr_format($discount_total,2,',','') }}</label></th>
                                        <th class="text-right"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($taxableamount_total,2,',','') }}</label></th>
                                        <th class="text-right"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($vatamount_total,2,',','') }}</label></th>
                                        <th class="text-right"><label id="total_amount">{{ @App\SysHelper::com_curr_format($total_amount,2,',','') }}</label></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>

                            <div style="display: none;">
                                @if(!isset($view))
                                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowDN"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                @endif
                            </div>

                        </div>
                <!-- Bank Info Details -->
                <!-- end row -->
                <div class="row mt-4">
                    <div class="col-lg-12 text-right">
                        @if(isset($from_deal))
                        <button class="btn btn-info" value="2" name="btnSubmit" id="btnSubmit">
                            <span class="ti-check"></span>
                            Create Delivery Note
                        </button>
                        @else
                        @if(!isset($view))
                        <button class="btn btn-info" value="1" name="btnSubmit" id="btnSubmit">
                            <span class="ti-check"></span>
                            Save & Print Delivery Note
                        </button>

                        <button class="btn btn-primary" name="btnSubmit" id="btnSubmit">
                            <span class="ti-check"></span>
                            @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('Delivery Note')
                        </button>
                        @endif
                        @endif
                    </div>
                </div>    
    </div>
    {{ Form::close() }}
            </div>
        </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">

            
    </div>
    </div>
</section>

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
        
        function set_license_key_normal(e, el) {
            e = e || window.event;
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }
            var part_id = $('#part_number_new').val();
            $('#ModalLabelHeading').text($('#part_number_new option:selected').text() || part_id);
            $('#part_no').val(part_id);
            $('#btn_ModalLicenseKey').click();
            get_license_key(part_id);
            e.preventDefault();
            return false;
        }
        function set_license_key_po(e, rowid, producttype, el) {
            e = e || window.event;
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }
            if (producttype == 2) {
                var part_id = $('#part_id_' + rowid).val();
                $('#ModalLabelHeading').text($('#part_number_' + rowid).val());
                $('#part_no').val(part_id);
                $('#btn_ModalLicenseKey').click();
                get_license_key(part_id);
                e.preventDefault();
                return false;
            }
            return true;
        }
        function get_license_key(part_id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('dn-get-grn-license-key') }}";
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
            var action = "{{ URL::to('dn-update-grn-license-key') }}";
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

    </script>

{{-- popup --}}
<form id="po">
    <div class="modal fade admin-query" id="dn_pending_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Sales Invoice Pending List</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
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
                                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('#') </th>
                                                <th>@lang('Part No')</th>
                                                <th>@lang('Qty')</th>
                                                <th>@lang('Unit Price')</th>
                                                <th>@lang('Value')</th>
                                                <th>@lang('Discount')</th>
                                                <th>@lang('Taxable Amount')</th>
                                                <th>@lang('VAT Amount')</th>
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
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('Close')
                                        </button>
                                        
                                        <button class="btn btn-success" type="button" id="addDNPendingItems">
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

<div>
    
    <script>
        
        function without_po(id) {
            $("#loading_bg").css("display", "block");

            $("#si_id").val(id);
            $("#table_id2").css("display", "");

            $("#loading_bg").css("display", "none");
        }

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

        function popup_si_pending(id){
        $("#loading_bg").css("display", "block");
        $("#hd_pending_dn_id").val(id);
        $("#si_id").val(id);
        $("#addDNPending").click();
        $("#addDNPending").prop("disabled", true);
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer_id", function () {
        var cus_id = $("#customer_id").val();
        get_vat(cus_id);
        get_dn_list(cus_id);
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
                        $("#vat").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");     }
                    }
            });
    }

    function get_dn_list(cus_id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('sales-invoice-pending') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                cus_id: cus_id,
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
                                "<input type='radio' onclick='popup_si_pending(" + id +
                                ")' id='pending_dn_" + i +
                                "' name='pending_dn' value='" + doc_number +
                                "'> <label for='pending_dn_" + i + "'> " + doc_number +
                                "</label><br />";
                            $("#plist").append(innerHtml);
                        }                        
                    }
                    else{
                        $("#plist").empty();
                    }
                    
                    var innerHtml ="<input type='radio' onclick='without_po(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without SIV</label><br />";
                    $("#plist").append(innerHtml);

                    $("#loading_bg").css("display", "none");
            }
        });
    }


    function calc_change(id) {
        var net_vat = $('#tax_' + id + '').val();    
        var qty = $('#qty_' + id + '').val();
        var unitprice = $('#unitprice_' + id + '').val();
        var discount = $('#discount_' + id + '').val();
    
    
        qty = (qty === '') ? '0' : qty;
        unitprice = (unitprice === '') ? '0' : unitprice;
        discount = (discount === '') ? '0' : discount;

        var fin_value = (unitprice * qty);
        $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));    
        
        var fin_taxableamount = ((unitprice * qty) - Number(discount));
        $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
        $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        var fin_totalamount = (fin_taxableamount + fin_vatableamount);
        $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        calc_total();
    }


    function calc_total()
    {
    //var countrow = $('#dn_row_count').val();
    var countrow = $('#DelNoteList_table .rc').length;

    //var countrow = $('#si-table >tbody >tr').length;
    var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
    for(var i=0; i < countrow; i++)
    {
        t1 += Number($('#qty_'+i).val());
        t3 += Number($('#value_'+i).val());
        t4 += Number($('#discount_'+i).val());
        t5 += Number($('#taxableamount_'+i).val());
        t6 += Number($('#vatamount_'+i).val());
        t7 += Number($('#totalamount_'+i).val());
    }
        $('#qty_total').text(t1);
        $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#total_amount').text((t7).toFixed(@json(session('logged_session_data.decimal_point'))));
    }
        
        $(document).ready(function () {
            $("#btnSubmit2").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit2").prop('disabled', true);
            }
        });
        
        $(window).ready(function() {
            $("#delivery-note-create-form").on("keypress", function (event) {           
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
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
    $(document).on('focus', '.js-account-select', function () {
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
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
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
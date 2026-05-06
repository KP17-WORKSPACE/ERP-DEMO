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
                <h2 class="page-heading m-0">Goods Receipt Note (GRN) Edit</h2>
                <span class="page-label">Home - Goods Receipt Note (GRN)</span>
            </div>
            <div>
                <a href="{{ url('goods-receipt-note/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('goods-receipt-note/'.$grn->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-list"></i> View</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="GRN Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-purchase-grn') }}";                
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
                <a href="{{ url('goods-receipt-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update', 'method' => 'POST', 'id' => 'goods-receipt-note-update']) }}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" id="grn_id" name="id" value="{{ isset($grn) ? $grn->id : '' }}">
            <input type="hidden" id="grn_po_id" value="{{ $grn->po_id }}">
            <input type="hidden" id="company_id" value="{{ session('logged_session_data.company_id') }}" />

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <label class="txtlbl">@lang('Vendor') <span>*</span></label>
                    <select class="form-control js-account-select" name="vendors" id="vendors">
                        <option value=""></option>
                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}"
                                {{ isset($grn) ? (!empty($grn->vendors) ? (@$grn->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->account_name }}
                            </option>
                        @endforeach
                    </select>

                    
                    <input type="hidden" id="vendors_old" value="{{ @$grn->vendors }}">
                    <input type="hidden" id="vendors_old_text" value="{{ @$grn->accountname->account_name }}">


                </div>
                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-lg-4">
                        <div class="input-effect">
                            <label class="txtlbl">GRN Number <span>*</span></label>
                            <input
                                class="form-control" type="text" name="doc_number" id="doc_number" value="{{ $grn->doc_number }}">
                                <input type="hidden" name="doc_number_main" value="{{ $grn->doc_number }}" >
                        </div>
                    </div>
                    <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">GRN Date</label>
                                    <input class="form-control" id="grn_date" type="date" autocomplete="off"
                                        name="grn_date" value="{{ @$grn->grn_date }}" style="margin-top: 0px">
                                </div>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <div class="input-effect">
                            <label class="txtlbl">Currency<span>*</span></label>
                            <a class="text-danger float-right" data-toggle="modal" data-target="#ModalChangeCurrancy">Change Currency</a>
                            <select class="form-control js-example-basic-single" name="currency" id="currency">
                                @foreach ($currency as $value)
                                @if($grn->currency == @$value->id)
                                    <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist"
                            style="width: 100%; height: 180px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a data-modal-size="modal-md" data-target="#po_pending_popup_win" id="addPoPending" data-toggle="modal"></a>
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
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
                                    value="{{ isset($grn) ? (!empty(@$grn->lpo_number) ? @$grn->lpo_number : old('lpo_number')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Date') <span>*</span></label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($grn) && !empty($grn->lpo_date)) {
                                        @$value = date('Y-m-d', strtotime(@$grn->lpo_date));
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
                                <label class="txtlbl">@lang('Payment Terms')<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
                                    <option value=""></option>
                                    @foreach ($paymentterms as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($grn) ? (!empty(@$grn->payment_terms) ? (@$grn->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                        value="{{ isset($grn) ? (!empty(@$grn->payment_terms2) ? @$grn->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Number')<span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($grn) ? (!empty(@$grn->bill_number) ? @$grn->bill_number : old('bill_number')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Date')</label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($grn) && !empty($grn->bill_date)) {
                                        @$value = date('Y-m-d', strtotime(@$grn->bill_date));
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
                                    value="{{ isset($grn) ? (!empty(@$grn->awbno) ? @$grn->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('BOE No.') <span></span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                    type="text" name="boeno" autocomplete="off"
                                    value="{{ isset($grn) ? (!empty(@$grn->boeno) ? @$grn->boeno : old('boeno')) : old('boeno') }}"
                                    id="boeno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Reference') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="reference" autocomplete="off"
                                    value="{{ isset($grn) ? (!empty(@$grn->reference) ? @$grn->reference : old('reference')) : old('reference') }}"
                                    id="reference">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">@lang('Salesman Name')*</label>
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if(@$grn->sales_person==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                
                                <select
                                class="form-control" name="createdby" id="createdby" >
                                <option value=""></option>
                                @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}" @if ($value->user_id==$grn->created_by) selected @endif>{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
        
                                {{--  <input class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{ isset($grn) ? (!empty(@$grn->number) ? @$grn->number : old('createdby')) : Auth::user()->full_name }}"
                                    readonly>  --}}
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off"
                                    value="{{ isset($grn) ? (!empty(@$grn->narration) ? @$grn->narration : old('narration')) : old('narration') }}"
                                    id="narration">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Warehouse') <span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($grn) ? (!empty(@$grn->warehouse) ? @$grn->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Deal Id') <span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off"
                                    value="{{ isset($grn) ? (!empty(@$grn->deal_id) ? @App\SysHelper::get_code_from_dealid_list($grn->deal_id) : old('deal_id')) : old('deal_id') }}"
                                    id="deal_id">
                            </div>
                        </div>
                    </div>

                </div>



                





        </div>
        <div class="equipment comon-status row mt-4 d-block">


            <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th style="width:150px;">@lang('Part No')</th>
                        <th>@lang('Description')</th>
                        <th style="width:100px;">@lang('Tax')</th>
                        <th style="width:100px;">@lang('Qty')</th>
                        <th style="width:120px;">@lang('Unit Price')</th>
                        <th style="width:120px;">@lang('Value')</th>
                        <th style="width:100px;">@lang('Discount')</th>
                        <th style="width:100px;">@lang('Freight')</th>
                        <th style="width:100px;">@lang('Customs')</th>
                        <th style="width:130px;">@lang('Taxable Amount')</th>
                        <th style="width:130px;">@lang('VAT Amount')</th>
                        <th style="width:130px;">@lang('Total')</th>
                        <th style="width:130px;">@lang('Serial No')</th>
                        <th style="width:20px;"></th>
                    </tr>
                    <tr>                        
                        <td>
                            <input class="form-control2" type="number" id="sort_id" />
                        </td>
                        <td><input type="checkbox" checked hidden>
                            <select class="form-control js-product-select" id="part_number_new">
                                <option value="none"></option>
                                {{-- @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                @endforeach --}}
                            </select>
                            <input class="form-control" type="hidden" id="part_no" autocomplete="off" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="description_new" autocomplete="off" readonly="true">
                            <input class="form-control" type="text" id="hscode_txt" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="tax" value="{{ $grn_items[0]->tax }}" autocomplete="off" min="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="qty" autocomplete="off" min="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="unitprice" step="Any" autocomplete="off" min="0" onchange="calc_change_new()">
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
                            <input class="form-control" type="number" id="value" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="discount" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="fright" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="customcharges" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
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
                            <input class="form-control" type="text" id="srl_no" autocomplete="off" onclick="srlno_add()">
                        </td>
                        <td><input type="hidden" id="item_id" />
                            <a onclick="return add_rows()" id="btn_add_row" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            <a onclick="return update_rows()" style="display: none;" id="update_add_row" class="btn btn-warning"><i class="fa fa-plus" aria-hidden="true"></i></a>
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

                        if($("#sort_id").val()==""){$("#sort_id").focus(); return false;}
                        if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                        if($("#qty").val()==""){$("#qty").focus(); return false;}
                        if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                        if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                        if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}

                        var company_id = $('#company_id').val();
                        var hscode = $('#hscode_txt').val();
                        if(company_id != 2){ hscode=0; }

                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('add-grn-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                part_number: $('#part_number_new option:selected').text(),
                                part_no: $("#part_number_new").val(),
                                hscode: hscode,
                                tax: $("#tax").val(),
                                qty: $("#qty").val(),
                                unitprice: $("#unitprice").val(),
                                value: $("#value").val(),
                                discount: $("#discount").val(),
                                fright: $("#fright").val(),
                                customcharges: $("#customcharges").val(),
                                taxableamount: $("#taxableamount").val(),
                                vatamount: $("#vatamount").val(),
                                grn_id: $("#grn_id").val(),
                                po_id: $("#grn_po_id").val(),
                                srl_no: $("#srl_no").val(),
                                vendors: $("#vendors").val(),
                                vendors_old: $("#vendors_old").val(),
                                sort_id: $("#sort_id").val(),
                            },
                            cache: false,
                            success: function(dataResult) {
                                location.reload();
                                var dataResult = JSON.parse(dataResult);
                                var len = 0;
                                var getSelectedRows="";
                                    if(dataResult['data'] != null){
                                        len = dataResult['data'].length;
                                    }
                                    if(len > 0){

                                        var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;

                                        for(var i=0; i<len; i++){


                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>";
                                                
                                                if(company_id==2){
                                                    getSelectedRows +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode[]' id='hscode_" + (i+1) + "' value='"+dataResult['data'][i].hscode+"' readonly></td>";
                                                } else{
                                                    getSelectedRows +=  "<input type='hidden' id='hscode_" + i + "' name='hscode[]' value='0' readonly></td>";
                                                }
                                                getSelectedRows += "<td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
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
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

                                        $('#po-table tbody').empty();
                                        $("#po-table tbody").append(getSelectedRows);
                                        row_clear();
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
                        $('#fright').val($('#fright_'+id).val());
                        $('#customcharges').val($('#customcharges_'+id).val());
                        $('#taxableamount').val($('#taxableamount_'+id).val());
                        $('#vatamount').val($('#vatamount_'+id).val());
                        $('#taxableamount').val($('#taxableamount_'+id).val());
                        //$('#totalamount').val($('#totalamount_'+id).val());
                        $('#srl_no').val($('#srl_'+id).val());
                        $('#sort_id').val($('#sort_id_'+id).val());
                        calc_change_new();
                        

                    }

                    function row_clear() {
                        $("#part_number_new").val('');
                        $("#select2-part_number_new-container").html('');
                        $('#description_new').val('');
                        $('#tax').val('');
                        $('#qty').val('');
                        $('#unitprice').val('');
                        $('#value').val('0');
                        $('#discount').val('0');
                        $('#fright').val('0');
                        $('#customcharges').val('0');
                        $('#taxableamount').val('');
                        $('#vatamount').val('');
                        $('#taxableamount').val('');
                        $('#totalamount').val('');
                        $('#srl_no').val('');
                        $('#sort_id').val('');
                        
                        $('#btn_add_row').css("display",'block');
                        $('#update_add_row').css("display",'none');
                    }
                    
                    function update_rows() {
                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('update-grn-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                id : $("#item_id").val(),
                                part_number: $('#part_number_new option:selected').text(),
                                part_no: $("#part_number_new").val(),
                                tax: $("#tax").val(),
                                qty: $("#qty").val(),
                                unitprice: $("#unitprice").val(),
                                value: $("#value").val(),
                                discount: $("#discount").val(),
                                fright: $("#fright").val(),
                                customcharges: $("#customcharges").val(),
                                taxableamount: $("#taxableamount").val(),
                                vatamount: $("#vatamount").val(),
                                grn_id: $("#grn_id").val(),
                                po_id: $("#grn_po_id").val(),
                                srl_no: $("#srl_no").val(),
                                sort_id: $("#sort_id").val(),
                            },
                            cache: false,
                            success: function(dataResult) {
                                location.reload();
                                var dataResult = JSON.parse(dataResult);
                                var len = 0;
                                var getSelectedRows="";
                                    if(dataResult['data'] != null){
                                        len = dataResult['data'].length;
                                    }
                                    if(len > 0){
                                        
                                    var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;
                                        
                                        for(var i=0; i<len; i++){

                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>";

                                                
                                                if(company_id==2){
                                                    getSelectedRows +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode[]' id='hscode_" + (i+1) + "' value='"+dataResult['data'][i].hscode+"' readonly></td>";
                                                } else{
                                                    getSelectedRows +=  "<input type='hidden' id='hscode_" + i + "' name='hscode[]' value='0' readonly></td>";
                                                }

                                                getSelectedRows +="<td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
                                                
                                                taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                vatamount_total += Number(dataResult['data'][i].vatamount);
                                                
                                                taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                amount_total += Number(taxableamount_total1 + vatamount_total1);
                                        }

                                        $("#part_number_new").val("none");
                                        $("#description_new").val("");
                                        //$("#tax").val("");
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

                                        $('#po-table tbody').empty();
                                        $("#po-table tbody").append(getSelectedRows);
                                        row_clear();
                                    }
                                    else{
                                        
                                    }
                            }
                        });
                        $("#loading_bg").css("display", "none");
                    }

                    function delete_row(id) {
                        if (confirm("Are you sure you want to delete this item?") == false) {
                            return false;
                        }
                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('delete-grn-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id,
                            },
                            cache: false,
                            success: function(dataResult) {
                                location.reload();
                                var dataResult = JSON.parse(dataResult);
                                var len = 0;
                                var getSelectedRows="";
                                    if(dataResult['data'] != null){
                                        len = dataResult['data'].length;
                                    }
                                    if(len > 0){

                                        var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var amount_total=0;

                                        for(var i=0; i<len; i++){


                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>";

                                                
                                                if(company_id==2){
                                                    getSelectedRows +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode[]' id='hscode_" + (i+1) + "' value='"+dataResult['data'][i].hscode+"' readonly></td>";
                                                } else{
                                                    getSelectedRows +=  "<input type='hidden' id='hscode_" + i + "' name='hscode[]' value='0' readonly></td>";
                                                }

                                                getSelectedRows +="<td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].po_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
                                                
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
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

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
            
            <table class="table table-bordered table-striped" id="edit-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>@lang('#') </th>
                        <th></th>
                        <th style="width: 150px;">@lang('Part&nbsp;Number')</th>
                        @if (session('logged_session_data.company_id')==2)
                        <th>@lang('HS Code')</th>
                        @endif
                        {{-- <th>@lang('PO Qty')</th> --}}
                        <th>@lang('Tax')</th>
                        <th>@lang('Qty')</th>
                        {{-- <th>@lang('Executed Qty')</th>
                        <th>@lang('Balance Qty')</th> --}}
                        <th class="text-right">@lang('Unitprice')</th>
                        <th class="text-right">@lang('Value')</th>
                        <th class="text-right" style="width:70px;">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a>
                        </th>
                        <th class="text-right" style="width:70px;">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalFreight">Freight</a>
                        </th>
                        <th class="text-right" style="width:70px;">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalCustom">Custom Charges</a>
                        </th>
                        <th>@lang('Taxable Amount')</th>
                        <th>@lang('Vat Amount')</th>
                        <th>@lang('Total')</th>
                        <th>@lang('Serial No')</th>
                        <th style="width: 75px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; $po_qty=0; $qty=0; $executed_qty=0; $balance_qty=0; $unitprice=0; $value=0; $discount=0; $fright=0; $custom=0; $taxableamount = 0; $vatamount = 0; $total = 0; $grn_qty=0; @endphp
                    @if (count($grn_items)>0)
                        @foreach ($grn_items as $items)
                        <tr>
                            <td>{{ $items->sort_id }}<input type="hidden" id="sort_id_{{ $i }}" name='sortid[]' name="sort_id[]" value="{{ $items->sort_id }}" /></td>
                            <td>
                                <input type="hidden" id="item_{{ $i }}" value="{{ $items->id }}" />
                                <input type="hidden" id="partno_{{ $i }}" name="part_number[]" value="{{ $items->part_number }}" />
                                <input type="hidden" id="pid_{{ $i }}" name="part_id[]" value="{{ $items->part_no }}" />
                                <input type="hidden" id="description_{{ $i }}" value="{{ @$items->sm_description }}" />
                                <input type="hidden" id="product_type_{{ $i }}" value="{{ $items->product_type }}" />
                                <input type="hidden" name="item_po_id[]" value="{{ $items->po_id }}" />
                            </td>
                            <td>{{ $items->part_number }}</td>
                            
                        @if (session('logged_session_data.company_id')==2)
                        <td>{{ $items->hscode }}</td>
                        @endif

                            <td style="display: none;"><input type="text" class="form-control" id="po_qty_{{ $i }}" name="po_qty[]" value="{{ $items->po_qty }}" /></td>
                            <td><input type="text" class="form-control" id="tax_{{ $i }}" name="tax[]" value="{{ $items->tax ?? 0 }}" /></td>
                            <td><input type="text" class="form-control" id="qty_{{ $i }}" name="qty[]" value="{{ $items->qty }}"  onkeypress="set_license_key_po({{ $i }})" /></td>
                            <td style="display: none;"><input type="text" class="form-control" id="executed_qty_{{ $i }}" name="grn_qty[]" value="{{ $items->grn_qty }}" /></td>
                            <td style="display: none;"><input type="text" class="form-control" id="balance_qty_{{ $i }}" name="balance_qty[]" value="{{ abs($items->po_qty - $items->grn_qty) }}" readonly /></td>
                            <td><input type="text" class="form-control text-right" step="Any" id="unitprice_{{ $i }}" name="unitprice[]" value="{{ $items->unitprice }}" /></td>
                            <td><input type="text" class="form-control text-right" id="value_{{ $i }}" name="value[]" value="{{ $items->value }}" /></td>
                            <td><input type="text" class="form-control text-right" id="discount_{{ $i }}" name="discount[]" value="{{ $items->discount }}" /></td>
                            <td><input type="text" class="form-control text-right" id="fright_{{ $i }}" name="fright[]" value="{{ $items->fright }}" /></td>
                            <td><input type="text" class="form-control text-right" id="customcharges_{{ $i }}" name="customcharges[]" value="{{ $items->customcharges }}" /></td>
                            
                            <td><input type="text" class="form-control text-right" id="taxamount_{{ $i }}" name="taxamount[]" value="{{ $items->taxableamount }}" /></td>
                            <td><input type="text" class="form-control text-right" id="vatamount_{{ $i }}" name="vatamount[]" value="{{ $items->vatamount }}" /></td>
                            <td><input type="text" class="form-control text-right" id="totalamount_{{ $i }}" name="totalamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', '') }}" /></td>
                            <td>

                                <?php
                                    $srno = $edit_list_srl->where('part_no',$items->part_no)->where('item_id',$items->id)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);

                                    if($string!=""){
                                        $string=str_replace('"', '',$string);
                                    }
                                ?>
                                            
                                            <input type="text" class="form-control" id="srl_{{ $i }}" name="srl[]" value="{{ $string }}" /></td>
                            <td>
                                <input type="hidden" id="cart_item_id_{{ $i }}" value="{{ $items->id }}" />
                                <a onclick="return row_edit({{ $i }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a onclick="return delete_row({{ $items->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @php
                        $po_qty += $items->po_qty;
                        $qty += $items->qty;
                        $grn_qty += $items->grn_qty;
                        $balance_qty += abs($items->po_qty - $items->grn_qty);
                        $unitprice += $items->unitprice;
                        $value += $items->value;
                        $discount += $items->discount;
                        $fright += $items->fright;
                        $custom += $items->customcharges;
                        $taxableamount += $items->taxableamount;
                        $vatamount += $items->vatamount;
                        $total += $items->taxableamount+$items->vatamount;
                        $i++;
                        @endphp
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr style="background: #f2f2f2;">
                        <th></th><th></th>
                        <th></th>
                        <th class="sstablefoot"></th>
                        @if (session('logged_session_data.company_id')==2)
                        <th></th>
                        @endif
                        <th class="sstablefoot">{{ $qty }}</th>
                        {{-- <th class="sstablefoot">{{ $grn_qty }}</th>
                        <th class="sstablefoot">{{ $balance_qty }}</th> --}}
                        <th class="text-right"></th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($value, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($fright, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($custom, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($taxableamount, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($vatamount, 2, '.', ',') }}</th>
                        <th class="text-right">{{ @App\SysHelper::com_curr_format($total, 2, '.', ',') }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                {{--<tr>
                        <td></td>
                        <td class="sstablefoot"><label id="qty_total">0</label></td>
                        <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
                        <td class="sstablefoot"><label id="value_total">0.00</label></td>
                        <td class="sstablefoot"><label id="discount_total">0.00</label></td>
                        <td class="sstablefoot"><label id="taxableamount_total">0.00</label></td>
                        <td class="sstablefoot"><label id="vatamount_total">0.00</label></td>
                    </tr>--}}
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
                <button type="submit" class="btn btn-warning" id="btnSubmit">
                    <span class="ti-check"></span>
                    @if (isset($grn))
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


    <!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add License Key (<label id="ModalLabelHeading" ></label> )</h5>
                    <button class="btn-sm btn-info ml-5" data-toggle="modal" data-target="#ModalImport" data-backdrop="static" data-keyboard="false">Import</button>
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
                                <button type="button" class="btn btn-primary" onclick="return add_license_key()">Add</button>
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


    <div class="modal fade" id="ModalImport" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import License Key</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="header_image_frm">
                    <div class="row">
                        <div class="col-md-10">
                                <label for="" class="form-label">Choose File</label>
                                <input type="file" class="form-control" name="import_file" id="import_file" />
                        </div>
                        <div class="col-md-2"><br />
                                <button type="button" class="btn btn-primary" onclick="return import_excel()">Add</button>
                        </div>
                    </div>
                </form>
                </div>
                <div class="modal-footer">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>


    <script>

        function import_excel(){            
            var action = "{{ URL::to('import-grn-license-key') }}";

            let formData = new FormData($('#header_image_frm')[0]);
        let file = $('input[type=file]')[0].files[0];
        formData.append('file', file, file.name);

        alert(formData);


            $.ajax({
                url: action,
                type: "POST",
                enctype: "multipart/form-data",
                data: formData,
                cache: false,
                success: function(dataResult) {
                    alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i)+1 +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
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

        function set_license_key_po(rowid){
            $('#qty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = $('#product_type_'+rowid).val();
                    if(pt == 2) {
                        $('#part_number_id').val($('#pid_'+rowid).val());
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

            var action = "{{ URL::to('add-grn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#part_number_id').val(),
                    license_key : $('#license_key').val(),
                    exp_date : $('#exp_date').val(),
                    license_qty : $('#license_qty').val(),
                    grn_id : $('#grn_id').val(),

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
                                    <td>"+ Number(i)+1 +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
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
            var action = "{{ URL::to('view-grn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : $('#part_number_id').val(),
                    grn_id : $('#grn_id').val(),
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
                                    <td>"+ Number(i)+1 +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
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
            var action = "{{ URL::to('delete-grn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : id,
                    grn_id : $('#grn_id').val(),
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
                                    <td>"+ Number(i)+1 +"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
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
                        <input type="hidden" id="hd_pending_po_id" />
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
                                                    <th>@lang('Part Number')</th>
                                                    {{-- <th>@lang('PO Qty')</th> --}}
                                                    <th>@lang('Qty')</th>
                                                    {{-- <th>@lang('Executed Qty')</th>
                                                    <th>@lang('Balance Qty')</th> --}}
                                                    <th>@lang('Unitprice')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($grn->currency == $value->id)
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
                    <input type="hidden" name="cur_grn_id" value="{{ @$grn->id }}"/>
                    <input type="hidden" name="cur_grn_doc_no" value="{{ @$grn->doc_number }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->
    
    
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="discount_amount_grn_id" value="{{ @$grn->id }}"/>                    
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="freight_amount_grn_id" value="{{ @$grn->id }}"/>                    
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
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                <input type="hidden" name="custom_amount_grn_id" value="{{ @$grn->id }}"/>                    
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
                                <textarea class="dynamicstxt primary-input form-control" id="srlno_textarea" name="srlno_textarea" rows="5"></textarea>
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
    function srlno_add(){
        var hdtxt = $("#part_number_new option:selected").text();
        var srl = $("#srl_no").val();        
        $("#srlno_textarea").val(srl);
        $("#div_serialno_title").html(hdtxt);
        document.getElementById('add_srlno_popup').click();
    }
    function srlno_add_item(){
        var srltxt = $("#srlno_textarea").val();
        $("#srl_no").val(srltxt);
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
            $("#loading_bg").css("display", "block");
            $("#hd_pending_po_id").val(id);
            $("#po_id").val(id);
            document.getElementById('addPoPending').click();

            if(id != 0){
                $("#table_id2").css("display", "none");    
            }

            $("#loading_bg").css("display", "none");
        }
        function without_po(id) {
            $("#loading_bg").css("display", "block");

            $("#po_id").val(id);
            $("#table_id2").css("display", "block");

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
                    item_id: $('#item_'+id).val(),
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
                    item_id:$('#item_'+id).val(),
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
            //var net_vat = $('#net_vat').val();
            //var net_vat = $('#vat_percentage').val();

            var qty = $('#qty_' + id + '').val();
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

            var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(5)) / 100);
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
            for (var i = 1; i <= countrow; i++) {
                t1 += Number($('#qty_' + i).val());
                t2 += Number($('#unitprice_' + i).val());
                t3 += Number($('#value_' + i).val());
                t4 += Number($('#discount_' + i).val());
                t5 += Number($('#taxamount_' + i).val());
                t6 += Number($('#vatamount_' + i).val());
            }
            $('#qty_total').text(t1);
            $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
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

            var result = confirm("Are you sure you want to remove the selected items?");
            if (result) {
                remove_items();
            } else {
                var selectedValue = $("#vendors_old").val();
                var selectedText = $("#vendors_old_text").val();
                $("#vendors").val(selectedValue);
                $("#select2-vendors-container").text(selectedText);
                return false;
            }

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
                            $('#tax').val(dataResult['data'].vat_percentage);
                            $("select[id=tax] option:first").text(dataResult['data'].vat_percentage +'%');
                            $("select[id=tax] option:first").val(dataResult['data'].vat_percentage);
                            $("#loading_bg").css("display", "none");     }
                        }
                });
        }
        function remove_items() {            
            $("#edit-table").remove();
            /*var action = "{{ URL::to('remove-grn-items') }}";
                $.ajax({
                    url: action,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $('#grn_id').val(),
                    },
                    cache: false,
                    success: function(dataResult) {
                        alert(dataResult);

                    }
                });*/
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
                            for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var doc_number = dataResult['data'][i].doc_number;
                                    var option = "<option value='" + id + "'>" + doc_number +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_po_pending(" + id +
                                        ")' id='pending_po_" + (i+1) +
                                        "' name='pending_po' value='" + doc_number +
                                        "'> <label for='pending_po_" + (i+1) + "'> " + doc_number +
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

        
    $('#goods-receipt-note-update').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
    });

    </script>
@endsection

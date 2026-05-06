    <?php try { ?>

        

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-update', 'method' => 'POST', 'id' => 'stl-update']) }}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="stl_id" id="stl_id" value="{{ $edit->id }}">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ $edit->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a class="btn btn-light" href="{{url('stl-add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>            
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
             {{-- <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i> Save & Download</button></li>
                </ul>
            </div> --}}
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-2 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Doc Number<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                    value="{{ $edit->doc_number }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Doc Date</label>
                                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off" name="doc_date" value="{{ @App\SysHelper::normalizeToDmy($edit->doc_date) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <label class="form-label">@lang('Bank') <span>*</span></label>
                            <select class="form-control js-example-basic-single" name="bank" id="bank" onchange="set_rate()" required>
                                <option value=""></option>
                                @foreach ($bank as $value)
                                <option value="{{ @$value->id }}" @if($edit->bank == $value->id) selected @endif>
                                    {{ @$value->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            function set_rate(){

                                var bank = $('#bank').val();
                                if(bank == 7996){ //RAK BANK
                                    $('#exchange_rate').val('3.674');
                                } else {
                                    $('#exchange_rate').val('3.675');
                                }
                                $('#amount_usd').val('');
                                $('#amount_aed').val('');
                            }
                            function set_amount_usd(){
                                var rate = $('#exchange_rate').val();
                                var usd = $('#amount_usd').val();
                                var aed = $('#amount_aed').val();
                                if(usd != "" || usd != "0" || usd != "0.00"){
                                    //$('#amount_aed').val(usd*rate);
                                    $('#amount_aed').val(formatAmount(usd * rate));
                                }
                                $('#amount_usd').val(formatAmount(usd));
                            }
                            function set_amount_aed(){
                                var rate = $('#exchange_rate').val();
                                var usd = $('#amount_usd').val();
                                var aed = $('#amount_aed').val();
                                if(aed != "" || aed != "0" || aed != "0.00"){
                                    //$('#amount_usd').val(aed/rate);
                                    $('#amount_usd').val(formatAmount(aed / rate));
                                }
                                $('#amount_aed').val(formatAmount(aed));
                            }
                        </script>

                        
                        <div class="col-1-5 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Ex Rate</label>
                                        <input class="form-control" id="exchange_rate" type="number" step="Any" autocomplete="off" name="exchange_rate" value="{{ $edit->exchange_rate }}" onchange="set_amount_usd()" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-1-5 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency_m" id="currency_m">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            @if($edit->currency_m == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <script>
                            function get_currency_code(){
                                $('#amt_txt').text('Amount in '+$('#currency :selected').text());
                            }
                        </script>
                        <div class="col-1-5 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">in USD</label>
                                        <input class="form-control" id="amount_usd" type="text" autocomplete="off" name="amount_usd" value="{{ $edit->amount_usd }}" onchange="set_amount_usd()" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency" id="currency" onchange="get_currency_code()">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            @if($edit->currency == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label" id="amt_txt">Amount in {{ $currency_code }}</label>
                                        <input class="form-control" id="amount_aed" type="text" autocomplete="off" name="amount_aed" value="{{ $edit->amount_aed }}" onchange="set_amount_aed()" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Syscom Representative</label>
                                        <input class="form-control" id="owner_name" type="text" autocomplete="off" name="owner_name" value="{{ $edit->owner_name }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Bank Representative</label>
                                        <input class="form-control" id="bank_representative" type="text" autocomplete="off" name="bank_representative" value="{{ $edit->bank_representative }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <label class="form-label">@lang('Vendor Name') <span>*</span></label>
                            <select class="form-control js-example-basic-single" name="vendor" id="vendor" required>
                                <option value=""></option>
                                @foreach ($vendor as $value)
                                <option value="{{ @$value->id }}" @if($value->id==$edit->vendor) selected @endif>
                                    {{ @$value->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <label class="form-label">@lang('Payment Type') <span>*</span></label>
                            <select class="form-control" name="payment_type" id="payment_type" required onchange="if_partial()">
                                <option value=""></option>
                                <option value="Partial" @if("Partial"==$edit->payment_type) selected @endif>Partial</option>
                                <option value="Full" @if("Full"==$edit->payment_type) selected @endif>Full</option>
                            </select>
                        </div>
                        <script>
                            function if_partial(){
                                if($('#payment_type').val()=="Partial"){
                                    $('#div_partial_remarks').css('display',''); $('#partial_remarks').prop('required',true);
                                    var usd = $('#amount_usd').val();
                                    var vend = $('#vendor :selected').text();
                                    var curr = $('#currency :selected').text();
                                    var rem_text = "Special Instruction:\n\
•	Mudaraba Financing is required for "+curr.trim()+" "+usd.trim()+"\n\
•	Balance amount we will pay from our own sources.\n\
TT Value to "+vend.trim()+" to be "+curr.trim()+" "+usd.trim()+" as per below details";
                                    $('#partial_remarks').val(rem_text);
                                    
                                } else {
                                     $('#div_partial_remarks').css('display','none'); $('#partial_remarks').prop('required',false); 
                                    var rem_text="";
                                    $('#partial_remarks').val(rem_text);
                                    }
                            }
                        </script>
                        <div class="col-lg-2 mb-2">
                            <label class="form-label">@lang('PI / PI / PO') <span>*</span></label>
                            <select class="form-control" name="pi_no" id="pi_no" onchange="get_pending_list()" required>
                                <option value=""></option>
                                <option value="1" @if("1"==$edit->pi_no) selected @endif>Purchase Invoice</option>
                                <option value="2" @if("2"==$edit->pi_no) selected @endif>Proforma Invoice</option>
                                <option value="3" @if("3"==$edit->pi_no) selected @endif>Purchase Order</option>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Submission Date</label>
                                        <input class="form-control date-picker" id="submition_date" type="text" autocomplete="off" name="submition_date" value="{{ @App\SysHelper::normalizeToDmy($edit->submition_date) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2" id="div_partial_remarks" style="display: none;">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Remarks (Partial Payment)</label>
                                        <textarea class="form-control" id="partial_remarks" rows="5" autocomplete="off" name="partial_remarks">{{ $edit->partial_remarks }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Narration</label>
                                        <input class="form-control" id="narration" type="text" autocomplete="off" name="narration" value="{{ $edit->narration }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <label class="form-label">@lang('With / Without Amount') <span>*</span></label>
                            <select class="form-control" name="with_amount" id="with_amount">
                                <option value="0" @if("0"==$edit->with_amount) selected @endif>Without Amount</option>
                                <option value="1" @if("1"==$edit->with_amount) selected @endif>With Amount</option>
                            </select>
                        </div>


                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Pending list</label>
                        <div id="plist" style="width: 100%; height: 250px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#pi_pending_popup_win" id="addPIPendingSTL" data-toggle="modal"></a>
                        <input type="hidden" id="pi_id" name="pi_id">
                    </div>
                </div>
                                    </div>
                                </div>
                            </div>


                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
        <div class="col-lg-12"><br /><b>List of <label id="list_name"></label></b><hr /></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
<div class="col-lg-12">
            <div class="table-striped">
                
                <table  id='table_id_stl_' class='table table-hover form-item-table' cellspacing='0' width='100%' style='border: solid 1px #f2f2f2;'>
                @php
$grouped_items = $edit_items->groupBy('pi_inv_no');
@endphp

@foreach($grouped_items as $pi_inv_no => $items_group)
    @if(count($items_group) > 0)
        <thead>
            <tr>
                <th></th>
                <th>
                    <span id='table_id_stl_docno_{{ $items_group[0]->id }}'></span>
                </th>
                <th>{{ $pi_inv_no }}&nbsp;|&nbsp;{{ $items_group[0]->bill_no }}</th>
                <th></th>
                <th class="text-end"><a class='btn-sm btn-light d-inline-flex align-items-center gap-1' style='line-height:1;' onclick='row_add({{ $items_group[0]->id }})'><i class="ico icon-outline-add-square text-success" style="font-size: 16px;"></i><span style='display:inline-block; vertical-align:middle;'>Add Item</span></a></th>
            </tr>
            <tr>
                <th style='width: 50px;'>Sr. No</th>
                <th style='width: 250px;'>Item Part Number</th>
                <th>Description of Goods</th>
                <th style='width: 150px;'>Amount</th>
                <th style='width: 150px; text-align: end;'>Action</th>
            </tr>
            <tr><th colspan="5"><hr class="m-0 p-0" /></th></tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($items_group as $items)
                <tr>
                    <td style='width: 50px;'>{{ $i }}</td>
                    <td style='width: 250px;'>{{ $items->part_no }}</td>
                    <td>{{ $items->description }}</td>
                    <td style='width: 150px;'>{{ $items->amount }}</td>
                    <td style='width: 150px; text-align: right;'>

                        <input type='hidden' id='stl_item_part_no_{{ $items->id }}' value='{{ $items->part_id }}' />
                        <input type='hidden' id='stl_item_description_{{ $items->id }}' value='{{ $items->description }}' />
                        <input type='hidden' id='stl_item_amount_{{ $items->id }}' value='{{ $items->amount }}' />

                        <input type='hidden' id='stl_item_pi_inv_no_{{ $items_group[0]->id }}' value='{{ $pi_inv_no }}' />
                        <input type='hidden' id='stl_item_bill_no_{{ $items_group[0]->id }}' value='{{ $items_group[0]->bill_no }}' />
                        <input type='hidden' id='stl_item_pi_no_{{ $items_group[0]->id }}' value='{{ $items_group[0]->pi_no }}' />

                        <a class='btn-sm btn-light edit-btn' onclick='row_edit({{ $items->id }})' title='Edit'>
                            <i class='ico icon-outline-pen-2 text-success' style='font-size: 16px;'></i>
                        </a>
                        <a class='btn-sm btn-light delete-btn' onclick='row_delete({{ $items->id }})' title='Delete'>
                            <i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i>
                        </a>
                        <!-- <button type='button' class='btn-sm btn-light' onclick='addRow(this)' title='Add row'><i class="ico icon-outline-add-square text-success" style="font-size: 16px;"></i></button> -->
                    </td>
                </tr>
                @php $i++; @endphp
            @endforeach
        </tbody>
    @endif
@endforeach
            </table>
            </div>
        </div>
                                    </div>
                                </div>
                            </div>
                            

                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
        <div class="col-lg-12">
            <table class="table-striped" style="width: 350px;">
                <tr><td style="width: 130px;">&nbsp;Value of Hardware</td><td class="text-end"><label class="mt-1" id="total_hardware">0.00</label></td></tr>
                <tr><td>&nbsp;Value of Licence</td><td class="text-end"><label class="mt-1" id="total_license">0.00</label></td></tr>
                <tr><td>&nbsp;<b>Total</b></td><td class="text-end"><label class="mt-1 font-weight-bold" id="total_hl">0.00</label></td></tr>
            </table>
        </div>
                                    </div>
                                </div>
                            </div>



                            {{ Form::close() }}



                            
    <script>

function row_delete(id) {
if (confirm("Are you sure you want to delete this item?") == false) {
    return false;
}
$("#loading_bg").css("display", "block");
var action = "{{ URL::to('delete-stl-items') }}";
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
    }
});
$("#loading_bg").css("display", "none");
}


        $('#payment_type').change();
        $('#pi_no').change();

        function get_pending_list() {

    document.getElementById('total_hardware').textContent = '0.00';
    document.getElementById('total_license').textContent = '0.00';
    document.getElementById('total_hl').textContent = '0.00';

            if($('#pi_no').val()==2){
                $("#plist").empty();
                $("#ptable").empty();
                $('#btn_performa_invoice_modal').click();
                $("#list_name").text('Proforma Invoice');
                return false;
            }
            if($('#pi_no').val()==1){
                get_pi_list();
                $("#list_name").text('Purchase Invoice');
            }
            if($('#pi_no').val()==3){
                get_po_list();
                $("#list_name").text('Purchase Order');
            }

            // $.ajax({
            //     url: action,
            //     type: "POST",
            //     data: {
            //         _token: '{{ csrf_token() }}',
            //         id: $('#vendor').val(),
            //     },
            //     cache: false,
            //     success: function(dataResult) {
            //         var dataResult = JSON.parse(dataResult);
            //         var len = 0;
            //         var len = 0;
            //             if(dataResult['data'] != null){
            //                 len = dataResult['data'].length;
            //             }
            //             if(len > 0){
            //                 $("#plist").empty();
            //                 for(var i=0; i<len; i++){
            //                         var id = dataResult['data'][i].id;
            //                         var doc_number = dataResult['data'][i].doc_number;
            //                         var bill_number = dataResult['data'][i].bill_number;
            //                         var option = "<option value='" + id + "'>" + doc_number +"</option>";
            //                         var innerHtml =
            //                             "<input type='checkbox' onclick='popup_pi_pending(" + id + ")' id='pending_pi_" + i +
            //                             "' name='pending_pi' value='" + doc_number +
            //                             "'> <label for='pending_pi_" + i + "'> " + doc_number +' - '+ bill_number +
            //                             "</label><br />";

            //                         var innerTable =
            //                             "<table  id='table_id_stl_"+id+"' class='mt-2 display school-table' cellspacing='0' width='100%' style='display:none; border: solid 1px #f2f2f2;'>\
            //                             <thead><tr><th class='mt-2'></th><th><span id='table_id_stl_docno_"+id+"'></span></th><th><span id='table_id_stl_billno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_awbno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_boeno_"+id+"'></span></th><th></th><th><a class='btn-sm btn-danger' onclick='deleteTable(this)'>Remove</a></th></tr>\
            //                                     <tr><th style='width: 50px;'>Sr. No</th>\
            //                                     <th style='width: 250px;'>Item Part Number</th>\
            //                                     <th>Description of Goods</th>\
            //                                     <th style='width: 150px;'>Amount</th>\
            //                                     <th style='width: 150px;'>Action</th></tr></thead><tbody></tbody>\
            //                                     <tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+id+"'></span></th></tr></tfoot></table>";

            //                         $("#plist").append(innerHtml);
            //                         $("#ptable").append(innerTable);
            //                 }                        
            //             }
            //             else{
            //                 $("#plist").empty();
            //             }
            //             $("#loading_bg").css("display", "none");
            //     }
            // });
        }
        function get_pi_list() {
            $("#ptable").empty();
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-pi-for-stl') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $('#vendor').val(),
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
                                    var bill_number = dataResult['data'][i].bill_number;
                                    var option = "<option value='" + id + "'>" + doc_number +"</option>";
                                    var innerHtml =
                                        "<input type='checkbox' onclick='popup_pi_pending(" + id + ")' id='pending_pi_" + i +
                                        "' name='pending_pi' value='" + doc_number +
                                        "'> <label for='pending_pi_" + i + "'> " + doc_number +' - '+ bill_number +"</label><br />";

                                    var innerTable =
                                        "<table  id='table_id_stl_"+id+"' class='mt-2 display school-table' cellspacing='0' width='100%' style='display:none; border: solid 1px #f2f2f2;'>\
                                        <thead><tr><th class='mt-2'></th><th><span id='table_id_stl_docno_"+id+"'></span></th><th><span id='table_id_stl_billno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_awbno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_boeno_"+id+"'></span></th><th></th><th><a class='btn-sm btn-danger' onclick='deleteTable(this)'>Remove</a></th></tr>\
                                                <tr><th style='width: 50px;'>Sr. No</th>\
                                                <th style='width: 250px;'>Item Part Number</th>\
                                                <th>Description of Goods</th>\
                                                <th style='width: 150px;'>Amount</th>\
                                                <th style='width: 150px;'>Action</th></tr></thead><tbody></tbody>\
                                                <tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+id+"'></span></th></tr></tfoot></table>";

                                    $("#plist").append(innerHtml);
                                    $("#ptable").append(innerTable);
                            }                        
                        }
                        else{
                            $("#plist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        function get_po_list() {
            $("#ptable").empty();
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-po-for-stl') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $('#vendor').val(),
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
                                    var bill_number = dataResult['data'][i].bill_number;
                                    var option = "<option value='" + id + "'>" + doc_number +"</option>";
                                    var innerHtml =
                                        "<input type='checkbox' onclick='popup_po_pending(" + id + ")' id='pending_po_" + i +
                                        "' name='pending_po' value='" + doc_number +
                                        "'> <label for='pending_po_" + i + "'> " + doc_number +"</label><br />";

                                    var innerTable =
                                        "<table  id='po_table_id_stl_"+id+"' class='mt-2 display school-table' cellspacing='0' width='100%' style='display:none; border: solid 1px #f2f2f2;'>\
                                        <thead><tr><th class='mt-2'></th><th><span id='table_id_stl_docno_"+id+"'></span></th><th><span id='table_id_stl_billno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_awbno_"+id+"'></span>&nbsp;|&nbsp;<span id='table_id_stl_boeno_"+id+"'></span></th><th></th><th><a class='btn-sm btn-danger' onclick='deleteTable(this)'>Remove</a></th></tr>\
                                                <tr><th style='width: 50px;'>Sr. No</th>\
                                                <th style='width: 250px;'>Item Part Number</th>\
                                                <th>Description of Goods</th>\
                                                <th style='width: 150px;'>Amount</th>\
                                                <th style='width: 150px;'>Action</th></tr></thead><tbody></tbody>\
                                                <tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+id+"'></span></th></tr></tfoot></table>";

                                    $("#plist").append(innerHtml);
                                    $("#ptable").append(innerTable);
                            }                        
                        }
                        else{
                            $("#plist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        function popup_pi_pending(id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_pi_id").val(id);
            $("#pi_id").val(id);
            document.getElementById('addPIPendingSTL').click();
            $("#loading_bg").css("display", "none");
        }
        function popup_po_pending(id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_po_id").val(id);
            $("#po_id").val(id);
            document.getElementById('addPOPendingSTL').click();
            $("#loading_bg").css("display", "none");
        }

        $(document).ready(function() {
  // Handle click event for edit button
  $('body').on('click', '.edit-btn', function() {
    var $row = $(this).closest('tr');
    var $inputs = $row.find('input[type="text"], input[type="number"]');

    // Make the inputs editable, except for the first one
    $inputs.not(':first').prop('readonly', false);
    
    // Add the onclick attribute to the first input element
    $inputs.first().attr('onclick', 'get_item(this)');
    
    // Change button text and class
    $(this).text('Save').removeClass('btn-info').addClass('btn-success');
});

  // Handle click event for save button (when "Save" is clicked)
  $('body').on('click', '.btn-success', function() {
    var $row = $(this).closest('tr');  // Find the row

    // Disable text inputs (make them readonly again)
    $row.find('input[type="text"], input[type="number"]').prop('readonly', true);
    
    // Change button text back to "Edit"
    $(this).text('Edit').removeClass('btn-success').addClass('btn-info');
  });

  // Handle click event for delete button (optional, already provided)
  $('body').on('click', '.delete-btn', function() {
    // Show confirmation popup
    // var confirmed = confirm("Are you sure you want to delete this row?");
    
    // if (confirmed) {
      // If confirmed, remove the row
      $(this).closest('tr').remove();
    // }
  });
});

        </script>

<form id="po">
    <div class="modal fade admin-query" id="pi_pending_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Purchase Invoice Pending List</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_pi_id" />
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table id="table_id" class="display school-table" cellspacing="0"
                                        width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">@lang('#') </th>
                                                <th style="width: 250px;">@lang('Item Part Number')</th>
                                                <th>@lang('Description of Goods')</th>
                                                <th style="width: 150px;">@lang('Amount')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
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
                                <div class="col-lg-12 text-end">
                                    <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                        id="btn_close2">
                                        @lang('Close')
                                    </button>

                                    <button class="btn btn-primary bg-success" type="button" id="addPIPendingSTLItems">
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

<a data-toggle="modal" id="btn_performa_invoice_modal" data-target="#PerformaInvoiceModal" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>
  <div class="modal fade" id="PerformaInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Proforma Invoice</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-3 pt-2">Invoice Count</div>
                    <div class="col-lg-9">
                    <input class="form-control" id="invoice_count" type="number" autocomplete="off" name="invoice_count" value="" onchange="set_invoice_count()">
                    <br />
                    <div id="invoice_boxes"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="closeInvoices" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="addInvoices()">Add</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    function set_invoice_count() {
        var count = document.getElementById("invoice_count").value;
        var container = document.getElementById("invoice_boxes");

        container.innerHTML = "";

        for (var i = 0; i < count; i++) {
            var inputHTML = "<div class='mb-3'><input class='form-control' type='text' name='invoiceno_" + (i + 1) + "' placeholder='Invoice #" + (i + 1) + "'></div>";
            container.innerHTML += inputHTML;
        }
    }


function addRow(button) {
    var currentRow = button.closest('tr');
    var tableBody = currentRow.parentNode;    
    var newRow = currentRow.cloneNode(true);    
    var inputs = newRow.querySelectorAll('input');
    inputs.forEach(function(input) {
        if (input.name !== 'pi_inv_no[]') {
            input.value = '';
        }
    });    
    var elementsToRemoveClass = newRow.querySelectorAll('.license, .networking');
    elementsToRemoveClass.forEach(function(element) {
        element.classList.remove('license');
        element.classList.remove('networking');
    });
    tableBody.insertBefore(newRow, currentRow.nextSibling);
    button.style.display = 'none';
}



        function deleteRow(btn) {
            var row = btn.closest("tr");
            row.parentNode.removeChild(row);
        }

        function deleteTable(btn) {
            var confirmDelete = confirm("Are you sure you want to delete this?");
            if (confirmDelete) {

                //$(btn).closest('table').remove();
            }
        }
        function importTable(btn) {
            var confirmDelete = confirm("Are you sure you want to delete this?");
            if (confirmDelete) {
                $(btn).closest('table').remove();
            }
        }
        

var clicked_part_number_input = null;
var clicked_description_input = null;
var clicked_pno_input = null;
var clicked_amount_input = null;

function get_item(element) {
    clicked_part_number_input = $(element);
    clicked_pno_input = $(element).next('input');
    clicked_description_input = $(element).closest('td').next('td').find('input');
    clicked_amount_input = $(element).closest('td').next('td').next('td').find('input');
    $('#btn_product_list_modal').click();
}

function add_get_item() {
    var id = $('#part_no').val();

    var description=$('#part_no_des_'+id).val(); 
    if (description.toLowerCase().includes('license'.toLowerCase())) {
        description = "Networking license ";
        clicked_amount_input.addClass('license');
    }
    else if (description.toLowerCase().includes('licence'.toLowerCase())) {
        description = "Networking License ";
        clicked_amount_input.addClass('license');
    } else {
        description = "Networking " + $('#part_no_cat_'+id).val();
        clicked_amount_input.addClass('networking');
    }
    
    clicked_part_number_input.val($('#part_number_'+id).val());
    clicked_description_input.val(description);
    clicked_pno_input.val($('#part_no').val());

    $('#productlistModal').modal('hide');
}


function set_total() {
    let license_amounts = document.querySelectorAll('.license');
    let networking_amounts = document.querySelectorAll('.networking');
    let license_total = 0;
    let networking_total = 0;

    license_amounts.forEach(function(input) {
        // Remove commas and convert to float
        let value = input.value.replace(/,/g, '');
        license_total += parseFloat(value) || 0;
    });

    networking_amounts.forEach(function(input) {
        // Remove commas and convert to float
        let value = input.value.replace(/,/g, '');
        networking_total += parseFloat(value) || 0;
    });

    document.getElementById('total_hardware').textContent = networking_total.toFixed(@json(session('logged_session_data.decimal_point')));
    document.getElementById('total_license').textContent = license_total.toFixed(@json(session('logged_session_data.decimal_point')));
    document.getElementById('total_hl').textContent = (networking_total + license_total).toFixed(@json(session('logged_session_data.decimal_point')));
}
function set_total2(id) {

let amts = document.querySelectorAll('.cl_' + id);
let amt_total = 0;
amts.forEach(function(input) {
    // Remove commas and convert to float
    let value = input.value.replace(/,/g, '');
    amt_total += parseFloat(value) || 0;
});
document.getElementById('table_id_total_' + id).textContent = amt_total.toFixed(@json(session('logged_session_data.decimal_point')));

let license_amounts = document.querySelectorAll('.license');
let networking_amounts = document.querySelectorAll('.networking');
let license_total = 0;
let networking_total = 0;

license_amounts.forEach(function(input) {
    // Remove commas and convert to float
    let value = input.value.replace(/,/g, '');
    license_total += parseFloat(value) || 0;
});

networking_amounts.forEach(function(input) {
    // Remove commas and convert to float
    let value = input.value.replace(/,/g, '');
    networking_total += parseFloat(value) || 0;
});

document.getElementById('total_hardware').textContent = networking_total.toFixed(@json(session('logged_session_data.decimal_point')));
document.getElementById('total_license').textContent = license_total.toFixed(@json(session('logged_session_data.decimal_point')));
document.getElementById('total_hl').textContent = (networking_total + license_total).toFixed(@json(session('logged_session_data.decimal_point')));
}



</script>


<a data-toggle="modal" id="btn_product_list_modal" data-target="#productlistModal" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>
<div class="modal fade bd-example-modal-lg" id="productlistModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="col-lg-3 pt-2">Part Number</div>
                  <div class="col-lg-9">
                  <select id="part_no" class="form-control js-example-basic-single">
                    <option>Select</option>
                    @foreach ($product as $p)
                    <option value="{{ $p->id }}">{{ $p->part_number }}</option>
                    @endforeach
                </select>    

                    @foreach ($product as $pr)
                    <input type="hidden" id="part_number_{{ $pr->id }}" value="{{ $pr->part_number }}"/>
                    <input type="hidden" id="part_no_cat_{{ $pr->id }}" value="{{ $pr->cat_name }}" />
                    <input type="hidden" id="part_no_des_{{ $pr->id }}" value="{{ $pr->description }}" />

                    @if (str_contains(strtolower($pr->description), 'license'))
                        <input type="hidden" id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}" value="Networking License" />
                    @elseif (str_contains(strtolower($pr->description), 'licence'))
                        <input type="hidden" id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}" value="Networking License" />
                    @else
                        <input type="hidden" id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}" value="Networking {{ $pr->cat_name }}" />
                    @endif

                    @endforeach 

                    
                                 
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeInvoices" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="add_get_item()">Add</button>
      </div>
    </div>
  </div>
</div>

<script>
function row_add(id){
    $('#add_pi_inv_no').val($('#stl_item_pi_inv_no_'+id).val());
    $('#addbill_no').val($('#stl_item_bill_no_'+id).val());
    $('#add_pi_no').val($('#stl_item_pi_no_'+id).val());
    $('#btn_add_product_modal').click();
}
function row_edit(id){
    $('#edit_item').val(id);
    $('#edit_part_no').val($('#stl_item_part_no_'+id).val());
    //$('#edit_description').val($('#stl_item_description_'+id).val());
    $('#edit_amount').val($('#stl_item_amount_'+id).val());
    $('#btn_edit_product_modal').click();
}
</script>
    
<button data-bs-toggle="modal" data-bs-target="#addProductModal" id="btn_add_product_modal" data-toggle="modal" hidden></button>
    <div class="modal side-panel fade" id="addProductModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Select Product</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-add-item', 'method' => 'POST', 'id' => 'stl-add-item']) }}
      <div class="modal-body">
          <div class="row">
              <div class="col-lg-3 pt-2">Part Number</div>
                  <div class="col-lg-9">
                  <select name="add_part_no" id="add_part_no" class="form-control js-example-basic-single" required>
                        <option>Select</option>
                        @foreach ($product as $p)
                        <option value="{{ $p->id }}">{{ $p->part_number }}</option>
                        @endforeach
                    </select>
              </div>
          </div>
          <div class="row mt-2">
                <div class="col-lg-3 pt-2">Amount</div>
                <div class="col-lg-9">
                    <input type="number" step="any" name="add_amount" id="add_amount" class="form-control" required />
                </div>
          </div>
      </div>
      <div class="modal-footer">        
            <input type="hidden" name="add_stl_id" id="add_stl_id" value="{{ $edit->id }}"/>
            <input type="hidden" name="add_pi_inv_no" id="add_pi_inv_no"/>
            <input type="hidden" name="addbill_no" id="addbill_no"/>
            <input type="hidden" name="add_pi_no" id="add_pi_no"/>

        
                                    <button type="submit" class="btn btn-light add-btn ms-2">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Items
                                    </button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>


<button data-bs-toggle="modal" data-bs-target="#editProductModal" id="btn_edit_product_modal" data-toggle="modal" hidden></button>
    <div class="modal side-panel fade" id="editProductModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Select Product</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-update-item', 'method' => 'POST', 'id' => 'stl-update-item']) }}
      <div class="modal-body">
          <div class="row">
              <div class="col-lg-3 pt-2">Part Number</div>
                  <div class="col-lg-9">
                  <select name="edit_part_no" id="edit_part_no" class="form-control" required>
                        <option>Select</option>
                        @foreach ($product as $p)
                        <option value="{{ $p->id }}">{{ $p->part_number }}</option>
                        @endforeach
                    </select>
              </div>
          </div>
          <div class="row mt-2">
                <div class="col-lg-3 pt-2">Amount</div>
                <div class="col-lg-9">
                    <input type="number" step="any" name="edit_amount" id="edit_amount" class="form-control" required />
                </div>
          </div>
      </div>
      <div class="modal-footer">        
            <input type="hidden" name="edit_item" id="edit_item"/>

                                    <button type="submit" class="btn btn-light add-btn ms-2">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update Items
                                    </button>

      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<a data-toggle="modal" id="btn_import_modal" data-target="#importModal" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>
  <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Import Items</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 pt-2">
                    Select File 
                    <input type="file" id="excel-file" /> (<a href="{{ url('public/uploads/product_upload/profoma_invoice_import_sample.xlsx') }}" target="_blank">Sample File</a>)
                    <input type="hidden" id="profoma_id"/>
                </div>
                </div>
            </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="closeimport" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>



  </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

    <script>
        function open_import(id){
            $("#profoma_id").val(id);
            $('#btn_import_modal').click();
        }

        $(document).ready(function() {
            $("#excel-file").change(function(e) {
                var file = e.target.files[0];
                
                if (file && file.name.endsWith(".xlsx")) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        var data = event.target.result;

                        // Use SheetJS to read the Excel file
                        var workbook = XLSX.read(data, { type: 'array' });

                        // Get the first sheet
                        var sheet = workbook.Sheets[workbook.SheetNames[0]];

                        // Convert the sheet to a JSON object (array of rows)
                        var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                        var id = $("#profoma_id").val();
                        // Populate the table with the rows
                        var tableBody = $("#profoma_table_"+id+" tbody");
                        tableBody.empty(); // Clear any existing rows

                        for (var i = 1; i < jsonData.length; i++) { // Skip the header row
                            var rowData = jsonData[i];
                            
                            var dyna_des = $('#dyna_part_no_des_'+rowData[0].trim().toLowerCase().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '')).val();
                            //alert(dyna_des);
                            //alert(rowData[0].trim().toLowerCase());

                            var row = $("<tr></tr>");

                            // Part Number (Cell 1)
                            row.append('<td>'+i+'</td>');
                            row.append('<td><input class="form-control" type="text" name="part_number[]" value="' + rowData[0] + '" onclick="get_item(this)" placeholder="Part Number"><input type="hidden" value="0" name="partno[]"></td>');

                            // Description (Cell 2)
                            row.append('<td><input class="form-control" type="text" name="description[]" value="' + dyna_des + '" placeholder="Description of Goods"></td>');

                            // Amount (Cell 3)
                            row.append('<td><input class="form-control text-end cl_'+(i-1)+'" type="text" name="amount[]" value="' + formatAmount(rowData[1]) + '" placeholder="Amount" onchange="set_total2('+id+')"></td>');

                            // Actions (Cell 4)
                            // row.append('<td><a class="btn-sm btn-info edit-btn">Edit</a><a class="btn-sm btn-danger delete-btn" onclick="deleteRow(this)">Delete</a><button type="button" class="btn-sm btn-primary" onclick="addRow(this)">+</button></td>');

                            tableBody.append(row);
                        }
                    };

                    reader.readAsArrayBuffer(file);
                    $('#closeimport').click();
                } else {
                    alert("Please upload a valid Excel file.");
                }
                $("#excel-file").val('');
            });
        });

    </script>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
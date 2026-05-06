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
                <h2 class="page-heading m-0">Receipt</h2>
                <span class="page-label">Home - Receipt</span>
            </div>
            <div>
                <a href="{{ url('receipt-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('receipt') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        
        <div class="card p-4 mb-2">
            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'receipt-create-form']) }}
            <input type="hidden" value="{{@$editData->id}}" name="cust_id">
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'receipt-create-form']) }}
            @endif

            <input type="hidden" id="receipt_process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}">




            <div class="row">
                <div class="col-lg-12">
                  <div class="white-box">
                        <div class="row mb-0">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Mode') <span>*</span> </label>
                                    <select class="form-control" name="mode" id="mode" required>
                                        <option value="1">Cash</option>
                                        <option value="2">Bank</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                                $('#mode').on('change', function() {
                                    var mode = $('#mode').val();
                                    if(mode == 1){
                                        $('#receipt_mode_cash').prop('required', true);
                                        $('#receipt_mode_bank').prop('required', false);
                                        $('#receipt_mode_cash').css("display", "block");
                                        $('#receipt_mode_bank').css("display", "none");
                                        $('#div_receipt_through').css("display", "none");
                                        $('#doc_number').val($('#cash_doc_number').val());
                                        $('#btn_submit').text('Add Cash Receipt');
                                        $('#txt_bi_cheque_amount').text('Cheque Amount');
                                        
                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_cheque_bank_name').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#cheque_bank_name').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                                    } else {
                                        $('#receipt_mode_cash').prop('required', false);
                                        $('#receipt_mode_bank').prop('required', true);
                                        $('#receipt_mode_cash').css("display", "none");
                                        $('#receipt_mode_bank').css("display", "block");
                                        $('#div_receipt_through').css("display", "");
                                        $('#doc_number').val($('#bank_doc_number').val());
                                        $('#btn_submit').text('Add Bank Receipt');
                                        $('#txt_bi_cheque_amount').text('Cheque Amount');
                                    }
                                });
                            </script>

                            <div class="col-lg-3 mb-4">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <?php
                                            $invno_cash=@App\SysHelper::get_new_code('sys_receipt','CR','doc_number');
                                            $invno_bank=@App\SysHelper::get_new_code('sys_receipt','BR','doc_number');
                                            ?>
                                            <input type="hidden" id="cash_doc_number" value="{{ $invno_cash }}" />
                                            <input type="hidden" id="bank_doc_number" value="{{ $invno_bank }}" />
                                            <label>  @lang('Doc Number') <span>*</span> </label>
                                            <input class="form-control" type="text" id="doc_number" name="doc_number" value="{{ $invno_cash }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Receipt Mode') <span>*</span> </label>
                                    <select class="form-control" name="receipt_mode_cash" id="receipt_mode_cash" required>
                                        @if(isset($receiptmode_cash))
                                        @foreach ($receiptmode_cash as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->receipt_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <select class="form-control" name="receipt_mode_bank" id="receipt_mode_bank" style="display: none;">
                                        @if(isset($receiptmode_bank))
                                        @foreach ($receiptmode_bank as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->receipt_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Doc Date')</label>
                                            @php
                                            $value = date('Y-m-d');
                                            @endphp
                                            <input class="form-control" id="doc_date" type="date" name="doc_date" value="{{ @$value }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 mb-4" id="div_receipt_through" style="display: none;">
                                <label>@lang('Receipt Through')<span>*</span></label>
                                <select class="form-control" name="receipt_through" id="receipt_through">
                                        <option value="1">Bank Transfer</option>
                                        <option value="2">CDC Cheque</option>
                                        <option value="3">PDC Cheque</option>
                                </select>
                            </div>
                            <script>
                                $('#receipt_through').on('change', function() {
                                    var receiptthrough = $('#receipt_through').val();
                                    if(receiptthrough == 1){
                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_cheque_bank_name').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#cheque_bank_name').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                                    }
                                    if(receiptthrough == 2 || receiptthrough == 3){
                                        $('#div_cheque_date').css("display", "");
                                        $('#div_cheque_number').css("display", "");
                                        $('#div_cheque_bank_name').css("display", "");
                                        $('#cheque_number').prop('required', true);
                                        $('#cheque_bank_name').prop('required', true);
                                        $('#cheque_date').prop('required', true);
                                    }
                                });
                            </script>

                            <div class="col-lg-2 mb-4" id="div_cheque_date" style="display: none;">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Cheque Date')</label>
                                            @php
                                            $value = date('Y-m-d');
                                            @endphp
                                            <input class="form-control" id="cheque_date" type="date" name="cheque_date" value="{{ @$value }}">
                                            <script>
                                                $('#cheque_date').on('change', function(){
                                                    $('#receipt_date').val($('#cheque_date').val());  
                                                    $('#receipt_date').focus();      
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4" id="div_cheque_number" style="display: none;">
                                <div class="input-effect">
                                    <label>  @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number" name="cheque_number" value="{{isset($editData)?@$editData->cheque_number:old('cheque_number')}}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4" id="div_cheque_bank_name" style="display: none;">
                                <div class="input-effect">
                                    <label>  @lang('Cheque Bank Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_bank_name" name="cheque_bank_name" value="{{isset($editData)?@$editData->cheque_bank_name:old('cheque_bank_name')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Receipt Date') <span>*</span> </label>
                                    @php
                                        $value = date('Y-m-d');
                                    @endphp
                                    <input class="form-control" type="date" id="receipt_date" name="receipt_date" value="{{ $value }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Remarks') <span></span></label>
                                    <input class="form-control" type="text" name="narration" autocomplete="off" value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}" id="narration">
                                    <input type="hidden" id="narration_1" />
                                    <input type="hidden" id="narration_2" />
                                    <input type="hidden" id="narration_row_id" />
                                </div>
                            </div>

                            <script>
                                $(document).ready(function() { generate_narration(); });
                                $('#mode').on('change', function(e) { generate_narration(); });
                                $('#receipt_mode_bank').on('change', function(e) { generate_narration(); });
                                $('#receipt_through').on('change', function(e) { generate_narration(); });

                                function generate_narration()
                                {
                                    var gn_mode = $('#mode').val();
                                    if(gn_mode == 1){
                                        $('#narration_1').val('Received Cash');
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();
                                        $('#narration').val(n1+' '+n2);
                                    }
                                    if(gn_mode == 2){
                                        var gn_bank_name = $("#receipt_mode_bank option:selected").text();
                                        var gn_receipt_through = $("#receipt_through option:selected").text();
                                        
                                        $('#narration_1').val('Received in '+gn_bank_name+' through '+gn_receipt_through);
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();

                                        $('#narration').val(n1+' '+n2);
                                    }
                                }
                                function generate_narration_fa(id)
                                {
                                    var gn_account = $("#account_id_"+id+" option:selected").text();
                                    var gn_remarks = $('#remarks_'+id).val();
                                    var gn_bi_lpo_no = $('#bi_lpo_no_'+id).val();

                                    $('#narration_2').val(' against '+gn_remarks+' ('+gn_bi_lpo_no+')');
                                    var n1 = $('#narration_1').val();
                                    var n2 = $('#narration_2').val();
                                    $('#narration').val(n1+' '+n2);
                                }
                            </script>

                            {{--  For Cash receipt
                            Received (Cash) From (Syscom) against (SIV-122/SIV-123/SIV-124)
                            
                            For Bank receipt
                            Received in RAK BANK  (Bank Name) through (Bank transfer - PDC Cheque-CDC cheque) from Customer (Syscom) against SIV-122/SIV-123/ SIV-124   --}}

                            <div class="col-lg-2 mb-4">
                                <label>@lang('Currency')<span>*</span></label>
                                <select class="form-control" name="currency" id="currency">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            @if($company->currency_id == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    @php
                                    $deal_id_txt = @App\SysHelper::get_code_from_dealid($deal_id);
                                    if($deal_id_txt == "Without Deal"){
                                        $deal_id_txt="";
                                    }

                                    @endphp
                                    <label>@lang('Deal ID')<span>*</span> <span class="text-sm">(Coma for multipple. eg: 1001,1004)</span></label>
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ $deal_id_txt }}">
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Created') @lang('By')<span>*</span></label>
                                    <input
                                        class="form-control"
                                        type="text" name="createdby" autocomplete="off" id="created_by"
                                        value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        
                        @if($mode_id == 2)
                            <script>
                                $('#mode').val(2);
                                $('#doc_number').val($('#bank_doc_number').val());
                                $('#div_receipt_through').css("display", "");								
                                $('#receipt_through').val(3);
                                $('#div_cheque_date').css("display", "");
                                $('#div_cheque_number').css("display", "");
                                $('#div_cheque_bank_name').css("display", "");
                                $('#cheque_number').prop('required', true);
                                $('#cheque_bank_name').prop('required', true);
                                $('#cheque_date').prop('required', true);
                                $('#mode').change();
                                $('#txt_bi_cheque_amount').text('Cheque Amount');
                            </script>
                        @endif

@if(session('logged_session_data.company_id')==6)
<script>
    $('#mode').val(2);
    $('#mode').change();
    $('#receipt_mode_bank').val(8065);    
    $('#receipt_through').val(3);
    $('#receipt_through').change();
</script>
@endif
                        
                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:auto;">@lang('Account Name')</th>
                                        <th style="width:200px;">@lang('Amount')</th>
                                        <th style="width:400px;">@lang('Narration')
                                            <a class="btn-sm btn-primary float-right pt-0 pb-0" onclick="add_row()"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $setroid=8;
                                    if(isset($editDataList))
                                    {
                                        if(count($editDataList)>0)
                                        {
                                            $setroid=count($editDataList)+1;
                                        }
                                    }
                                    ?>

                                    @for ($roid= 1;  $roid < $setroid ; $roid++)
                                    <tr id="rowone{{$roid}}" onclick="fn_addRow({{$roid}})">
                                    <td>{{$roid}}</td>
                                        <td><select class="form-control js-example-basic-single" name="account_id[]" id="account_id_{{$roid}}">
                                                <option value=""></option>
                                                @foreach ($accounts as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                         {{isset($editDataList[$roid-1])? !empty(@$editDataList[$roid-1]->account_id)? @$editDataList[$roid-1]->account_id==@$value->id ? 'selected':'':'':''}} @if($roid==1) @if($account_id==$value->id) selected @endif @endif>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="amount_{{$roid}}" name="amount[]" step="any" autocomplete="off" min="0" value="{{@$editDataList[$roid-1]->credit_amount}} @if($roid==1){{ $amount }}@endif" onchange="calc_total()" onkeypress="cr_popup_fun({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="remarks_{{$roid}}" name="remarks[]" autocomplete="off" value="{{@$editDataList[$roid-1]->remarks}}">
                                        </td>
                                    </tr>
                                    @endfor
                                    <?php $roid--;?>
                                    <input type="hidden" id="br-row-count" value="{{$roid}}">

                                    <script>
                                        function add_row(){
                                            var r = $('#add_br_row_count').val();
                                            var r2 = $('#br-row-count').val();
                                            $('#addrow_'+r).css("display",'');
                                            $('#add_br_row_count').val(Number(r)+1);
                                            $('#br-row-count').val(Number(r2)+1);
                                        }
                                    </script>
                                    <input type="hidden" id="add_br_row_count" value="1">
                        
                                    @for ($i = 1; $i < 10; $i++)
                                    <tr id="addrow_{{ $i }}" style="display: none;" >
                                        <td>{{ $roid+$i }}</td>
                                        <td><select class="form-control js-example-basic-single" name="account_id[]" id="account_id_{{$roid+$i}}">
                                            <option value=""></option>
                                            @foreach ($accounts as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                     {{isset($editDataList[$roid-1])? !empty(@$editDataList[$roid-1]->account_id)? @$editDataList[$roid+$i-1]->account_id==@$value->id ? 'selected':'':'':''}} >{{ @$value->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" id="amount_{{$roid+$i}}" name="amount[]" step="any" autocomplete="off" min="0" value="{{@$editDataList[$roid+$i-1]->credit_amount}}" onchange="calc_total()" onkeypress="cr_popup_fun({{$roid+$i}})">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" id="remarks_{{$roid+$i}}" name="remarks[]" autocomplete="off" value="{{@$editDataList[$roid+$i-1]->remarks}}">
                                    </td>
                                    </tr>
                                    @endfor

                                </tbody>
                                <tfoot>
                                    <tr>
                                      <td></td>
                                      <td></td>
                                      <td class="sstablefoot"><label id="amount_total">0.00</label></td>
                                      <td></td>
                                    </tr>
                                  </tfoot>
                            </table>
                            <a data-modal-size="modal-md" data-target="#cr_popup_win" id="addCtrlBankBookAdjest" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>

                            <div style="display: none;">
                                @if(!isset($view))
                                    {{--  <button type="button" class="primary-btn small fix-gr-bg" id="addRowRE"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>  --}}
                                @endif
                            </div>
                            

<script>
function fn_addRow(id) {
    var rownum = document.getElementById('br-row-count').value;
    if(id==rownum) {
     document.getElementById('br-row-count').value = (Number(rownum) + Number(1));
        document.getElementById('addRowRE').click();
    }
}

function calc_total(){
    var countrow = document.getElementById('br-row-count').value;
    var t1=0;
    for(var i=1; i<=countrow; i++)
    {
        t1 += Number($('#amount_'+i).val());
    }
    $('#amount_total').text(t1.toFixed(@json(session('logged_session_data.decimal_point'))));
}
</script>



                        </div>



                <!-- Bank Info Details -->


                <!-- end row -->


                <div class="row mt-40">
                    <div class="col-lg-12 text-center">
                        <button class="btn btn-primary" id="btn_submit">Add Receipt</button>
                    </div>
                </div>
            </div>
        </div>        
    </div>
    {{ Form::close() }}
        </div>

    </div>
    


    <form id="ta" >
    <div class="modal fade admin-query" id="cr_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Selection</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="br_account_id">
                    <input type="hidden" id="br_account_id_amount">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label id="txt_bi_cheque_amount">@lang('Cash Amount') <span>*</span></label>
                                    <input class="primary-input form-control" type="text" id="bi_cheque_amount" name="bi_cheque_amount" value="0" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Amount Adjusted') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_amount_adjusted" name="bi_amount_adjusted" value="0" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                    
                                    <input type="hidden" id="bi_balance_adjest" value="">

                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Balance to Adjust') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_extra_amount" name="bi_extra_amount" value="0" >                                    
                                    <div style="display: none;">
                                    <input class="primary-input form-control" type="text" id="bi_balance_to_adjust" name="bi_balance_to_adjust" value="0" ></div>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%" id="crListBankBookAdjest">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('Deal Id')</th>
                                                <th style="width:100px;">@lang('Doc No')</th>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('LPO NO')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th>
                                                <th style="width:100px;">@lang('Adjustment')</th>
                                                <th style="width:100px;">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><label id="footer_total" /></th>
                                                <th><label id="footer_paid" /></th>
                                                <th><label id="footer_balance" /></th>
                                                <th><label id="footer_adjustment" /></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            function get_set_amount(id)
                            {
                                var form_amt = Number($('#bi_cheque_amount').val());bi_amount_adjusted
                                var bal_amt = Number($('#bi_balance_'+id).val());


                                var bi_amount = Number($('#bi_amount_'+id).val());
                                var adjested_sum = 0;
                                $(".tot_amt").each(function () {
                                    adjested_sum += +$(this).val();
                                });
                                $('#bi_amount_adjusted').val(Number(adjested_sum));
                                $('#bi_balance_adjest').val(Number(form_amt)-Number(adjested_sum));



                                if($('#bi_balance_adjest').val()==""){
                                    $('#bi_balance_adjest').val(form_amt);
                                }
                                var amt = Number($('#bi_balance_adjest').val());
                                var pending = Number($('#bi_balance_to_adjust').val());

                                if(amt > 0 && amt != "" && pending > 0){
                                    if(amt == bal_amt) {
                                        //alert("1.if(amt == bal_amt)");

                                        $('#bi_amount_'+id).val(amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust-(adjusted+amt));
                                        var extra = Number($('#bi_extra_amount').val());

                                        if(form_amt >= (adjusted+amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                                        }

                                        $('#bi_balance_adjest').val(0);
                                    } else if(amt > bal_amt) {
                                        //alert("2.else if(amt > bal_amt)");

                                        $('#bi_amount_'+id).val(bal_amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+bal_amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust-bal_amt);
                                        var extra = Number($('#bi_extra_amount').val());
                                        
                                        if(form_amt >= (adjusted+bal_amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+bal_amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+bal_amt) - form_amt);
                                        }

                                        if(amt >= bal_amt){
                                            $('#bi_balance_adjest').val(amt - bal_amt);
                                        } else {
                                            $('#bi_balance_adjest').val(bal_amt - amt);
                                        }
                                    } else if(amt < bal_amt) {
                                        //alert("3.else if(amt < bal_amt)");

                                        $('#bi_amount_'+id).val(amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust- amt);
                                        var extra = Number($('#bi_extra_amount').val());
                                        
                                        if(form_amt >= (adjusted+amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                                        }
                                        
                                        $('#bi_balance_adjest').val(0);
                                    }
                                    else {
                                        //alert("4.else");

                                        $('#bi_amount_'+id).val(0);
                                        $('#bi_balance_adjest').val(0);
                                    }
                                    
                                        var num_tot_amt = $('.tot_amt').length;
                                        var n = 0;
                                        for(i=1; i<=num_tot_amt; i++){
                                            if($('#bi_amount_'+i).val() !=""){
                                                n += Number($('#bi_amount_'+i).val()); } }
                                        $('#footer_adjustment').text(n);

                                        var d = '';
                                        for(i=1; i<=num_tot_amt; i++){
                                            if($('#bi_amount_'+i).val() !="" && $('#bi_amount_'+i).val() != 0){
                                                if(d==''){
                                                    d = $('#bi_doc_no_'+i).val();}
                                                else{
                                                    d += ', '+$('#bi_doc_no_'+i).val(); }
                                            } }
                                            var re_id = $('#narration_row_id').val();
                                            $('#remarks_'+re_id).val(d);
                                }
                            }
                        </script>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="btn btn-primary fix-gr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="btn btn-success fix-gr-bg" type="button" value="Save" id="btn_save" onclick="validateAttachForm()" />
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>

    function add_deal_code_from_adjestment() {
        var deal_codes = [];
        var existing = $('#deal_id').val();
        if (existing) {
            deal_codes = existing.split(',').map(code => code.trim());
        }

        var num_tot_amt2 = $('.tot_amt').length;
        for (var i = 1; i <= num_tot_amt2; i++) {
            var amount = $('#bi_amount_' + i).val();
            var new_code = $('#bi_deal_id_' + i).val();

            if (amount && amount != 0 && new_code) {
                deal_codes.push(new_code.trim());
            }
        }
        var unique_deal_codes = [...new Set(deal_codes)];
        $('#deal_id').val(unique_deal_codes.join(', '));
    }

    function validateAttachForm() {
        $("#loading_bg").css("display", "block");
        var numRows = $('.row_ctrl').length;
        for(i=1; i<=numRows; i++){
            if($("#bi_amount_"+i).val() != "" && $("#bi_amount_"+i).val() != 0){
                validateBankBookAdjestForm(i);
            }
        }
        
        generate_narration_fa($('#narration_row_id').val());
        $('#remarks_'+$('#narration_row_id').val()).val($('#narration').val());

        add_deal_code_from_adjestment();

        $("#btn_close2").click();
        $("#loading_bg").css("display", "none");
    }

    function delete_before_update() {
        var doc_number = $("#doc_number").val();
        var account_id = $('#br_account_id').val();
        var url = $('#url').val();
        $.ajax({
            url: url + '/' + 'receivable-outstanding-store-temp-delete',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                    doc_number : doc_number,
                    account_id : account_id,

            },
            cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");                
            }
            else
            {
                //$("#btn_close2").click();
                //$("#addCtrlBankBookAdjest").click();
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });
    }

    function BankBookAdjestBalance(id) {
        var bi_total = $('#bi_total_'+id).val();
        var bi_paid = $('#bi_paid_'+id).val();
        var tot = (parseFloat(bi_total)-parseFloat(bi_paid)).toFixed(@json(session('logged_session_data.decimal_point')));
        $('#bi_balance_'+id).val(tot);
        $('#bi_amount_'+id).val(bi_paid);
    }

    function validateBankBookAdjestForm(id) {
    var val1 = $("#bi_cheque_amount").val();
    var val2 = $("#bi_amount_adjusted").val();
    var val3 = $("#bi_extra_amount").val();
    var val4 = $("#bi_balance_to_adjust").val();


    var bi_doc_no = $('#bi_doc_no_'+id).val();
    var bi_doc_date = $('#bi_doc_date_'+id).val();
    var bi_lpo_no = $('#bi_lpo_no_'+id).val();
    var bi_total = $('#bi_total_'+id).val();
    var bi_paid = $('#bi_paid_'+id).val();
    var bi_balance = $('#bi_balance_'+id).val();
    var bi_amount = $('#bi_amount_'+id).val();
    var bi_narration = $('#bi_narration_'+id).val();
    var account_id = $('#br_account_id').val();
    var entry_date = $('#doc_date').val();
    var bi_currency = $('#currency').val();
    
    if($('#mode').val()==1){
        transaction_type = 'cashreceipt';
    } else {
        transaction_type = 'bankreceipt';
    }
    var entry_type =2; //1 Debit, 2 Credit
    var process_id = $('#receipt_process_id').val();

    

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

    //return true;
    
    //$(".btn_ajax_br").prop('disabled', true);
        $("#loading_bg").css("display", "block");
    
    var url = $('#url').val();

    $.ajax({
            url: url + '/' + 'receivable-outstanding-store-temp',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bi_cheque_amount : val1,
                bi_amount_adjusted : val2,
                bi_extra_amount : val3,
                bi_balance_to_adjust : val4,
                    bi_currency : bi_currency,
                    bi_doc_no : bi_doc_no,
                    bi_doc_number : $("#doc_number").val(),
                    bi_doc_date : bi_doc_date,
                    bi_lpo_no : bi_lpo_no,
                    bi_total : bi_total,
                    bi_paid : bi_paid,
                    bi_balance : bi_balance,
                    bi_amount : bi_amount,
                    bi_narration : bi_narration,
                    account_id : account_id,
                    entry_date : entry_date,
                    transaction_type : transaction_type,
                    entry_type : entry_type,
                    process_id : process_id,

            },
            cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");                
            }
            else
            {

            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });

    //preventDefault();
        $("#loading_bg").css("display", "none");
    }

    function cr_popup_fun(id)
    {
        $('#amount_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                $('#narration_row_id').val(id);
                var br_account_id = $('#account_id_'+id+'').val();
                var br_account = $('#amount_'+id+'').val();
                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    $("#addCtrlBankBookAdjest").click();
                    $("#addCtrlBankBookAdjest").prop("disabled", true);
                    delete_before_update();
                }
                else{ alert("Amount Missing") }
                return false;
            }
        });
        return false;
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    $('#receipt-create-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
    });

    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    @endsection
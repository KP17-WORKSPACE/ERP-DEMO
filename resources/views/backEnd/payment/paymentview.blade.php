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
                <h2 class="page-heading m-0">Payment View</h2>
                <span class="page-label">Home - Payment View</span>
            </div>
            <div>
                <a href="{{ url('payment-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('payment/'.$editData->id.'/edit') }}" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="Doc Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-payment') }}";                
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
                <a href="{{ url('payment') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        
        <div class="card p-4 mb-2">
            <input type="hidden" id="bankpayment_process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
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
                                        @if ($editData->mode == 1) <option value="1">Cash</option> @endif
                                        @if ($editData->mode == 2) <option value="2">Bank</option> @endif
                                    </select>
                                </div>
                            </div>
                            <script>
                                $('#mode').on('change', function() {
                                    var mode = $('#mode').val();
                                    if(mode == 1){
                                        $('#payment_mode_cash').prop('required', true);
                                        $('#payment_mode_bank').prop('required', false);
                                        $('#payment_mode_cash').css("display", "block");
                                        $('#payment_mode_bank').css("display", "none");
                                        $('#div_payment_through').css("display", "none");
                                        $('#doc_number').val($('#cash_doc_number').val());
                                        $('#btn_submit').text('Update Cash Payment');
                                    } else {
                                        $('#payment_mode_cash').prop('required', false);
                                        $('#payment_mode_bank').prop('required', true);
                                        $('#payment_mode_cash').css("display", "none");
                                        $('#payment_mode_bank').css("display", "block");
                                        $('#div_payment_through').css("display", "");
                                        $('#doc_number').val($('#bank_doc_number').val());
                                        $('#btn_submit').text('Update Bank Payment');
                                    }
                                });
                            </script>
                            <div class="col-lg-3 mb-4">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input type="hidden" id="cash_doc_number" value="{{ $editData->doc_number }}" />
                                            <input type="hidden" id="bank_doc_number" value="{{ $editData->doc_number }}" />
                                            <label>  @lang('Doc Number') <span>*</span> </label>
                                            <input class="form-control" type="text" id="doc_number" name="doc_number" value="{{ $editData->doc_number }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Payment Mode') <span>*</span> </label>
                                    <select class="form-control" name="payment_mode_cash" id="payment_mode_cash" @if ($editData->mode == 2) style="display: none;" @endif>
                                        <option data-display="Payment Mode *" value="">@lang('Payment Mode') *</option>
                                        @if(isset($paymentmode_cash))
                                        @foreach ($paymentmode_cash as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <select class="form-control" name="payment_mode_bank" id="payment_mode_bank" @if ($editData->mode == 1) style="display: none;" @endif>
                                        <option data-display="Payment Mode *" value="">@lang('Payment Mode') *</option>
                                        @if(isset($paymentmode_bank))
                                        @foreach ($paymentmode_bank as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
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
                                            if(isset($editData) && !empty($editData->doc_date) ){ @$value =
                                            date('Y-m-d', strtotime(@$editData->doc_date)); }
                                            else{ if(!empty(old('doc_date'))){ @$value = old('doc_date');
                                            }else{
                                            @$value = date('Y-m-d'); } }
                                            @endphp
                                            <input class="form-control" id="doc_date" type="date" name="doc_date" value="{{ @$value }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 mb-4" id="div_payment_through" @if ($editData->mode == 1) style="display: none;" @endif>
                                <label>@lang('Payment Through')<span>*</span></label>
                                <select class="form-control" name="payment_through" id="payment_through">
                                        <option value="1" @if ($editData->payment_through == 1) selected @endif>Bank Transfer</option>
                                        {{--  <option value="2" @if ($editData->payment_through == 2) selected @endif>CDC Cheque</option>  --}}
                                        <option value="3" @if ($editData->payment_through == 3) selected @endif>Cheque</option>
                                </select>
                            </div>
                            <script>
                                $('#payment_through').on('change', function() {
                                    var paymentthrough = $('#payment_through').val();
                                    if(paymentthrough == 1){
                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_cheque_bank_name').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#cheque_bank_name').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                                    }
                                    if(paymentthrough == 2 || paymentthrough == 3){
                                        $('#div_cheque_date').css("display", "");
                                        $('#div_cheque_number').css("display", "");
                                        $('#div_cheque_bank_name').css("display", "");
                                        $('#cheque_number').prop('required', true);
                                        $('#cheque_bank_name').prop('required', true);
                                        $('#cheque_date').prop('required', true);
                                    }
                                });
                            </script>
                            <div class="col-lg-2 mb-4" id="div_cheque_date" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Cheque Date')</label>
                                            @php
                                            $value = date('Y-m-d');
                                            if(isset($editData) && !empty($editData->cheque_date) ){ @$value = date('Y-m-d', strtotime(@$editData->cheque_date)); }
                                            @endphp
                                            <input class="form-control" id="cheque_date" type="date" name="cheque_date" value="{{ @$value }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4" id="div_cheque_number" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label>  @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number" name="cheque_number" value="{{isset($editData)?@$editData->cheque_number:old('cheque_number')}}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4" id="div_cheque_bank_name" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label>  @lang('Cheque Bank Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_bank_name" name="cheque_bank_name" value="{{isset($editData)?@$editData->cheque_bank_name:old('cheque_bank_name')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Payment Date') <span>*</span> </label>
                                    <input class="form-control" type="date" id="payment_date" name="payment_date" value="{{isset($editData)?@$editData->payment_date:old('payment_date')}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Narration') <span></span></label>
                                    <input
                                        class="form-control"
                                        type="text" name="narration" autocomplete="off"
                                        value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                        id="narration">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-4">
                                <label>@lang('Currency')<span>*</span></label>
                                <select class="form-control" name="currency" id="currency">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 mb-4">
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
                        
                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:auto;">@lang('Account Name')</th>
                                        <th style="width:200px;">@lang('Amount')</th>
                                        <th style="width:400px;">@lang('Remarks')</th>
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
                                    <tr id="rowone{{$roid}}"> {{--  onclick="fn_addRow({{$roid}})"  --}}
                                    <td>{{$roid}}</td>
                                        <td><select class="form-control js-example-basic-single" name="account_id[]" id="account_id_{{$roid}}" disabled>
                                                <option value=""></option>
                                                @foreach ($accounts as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                         {{isset($editDataList[$roid-1])? !empty(@$editDataList[$roid-1]->account_id)? @$editDataList[$roid-1]->account_id==@$value->id ? 'selected':'':'':''}} >{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="amount_{{$roid}}" readonly name="amount[]" autocomplete="off" min="0" value="{{@$editDataList[$roid-1]->debit_amount}}" onchange="calc_total()" onkeypress="cr_popup_fun({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="remarks_{{$roid}}" name="remarks[]" autocomplete="off" value="{{@$editDataList[$roid-1]->remarks}}">
                                        </td>
                                    </tr>
                                    @endfor
                                    <?php $roid--;?>
                                    <input type="hidden" id="br-row-count" value="{{$roid}}">
                                    <a data-modal-size="modal-md" data-target="#cr_popup_win" id="addCtrlBankBookAdjest" data-toggle="modal"></a>
                                    <script>                                        
                                        
                                    </script>
                                </tbody>
                                <tfoot>
                                    <tr>
                                      <td></td>
                                      <td></td>
                                      <td class="sstablefoot"><label id="amount_total">{{ @App\SysHelper::com_curr_format($editDataList->sum('debit_amount'),2,'.',',') }}</label></td>
                                      <td></td>
                                    </tr>
                                  </tfoot>
                            </table>

                            <div style="display: none;">
                                @if(!isset($view))
                                @endif
                            </div>
                            

<script>
function fn_addRow(id) {
    var rownum = document.getElementById('br-row-count').value;
    if(id==rownum) {
     document.getElementById('br-row-count').value = (Number(rownum) + Number(1));
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

                        
                <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                        <b>Adjusted Items</b>
                            <table class="table table-bordered table-striped" id="adjustment-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:100px;">@lang('Doc Number')</th>
                                        <th style="width:100px;">@lang('Doc Date')</th>
                                        <th style="width:100px;">@lang('LPO NO')</th>
                                        <th style="width:100px;" class="text-right">Total</th>
                                        <th style="width:100px;" class="text-right">Paid</th>
                                        <th style="width:100px;" class="text-right">Balance</th>
                                        <th style="width:100px;" class="text-right">Adjusted</th>
                                        <th style="width:100px;" class="text-right">Unadjusted</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @if(count($editDataAdjustments)>0)
                                @foreach ($editDataAdjustments as $item)
                                    <tr>
                                        <td>{{ @$loop->iteration }}</td>
                                        <td>{{ @$item->bi_doc_no }}</td>
                                        <td>{{ @$item->bi_doc_date }}</td>
                                        <td>{{ @$item->bi_lpo_no }}</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$item->bi_total,2,'.',',') }}</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$item->bi_paid,2,'.',',') }}</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$item->bi_balance_to_adjust,2,'.',',') }}</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$item->bi_amount,2,'.',',') }}</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$item->bi_cheque_amount - @$item->bi_amount_adjusted,2,'.',',') }}</td>
                                    </tr>
                                @endforeach
                        @endif
                                </tbody>
                            </table>
                    </div>
                </div>


                <!-- Bank Info Details -->


                <!-- end row -->

            </div>
        </div>        
    </div>
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
                                    <input class="primary-input form-control" type="text" id="bi_new_reference" name="bi_new_reference" value="" >
                                    <label>  @lang('New Reference') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" id="bi_amount_to_adjust" name="bi_amount_to_adjust" value="" >
                                    <label>  @lang('Amount to Adjust') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" id="bi_adjusted_amount" name="bi_adjusted_amount" value="" >
                                    <label>  @lang('Adjusted Amount') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>                                    
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" id="bi_currency" name="bi_currency" value="" >
                                    <label>  @lang('Currency') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20" style="display: none;">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" id="bi_doc_number" name="bi_doc_number" value="" >
                                    <label>  @lang('Doc Number') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" id="bi_contains" name="bi_contains" value="" >
                                    <label>  @lang('Contains') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="sstable" cellspacing="0" width="100%" id="crListBankBookAdjest">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('Doc No')</th>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('LPO NO')</th>
                                                <th style="width:100px;">@lang('Due Date')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th>
                                                <th style="width:100px;">@lang('Amount')</th>
                                                <th style="width:100px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_doc_no_{{$roid}}" name="bi_doc_no[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_doc_date_{{$roid}}" name="bi_doc_date[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_lpo_no_{{$roid}}" name="bi_lpo_no[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_due_date_{{$roid}}" name="bi_due_date[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_total_{{$roid}}" name="bi_total[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_paid_{{$roid}}" name="bi_paid[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_balance_{{$roid}}" name="bi_balance[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" id="bi_amount_{{$roid}}" name="bi_amount[]" autocomplete="off" min="0"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('lang.cancel')
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
    </div>
</form>


<script>
    function BankBookAdjestBalance(id) {
        var bi_total = $('#bi_total_'+id).val();
        var bi_paid = $('#bi_paid_'+id).val();
        var tot = (parseFloat(bi_total)-parseFloat(bi_paid)).toFixed(@json(session('logged_session_data.decimal_point')));
        $('#bi_balance_'+id).val(tot);
        $('#bi_amount_'+id).val(bi_paid);
    }

    function validateBankBookAdjestForm(id) {
    var val1 = $("#bi_new_reference").val();
    var val2 = $("#bi_amount_to_adjust").val();
    var val3 = $("#bi_adjusted_amount").val();
    var val4 = $("#bi_currency").val();
    var val5 = $("#bi_doc_number").val();
    var val6 = $("#bi_contains").val();

    var bi_doc_no = $('#bi_doc_no_'+id).val();
    var bi_doc_date = $('#bi_doc_date_'+id).val();
    var bi_lpo_no = $('#bi_lpo_no_'+id).val();
    var bi_due_date = $('#bi_due_date_'+id).val();
    var bi_total = $('#bi_total_'+id).val();
    var bi_paid = $('#bi_paid_'+id).val();
    var bi_balance = $('#bi_balance_'+id).val();
    var bi_amount = $('#bi_amount_'+id).val();
    var account_id = $('#br_account_id').val();
    var entry_date = $('#doc_date').val();
    var transaction_type ='bankreceipt';
    var entry_type =2; //1 Debit, 2 Credit
    var process_id = $('#bankreceipt_process_id').val();

    

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
    // if (val5 === "") {
    //     $('.modal_input_validation_5').show();
    //     $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
    //     $("span.modal_input_validation_5").addClass("red_alert");
    //     return false;
    // }
    if (val6 === "") {
        $('.modal_input_validation_6').show();
        $(".modal_input_validation_6").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_6").addClass("red_alert");
        return false;
    }

    //return true;
    
    $(".btn_ajax_br").prop('disabled', true);
    
    var url = $('#url').val();

    $.ajax({
            url: url + '/' + 'receipt-adjustments-store',
            type: 'POST',
            data: {
                    bi_new_reference : val1,
                    bi_amount_to_adjust : val2,
                    bi_adjusted_amount : val3,
                    bi_currency : val4,
                    bi_doc_number : val5,
                    bi_contains : val6,
                    bi_doc_no : bi_doc_no,
                    bi_doc_date : bi_doc_date,
                    bi_lpo_no : bi_lpo_no,
                    bi_due_date : bi_due_date,
                    bi_total : bi_total,
                    bi_paid : bi_paid,
                    bi_balance : bi_balance,
                    bi_amount : bi_amount,
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
                $("#btn_close2").click();
                $("#addCtrlBankBookAdjest").click();
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });

    //preventDefault();
    }

    function cr_popup_fun(id)
    {
        $('#amount_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();
                var br_account = $('#amount_'+id+'').val();
                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_adjusted_amount').val(br_account).focus();
                    $('#bi_adjusted_amount').focus();
                    $("#addCtrlBankBookAdjest").click();
                }
                return false;  
            }
        });

        // $('#amount_'+id+'').on("keypress", function(e) {
        //     if (e.keyCode == 13) {
        //         alert("1");
        //         var br_account_id = $('#account_id_'+id+'').val();
        //         var cr_account = $('#amount_'+id+'').val();
        //         if(br_account_id != "" && cr_account != ""){
        //             $('#br_account_id').val(br_account_id);
        //             $('#br_account_id_amount').val(cr_account);
        //             $('#bi_adjusted_amount').val(cr_account).focus();
        //             $('#bi_adjusted_amount').focus();
        //             $("#addCtrlBankBookAdjest").click();
        //         return false;
        //         }         
        //         return false;
        //     }
        // });
        //preventDefault();
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    </script>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    @endsection
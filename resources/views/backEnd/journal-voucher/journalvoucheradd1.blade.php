@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Journal Voucher</h2>
                <span class="page-label">Home - Journal Voucher</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('journalvoucher-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('journalvoucher') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
        </div>
        <div class="card shadow mb-4 p-4">
            @if (isset($editData))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
                <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
            @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
            @endif

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}">
            <input type="hidden" id="process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            
            <input type="hidden" id="company_vat_rate" value="{{ $company->company_vat_rate }}">
            <input type="hidden" id="vat_account_val" value="{{ $vat_account_val }}">
            <input type="hidden" id="vat_account_text" value="{{ $vat_account_text }}">
            

            <div class="row">
                <div class="col-lg-3 mb-4">
                    <div class="input-effect">
                        <label class="dynamicslbl"> @lang('Doc Number') <span>*</span> </label>
                        <input class="form-control {{ $errors->has('doc_number') ? 'is-invalid' : ' ' }}" readonly
                            type="text" id="doc_number" name="doc_number"
                            value="{{ isset($editData) ? (!empty(@$editData->doc_number) ? @$edit->doc_number : old('doc_number')) : @App\SysHelper::get_new_code('sys_journalvoucher','JV','doc_number') }}">
                        <span class="focus-border"></span>
                        @if ($errors->has('doc_number'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('doc_number') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Doc Date')</label>
                        @php
                        $value = date('Y-m-d');
                        if(isset($editData) && !empty($editData->doc_date) ){
                            $value = date('Y-m-d', strtotime(@$editData->doc_date)); }

                            if(isset($cheque_date)){
                                $value = $cheque_date;
                            }
                        @endphp
                        <input class="form-control" id="doc_date" type="date"
                            autocomplete="off" name="doc_date" value="{{ @$value }}">
                        @if ($errors->has('doc_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('doc_date') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Currency')</label>
                        <select
                            class="form-control {{ $errors->has('currency') ? ' is-invalid' : '' }}"
                            name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}"
                                    @if($company->currency_id == $value->id) selected @endif>
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
                <div class="col-lg-3 mb-4">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                        <input
                            class="form-control {{ $errors->has('created_by') ? ' is-invalid' : '' }}"
                            type="text" name="createdby" autocomplete="off" id="created_by"
                            value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                            readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('createdby'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('createdby') }}</strong>
                            </span>
                        @endif                        
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Narration') <span></span></label>
                        <input
                            class="dynamicstxt primary-input form-control {{ $errors->has('narration') ? ' is-invalid' : '' }}"
                            type="text" name="narration" autocomplete="off"
                            value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                            id="narration" required>
                        <span class="focus-border"></span>
                        @if ($errors->has('narration'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('narration') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 mb-4 pt-2">&nbsp;<br />
                    
                    <a class="btn btn-success" data-toggle="modal" id="btn_import_modal" data-target="#importModal" data-toggle="modal" data-backdrop="static" data-keyboard="false">Import</a>
                </div>
                {{-- <div class="col-lg-2 mb-4">
                    <div class="input-effect">
                        <label>@lang('Deal ID')<span>*</span> <span class="text-sm"></span></label>
                        <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @App\SysHelper::get_code_from_dealid($deal_id) }}">
                    </div>
                </div> --}}
                <input type="hidden" name="deal_id" id="deal_id" value="0">
            </div>
            <div class="row">
                <div class="col-lg-12 mb-4">

                <table class="table table-bordered table-striped" id="jv-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:20px;">@lang('#')</th>
                            <th>@lang('Account Name')</th>
                            <th style="width:100px;">@lang('Debit')</th>
                            <th style="width:100px;">@lang('Credit')</th>
                            <th style="width:200px;">@lang('Narration')</th>
                            <th style="width:100px;">@lang('Deal Id')</th>
                            <th style="width:10px;"><a class="btn-sm btn-primary float-right pt-0 pb-0" onclick="add_jv_row()"><i class="fa fa-plus-square" aria-hidden="true"></i></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $setroid = 8;
                        if (isset($editDataList)) {
                            if (count($editDataList) > 0) {
                                $setroid = count($editDataList) + 1;
                            }
                        }
                        ?>

                        @for ($roid = 1; $roid < $setroid; $roid++)
                            <tr id="rowone{{ $roid }}" onclick="fn_addRow({{ $roid }})">
                                <td>{{ $roid }}</td>
                                <td><select class="form-control js-account-select" name="account_id[]" id="account_id_{{ $roid }}" onchange="account_id_change({{ $roid }})">
                                    <option></option>
                                </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="amount_dr_{{ $roid }}" name="amount_dr[]" step="any" autocomplete="off" min="0" onchange="calc_change({{ $roid }})" value="{{ @$editDataList[$roid - 1]->amount_dr }}" onkeypress="all_popup_fun({{$roid}})">  {{--  dr  --}}
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="amount_cr_{{ $roid }}" name="amount_cr[]" step="any" autocomplete="off" min="0" onchange="calc_change({{ $roid }})" value="{{ @$editDataList[$roid - 1]->amount_cr }}" onkeypress="all_popup_fun({{$roid}})">  {{--  cr  --}}
                                </td>
                                <td>
                                    <input class="form-control class_remarks" type="text" id="remarks_{{ $roid }}" name="remarks[]" autocomplete="off" value="{{ @$editDataList[$roid - 1]->remarks }}" onchange="add_narration({{ $roid }})">
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="dealid_{{ $roid }}" name="dealid[]" autocomplete="off" value="@if($deal_id != 0 && $roid ==1) {{ $deal_id }} @endif">
                                </td>
                                <td class="text-right"><input type="checkbox" name="vat_account[]" id="vat_account_{{ $roid }}" style="display: none;" onchange="vat_account_checked({{ $roid }})" /></td>
                            </tr>


                        @endfor


                        <?php $roid--; ?>
                        <input type="hidden" id="jv-row-count" value="{{ $roid }}">

                        <script>
                            function add_jv_row(){
                                var r = $('#add_jv_row_count').val();
                                var r2 = $('#jv-row-count').val();
                                $('#addrow_'+r).css("display",'');
                                $('#add_jv_row_count').val(Number(r)+1);
                                $('#jv-row-count').val(Number(r2)+1);
                            }
                            function add_narration(id){
                                var r = $('.class_remarks').length;
                                var re = "";
                                for(i=1; i<=r; i++){
                                    if($('#remarks_'+i).val() !="" && $('#amount_cr_'+i).val() ==""){                                        
                                        if(re=="") { re = $('#remarks_'+i).val(); }
                                        else { re += ', ' + $('#remarks_'+i).val(); }
                                    }
                                }
                                $('#narration').val(re);
                            }
                        </script>
                        <input type="hidden" id="add_jv_row_count" value="1">
                        
                        @for ($i = 1; $i <= 23; $i++)
                        <tr id="addrow_{{ $i }}" style="display: none;" >
                            <td>{{ $roid+$i }}</td>
                            <td><select class="form-control js-account-select" name="account_id[]" id="account_id_{{ $roid+$i }}" onchange="account_id_change({{ $roid+$i }})">
                                    <option></option></select>
                            </td>
                            <td>
                                <input class="form-control" type="number" id="amount_dr_{{ $roid+$i }}" name="amount_dr[]" step="any" autocomplete="off" min="0" onchange="calc_change({{ $roid+$i }})" onkeypress="all_popup_fun({{$roid+$i}})" value="{{ @$editDataList[$roid+$i - 1]->amount_dr }}">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="amount_cr_{{ $roid+$i }}" name="amount_cr[]" step="any" autocomplete="off" min="0" onchange="calc_change({{ $roid+$i }})" onkeypress="all_popup_fun({{$roid+$i}})" value="{{ @$editDataList[$roid+$i - 1]->amount_cr }}">
                            </td>
                            <td>
                                <input class="form-control class_remarks" type="text" id="remarks_{{ $roid+$i }}" name="remarks[]" autocomplete="off" value="{{ @$editDataList[$roid+$i - 1]->remarks }}" onchange="add_narration({{ $roid+$i }})">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="dealid_{{ $roid+$i }}" name="dealid[]" autocomplete="off" value="{{ @$editDataList[$roid+$i - 1]->dealid }}">
                            </td>
                                <td class="text-right"><input type="checkbox" name="vat_account[]" id="vat_account_{{ $roid+$i }}" style="display: none;" /></td>
                        </tr>

                        @endfor


                    </tbody>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <?php $dr='0.00'; $cr='0.00';
                                if (isset($editDataList)) {
                                    if (count($editDataList) > 0) {
                                        $dr = $editDataList->sum('amount_dr');
                                        $cr = $editDataList->sum('amount_cr');
                                    }
                                }
                            ?>
                            <th class="sstablefoot"><b><label id="dr_total">{{ $dr }}</label></b></th>
                            <th class="sstablefoot"><b><label id="cr_total">{{ $cr }}</label></b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>

<script>
    function vat_account_checked(roid) {
    const checkbox = $('#vat_account_' + roid);

    if (checkbox.is(':checked')) {
        const targetRoid1 = parseInt(roid) + 2;
        const targetSelect1 = $('#account_id_' + targetRoid1);

        const targetRoid2 = parseInt(roid) + 3;
        const targetSelect2 = $('#account_id_' + targetRoid2);
        
        const selectedText = $('#account_id_' + roid + ' option:selected').text();     
        const selectedVal = $('#account_id_' + roid).val();
        
        const vatRate = $('#company_vat_rate').val();
        const vat_account_val = $('#vat_account_val').val();
        const vat_account_text = $('#vat_account_text').val();

        const  mainAmt = $('#amount_dr_' + roid).val();
        const vr = 100 + parseFloat(vatRate || 0);
        const  vatAmt = mainAmt*vatRate/vr;

        $('#amount_dr_' + targetRoid1).val(vatAmt);
        $('#amount_cr_' + targetRoid2).val(vatAmt);
        $('#remarks_' + targetRoid1).val("VAT paid on "+selectedText);
        $('#remarks_' + targetRoid2).val("VAT paid on "+selectedText);


        if (targetSelect1.length) {
            const option = new Option(vat_account_text, vat_account_val, true, true);
            targetSelect1.append(option).trigger('change');
        } else {
            console.warn('Select not found for roid:', targetRoid1);
        }
        if (targetSelect2.length) {
            const option = new Option(selectedText, selectedVal, true, true);
            targetSelect2.append(option).trigger('change');
        } else {
            console.warn('Select not found for roid:', targetRoid2);
        }
    } else{
        const targetRoid1 = parseInt(roid) + 2;
        const targetRoid2 = parseInt(roid) + 3;
        $('#account_id_' + targetRoid1).val(null).trigger('change');
        $('#account_id_' + targetRoid1).empty();
        $('#account_id_' + targetRoid2).val(null).trigger('change');
        $('#account_id_' + targetRoid2).empty();        
        $('#amount_dr_' + targetRoid1).val('');
        $('#amount_cr_' + targetRoid2).val('');
        $('#remarks_' + targetRoid1).val('');
        $('#remarks_' + targetRoid2).val('');
    }
}

    function account_id_change(roid) {
        const selectedText = $('#account_id_' + roid + ' option:selected').text();
        const checkbox = $('#vat_account_' + roid);

        if (selectedText.startsWith('ACC') || selectedText.startsWith('SACC')) {
            checkbox.show();
        } else {
            checkbox.hide().prop('checked', false);
        }
    }


$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_account_list_ajax") }}',
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

                <a data-modal-size="modal-md" data-target="#cr_popup_win" id="addCtrlJournalVoucherAdjest" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>

                <div style="display: none;">
                    @if (!isset($view))
                        <?php /* <button type="button" class="primary-btn small fix-gr-bg" id="addRowJV"><span
                                class="ti-plus pr-2"></span>@lang('lang.item')</button> */ ?>
                    @endif
                </div>

                <script>
                    function fn_addRow(id) {
                        var rownum = document.getElementById('jv-row-count').value;
                        if (id == rownum) {
                            document.getElementById('jv-row-count').value = (Number(rownum) + Number(1));
                            document.getElementById('addRowJV').click();
                        }
                    }

                    function calc_change(id) {
                        var dr = $('#amount_dr_' + id + '').val();
                        var cr = $('#amount_cr_' + id + '').val();
                        //$('#amount_cr_' + (id + 1) + '').val(dr);

                        calc_total();
                    }

                    function calc_total() {
                        var countrow = document.getElementById('jv-row-count').value;
                        var t1 = 0,
                            t2 = 0,
                            t3 = 0,
                            t4 = 0,
                            t5 = 0,
                            t6 = 0,
                            t7 = 0;
                        for (var i = 1; i <= countrow; i++) {
                            t1 += Number($('#amount_dr_' + i).val());
                            t2 += Number($('#amount_cr_' + i).val());
                        }
                        $('#dr_total').text(t1.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#cr_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                    }

                    function validete_entries(){
                        
                        var total_dr = $('#dr_total').text();
                        var total_cr = $('#cr_total').text();
                        if(total_dr == 0 && total_cr == 0){
                            alert("No data found!!");
                            return false;
                        }
                        if(total_dr != total_cr){
                            alert("Data missmatch!!");
                            return false;
                        }
                        var countrow = document.getElementById('jv-row-count').value;
                        for(i=1; i <= countrow; i++){
                            var val_acc = $('#account_id_' + i).val();
                            var val_dr = $('#amount_dr_' + i).val();
                            var val_cr = $('#amount_cr_' + i).val();
                            var val_rem = $('#remarks_' + i).val();
                            
                            if(val_acc != "" && (val_dr != "" || val_cr != "") && val_rem != ""){
                                $('#rowone' + i).removeAttr("style");
                            }
                            else if(val_acc == "" && val_dr == "" && val_cr == "" && val_rem == ""){
                                $('#rowone' + i).removeAttr("style");
                            }
                            else{
                                $('#rowone' + i).css("background-color", "#ffc7c7");
                                    return false;
                            }
                        }
                        return true;
                    }

                </script>


                <div class="col-lg-4 mb-4 mt-4">
                    @if (!isset($view))
                                    <button class="btn btn-primary" id="btnSubmit" onclick="return validete_entries()">
                                        <span class="ti-check"></span>
                                        @if (isset($editData))
                                            @lang('lang.update')
                                        @else
                                            @lang('lang.add')
                                        @endif @lang('Journal Voucher')
                                    </button>
                                @endif
                </div>
            </div>


            {{ Form::close() }}

        </div>


    </div>

    <input type="hidden" id="account_type" value="0" />

    <input type="hidden" id="add_url" value="" />
    <input type="hidden" id="delete_url" value="" />

    
    <script>
        function activate_button() {        
        $("#addCtrlJournalVoucherAdjest").prop("disabled", false);
        }


    function all_popup_fun(id){
        $('#amount_dr_'+id+', #amount_cr_'+id+'').keypress(function(e) {
            var key = e.which;            
            if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();                
                $('#br_account_id').val(br_account_id);
                var acc_name =  $('#account_id_'+id+' option:selected').text();
                var acc_type=0;
                if(acc_name.indexOf('SUP') > -1) {
                    $('#account_type').val('SUP');
                    $('#add_url').val('payables-outstanding-store-temp');
                    $('#delete_url').val('payables-outstanding-store-temp-delete');
                    acc_type = 1;
                    if($('#amount_cr_'+id+'').val() > 0){
                        acc_type = 4;
                    }
                }
                if(acc_name.indexOf('CUS') > -1) {
                    $('#account_type').val('CUS');
                    $('#add_url').val('receivable-outstanding-store-temp');
                    $('#delete_url').val('receivable-outstanding-store-temp-delete');
                    acc_type = 2;
                    if($('#amount_dr_'+id+'').val() > 0){
                        acc_type = 3;
                    }
                }
                
                // if(acc_name.indexOf('ACC') > -1 || acc_name.indexOf('SAC') > -1) {
                //     $('#atag_dealId_'+id).click();
                // }

                var br_account = $('#amount_cr_'+id+'').val();
                if(br_account==""){
                    br_account = $('#amount_dr_'+id+'').val();
                }
                $('#bi_cheque_amount').val(br_account).focus();
                $('#bi_cheque_amount').focus();
                if(acc_type == 1 || acc_type == 2){
                    $("#addCtrlJournalVoucherAdjest").click();
                    $("#addCtrlJournalVoucherAdjest").prop("disabled", true);
                }
                if(acc_type == 3){
                    $("#btnModalAdjustment").click();
                    $('#adj_siv_amount').val($('#amount_dr_'+id+'').val());
                    $('#adj_account_id').val(br_account_id);
                    $('#adj_account_id_amount').val($('#amount_dr_'+id+'').val());
                    get_customer_adjustment_list(br_account_id);
                    //$("#btnModalAdjustment").prop("disabled", true);
                }
                if(acc_type == 4){
                    $("#btnModalPaymentAdjustment").click();
                    $('#adj_siv_amount').val($('#amount_cr_'+id+'').val());
                    $('#adj_account_id').val(br_account_id);
                    $('#adj_account_id_amount').val($('#amount_cr_'+id+'').val());
                    get_supplier_adjustment_list(br_account_id);
                    //$("#btnModalAdjustment").prop("disabled", true);
                }
                return false;                
            }
        });
        return false;
    }

    function dr_popup_fun(id)
    {
        $('#amount_dr_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();
                var acc_name =  $('#account_id_'+id+' option:selected').text();
                var acc_type=0;

                if(acc_name.indexOf('SUP') > -1) {
                    $('#account_type').val('SUP');
                    $('#add_url').val('payables-outstanding-store-temp');
                    $('#delete_url').val('payables-outstanding-store-temp-delete');
                    acc_type = 1;
                    var br_account = $('#amount_dr_'+id+'').val();
                } 
                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjest").click();
                        $("#addCtrlJournalVoucherAdjest").prop("disabled", true);
                    }
                }
                else{ alert("Amount Missing") }
                return false;
            }
        });
        return false;
    }
    function cr_popup_fun(id)
    {
        $('#amount_cr_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();
                var acc_name =  $('#account_id_'+id+' option:selected').text();
                var acc_type=0;
                
                if(acc_name.indexOf('CUS') > -1) {
                    $('#account_type').val('CUS');
                    $('#add_url').val('receivable-outstanding-store-temp');
                    $('#delete_url').val('receivable-outstanding-store-temp-delete');
                    acc_type = 2;
                    var br_account = $('#amount_cr_'+id+'').val();
                }

                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjest").click();
                        $("#addCtrlJournalVoucherAdjest").prop("disabled", true);
                    }
                }
                else{ alert("Amount Missing") }
                return false;
            }
        });
        return false;
    }

    $('#journalvoucher-create-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
    });
    function get_customer_adjustment_list(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-receipt-adjustment-list-jv') }}";
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
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){

                            var amt = (dataResult['data'][i].amount - dataResult['data'][i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');

                            getSelectedRows +="<tr>\
                                <td class='border'>"+dataResult['data'][i].doc_date+"</td>\
                                <td class='border'>"+dataResult['data'][i].doc_number+"</td>\
                                <td class='border text-right'>"+amt+"</td>\
                                <td class='border'>"+dataResult['data'][i].remarks+"</td>\
                                <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+dataResult['data'][i].doc_number+"' class='form-control text-right' onclick=set_adjust('"+dataResult['data'][i].amount+"','"+dataResult['data'][i].doc_number+"') /></td>\
                                <input type='hidden' name='receiptno[]' value='"+dataResult['data'][i].doc_number+"'/>\
                                <input type='hidden' name='set_amt_act[]' value='"+amt+"'/>\
                                </tr>";
                        }

                        $('#table_jv_receipt_list tbody').empty();
                        $("#table_jv_receipt_list tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#table_jv_receipt_list tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function get_supplier_adjustment_list(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-payment-adjustment-list-jv') }}";
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
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            var amt = (dataResult['data'][i].amount - dataResult['data'][i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');


                            getSelectedRows +="<tr>\
                                <td class='border'>"+dataResult['data'][i].doc_date+"</td>\
                                <td class='border'>"+dataResult['data'][i].doc_number+"</td>\
                                <td class='border text-right'>"+amt+"</td>\
                                <td class='border'>"+dataResult['data'][i].remarks+"</td>\
                                <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+dataResult['data'][i].doc_number+"' class='form-control text-right' onclick=set_adjust('"+dataResult['data'][i].amount+"','"+dataResult['data'][i].doc_number+"') /></td>\
                                <input type='hidden' name='paymentno[]' value='"+dataResult['data'][i].doc_number+"'/>\
                                <input type='hidden' name='set_amt_act[]' value='"+amt +"'/>\
                                </tr>";                                            
                                
                        }

                        $('#table_jv_payment_list tbody').empty();
                        $("#table_jv_payment_list tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#table_jv_payment_list tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function add_receipt_adjustment(){
        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('add-receipt-adjustment-list-jv') }}";

        const set_amt = [];
        document.querySelectorAll('input[name="set_amt[]"]').forEach(input => {
            set_amt.push(input.value);
        });
        const receiptno = [];
        document.querySelectorAll('input[name="receiptno[]"]').forEach(input => {
            receiptno.push(input.value);
        });
        const set_amt_act = [];
        document.querySelectorAll('input[name="set_amt_act[]"]').forEach(input => {
            set_amt_act.push(input.value);
        });
        $.ajax({
            url: action,
            type: "POST",
             data: {
                _token: '{{ csrf_token() }}',
                set_amt: set_amt,
                receiptno: receiptno,
                set_amt_act: set_amt_act,
                account_id: $('#adj_account_id').val(),
                account_amount: $('#adj_account_id_amount').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if(dataResult == "SUCCESS"){
                    alert("Adjustment Added Successfully");
                }
                else{
                    alert("Error: "+dataResult);
                }
                
            }
        });
        $('#ModalAdjustmentClose').click();
        $("#loading_bg").css("display", "none");
    }
    function add_payment_adjustment(){
        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('add-payment-adjustment-list-jv') }}";

        const set_amt = [];
        document.querySelectorAll('input[name="set_amt[]"]').forEach(input => {
            set_amt.push(input.value);
        });
        const paymentno = [];
        document.querySelectorAll('input[name="paymentno[]"]').forEach(input => {
            paymentno.push(input.value);
        });
        const set_amt_act = [];
        document.querySelectorAll('input[name="set_amt_act[]"]').forEach(input => {
            set_amt_act.push(input.value);
        });
        $.ajax({
            url: action,
            type: "POST",
             data: {
                _token: '{{ csrf_token() }}',
                set_amt: set_amt,
                paymentno: paymentno,
                set_amt_act: set_amt_act,
                account_id: $('#adj_account_id').val(),
                account_amount: $('#adj_account_id_amount').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if(dataResult == "SUCCESS"){
                    alert("Adjustment Added Successfully");
                }
                else{
                    alert("Error: "+dataResult);
                }
                
            }
        });
        $('#ModalPaymentAdjustmentClose').click();
        $("#loading_bg").css("display", "none");
    }
</script>
<script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_siv_amount']").val());
    let currentAdjusted = 0;

    // Sum up all currently adjusted values
    $("input[id^='set_amt_']").each(function () {
        let val = parseFloat($(this).val());
        if (!isNaN(val)) {
            currentAdjusted += val;
        }
    });

    let remaining = maxAdjustable - currentAdjusted;

    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    // Check how much is available for this line
    let adjustAmount = parseFloat(amt);
    if (adjustAmount > remaining) {
        adjustAmount = remaining;
    }

    $('#set_amt_' + id).val(adjustAmount);

    // Optional: update hidden adjusted total
    $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>
    
    <form id="ta" >
    <div class="modal fade admin-query" id="cr_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Selection</h4>
                    <button class="close" data-dismiss="modal" type="button" onclick="activate_button()">
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

                                        //var d = '';
                                        //for(i=1; i<=num_tot_amt; i++){
                                        //    if($('#bi_amount_'+i).val() !="" && $('#bi_amount_'+i).val() != 0){
                                        //        if(d==''){
                                        //            d = $('#bi_doc_no_'+i).val();}
                                        //        else{
                                        //            d += ', '+$('#bi_doc_no_'+i).val(); }
                                        //    } }
                                        //    var re_id = $('#narration_row_id').val();
                                        //    $('#remarks_'+re_id).val(d);
                                }
                            }
                        </script>


                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="btn btn-primary fix-gr-bg" data-dismiss="modal" type="button" onclick="activate_button()" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="btn btn-success fix-gr-bg" type="button" value="Save" onclick="validateAttachForm()" />
    
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


<a id="btnModalAdjustment" data-toggle="modal" data-target="#ModalAdjustment"></a>
<a id="btnModalPaymentAdjustment" data-toggle="modal" data-target="#ModalPaymentAdjustment"></a>
<input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value="0">>
<input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value="0"/>
<input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted" value="0"/>
<input type="hidden" id="adj_account_id">
<input type="hidden" id="adj_account_id_amount">
<!-- Modal Receipt Adjustment-->
    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Unadjusted List</h5>
                    <button class="close" id="ModalAdjustmentClose" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="table_jv_receipt_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Receipt No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-right">                                            
                                            <button class="btn btn-success" type="button" onclick="add_receipt_adjustment()">
                                                Add Adjusement
                                            </button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- Modal Receipt Adjustment-->
    
<!-- Modal Payment Adjustment-->
    <div class="modal fade" id="ModalPaymentAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Unadjusted List</h5>
                    <button class="close" id="ModalPaymentAdjustmentClose" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="table_jv_payment_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-right">                                            
                                            <button class="btn btn-success" type="button" onclick="add_payment_adjustment()">
                                                Add Adjusement
                                            </button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- Modal Payment Adjustment-->

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

        var action = "{{ URL::to('add-journal-voucher-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', 0);
        formData.append('att_date', $('#att_date').val()); // Append other form data
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
        $('#att_cust_name').text(" " + $('#doc_number').val());

        var action = "{{ URL::to('view-journal-voucher-attachment') }}";
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
        var action = "{{ URL::to('delete-journal-voucher-attachment') }}";
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
            $("#loading_bg").css("display", "block");
            var numRows = $('.row_ctrl').length;
            for(i=1; i<=numRows; i++){
                if($("#bi_amount_"+i).val() != "" && $("#bi_amount_"+i).val() != 0){
                    validateBankBookAdjestForm(i);
                }
            }
            alert("Added!!");
            
            //generate_narration_fa($('#narration_row_id').val());
            //$('#remarks_'+$('#narration_row_id').val()).val($('#narration').val());
    
            $("#btn_close2").click();
            $("#loading_bg").css("display", "none");
        }

        function delete_before_update() {
            var doc_number = $("#doc_number").val();
            var account_id = $('#br_account_id').val();
            var url = $('#url').val();
            var url2 = $('#delete_url').val();
            $.ajax({
                url: url + '/' + url2,
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
            
            if($('#account_type').val()=="CUS"){
                transaction_type = 'journalreceipt';
            } else {
                transaction_type = 'journalpayment';
            }

            //alert(transaction_type);
            var entry_type =2; //1 Debit, 2 Credit
            var process_id = $('#process_id').val();
        
            
        
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
            var url2 = $('#add_url').val();
            //alert(url2);
        
            $.ajax({
                    url: url + '/' + url2,
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
        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                    <input type="file" id="excel-file" /> (<a href="{{ url('public/uploads/product_upload/jv_items_import_sample.xlsx') }}" target="_blank">Sample File</a>)
                    <br />
                    <table class="table table-bordered table-striped" id="jv-table-import" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:20px;">@lang('#')</th>
                                <th style="width:100px;">@lang('Account code')</th>
                                <th style="width:250px;">@lang('Account Name')</th>
                                <th style="width:100px;">@lang('Debit')</th>
                                <th style="width:100px;">@lang('Credit')</th>
                                <th style="width:200px;">@lang('Narration')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="closeimport" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveImportDataBtn">Add</button>
        </div>
      </div>
    </div>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

<script>
    function open_import(id){
        $("#profoma_id").val(id);
        $('#btn_import_modal').click();
    }

  // Function to read the Excel file
  document.getElementById('excel-file').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const data = e.target.result;
        const workbook = XLSX.read(data, { type: 'binary' });

        // Assuming the Excel data is in the first sheet
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        // Call function to add rows to the table
        addRowsToTable(rows);
      };
      reader.readAsBinaryString(file);
    }
  });

  // Function to insert data into the table
  function addRowsToTable(rows) {
    const tableBody = document.querySelector('#jv-table-import tbody');
    tableBody.innerHTML = ''; // Clear any existing rows

    rows.forEach((row, index) => {
      if (index === 0) return; // Skip the header row if it exists

      const tr = document.createElement('tr');
      tr.id = `rowone${index}`;

      // Serial number column
      const td0 = document.createElement('td');
      td0.textContent = index; // Serial number (1, 2, 3, ...)
      tr.appendChild(td0);

      // Account code select
      const td1 = document.createElement('td');
      const accountIdInput = document.createElement('input');
      accountIdInput.type = 'text';
      accountIdInput.classList.add('form-control');
      accountIdInput.name = 'account_id[]';
      accountIdInput.id = `account_id_${index}`;
      accountIdInput.value = row[0]; // Assuming amount DR is in the second column
      td1.appendChild(accountIdInput);
      tr.appendChild(td1);

      // Account Name select
      const td2 = document.createElement('td');
      const accountNameInput = document.createElement('input');
      accountNameInput.type = 'text';
      accountNameInput.classList.add('form-control');
      accountNameInput.name = 'account_name[]';
      accountNameInput.id = `account_name_${index}`;
      accountNameInput.value = row[1]; // Assuming amount DR is in the second column
      td2.appendChild(accountNameInput);
      tr.appendChild(td2);

      // Amount DR input
      const td3 = document.createElement('td');
      const amountDrInput = document.createElement('input');
      amountDrInput.type = 'number';
      amountDrInput.classList.add('form-control');
      amountDrInput.name = 'amount_dr[]';
      amountDrInput.id = `amount_dr_${index}`;
      amountDrInput.value = row[2]; // Assuming amount DR is in the second column
      td3.appendChild(amountDrInput);
      tr.appendChild(td3);

      // Amount CR input
      const td4 = document.createElement('td');
      const amountCrInput = document.createElement('input');
      amountCrInput.type = 'number';
      amountCrInput.classList.add('form-control');
      amountCrInput.name = 'amount_cr[]';
      amountCrInput.id = `amount_cr_${index}`;
      amountCrInput.value = row[3]; // Assuming amount CR is in the third column
      td4.appendChild(amountCrInput);
      tr.appendChild(td4);

      // Remarks input
      const td5 = document.createElement('td');
      const remarksInput = document.createElement('input');
      remarksInput.type = 'text';
      remarksInput.classList.add('form-control', 'class_remarks');
      remarksInput.name = 'remarks[]';
      remarksInput.id = `remarks_${index}`;
      remarksInput.value = row[4]; // Assuming remarks are in the fourth column
      td5.appendChild(remarksInput);
      tr.appendChild(td5);

      tableBody.appendChild(tr);
    });
  }

  $('#saveImportDataBtn').click(function() {
    const tableData = [];
    $('#jv-table-import tbody tr').each(function() {
        const row = {
            account_id: $(this).find('input[name="account_id[]"]').val(),
            account_name: $(this).find('input[name="account_name[]"]').val(),
            amount_dr: $(this).find('input[name="amount_dr[]"]').val(),
            amount_cr: $(this).find('input[name="amount_cr[]"]').val(),
            remarks: $(this).find('input[name="remarks[]"]').val()
        };
        tableData.push(row);
    });

    var action = "{{ URL::to('journalvoucher-import') }}";

    $.ajax({
        url: action,
        type: "POST",
        data: {
            data: JSON.stringify(tableData), // Send the table data as a JSON string
            _token: '{{ csrf_token() }}',     // Include CSRF token for Laravel
        },
        cache: false,
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Error saving data.');
        }
    });
});
</script>
</div>
@endsection

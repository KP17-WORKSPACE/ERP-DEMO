    <?php try { ?>

    
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
          

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}">
            <input type="hidden" id="process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            
            <input type="hidden" id="company_vat_rate" value="{{ $company->company_vat_rate }}">
            <input type="hidden" id="vat_account_val" value="{{ $vat_account_val }}">
            <input type="hidden" id="vat_account_text" value="{{ $vat_account_text }}">
            <input type="hidden" id="vat_account_code" value="{{ $vat_account_code }}">

                <input type="hidden" value="{{ @$editData->id }}" name="cust_id">



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New ({{  @App\SysHelper::get_new_code('sys_journalvoucher','JV','doc_number') }})
        </h4>
        <div class="purchase-order-content-header-right">
            {{-- <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="ico icon-outline-upload text-success"></i> Import
            </button> --}}

            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>


                 <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal"><i
                                class="ico icon-outline-upload title-15 me-2"></i> Import</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#attachment_popup_win" onclick="view_attachment()"><i class="ico icon-bold-paperclip title-15 me-2"></i> Attachment</a></li>

                </ul>
            </div>

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
                                        <div class="col-1-5">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">
                                                <input class="form-control {{ $errors->has('doc_number') ? 'is-invalid' : ' ' }}" readonly
                            type="text" id="doc_number" name="doc_number"
                            value="{{  @App\SysHelper::get_new_code('sys_journalvoucher','JV','doc_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-1-5">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                          

                                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                            name="doc_date" value="{{ date('d/m/Y') }}">
                                            </div>
                                        </div>
                                        <div class="col-1-5">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                            <select
                            class="form-control js-example-basic-single {{ $errors->has('currency') ? ' is-invalid' : '' }}"
                            name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}"
                                    @if($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                                            </div>
                                        </div>
                                        <div class="col-2-5">
                                            <label class="form-label">Created By:</label>
                                            <input
                                    class="form-control"
                                    type="text" name="created_by" autocomplete="off" id="created_by"
                                    value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}"
                                    readonly>
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-group">
                                                <input
                            class="dynamicstxt primary-input form-control {{ $errors->has('narration') ? ' is-invalid' : '' }}"
                            type="text" name="narration" autocomplete="off"
                            value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                            id="narration" required>
                            
                <input type="hidden" name="deal_id" id="deal_id" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    
                            <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                           
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="300px">@lang('Account Name')
                                                <a class="icon icon-outline-book text-dark"      data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountModal"></a>
                                                  <a class="icon icon-outline-book text-dark"      data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Sub Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountSubModal"></a>
                                                <div class="resizer"></div></th>
                                            <th class="resizable text-center" width="97px">@lang('Debit')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="97px">@lang('Credit')<div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="90px">@lang('Deal ID')<div class="resizer"></div></th>
                                            @if($company->vat_percentage!=0)
                                                <th width="20px"></th>
                                            @endif

                                        </tr>
                                    </thead>
                                    <tbody>

                                        

                                        <?php
                                    $setroid=0;
                                    if(isset($editDataList))
                                    {
                                        if(count($editDataList)>0)
                                        {
                                            $setroid=count($editDataList)+1;
                                        }
                                    }
                                    $roid=1;
                                    ?>
                                    
                                    @for ($roid= 1;  $roid < $setroid ; $roid++)
                                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]">
                                                    <option value="{{ @$editDataList[$roid-1]->account_id }}" data-code="{{ @$editDataList[$roid-1]->account_code }}">{{ @$editDataList[$roid-1]->account_code }} - {{ @$editDataList[$roid-1]->account_name }}</option>
                                                </select>
                                            </td> 
                                            <td>
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_dr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->amount_dr,2,'.','') }}" onchange="update_totals()">
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_cr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->amount_cr,2,'.','') }}" onchange="update_totals()">
                                            </td>
                                            <td><input type="text" class="form-control text-start" name="remarks[]" value="{{ @$editDataList[$roid-1]->remarks }}"></td>
                                            <td><input type="text" class="form-control text-center" name="dealid[]" value="{{ @$editDataList[$roid-1]->dealid }}"></td>
                                            @if($company->vat_percentage!=0)
                                            <td><input type="checkbox" class="form-control" name="vat_account[]" style="display: none;" onchange="vat_account_checked(this)" /></td>
                                            @endif
                                        </tr>
                                    @endfor
                                    <script>
                                    update_totals();
                                    </script>
                                    
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                            <select class="form-control" name="account_id[]">
                                                <option value=""></option>
                                            </select>
                                            </td>
                                            <td>                                                                    
                                                <input class="form-control text-end" type="text" step="any" name="amount_dr[]" autocomplete="off" onblur="formatCurrency(this)"  onchange="update_totals()">
                                            </td>
                                            <td>                                                                    
                                                <input class="form-control text-end" type="text" step="any" name="amount_cr[]" autocomplete="off" onblur="formatCurrency(this)"  onchange="update_totals()">
                                            </td>
                                            <td><input type="text" class="form-control  text-start" name="remarks[]"></td>
                                            <td>                                                                    
                                                <input class="form-control text-center" type="text" name="dealid[]" autocomplete="off">
                                            </td>
                                            @if($company->vat_percentage!=0)
                                            <td><input type="checkbox" class="form-control" name="vat_account[]" onchange="vat_account_checked(this)" 
                                                  style="appearance: checkbox; -webkit-appearance: checkbox; border: solid 1px #000000; height: 18px; display: none;"/></td>
                                            @endif
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" scope="col" >Total</th>
                                            <th class="text-end"><label id="dr_total" >0</label></th>
                                            <th class="text-end"><label id="cr_total" >0</label></th>
                                            <th colspan="2" class="text-end" scope="col" ></th>
                                            @if($company->company_vat_rate!=0)
                                            <th class="text-end" scope="col" ></th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                            
                            {{ Form::close() }}
                            
                            @include('backEnd.chart-of-accounts.accountadd_form')
                            @include('backEnd.chart-of-accounts.accountsubadd_form')



<script>
$(document).on("change", 'input[name="remarks[]"], input[name="dealid[]"], input[name="amount_dr[]"]', function () {
    let $row = $(this).closest('tr');
    let dealidVal = $row.find('input[name="dealid[]"]').val()?.trim() || "";
    let $remarks = $row.find('input[name="remarks[]"]');

    // If dealid was changed, append it inside remarks (avoid duplicate append)
    if ($(this).is('input[name="dealid[]"]') && dealidVal) {
        let remarksVal = $remarks.val().trim();
        if (!remarksVal.includes("(Deal Id: " + dealidVal + ")")) {
            $remarks.val(remarksVal + (remarksVal ? " " : "") + "(Deal Id: " + dealidVal + ")");
        }
    }

    // Now rebuild narration from all rows
    let narrations = [];
    $('table tr').each(function () {
        let row = $(this);
        let amountCr = row.find('input[name="amount_dr[]"]').val()?.trim() || "";
        let remarks = row.find('input[name="remarks[]"]').val()?.trim() || "";

        if (remarks !== "" && amountCr !== "" && parseFloat(amountCr) !== 0) {
            if (!narrations.includes(remarks)) {
                narrations.push(remarks);
            }
        }
    });

    $("#narration").val(narrations.join(", "));
});


function account_id_change(selectElement, forceCode) {
    const $row = $(selectElement).closest('tr');
    let code = forceCode || "";
    
    if (!code) {
        // 1. Try to get code from Select2 data
        if ($(selectElement).hasClass('select2-hidden-accessible')) {
            const data = $(selectElement).select2('data');
            if (data && data.length > 0) {
                code = data[0].account_code || "";
            }
        }
    }
    
    // 2. Try to get code from option's data-code attribute
    if (!code) {
        code = $(selectElement).find("option:selected").attr('data-code') || "";
    }
    
    // 3. Fallback: Parse from text if code wasn't found directly
    if (!code) {
        code = $(selectElement).find("option:selected").text() || "";
    }
    
    code = code.toUpperCase();
    const $checkbox = $row.find('input[name="vat_account[]"]');

    if (code.includes('ACC') || code.includes('SACC')) {
        $checkbox.show();
    } else {
        $checkbox.hide().prop('checked', false).trigger('change');
    }
}

$(function () {
    $(document).on('change', 'select[name="account_id[]"]', function () {
        account_id_change(this);
    });
});

function vat_account_checked(checkboxElement) {
    const checkbox = $(checkboxElement);
    const currentRow = checkbox.closest('tr');
    
    // Find the next adjacent rows relative to the current row
    const targetRow1 = currentRow.next().next(); 
    const targetRow2 = targetRow1.next();        
    
    const targetSelect1 = targetRow1.find('select[name="account_id[]"]');
    const targetSelect2 = targetRow2.find('select[name="account_id[]"]');

    if (checkbox.is(':checked')) {
        const selectedSelect = currentRow.find('select[name="account_id[]"]');
        let selectedText = "";
        if (selectedSelect.hasClass('select2-hidden-accessible')) {
            const data = selectedSelect.select2('data');
            if (data && data.length > 0) {
                selectedText = data[0].text || "";
            }
        }
        if (!selectedText) {
            selectedText = selectedSelect.find("option:selected").text() || "";
        }
        const selectedVal = selectedSelect.val();
        
        const vatRate = $('#company_vat_rate').val();
        const vat_account_val = $('#vat_account_val').val();
        const vat_account_text = $('#vat_account_text').val();

        const mainAmt = parseFloat(currentRow.find('input[name="amount_dr[]"]').val()?.replace(/,/g, '') || 0);
        const vr = 100 + parseFloat(vatRate || 0);
        const vatAmt = (mainAmt * vatRate / vr).toFixed(2);

        targetRow1.find('input[name="amount_dr[]"]').val(vatAmt);
        targetRow2.find('input[name="amount_cr[]"]').val(vatAmt);
        targetRow1.find('input[name="remarks[]"]').val("VAT paid on " + selectedText);
        targetRow2.find('input[name="remarks[]"]').val("VAT paid on " + selectedText);

        if (targetSelect1.length) {
            if (targetSelect1.hasClass("select2-hidden-accessible")) {
                try { targetSelect1.select2('destroy'); } catch(e) {}
            }
            if (window.initAccountSelect2) {
                window.initAccountSelect2(targetSelect1);
            }
            const option = new Option(vat_account_text, vat_account_val, true, true);
            targetSelect1.append(option).trigger('change');
        } else {
            console.warn('Select not found for targetRow1');
        }
        if (targetSelect2.length) {
            if (targetSelect2.hasClass("select2-hidden-accessible")) {
                try { targetSelect2.select2('destroy'); } catch(e) {}
            }
            if (window.initAccountSelect2) {
                window.initAccountSelect2(targetSelect2);
            }
            const option = new Option(selectedText, selectedVal, true, true);
            targetSelect2.append(option).trigger('change');
        } else {
            console.warn('Select not found for targetRow2');
        }
    } else {
        if (targetSelect1.length) {
            targetSelect1.val(null).trigger('change');
            targetSelect1.empty();
        }
        if (targetSelect2.length) {
            targetSelect2.val(null).trigger('change');
            targetSelect2.empty();
        }
        targetRow1.find('input[name="amount_dr[]"]').val('');
        targetRow2.find('input[name="amount_cr[]"]').val('');
        targetRow1.find('input[name="remarks[]"]').val('');
        targetRow2.find('input[name="remarks[]"]').val('');
    }
}
</script>




<script>
$(document).ready(function () {

    window.activate_button = function () {
        $("#addCtrlJournalVoucherAdjest").prop("disabled", false);
    };

    // ENTER on debit/credit amount fields
    $(document).on('keydown', 'input[name="amount_dr[]"], input[name="amount_cr[]"]', function (e) {

        if (e.key !== 'Enter' && e.which !== 13) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        console.log("Enter pressed in amount field");

        var $row = $(this).closest('tr');

        var br_account_id = $row.find('select[name="account_id[]"]').val();

        if (!br_account_id) {
            console.log("Account id not found");
            return false;
        }

        // --- NEW CHECK FOR YES_NO ACCOUNT ---
        var isDebit = $(this).is('input[name="amount_dr[]"]');
        var selectedOption = $row.find('select[name="account_id[]"] option:selected');
        var yesNo = selectedOption.attr('data-yes-no');

        if (isDebit && yesNo == '1') {
            const amountVal = $(this).val();
            $('#ad_row_index').val($row.index());
            $('#ad_account_id').val(br_account_id);
            $('#ad_amount').val(amountVal);
            
            // Open Modal
            $('#accrual_deferral_modal').appendTo('body').modal('show');
            return false;
        }

        $('#br_account_id').val(br_account_id);

        var $accountSelect = $row.find('select[name="account_id[]"]');
        var selectedOption = $accountSelect.find('option:selected');
        var selectedData = [];
        if ($accountSelect.hasClass('select2-hidden-accessible')) {
            selectedData = $accountSelect.select2('data') || [];
        }

        var selectedAccount = selectedData.length ? selectedData[0] : {};
        var accountGroupRaw = selectedAccount.group;
        if (accountGroupRaw === undefined || accountGroupRaw === null || accountGroupRaw === '') {
            accountGroupRaw = selectedOption.data('group');
        }
        var accountGroup = parseInt(accountGroupRaw, 10);

        var acc_name = selectedOption.text();
        var acc_type = 0;

        console.log("Account:", br_account_id, acc_name);

        var amountDr = parseFloat(String($row.find('input[name="amount_dr[]"]').val() || '').replace(/,/g, '')) || 0;
        var amountCr = parseFloat(String($row.find('input[name="amount_cr[]"]').val() || '').replace(/,/g, '')) || 0;

        if (accountGroup === 2 || acc_name.indexOf('SUP') > -1) {
            $('#account_type').val('SUP');
            $('#add_url').val('payables-outstanding-store-temp');
            $('#delete_url').val('payables-outstanding-store-temp-delete');

            acc_type = 1;

            if (amountCr > 0) {
                acc_type = 4;
            }
        }

        if (accountGroup === 1 || acc_name.indexOf('CUS') > -1) {
            $('#account_type').val('CUS');
            $('#add_url').val('receivable-outstanding-store-temp');
            $('#delete_url').val('receivable-outstanding-store-temp-delete');

            acc_type = 2;

            if (amountDr > 0) {
                acc_type = 3;
            }
        }

        var br_account = amountCr > 0 ? amountCr : amountDr;

        setBillWiseEnteredAmount(br_account);
        $('#bi_cheque_amount').focus();


        console.log("Account type:", acc_type);
        console.log("Amount:", br_account);

        if (acc_type == 1 || acc_type == 2) {
            $("#addCtrlJournalVoucherAdjest")
                .prop("disabled", false)
                .trigger("click")
                .prop("disabled", true);
        }

        if (acc_type == 3) {
            $("#btnModalAdjustment").trigger("click");

            $('#adj_siv_amount').val(amountDr);
            $('#adj_account_id').val(br_account_id);
            $('#adj_account_id_amount').val(amountDr);

            get_customer_adjustment_list(br_account_id);
        }

        if (acc_type == 4) {
            $("#btnModalPaymentAdjustment").trigger("click");

            $('#adj_siv_amount').val(amountCr);
            $('#adj_account_id').val(br_account_id);
            $('#adj_account_id_amount').val(amountCr);

            get_supplier_adjustment_list(br_account_id);
        }

        return false;
    });

    $('#journalvoucher-create-form').on('keydown keypress keyup', function (e) {
        var isEnter = e.key === 'Enter' || e.which === 13 || e.keyCode === 13;
        if (!isEnter) {
            return;
        }

        var isAmountInput =
            $(e.target).is('input[name="amount_dr[]"]') ||
            $(e.target).is('input[name="amount_cr[]"]');

        if (isAmountInput && e.type === 'keydown') {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        return false;
    });

});
</script>

<script>
function validateAttachForm() {
            $("#loading_bg").css("display", "block");
            if (!isBillWiseAdjustmentWithinLimit()) {
                $("#loading_bg").css("display", "none");
                alert("Adjustment amount cannot exceed entered amount.");
                return false;
            }
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
            if (!url2) {
                $("#loading_bg").css("display", "none");
                alert("Please open Bill Wise Selection from a customer or supplier account before saving.");
                return false;
            }

            console.log("url:", url + '/' + url2);
        
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
</script>

<script>
var jvAdjustmentDecimals = parseInt(@json(session('logged_session_data.decimal_point')), 10) || 2;

function parseAdjustNumber(value) {
    if (value === null || value === undefined) {
        return 0;
    }
    if (typeof value === 'number') {
        return value;
    }
    return parseFloat(String(value).replace(/,/g, '')) || 0;
}

function formatAdjustNumber(value) {
    return parseAdjustNumber(value).toLocaleString('en-US', {
        minimumFractionDigits: jvAdjustmentDecimals,
        maximumFractionDigits: jvAdjustmentDecimals
    });
}

function escapeJvAdjustmentText(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function renderJvAdjustmentRows(rows, tableSelector, docInputName) {
    var getSelectedRows = "";

    $.each(rows || [], function(i, row) {
        var availableRaw = parseAdjustNumber(row.amount) - parseAdjustNumber(row.adj_amount);
        if (availableRaw < 0) {
            availableRaw = 0;
        }
        var removedAmountRaw = parseAdjustNumber(row.removed_amount);
        if (availableRaw <= 0 && removedAmountRaw <= 0) {
            return true;
        }
        var inputId = tableSelector.replace('#', '') + '_set_amt_' + i;
        var docNumber = escapeJvAdjustmentText(row.doc_number);
        var docDate = typeof get_format_date === 'function' ? get_format_date(row.doc_date) : row.doc_date;

        getSelectedRows += "<tr class='js-adj-row' data-input='" + inputId + "' data-amt='" + availableRaw + "'>\
            <td class='border'>" + escapeJvAdjustmentText(docDate) + "</td>\
            <td class='border'>" + docNumber + "</td>\
            <td class='border text-right'>" + formatAdjustNumber(availableRaw) + "</td>\
            <td class='border'>" + escapeJvAdjustmentText(row.remarks) + "</td>\
            <td class='border text-right'><input type='text' name='set_amt[]' id='" + inputId + "' data-max-adjust='" + availableRaw + "' class='form-control text-right jv-adjustment-amount' onclick=\"set_adjust('" + availableRaw + "','" + inputId + "')\" value='" + (removedAmountRaw > 0 ? formatAdjustNumber(removedAmountRaw) : '') + "' /></td>\
            <input type='hidden' name='" + docInputName + "[]' value='" + docNumber + "'/>\
            <input type='hidden' name='set_amt_act[]' value='" + availableRaw + "'/>\
        </tr>";
    });

    $(tableSelector + ' tbody').empty().append(getSelectedRows);
    updateJvAdjustmentTotal($(tableSelector));
}

function collectJvAdjustmentInputs(tableSelector, docInputName) {
    var set_amt = [];
    var docNos = [];
    var set_amt_act = [];

    $(tableSelector).find('input[name="set_amt[]"]').each(function() {
        set_amt.push($(this).val());
    });
    $(tableSelector).find('input[name="' + docInputName + '[]"]').each(function() {
        docNos.push($(this).val());
    });
    $(tableSelector).find('input[name="set_amt_act[]"]').each(function() {
        set_amt_act.push($(this).val());
    });

    return { set_amt: set_amt, docNos: docNos, set_amt_act: set_amt_act };
}

function get_customer_adjustment_list(id) {
    $("#loading_bg").css("display", "block");
    $.ajax({
        url: "{{ URL::to('get-receipt-adjustment-list-jv') }}",
        type: "GET",
        data: { _token: '{{ csrf_token() }}', id: id },
        cache: false,
        success: function(dataResult) {
            dataResult = JSON.parse(dataResult);
            renderJvAdjustmentRows(dataResult['data'], '#table_jv_receipt_list', 'receiptno');
        },
        complete: function() {
            $("#loading_bg").css("display", "none");
        }
    });
}

function get_supplier_adjustment_list(id) {
    $("#loading_bg").css("display", "block");
    $.ajax({
        url: "{{ URL::to('get-payment-adjustment-list-jv') }}",
        type: "GET",
        data: { _token: '{{ csrf_token() }}', id: id },
        cache: false,
        success: function(dataResult) {
            dataResult = JSON.parse(dataResult);
            renderJvAdjustmentRows(dataResult['data'], '#table_jv_payment_list', 'paymentno');
        },
        complete: function() {
            $("#loading_bg").css("display", "none");
        }
    });
}

function add_receipt_adjustment() {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('add-receipt-adjustment-list-jv') }}";
    var payload = collectJvAdjustmentInputs('#table_jv_receipt_list', 'receiptno');

    $.ajax({
        url: action,
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            set_amt: payload.set_amt,
            receiptno: payload.docNos,
            set_amt_act: payload.set_amt_act,
            account_id: $('#adj_account_id').val(),
            account_amount: $('#adj_account_id_amount').val(),
        },
        cache: false,
        success: function(dataResult) {
            var dataResult = JSON.parse(dataResult);
            if (dataResult == "SUCCESS") {
                alert("Adjustment Added Successfully");
            } else {
                alert("Error: " + dataResult);
            }
        },
        complete: function() {
            $('#ModalAdjustmentClose').click();
            $("#loading_bg").css("display", "none");
        }
    });
}

function add_payment_adjustment() {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('add-payment-adjustment-list-jv') }}";
    var payload = collectJvAdjustmentInputs('#table_jv_payment_list', 'paymentno');

    $.ajax({
        url: action,
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            set_amt: payload.set_amt,
            paymentno: payload.docNos,
            set_amt_act: payload.set_amt_act,
            account_id: $('#adj_account_id').val(),
            account_amount: $('#adj_account_id_amount').val(),
        },
        cache: false,
        success: function(dataResult) {
            var dataResult = JSON.parse(dataResult);
            if (dataResult == "SUCCESS") {
                alert("Adjustment Added Successfully");
            } else {
                alert("Error: " + dataResult);
            }
        },
        complete: function() {
            $('#ModalPaymentAdjustmentClose').click();
            $("#loading_bg").css("display", "none");
        }
    });
}

function updateJvAdjustmentTotal($scope) {
    var adjusted = 0;
    var $inputs = $scope && $scope.length ? $scope.find("input[name='set_amt[]']") : $("input[name='set_amt[]']");

    $inputs.each(function () {
        adjusted += parseAdjustNumber($(this).val());
    });
    $("input[name='adj_siv_amount_adjusted']").val(formatAdjustNumber(adjusted));
}

function clampJvAdjustmentInput($input, shouldFormat) {
    var $scope = $input.closest('table');
    var maxAdjustable = parseAdjustNumber($("input[name='adj_siv_amount']").val());
    var rowMax = parseAdjustNumber($input.data('max-adjust'));
    var otherAdjusted = 0;

    $scope.find("input[name='set_amt[]']").not($input).each(function () {
        otherAdjusted += parseAdjustNumber($(this).val());
    });

    var remaining = Math.max(0, maxAdjustable - otherAdjusted);
    var value = parseAdjustNumber($input.val());
    value = Math.min(value, rowMax, remaining);
    if (value < 0) {
        value = 0;
    }
    $input.val(shouldFormat ? formatAdjustNumber(value) : value);
    updateJvAdjustmentTotal($scope);
}

function set_adjust(amt, inputId) {
    var $input = $('#' + inputId);
    if (!$input.length) {
        return;
    }

    var $scope = $input.closest('table');
    var rowMax = parseAdjustNumber(amt);
    var maxAdjustable = parseAdjustNumber($("input[name='adj_siv_amount']").val());
    var otherAdjusted = 0;

    $scope.find("input[name='set_amt[]']").not($input).each(function () {
        otherAdjusted += parseAdjustNumber($(this).val());
    });

    var remaining = Math.max(0, maxAdjustable - otherAdjusted);
    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    $input.val(formatAdjustNumber(Math.min(rowMax, remaining)));
    updateJvAdjustmentTotal($scope);
}

$(document).on('click', '#table_jv_receipt_list tbody tr.js-adj-row, #table_jv_payment_list tbody tr.js-adj-row', function (e) {
    if ($(e.target).is('input, textarea, select, button, a, label')) {
        return;
    }
    set_adjust($(this).data('amt'), $(this).data('input'));
});

$(document).on('input', '.jv-adjustment-amount', function () {
    clampJvAdjustmentInput($(this), false);
});

$(document).on('blur', '.jv-adjustment-amount', function () {
    clampJvAdjustmentInput($(this), true);
});
</script>

<script>
   
function update_totals() {
    let total_amount_dr = 0;
    let total_amount_cr = 0;

    const decimal_point = @json(session('logged_session_data.decimal_point'));

    $('#myTable tbody tr').each(function () {
        const $row = $(this);
        total_amount_dr += parseFloat($row.find('input[name="amount_dr[]"]').val().replace(/,/g, '')) || 0;
        total_amount_cr += parseFloat($row.find('input[name="amount_cr[]"]').val().replace(/,/g, '')) || 0;
    });

    $('#dr_total').text(formatAmount(total_amount_dr));
    $('#cr_total').text(formatAmount(total_amount_cr));
}

// when user types a debit amount, clear the credit column in same row (and vice versa)
$(document).on('input', 'input[name="amount_dr[]"]', function() {
    var $row = $(this).closest('tr');
    if ($.trim($(this).val()) !== '') {
        $row.find('input[name="amount_cr[]"]').val('0');
    }
    update_totals();
});

$(document).on('input', 'input[name="amount_cr[]"]', function() {
    var $row = $(this).closest('tr');
    if ($.trim($(this).val()) !== '') {
        $row.find('input[name="amount_dr[]"]').val('0');
    }
    update_totals();
});
</script>
<script>

    $(document).on('focus', 'select[name="account_id[]"]', function () {
    const $select = $(this);

    // Add the class if not present
    if (!$select.hasClass('js-account-select')) {
        $select.addClass('js-account-select');
        //$select.remove('select2-hidden-accessible');

        // Initialize Select2
        initAccountSelect2(this); // your existing function
    }
});

</script>

<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        window.initAccountSelect2 = initAccountSelect2;
        $(selector).siblings('.select2, .select2-container').remove();
        $(selector).removeClass('select2-hidden-accessible');
        $(selector).select2({
            width: '100%',
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
                              let text = '';
                              if (item.account_code) {
                                  text += item.account_code + ' - ';
                              }
                              text += item.account_name;
                             return {
                                 id: item.id,
                                 text: text,
                                 group: item.group,
                                 account_code: item.original_code || item.account_code,
                                 yes_no: item.yes_no
                             };
                         })
                     };
                 },

                cache: true
            },
            placeholder: '',
            minimumInputLength: 2,
            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
        });

        $(selector).on('select2:select', function (e) {
             var selectedData = e.params.data;
             var $row = $(this).closest('tr'); // find the closest row
             $(this).find('option:selected').attr('data-group', selectedData.group || '');
             $(this).find('option:selected').attr('data-code', selectedData.account_code || '');
             $(this).find('option:selected').attr('data-yes-no', selectedData.yes_no || '0');
             $row.find('input[name="amount_dr[]"]').focus();
             // Trigger checkbox visibility update
             account_id_change(this, selectedData.account_code);
         });

            // When any .js-account-select select2 opens, prefill the search box with the currently selected value
        $(document).on('select2:open', function(e) {
            // Find the select2 element that triggered the event
            var $select = $(document.activeElement).closest('.js-account-select');
            if ($select.length === 0) {
                // fallback: try to get the open dropdown's select
                $select = $('.js-account-select').filter(function() {
                    return $(this).data('select2') && $(this).data('select2').isOpen();
                });
            }
            if ($select.length > 0) {
                var sel = $select.select2('data');
                if (sel && sel.length && sel[0].text) {
                    setTimeout(function() {
                        const searchInput = document.querySelector(
                            '.select2-container--open .select2-search__field');
                        if (searchInput) {
                            // Put current selected text into search box so user can edit / refine
                            searchInput.value = sel[0].text.trim();
                            // trigger input so select2 filters on prefilling
                            var event = new Event('input', {
                                bubbles: true
                            });
                            searchInput.dispatchEvent(event);

                            // Move cursor to end of the text
                            try {
                                var len = searchInput.value.length;
                                searchInput.setSelectionRange(len, len);
                            } catch (err) {
                                // ignore if not supported
                            }
                        }
                    }, 0);
                }
            }
        });

        
    }

    initAccountSelect2('.js-account-select');

    // automatically open the first account selector when the page loads
    // some rows may not yet have the js-account-select class, so initialise it manually
    (function openFirstAccount() {
        var $first = $('select[name="account_id[]"]').first();
        if ($first.length) {
            // add class and initialize if not already done
            if (!$first.hasClass('js-account-select')) {
                $first.addClass('js-account-select');
                initAccountSelect2($first);
            }
            // give select2 a moment to render then open dropdown
            setTimeout(function() {
                try { $first.select2('open'); } catch (e) { /* ignore */ }
            }, 50);
        }
    })();

    // Re-initialize on focus if needed
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
            $(this).select2('open');
        }
    });

    // On click, open dropdown and focus on search field
    $(document).on('click', '.js-account-select', function () {
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
    /*table row fill based on layout height*/
 window.onload = function () {
    const table = document.getElementById('myTable');
    const tbody = table.querySelector('tbody');

    // If there are no rows, do nothing
    if (tbody.rows.length === 0) return;

    const rowHeight = tbody.rows[0].offsetHeight;
    const pageHeight = window.innerHeight-65;
    const tableTop = table.getBoundingClientRect().top;
    const availableHeight = pageHeight - tableTop;

    let existingRows = tbody.rows.length;
    let totalRows = Math.floor(availableHeight / rowHeight);

    const lastRow = tbody.rows[tbody.rows.length - 1];

    for (let i = existingRows + 1; i <= totalRows; i++) {
      const newRow = lastRow.cloneNode(true); // clone entire row

        const firstCellInput = newRow.cells[0].querySelector('input');
        if (firstCellInput) {
            firstCellInput.value = i;
        }
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach((input, index) => {
            if (index !== 0) input.value = "";
        });

        // Clean up Select2 components on the cloned row and re-initialize
        const select = newRow.querySelector('select[name="account_id[]"]');
        if (select) {
            select.classList.remove('select2-hidden-accessible');
            select.classList.remove('js-account-select');
            select.value = "";
            const select2Containers = newRow.querySelectorAll('.select2, .select2-container');
            select2Containers.forEach(container => container.remove());
            
            // Re-initialize Select2 after append
            setTimeout(() => {
                if (window.initAccountSelect2) {
                    window.initAccountSelect2(select);
                }
            }, 0);
        }

      tbody.appendChild(newRow);
    }
  };
/*table row fill based on layout height*/
</script>


<button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlJournalVoucherAdjest" hidden></button>
<form id="ta">
<div class="modal side-panel modal-draggable fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bill Wise Selection</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="activate_button()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="br_account_id">
                    <input type="hidden" id="br_account_id_amount">
                    <input type="hidden" id="account_type">
                    <input type="hidden" id="add_url">
                    <input type="hidden" id="delete_url">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label id="txt_bi_cheque_amount">@lang('Cash Amount') <span>*</span></label>
                                    <input class="primary-input form-control" type="text" id="bi_cheque_amount" name="bi_cheque_amount" value="0" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Amount Adjusted') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_amount_adjusted" name="bi_amount_adjusted" value="0" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                    
                                    <input type="hidden" id="bi_balance_adjest" value="">

                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Balance to Adjust') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_extra_amount" name="bi_extra_amount" value="0" >                                    
                                    <div style="display: none;">
                                    <input class="primary-input form-control" type="text" id="bi_balance_to_adjust" name="bi_balance_to_adjust" value="0" ></div>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>

                            
                            <div class="col mb-20">
                                        <div class="input-effect">
                                            <label>  @lang('Search in table') </label>
                                            <input class="primary-input form-control" type="text" id="tableSearchBill" name="tableSearchBill" value="" >                                       
                                        </div>
                                    </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mt-2">
                                <div class="equipment comon-status mt-3">
                                    <table class="table table-hover form-item-table crListBankBookAdjest" id="long-list" style="border: solid 1px #e3e6f0; width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-center">@lang('Doc No')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                                <th style="width:100px;" class="text-end">@lang('Total')</th>
                                                <th style="width:100px;" class="text-end">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-end">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-end">@lang('Adjustment')</th>
                                                <th style="width:100px;" class="text-start">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-end"><label id="footer_total" /></th>
                                                <th class="text-end"><label id="footer_paid" /></th>
                                                <th class="text-end"><label id="footer_balance" /></th>
                                                <th class="text-end"><label id="footer_adjustment" /></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="equipment comon-status mt-3" id="billWisePositiveUnadjustedSection" style="display:none;">
                                    <h6 class="mb-2"> Unadjusted Balance</h6>
                                    <table class="table table-hover form-item-table" id="crListBankBookAdjestUnadjusted" style="border: solid 1px #e3e6f0; width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-center">@lang('Deal ID')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('Receipt No')</th>
                                                <th style="width:120px;" class="text-end">@lang('Amount')</th>
                                                <th style="width:120px;" class="text-end">@lang('Adjustment')</th>
                                                <th style="width:100px;" class="text-start">@lang('Remarks')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">@lang('Total')</th>
                                                <th class="text-end"><label id="footer_unadjusted_amount">0.00</label></th>
                                                <th class="text-end"><label id="footer_unadjusted_adjustment">0.00</label></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                   <script>
function parseAmount(value) {
    if (value === null || value === undefined) return 0;

    var cleaned = String(value).replace(/,/g, '').trim();

    if (cleaned === '') return 0;

    var amount = Number(cleaned);

    return Number.isFinite(amount) ? amount : 0;
}

function setAmount(selector, value) {
    $(selector).val(formatAmount(parseAmount(value)));
}

function setTextAmount(selector, value) {
    $(selector).text(formatAmount(parseAmount(value)));
}

function get_set_amount(id) {
    if (typeof autoFillBillWiseAdjustmentInput === 'function') {
        autoFillBillWiseAdjustmentInput(id);
        return;
    }
    if (typeof updateBillWiseAdjustmentTotals === 'function') {
        updateBillWiseAdjustmentTotals();
    }
}
</script>

                        <div class="row">
                            <div class="col-lg-12 mt-2">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-light add-btn ms-2" type="button" value="Save" onclick="validateAttachForm()">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
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

<button id="btnModalAdjustment" data-bs-toggle="modal" data-bs-target="#ModalAdjustment" hidden></button>
<button id="btnModalPaymentAdjustment" data-bs-toggle="modal" data-bs-target="#ModalPaymentAdjustment" hidden></button>
<input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value="0">
<input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value="0"/>
<input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted" value="0"/>
<input type="hidden" id="adj_account_id">
<input type="hidden" id="adj_account_id_amount">
<!-- Modal Receipt Adjustment-->
<div class="modal side-panel modal-draggable fade" id="ModalAdjustment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Customer Unadjusted List</h4>
						<button type="button" id="ModalAdjustmentClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover form-item-table" id="table_jv_receipt_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Receipt No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjustment</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-center">
                        <button type="button" class="btn btn-light add-btn" onclick="add_receipt_adjustment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Adjustment
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
<div class="modal side-panel modal-draggable fade" id="ModalPaymentAdjustment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Supplier Unadjusted List</h4>
						<button type="button" id="ModalPaymentAdjustmentClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover form-item-table" id="table_jv_payment_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjustment</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-right">
                        <button type="button" class="btn btn-light add-btn ms-2" onclick="add_payment_adjustment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Adjustment
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

<div class="modal side-panel modal-draggable fade" id="importModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel">Import Items</h4>
						<button type="button" id="ModalPaymentAdjustmentClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-2 pt-2" style="padding-left:11px">Select File</div>
                <div class="col-lg-4 pt-2">
                    <input type="file" class="form-control" id="excel-file" /></div>
                <div class="col-lg-6 pt-2"> (<a href="{{ url('public/uploads/product_upload/jv_items_import_sample.xlsx') }}" target="_blank">Sample File</a>)</div>
                <div class="col-lg-12 pt-2">
                    <table class="table table-hover table-striped" id="jv-table-import" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:20px;">@lang('#')</th>
                                <th style="width:100px;">@lang('Code')</th>
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
            <button type="button" class="btn btn-light add-btn ms-2" id="saveImportDataBtn">
                <i class="ico icon-outline-upload text-success"></i> Add
            </button>
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


<div class="modal modal-draggable fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Attachments - <label class="font-weight-600" id="att_cust_name"></label></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
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
                                <input class="form-control date-picker" type="text" id="att_date" name="att_date" value="{{ date('d/m/Y') }}" autocomplete="off"/>
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
                                <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
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


                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="srl_id" />

                <button type="button" onclick="add_attachment()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

    

<script>
    function add_attachment(){
        console.log('add_attachment');
        
        if($('#att_file').val()==""){ 
            alert('Please select a file to upload');
            $('#att_file').focus(); 
            return false; 
        }
        if($('#att_date').val()==""){ 
            alert('Please select a date');
            $('#att_date').focus(); 
            return false; 
        }
        if($('#doc_name').val()==""){ 
            alert('Please enter a file name');
            $('#doc_name').focus(); 
            return false; 
        }

        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('add-journal-voucher-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('doc_id', $('#jv_id').val());
        formData.append('att_date', $('#att_date').val());
        formData.append('att_file', $('#att_file')[0].files[0]); 
        formData.append('doc_name', $('#doc_name').val());

        console.log('Sending data:', {
            doc_id: $('#jv_id').val(),
            att_date: $('#att_date').val(),
            doc_name: $('#doc_name').val()
        });

        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                console.log('Response:', dataResult);
                var data = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                var len = 0;
                var getSelectedRows="";
                
                if(data['data'] != null){
                    len = data['data'].length;
                }
                
                if(len > 0){
                    for(var i=0; i<len; i++){
                        getSelectedRows +="<tr>\
                            <td>"+ Number(i+1) +"</td>\
                            <td>"+get_format_date(data['data'][i].doc_date)+"</td>\
                            <td><a href='../../"+data['data'][i].doc_file+"' target='_blank'>"+data['data'][i].doc_name+"</a></td>\
                            <td><a onclick='delete_attachment("+data['data'][i].id+")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark' aria-hidden='true'></i></a></td>\
                            </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows); 
                    console.log('Attachment added successfully');
                    toastr.success('Attachment added successfully', 'Success');
                    //close the modal after successful upload
                    $('#attachment_popup_win').modal('hide');
                } else {
                    $('#att-table tbody').empty();
                }
                $("#loading_bg").css("display", "none");
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:");
                console.error("Status: " + status);
                console.error("Error: " + error);
                console.error("Response Text: " + xhr.responseText);
                $("#loading_bg").css("display", "none");
                alert("Something went wrong while processing your request. Please try again.");
            }
        });
    }
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        console.log($('#doc_number').val())
        $('#att_cust_name').text(" " + $('#doc_number').val());

        var action = "{{ URL::to('view-journal-voucher-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#jv_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
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
                doc_id : $('#jv_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                        toastr.success('Attachment deleted successfully', 'Success');

                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
</script>

<!-- Accrual/Deferral Modal -->
<div class="modal fade" id="accrual_deferral_modal" tabindex="-1" aria-labelledby="accrualDeferralModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="accrualDeferralModalLabel">Accrual/Deferral Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="accrual_deferral_form">
                    <input type="hidden" id="ad_row_index">
                    <input type="hidden" id="ad_account_id">
                    <div class="mb-3 input-effect">
                        <label class="dynamicslbl">Amount</label>
                        <input type="text" class="form-control text-end" id="ad_amount" readonly>
                    </div>
                    <div class="mb-3 input-effect mt-15">
                        <label class="dynamicslbl">From Date</label>
                        <input type="date" class="form-control" id="ad_from_date" required>
                    </div>
                    <div class="mb-3 input-effect mt-15">
                        <label class="dynamicslbl">To Date</label>
                        <input type="date" class="form-control" id="ad_to_date" required>
                    </div>
                    <div class="mb-3 input-effect mt-15">
                        <label class="dynamicslbl">No. of Days</label>
                        <input type="text" class="form-control text-end" id="ad_no_of_days" readonly>
                    </div>
                    <div class="mb-3 input-effect mt-15">
                        <label class="dynamicslbl">Per Day</label>
                        <input type="text" class="form-control text-end" id="ad_per_day" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn_submit_accrual">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#ad_from_date, #ad_to_date').on('change', function() {
        const fromVal = $('#ad_from_date').val();
        const toVal = $('#ad_to_date').val();
        const amtVal = parseFloat($('#ad_amount').val()?.replace(/,/g, '') || 0);

        if (fromVal && toVal) {
            const fromDate = new Date(fromVal);
            const toDate = new Date(toVal);
            
            const diffTime = toDate - fromDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                $('#ad_no_of_days').val(diffDays);
                const perDay = amtVal / diffDays;
                $('#ad_per_day').val(perDay.toFixed(6));
            } else {
                $('#ad_no_of_days').val('');
                $('#ad_per_day').val('');
            }
        }
    });

    $('#btn_submit_accrual').on('click', function() {
        const adForm = document.getElementById('accrual_deferral_form');
        if (!adForm.checkValidity()) {
            adForm.reportValidity();
            return;
        }

        const data = {
            _token: '{{ csrf_token() }}',
            account_id: $('#ad_account_id').val(),
            amount: $('#ad_amount').val(),
            from_date: $('#ad_from_date').val(),
            to_date: $('#ad_to_date').val(),
            no_of_days: $('#ad_no_of_days').val(),
            per_day_amount: $('#ad_per_day').val()
        };

        $.ajax({
            url: '{{ URL::to("journalvoucher-save-prepaid-accrued") }}',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message, 'Success');
                    $('#accrual_deferral_modal').modal('hide');
                } else {
                    alert('Error saving data: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + error);
            }
        });
    });
});
</script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

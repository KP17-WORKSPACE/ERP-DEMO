//enter key next ctrl focus
// jQuery(function() {
//     $('input').keydown(function(e) {
//         var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
//         if (key == 13) {
//             e.preventDefault();
//             var inputs = $(this).closest('form').find(':input:visible');
//             inputs.eq(inputs.index(this) + 1).focus();
//         }
//     });
// });
//enter key next ctrl focus

function get_format_time(time) {
    if(time==null){
        return "--";
    }
    // Split the time string into hours, minutes, and seconds
    var timeParts = time.split(':');
    var hours = parseInt(timeParts[0], 10);
    var minutes = timeParts[1];
    var seconds = timeParts[2];

    // Determine AM/PM suffix
    var suffix = hours >= 12 ? 'PM' : 'AM';

    // Convert to 12-hour format
    hours = hours % 12;
    hours = hours ? hours : 12; // Handle midnight (00:00 becomes 12)
    
    // Format the time as HH:MM AM/PM
    var formattedTime = (hours < 10 ? '0' + hours : hours) + ':' + minutes + ' ' + suffix;
    return formattedTime;
}
function get_format_date(date) {
    if(date==null){
        return "--";
    }
    const dateStr = date;
    const dateObj = new Date(dateStr);

    // Get day, month, and year
    const day = String(dateObj.getDate()).padStart(2, '0'); // Ensure 2 digits
    const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Month is 0-based
    const year = dateObj.getFullYear();

    // Format as "dd/mm/yyyy"
    const formattedDate = `${day}/${month}/${year}`;
    return formattedDate;
}
function get_format_date2(date) {
    if(date==null){
        return "--";
    }
    const dateStr = date;
    const dateObj = new Date(dateStr);

    // Get day, month, and year
    const day = String(dateObj.getDate()).padStart(2, '0'); // Ensure 2 digits
    const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Month is 0-based
    const year = dateObj.getFullYear();

    // Format as "dd/mm/yyyy"
    const formattedDate = `${year}-${month}-${day}`;
    return formattedDate;
}
function get_format_date_time(datetime) {
    if(datetime==null){
        return "--";
    }
    var parts = datetime.split(' ');
    var date = get_format_date(parts[0]);
    var time = get_format_time(parts[1]);
    return date+' '+time;
}
function formatAmount(input) {
    console.log(input);
    let inputStr = input.toString();

    let number = parseFloat(inputStr.replace(/,/g, ''));
    console.log(number);

    if (!isNaN(number)) {
        return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        return '';
    }
}
function parseErpAmount(value) {
    var amount = parseFloat((value || '0').toString().replace(/,/g, ''));
    return isNaN(amount) ? 0 : amount;
}
function escapeErpHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
function setBillWiseEnteredAmount(amount, adjustedAmount) {
    var enteredAmount = parseErpAmount(amount);
    var displayAmount = formatAmount(enteredAmount);
    var displayAdjusted = formatAmount(adjustedAmount === undefined ? enteredAmount : parseErpAmount(adjustedAmount));

    $('#bi_cheque_amount').val(displayAmount);
    $('#bi_amount_adjusted').val(displayAdjusted);
    $('#bi_extra_amount').val(displayAmount);
    $('#bi_balance_to_adjust').val(displayAmount);
    $('#bi_balance_adjest').val(displayAmount);
}
function updateBillWiseAdjustmentTotals() {
    var enteredAmount = parseErpAmount($('#bi_cheque_amount').val());
    var adjustedTotal = 0;

    $('#cr_popup_win .tot_amt').each(function () {
        adjustedTotal += parseErpAmount($(this).val());
    });

    var balance = enteredAmount - adjustedTotal;
    if (balance < 0) {
        balance = 0;
    }

    $('#bi_amount_adjusted').val(formatAmount(adjustedTotal));
    $('#bi_balance_adjest').val(formatAmount(balance));
    $('#bi_balance_to_adjust').val(formatAmount(balance));
    $('#bi_extra_amount').val(formatAmount(Math.abs(enteredAmount - adjustedTotal)));
    $('#footer_adjustment').text(formatAmount(adjustedTotal));
}
function isBillWiseAdjustmentWithinLimit() {
    var enteredAmount = parseErpAmount($('#bi_cheque_amount').val());
    var adjustedTotal = 0;

    $('#cr_popup_win .tot_amt').each(function () {
        adjustedTotal += parseErpAmount($(this).val());
    });

    return adjustedTotal <= enteredAmount;
}
function enforceBillWiseAdjustmentLimit(input) {
    var $input = $(input);
    var enteredAmount = parseErpAmount($('#bi_cheque_amount').val());
    var otherTotal = 0;

    $('#cr_popup_win .tot_amt').not($input).each(function () {
        otherTotal += parseErpAmount($(this).val());
    });

    var maxAllowed = enteredAmount - otherTotal;
    if (maxAllowed < 0) {
        maxAllowed = 0;
    }

    var currentAmount = parseErpAmount($input.val());
    if (currentAmount > maxAllowed) {
        currentAmount = maxAllowed;
        $input.val(formatAmount(currentAmount));
        alert('Adjustment amount cannot exceed entered amount.');
    }

    updateBillWiseAdjustmentTotals();
}
$(document).on('input', '#cr_popup_win .tot_amt', function () {
    enforceBillWiseAdjustmentLimit(this);
});
$(document).on('blur', '#cr_popup_win .tot_amt', function () {
    $(this).val(formatAmount(parseErpAmount($(this).val())));
    updateBillWiseAdjustmentTotals();
});
$(document).on('submit', '#journalvoucher-get-adjestment-update, #ta', function (event) {
    if ($('#cr_popup_win').is(':visible') && !isBillWiseAdjustmentWithinLimit()) {
        event.preventDefault();
        $("#loading_bg").css("display", "none");
        alert('Adjustment amount cannot exceed entered amount.');
        return false;
    }
});
function formatCurrency(input) {
    
    let value = input.value.replace(/,/g, '');
        if (value === '' || isNaN(value)) {
            input.value = '';
            return;
        }
    let floatValue = formatAmount(value);
    input.value = floatValue;
    console.log(floatValue);
    
}

// cashreceipt-add
$(document).on("click", "#addRowCR", function(event) {
    var i = $('#cr-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-cr-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#cr-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addCtrlCashBookAdjest", function(event) {
    var url = $('#url').val();
    var cr_account_id = $('#cr_account_id').val();
        var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-cr-balancelist',
        type: 'GET',
        data: { account_id: cr_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="w-100 sstxtbx" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.si_date + '" class="w-100 sstxtbx" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_number + '" class="w-100 sstxtbx" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_date + '" class="w-100 sstxtbx" type="text" id="bi_due_date_' + i + '" name="bi_due_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.total + '" class="w-100 sstxtbx" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.paid + '" class="w-100 sstxtbx" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="CashBookAdjestBalance(' + i + ')"></td>\
                        <td><input value="' + value.balance + '" class="w-100 sstxtbx" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0"></td>\
                        <td><input value="" class="w-100 sstxtbx" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0"></td>\
                        <td><input class="primary-btn fix-gr-bg btn_ajax_cr" type="button" value="update" onclick="return validateCashBookAdjestForm(' + i + ')"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                i++;
            });
            $("#bi_amount_to_adjust").val(outstamount);
            $('#crListCashBookAdjest tbody').empty();
            $("#crListCashBookAdjest tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            //alert(XMLHttpRequest);
            $('#crListCashBookAdjest tbody').empty();
        }
    }); // get the product data
});
// cashreceipt-add

// receipt-add
$(document).on("click", "#addRowRE", function(event) {
    var i = $('#br-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-re-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#br-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

// receipt-add
$(document).on("click", "#addCtrlBankBookAdjest", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    
    var i = 1;
    var outstamount = 0;

    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;

    $.ajax({
        url: url + '/' + 'get-re-balancelist',
        type: 'GET',
        data: { account_id: br_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";

            // empty-response fallback: show 'No outstanding bills' + one empty editable row
            if (!response || response.length === 0) {
                // reset computed totals
                $("#bi_balance_to_adjust").val(0);
                $("#footer_total").text(formatAmount(0));
                $("#footer_paid").text(formatAmount(0));
                $("#footer_balance").text(formatAmount(0));

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">No outstanding bills found</td>\
                       </tr>';

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">&nbsp;</td>\
                       </tr>';
            $("#addCtrlBankBookAdjest").prop("disabled", false);


                $('#crListBankBookAdjest tbody').empty().append(tr);
                return;
            }

            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td class="text-center">&nbsp;&nbsp;<a target="_blank" href="' + url + '/get-url-deal/' + value.deal_id + '">' + value.deal_id + '</a><input value="' + value.deal_id + '" class="form-control text-center" type="hidden" id="bi_deal_id_' + i + '" name="bi_deal_id[]" autocomplete="off" readonly></td>\
                        <td  class="text-center"><a target="_blank" href="' + url + '/get-url-sales-invoice/' + value.doc_number + '">' + value.doc_number + '</a><input value="' + value.doc_number + '" class="form-control text-center row_ctrl" type="hidden" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td  class="text-center">' + get_format_date(value.doc_date) + ' <input value="' + value.doc_date + '" class="form-control text-center" type="hidden" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly><input value="' + get_format_date(value.doc_date) + '" class="form-control text-center" type="hidden" autocomplete="off" readonly></td>\
                        <td  class="text-center">' + value.lpo_number + '<input value="' + value.lpo_number + '" class="form-control text-center" type="hidden" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-end">' + formatAmount(value.total) + '<input value="' + formatAmount(value.total) + '" class="form-control text-end" type="hidden" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end">' + formatAmount(value.paid) + '<input value="' + formatAmount(value.paid) + '" class="form-control text-end" type="hidden" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end">' + formatAmount(value.balance) + '<input value="' + formatAmount(value.balance) + '" class="form-control text-end" type="hidden" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="" class="form-control tot_amt text-end" type="decimal" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseErpAmount(value.total);
                footer_total += parseErpAmount(value.total);
                footer_paid += parseErpAmount(value.paid);
                footer_balance += parseErpAmount(value.balance);
                footer_adjustment += parseErpAmount(value.total);
                i++;

            });

            console.log(outstamount, footer_total, footer_paid, footer_balance);

            $("#bi_balance_to_adjust").val(parseFloat(outstamount));
            $("#footer_total").text(parseFloat(footer_total));
            $("#footer_paid").text(parseFloat(footer_paid));
            $("#footer_balance").text(parseFloat(footer_balance));


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjest").prop("disabled", false);

               // initialize Amount Adjusted / Balance to Adjust after rows are added
            if (typeof get_set_amount === 'function') { get_set_amount(); }


            

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// receipt-add

// receipt-edit
$(document).on("click", "#addCtrlBankBookAdjestEdit", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var doc_number = $('#doc_number').val();
    
    var i = 1;
    var outstamount = 0;

    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;

    $.ajax({
        url: url + '/' + 'get-re-balancelist-edit',
        type: 'GET',
        data: { account_id: br_account_id,doc_number: doc_number },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";

            // empty-response fallback: show 'No outstanding bills' + one empty editable row
            if (!response || response.length === 0) {
                // reset totals shown in the modal
                $("#bi_balance_to_adjust").val(0);
                $("#footer_total").text(formatAmount(0));
                $("#footer_paid").text(formatAmount(0));
                $("#footer_balance").text(formatAmount(0));

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">No outstanding bills found</td>\
                       </tr>';

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">&nbsp;</td>\
                       </tr>';
            $("#addCtrlBankBookAdjestEdit").prop("disabled", false);

                $('#crListBankBookAdjest tbody').empty().append(tr);
                return;
            }

            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td class="text-center">&nbsp;&nbsp;<a target="_blank" href="' + url + '/get-url-deal/' + value.deal_id + '">' + value.deal_id + '</a><input value="' + value.deal_id + '" class="form-control text-center" type="hidden" id="bi_deal_id_' + i + '" name="bi_deal_id[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><a target="_blank" href="' + url + '/get-url-sales-invoice/' + value.doc_number + '">' + value.doc_number + '</a><input value="' + value.doc_number + '" class="form-control text-center row_ctrl" type="hidden" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center">' + get_format_date(value.doc_date) + '<input value="' + value.doc_date + '" class="form-control text-center" type="hidden" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly><input value="' + get_format_date(value.doc_date) + '" class="form-control text-center" type="hidden" autocomplete="off" readonly></td>\
                        <td class="text-center">' + value.lpo_number + '<input value="' + value.lpo_number + '" class="form-control" type="hidden" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-end">' + formatAmount(value.total) + '<input value="' + formatAmount(value.total) + '" class="form-control" type="hidden" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end">' + formatAmount(value.paid) + '<input value="' + formatAmount(value.paid) + '" class="form-control" type="hidden" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end">' + formatAmount(value.balance) + '<input value="' + formatAmount(value.balance) + '" class="form-control" type="hidden" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="'+ formatAmount(value.bi_amount) +'" data-current-amount="' + parseErpAmount(value.bi_amount) + '" class="form-control tot_amt text-end" step="any" type="decimal" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                footer_total += parseFloat(value.total);
                footer_paid += parseFloat(value.paid);
                footer_balance += parseFloat(value.balance);
                footer_adjustment += parseFloat(value.total);
                i++;

            });
            $("#bi_balance_to_adjust").val(parseFloat(outstamount));
            $("#footer_total").text(parseFloat(footer_total));
            $("#footer_paid").text(parseFloat(footer_paid));
            $("#footer_balance").text(parseFloat(footer_balance));


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjestEdit").prop("disabled", false);

            // initialize calculated fields right after rows are added
            if (typeof get_set_amount === 'function') { get_set_amount(); }

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// receipt-edit

// journal-voucher-add
$(document).on("click", "#addCtrlJournalVoucherAdjest", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var br_account_type = $('#account_type').val();
    var get_url='';
    if(br_account_type=="CUS"){
        get_url='journalvoucher-get-adjestment-list-cus';
    } else if(br_account_type=="SUP"){
        get_url='journalvoucher-get-adjestment-list-sup';
    } else{
        get_url='journalvoucher-get-adjestment-list';
    }
    
    var i = 1;
    var outstamount = 0;
    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;

    $.ajax({
        url: url + '/' + get_url,
        type: 'GET',
        data: { account_id: br_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                

                date = value.doc_date ? value.doc_date.split('-').reverse().join('/') : '';
                if(value.balance > 0){
                tr += '<tr>\
                        <td class="text-center"><input value="' + value.doc_number + '" class="form-control row_ctrl border-0 text-center" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><input value="' + date + '" class="form-control text-center border-0" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><input value="' + value.lpo_number + '" class="form-control text-center border-0" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(value.total) + '" class="form-control text-end border-0" type="text" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(value.paid) + '" class="form-control text-end border-0" type="text" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(value.balance) + '" class="form-control text-end border-0" type="text" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end"><input value="" class="form-control tot_amt text-end border-0" type="text" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control  border-0" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                footer_total += parseFloat(value.total);
                footer_paid += parseFloat(value.paid);
                footer_balance += parseFloat(value.balance);
                footer_adjustment += parseFloat(value.total);
                i++;
                }
            });
            $("#bi_balance_to_adjust").val(formatAmount(outstamount));
            $("#footer_total").text(formatAmount(footer_total));
            $("#footer_paid").text(formatAmount(footer_paid));
            $("#footer_balance").text(formatAmount(footer_balance));


            $('.crListBankBookAdjest tbody').empty();
            $(".crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjest").prop("disabled", false);

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('.crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// journal-voucher-add

// journal-voucher-edit
$(document).on("click", "#addCtrlJournalVoucherAdjestEdit", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var doc_number = $('#doc_number').val();
    
    var br_account_type = $('#account_type').val();
    var get_url='';
    if(br_account_type=="CUS"){
        get_url='journalvoucher-get-adjestment-list-edit-cus';
    } else if(br_account_type=="SUP"){
        get_url='journalvoucher-get-adjestment-list-edit-sup';
    } else{
        get_url='journalvoucher-get-adjestment-list-edit';
    }

    var i = 1;
    var outstamount = 0;

    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;
    $("#loading_bg").css("display", "block");
    $.ajax({
        url: url + '/' + get_url,
        type: 'GET',
        data: { account_id: br_account_id,doc_number: doc_number },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                //if(value.balance > 0){
                var total = parseErpAmount(value.total);
                var storedAmount = parseErpAmount(value.bi_amount);
                var paid = parseErpAmount(value.paid);
                var balance = parseErpAmount(value.balance);
                var date = value.doc_date ? get_format_date(value.doc_date) : '';
                var narration = escapeErpHtml(value.remarks);
                var docNumber = escapeErpHtml(value.doc_number);
                var lpoNumber = escapeErpHtml(value.lpo_number);
                tr += '<tr>\
                        <td class="text-center"><input value="' + docNumber + '" class="form-control row_ctrl border-0 text-center" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><input value="' + date + '" class="form-control text-center border-0" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><input value="' + lpoNumber + '" class="form-control text-center border-0" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(total) + '" class="form-control text-end border-0" type="text" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(paid) + '" class="form-control text-end border-0" type="text" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(balance) + '" class="form-control text-end border-0" type="text" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end"><input value="' + formatAmount(storedAmount) + '" data-current-amount="' + storedAmount + '" class="form-control tot_amt text-end border-0" step="any" type="text" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="' + narration + '" class="form-control border-0" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += total;
                footer_total += total;
                footer_paid += paid;
                footer_balance += balance;
                footer_balance += storedAmount;

                i++;
                //}

            });
            var enteredAmount = parseErpAmount($('#bi_cheque_amount').val());
            $("#bi_balance_to_adjust").val(formatAmount(Math.max(enteredAmount - footer_adjustment, 0)));
            $("#footer_total").text(formatAmount(footer_total));
            $("#footer_paid").text(formatAmount(footer_paid));
            $("#footer_balance").text(formatAmount(footer_balance));
            $("#footer_adjustment").text(formatAmount(footer_adjustment));
            $("#bi_amount_adjusted").val(formatAmount(enteredAmount));
            $("#bi_extra_amount").val(formatAmount(Math.max(enteredAmount - footer_adjustment, 0)));


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjestEdit").prop("disabled", false);


            

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
    $("#loading_bg").css("display", "none");
});
// journal-voucher-edit

// payment-add
$(document).on("click", "#addRowPY", function(event) {
    var i = $('#br-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-py-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#br-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

// payment-add
$(document).on("click", "#addCtrlPaymentAdjest", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    
    var i = 1;
    var outstamount = 0;

    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;

    $.ajax({
        url: url + '/' + 'get-py-balancelist',
        type: 'GET',
        data: { account_id: br_account_id },
        dataType: 'json',
        success: function(response) {
            console.log("get list",response);
            var tr = "";

    $('#crListBankBookAdjest tbody').empty();
            
    // ✅ If no data
    if (!response || response.length === 0) {
            $("#bi_balance_to_adjust").val(0);
            $('#bi_extra_amount').val(0);
              $("#footer_total").text(formatAmount(footer_total));
            $("#footer_paid").text(formatAmount(footer_paid));
            $("#footer_balance").text(formatAmount(footer_balance));


        tr += '<tr class="text-muted">\
                <td colspan="10" class="text-center">No outstanding bills found</td>\
               </tr>';

        // ✅ Add one empty editable row
            tr += '<tr class="text-muted">\
                <td colspan="10" class="text-center">&nbsp;</td>\
               </tr>';

            $("#addCtrlPaymentAdjest").prop("disabled", false);


        $("#crListBankBookAdjest tbody").append(tr);
        return;
    }

            

            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td>&nbsp;&nbsp;<a target="_blank" href="' + url + '/crm-deals/show/' + value.deal_id + '">' + value.deal_code + '</a><input value="' + value.deal_code + '" class="form-control row_ctrl" type="hidden" id="bi_deal_code_' + i + '" name="bi_deal_code[]" autocomplete="off" readonly>\
                        <input value="  ' + value.deal_id + '" class="form-control row_ctrl" type="hidden" id="bi_deal_id_' + i + '" name="bi_deal_id[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><a target="_blank" href="' + url + '/get-url-pi/' + value.doc_number + '">' + value.doc_number + '</a><input value="  ' + value.doc_number + '" class="form-control row_ctrl" type="hidden" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center">' + get_format_date(value.doc_date) + ' <input value="' + value.doc_date + '" class="form-control text-center" type="hidden" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly><input value="' + get_format_date(value.doc_date) + '" class="form-control text-center" type="hidden" autocomplete="off" readonly></td>\
                        <td class="text-center"><a target="_blank" href="' + url + '/get-url-po/' + value.lpo_number + '">' + value.lpo_number + '</a><input value="' + value.lpo_number + '" class="form-control text-center" type="hidden" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center"> '+ value.bill_number  + '<input value="' + value.bill_number + '" class="form-control text-center" type="hidden" id="bi_bill_number_' + i + '" name="bi_bill_number[]" autocomplete="off" readonly></td>\
                        <td class="text-end">' + formatAmount(value.total) + '<input value="' + formatAmount(value.total) + '" class="form-control text-end" type="hidden" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end">' + formatAmount(value.paid) + '<input value="' + formatAmount(value.paid) + '" class="form-control text-end" type="hidden" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end">' + formatAmount(value.balance) + '<input value="' + formatAmount(value.balance) + '" class="form-control text-end" type="hidden" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="" class="form-control tot_amt text-end" type="decimal" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                footer_total += parseFloat(value.total);
                footer_paid += parseFloat(value.paid);
                footer_balance += parseFloat(value.balance);
                footer_adjustment += parseFloat(value.total);
                i++;

            });
            $("#bi_balance_to_adjust").val(outstamount);
            $("#footer_total").text(formatAmount(footer_total));
            $("#footer_paid").text(formatAmount(footer_paid));
            $("#footer_balance").text(formatAmount(footer_balance));


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);            
            $("#addCtrlPaymentAdjest").prop("disabled", false);

            // initialize Amount Adjusted / Balance to Adjust after rows are added
            if (typeof get_set_amount === 'function') { get_set_amount(); }

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("error", errorThrown, XMLHttpRequest.responseText);
            // show helpful UI and notify user
            $('#crListBankBookAdjest tbody').empty().append('<tr class="text-muted"><td colspan="10" class="text-center">No outstanding bills found for this account</td></tr>');
            if (typeof toastr !== 'undefined') { toastr.error('Could not load bill-wise data'); }
            try { var modalEl = document.getElementById('cr_popup_win'); if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show(); } catch (e) { /* ignore */ }
        }
    }); // get the product data
});
// payment-add

// payment-edit
$(document).on("click", "#addCtrlPaymentAdjestEdit", function(event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var doc_number = $('#doc_number').val();    
    var i = 1;
    var outstamount = 0;

    var footer_total = 0;
    var footer_paid = 0;
    var footer_balance = 0;
    var footer_adjustment = 0;

    $.ajax({
        url: url + '/' + 'get-py-balancelist-edit',
        type: 'GET',
        data: { account_id: br_account_id,doc_number: doc_number },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";

            // if no rows returned, show placeholder + one editable empty row
            if (!response || response.length === 0) {
                $("#bi_balance_to_adjust").val(0);
                $("#bi_extra_amount").val(0);
                $("#footer_total").text(formatAmount(0));
                $("#footer_paid").text(formatAmount(0));
                $("#footer_balance").text(formatAmount(0));

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">No outstanding bills found</td>\
                       </tr>';

                tr += '<tr class="text-muted">\
                        <td colspan="10" class="text-center">&nbsp;</td>\
                       </tr>';

            $("#addCtrlPaymentAdjest").prop("disabled", false);


                $('#crListBankBookAdjest tbody').empty().append(tr);
                return;
            }

            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td>&nbsp;&nbsp;<a target="_blank" href="' + url + '/crm-deals/show/' + value.deal_id + '">' + value.deal_code + '</a><input value=" ' + value.deal_code + '" class="form-control row_ctrl" type="hidden" id="bi_deal_code_' + i + '" name="bi_deal_code[]" autocomplete="off" readonly>\
                        <input value="' + value.deal_id + '" class="form-control row_ctrl" type="hidden" id="bi_deal_id_' + i + '" name="bi_deal_id[]" autocomplete="off" readonly></td>\
                        <td class="text-center"><a target="_blank" href="' + url + '/get-url-pi/' + value.doc_number + '">' + value.doc_number + '</a><input value="' + value.doc_number + '" class="form-control row_ctrl text-center" type="hidden" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center">' + get_format_date(value.doc_date) + '<input value="' + value.doc_date + '" class="form-control text-center" type="hidden" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly><input value="' + get_format_date(value.doc_date) + '" class="form-control text-center" type="hidden" autocomplete="off" readonly></td>\
                        <td class="text-center"><a target="_blank" href="' + url + '/get-url-po/' + value.lpo_number + '">' + value.lpo_number + '</a><input value="' + value.lpo_number + '" class="form-control text-center" type="hidden" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td class="text-center"> '+ value.bill_number  + '<input value="' + value.bill_number + '" class="form-control text-center" type="hidden" id="bi_bill_number_' + i + '" name="bi_bill_number[]" autocomplete="off" readonly></td>\
                        <td class="text-end">' + formatAmount(value.total) + '<input value="' + formatAmount(value.total) + '" class="form-control text-end" type="hidden" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end">' + formatAmount(value.paid) + '<input value="' + formatAmount(value.paid) + '" class="form-control text-end" type="hidden" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td class="text-end">' + formatAmount(value.balance) + '<input value="' + formatAmount(value.balance) + '" class="form-control text-end" type="hidden" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td class="text-end"><input value="'+ formatAmount(value.bi_amount) +'" data-current-amount="' + parseErpAmount(value.bi_amount) + '" class="form-control tot_amt text-end" step="any" type="text" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                footer_total += parseFloat(value.total);
                footer_paid += parseFloat(value.paid);
                footer_balance += parseFloat(value.balance);
                footer_adjustment += parseFloat(value.total);
                i++;

            });
            $("#bi_balance_to_adjust").val(outstamount);
            $("#footer_total").text(formatAmount(footer_total));
            $("#footer_paid").text(formatAmount(footer_paid));
            $("#footer_balance").text(formatAmount(footer_balance));


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);            
            $("#addCtrlPaymentAdjest").prop("disabled", false);

            // initialize Amount Adjusted / Balance to Adjust after rows are added
            if (typeof get_set_amount === 'function') { get_set_amount(); }

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log('error', errorThrown, XMLHttpRequest.responseText);
            $('#crListBankBookAdjest tbody').empty().append('<tr class="text-muted"><td colspan="10" class="text-center">No outstanding bills found for this account</td></tr>');
            if (typeof toastr !== 'undefined') { toastr.error('Could not load bill-wise data'); }
            try { var modalEl = document.getElementById('cr_popup_win'); if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show(); } catch (e) { /* ignore */ }
        }
    }); // get the product data
});
// payment-edit


// cashpayment-add
$(document).on("click", "#addRowCP", function(event) {
    var i = $('#cp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-cp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#cp-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addCtrlCashPaymentAdjest", function(event) {
    var url = $('#url').val();
    var cp_account_id = $('#cp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-cp-balancelist',
        type: 'GET',
        data: { account_id: cp_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="w-100 sstxtbx" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.pi_date + '" class="w-100 sstxtbx" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_number + '" class="w-100 sstxtbx" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_date + '" class="w-100 sstxtbx" type="text" id="bi_due_date_' + i + '" name="bi_due_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.total + '" class="w-100 sstxtbx" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.paid + '" class="w-100 sstxtbx" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="CashPaymentAdjestBalance(' + i + ')"></td>\
                        <td><input value="' + value.balance + '" class="w-100 sstxtbx" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0"></td>\
                        <td><input value="" class="w-100 sstxtbx" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0"></td>\
                        <td><input class="primary-btn fix-gr-bg btn_ajax_cp" type="button" value="update" onclick="return validateCashPaymentAdjestForm(' + i + ')"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                i++;
            });
            $("#bi_amount_to_adjust").val(outstamount);
            $('#cpListCashPaymentAdjest tbody').empty();
            $("#cpListCashPaymentAdjest tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#cpListCashPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// cashpayment-add

// bankpayment-add
$(document).on("click", "#addRowBP", function(event) {
    var i = $('#bp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-bp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#bp-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addCtrlBankPaymentAdjest", function(event) {
    var url = $('#url').val();
    var bp_account_id = $('#bp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-bp-balancelist',
        type: 'GET',
        data: { account_id: bp_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="w-100 sstxtbx" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.pi_date + '" class="w-100 sstxtbx" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_number + '" class="w-100 sstxtbx" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_date + '" class="w-100 sstxtbx" type="text" id="bi_due_date_' + i + '" name="bi_due_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.total + '" class="w-100 sstxtbx" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.paid + '" class="w-100 sstxtbx" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankPaymentAdjestBalance(' + i + ')"></td>\
                        <td><input value="' + value.balance + '" class="w-100 sstxtbx" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0"></td>\
                        <td><input value="" class="w-100 sstxtbx" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0"></td>\
                        <td><input class="primary-btn fix-gr-bg btn_ajax_bp" type="button" value="update" onclick="return validateBankPaymentAdjestForm(' + i + ')"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                i++;

            });
            $("#bi_amount_to_adjust").val(outstamount);
            $('#cpListBankPaymentAdjest tbody').empty();
            $("#cpListBankPaymentAdjest tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#cpListBankPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// bankpayment-add

// postdatedreceipt-add
$(document).on("click", "#addRowPDR", function(event) {
    var i = $('#pdr-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-pdr-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#pdr-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addCtrlPostdatedReceiptAdjest", function(event) {
    var url = $('#url').val();
    var pdr_account_id = $('#pdr_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-pdr-balancelist',
        type: 'GET',
        data: { account_id: pdr_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="w-100 sstxtbx" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.si_date + '" class="w-100 sstxtbx" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_number + '" class="w-100 sstxtbx" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_date + '" class="w-100 sstxtbx" type="text" id="bi_due_date_' + i + '" name="bi_due_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.total + '" class="w-100 sstxtbx" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.paid + '" class="w-100 sstxtbx" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="PostdatedReceiptAdjestBalance(' + i + ')"></td>\
                        <td><input value="' + value.balance + '" class="w-100 sstxtbx" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0"></td>\
                        <td><input value="" class="w-100 sstxtbx" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0"></td>\
                        <td><input class="primary-btn fix-gr-bg btn_ajax_pdr" type="button" value="update" onclick="return validatePostdatedReceiptAdjestForm(' + i + ')"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                i++;

            });
            $("#bi_amount_to_adjust").val(outstamount);
            $('#pdrListPostdatedReceiptAdjest tbody').empty();
            $("#pdrListPostdatedReceiptAdjest tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#pdrListPostdatedReceiptAdjest tbody').empty();
        }
    }); // get the product data
});
// postdatedreceipt-add

// postdatedpayment-add
$(document).on("click", "#addRowPDP", function(event) {
    var i = $('#pdp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-pdp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control" type="number" id="amount_' + i + '" name="amount[]" autocomplete="off" min="0"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                tr += '</tr>';

                $("#pdp-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addRowJV", function(event) {
    var i = $('#jv-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-jv-accolist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>" + i + "</td>";
                tr += "<td>";
                tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
                tr += "<option value=''></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.name + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td><input class="form-control text-end" type="number" id="amount_dr_' + i + '" name="amount_dr[]" step="any" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';
                tr += '<td><input class="form-control text-end" type="number" id="amount_cr_' + i + '" name="amount_cr[]" step="any" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';
                tr += '<td><input class="form-control" type="text" id="remarks_' + i + '" name="remarks[]" autocomplete="off" ></td>';
                /*tr += '<td><select class="form-control" name="plan[]" id="plan_' + i + '">\
                                            <option value="0">Daily</option>\
                                            <option value="1">1 Year</option>\
                                            <option value="2">2 Year</option>\
                                            <option value="3">3 Year</option>\
                                            <option value="4">4 Year</option>\
                                            <option value="5">5 Year</option></select></td>';*/
                tr += '</tr>';

                $("#jv-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data
});

$(document).on("click", "#addCtrlPostdatedPaymentAdjest", function(event) {
    var url = $('#url').val();
    var pdp_account_id = $('#pdp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-pdp-balancelist',
        type: 'GET',
        data: { account_id: pdp_account_id },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="w-100 sstxtbx" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.pi_date + '" class="w-100 sstxtbx" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_number + '" class="w-100 sstxtbx" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off"></td>\
                        <td><input value="' + value.lpo_date + '" class="w-100 sstxtbx" type="text" id="bi_due_date_' + i + '" name="bi_due_date[]" autocomplete="off"></td>\
                        <td><input value="' + value.total + '" class="w-100 sstxtbx" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.paid + '" class="w-100 sstxtbx" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="PostdatedPaymentAdjestBalance(' + i + ')"></td>\
                        <td><input value="' + value.balance + '" class="w-100 sstxtbx" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0"></td>\
                        <td><input value="" class="w-100 sstxtbx" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0"></td>\
                        <td><input class="primary-btn fix-gr-bg btn_ajax_pdp" type="button" value="update" onclick="return validatePostdatedPaymentAdjestForm(' + i + ')"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                i++;
            });
            $("#bi_amount_to_adjust").val(outstamount);
            $('#pdpListPostdatedPaymentAdjest tbody').empty();
            $("#pdpListPostdatedPaymentAdjest tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#pdpListPostdatedPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// postdatedpayment-add

// purchease-return-invoiceno
$("#pr_supplier_id").on('change', function() {
    var url = $('#url').val();
    var pi_id = $('#pr_supplier_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { pi_id: pi_id },
        dataType: 'json',
        url: url + '/' + 'get_pi_list',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {
                    $('#pi_numbers').find('option').not(':first').remove();
                    $('#sectionPINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#pi_numbers').append($('<option>', {
                            value: pin.id,
                            text: pin.doc_number
                        }));

                        $("#sectionPINumberDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.doc_number + "</li>");
                    });
                } else {
                    $('#sectionPINumberDiv .current').html('Select Purchease Invoive Number *');
                    $('#pi_numbers').find('option').not(':first').remove();
                    $('#sectionPINumberDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#getCtrlPiRetNum", function(event) {

    var selected = $("#pi_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var pi_ids = "";
    selected.each(function() {
        if (pi_ids == "") { pi_ids = $(this).val(); } else { pi_ids += "," + $(this).val(); }
    });

    var url = $('#url').val();
    var i = 1;
    var outstamount = 0;

    $.ajax({
        url: url + '/' + 'get_pi_list_for_pi_return',
        type: 'GET',
        data: { pi_ids: pi_ids },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.pi_id + '" class="w-100 sstxtbx" type="text" id="pi_id_' + i + '" name="pi_id[]" autocomplete="off"></td>\
                        <td><input value="' + value.part_number + '" class="w-100 sstxtbx" type="text" id="part_number_' + i + '" name="part_number[]" autocomplete="off"></td>\
                        <td><input value="' + value.tax + '" class="w-100 sstxtbx" type="text" id="tax_' + i + '" name="tax[]" autocomplete="off"></td>\
                        <td><input value="' + value.qty + '" class="w-100 sstxtbx" type="text" id="qty_' + i + '" name="qty[]" autocomplete="off"></td>\
                        <td><input value="' + value.unitprice + '" class="w-100 sstxtbx" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.value + '" class="w-100 sstxtbx" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.discount + '" class="w-100 sstxtbx" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.customcharges + '" class="w-100 sstxtbx" type="number" id="customcharges_' + i + '" name="customcharges[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.vatamount + '" class="w-100 sstxtbx" type="number" id="vatamount_' + i + '" name="vatamount[]" autocomplete="off" min="0">\
                        <input type="hidden" value="' + value.vatamount + '" id="taxableamount_' + i + '">\
                        <input type="hidden" value="' + value.description + '" id="description_' + i + '">\
                        </td>\
                        <td><input class="primary-btn fix-gr-bg" id="add_to_pi_return_' + i + '" onclick="fun_add_to_pi_return(' + i + ')" type="button" value="Add To Return"></td>\
                    </tr>';
                outstamount += parseFloat(value.discount);
                i++;
            });

            //$("#bi_amount_to_adjust").val(outstamount);

            $('#piListRetInvo tbody').empty();
            $("#piListRetInvo tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#piListRetInvo tbody').empty();
        }
    }); // get the product data
});

function fun_add_to_pi_return(id) {
    var pi_id = $('#pi_id_' + id).val();
    var part_number = $('#part_number_' + id).val();
    var tax = $('#tax_' + id).val();
    var qty = $('#qty_' + id).val();
    var unitprice = $('#unitprice_' + id).val();
    var value = $('#value_' + id).val();
    var discount = $('#discount_' + id).val();
    var customcharges = $('#customcharges_' + id).val();
    var vatamount = $('#vatamount_' + id).val();
    var taxableamount = $('#taxableamount_' + id).val();
    var description = $('#description_' + id).val();



    var tr = "";
    tr += '<tr>\
    <td>' + id + '</td>\
    <td><input value="' + part_number + '" class="w-100 sstxtbx" type="text" id="pi_ret_part_number_' + id + '" name="pi_ret_part_number[]" autocomplete="off"></td>\
    <td><input value="' + description + '"class="w-100 sstxtbx" type="text" id="pi_ret_descripttion' + id + '" name="pi_ret_descripttion[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="pi_ret_product_type_' + id + '" name="pi_ret_product_type[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="pi_ret_tax_cat_' + id + '" name="pi_ret_tax_cat[]" autocomplete="off"></td>\
    <td><input value="' + qty + '" class="w-100 sstxtbx" type="text" id="pi_ret_qty_' + id + '" name="qty[]" autocomplete="off"></td>\
    <td><input value="' + unitprice + '" class="w-100 sstxtbx" type="text" id="pi_ret_unitprice_' + id + '" name="unitprice[]" autocomplete="off"></td>\
    <td><input value="' + value + '" class="w-100 sstxtbx" type="text" id="pi_ret_value_' + id + '" name="value[]" autocomplete="off"></td>\
    <td><input value="' + taxableamount + '" class="w-100 sstxtbx" type="text" id="pi_ret_taxableamount_' + id + '" name="taxableamount[]" autocomplete="off"></td>\
    <td><input value="' + tax + '" class="w-100 sstxtbx" type="text" id="pi_ret_vat_' + id + '" name="vat[]" autocomplete="off"></td>\
    <td><input value="' + vatamount + '" class="w-100 sstxtbx" type="text" id="pi_ret_vatamount_' + id + '" name="vatamount[]" autocomplete="off"></td>\
    <td><input  class="w-100 sstxtbx" type="text" id="pi_ret_remarks_' + id + '" name="remarks[]" autocomplete="off"></td>\
    <td><input value="' + pi_id + '" class="w-100 sstxtbx" type="text" id="pi_ret_ref_no_' + id + '" name="pi_id_ref[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="pi_ret_serial_no_' + id + '" name="serialno[]" autocomplete="off">\
    <input type="hidden" value="' + part_number + '" name="partno[]" id="pi_ret_partno_' + id + '">\
    </td>\
    /tr>';
    $("#PIRetList_table").append(tr);
    $("#add_to_pi_return_" + id).hide();

}

/* <td><input value="' + discount + '" class="w-100 sstxtbx" type="text" id="pi_ret_discount_' + id + '" name="pi_ret_discount[]" autocomplete="off"></td>\
    <td><input value="' + customcharges + '" class="w-100 sstxtbx" type="text" id="pi_ret_customcharges_' + id + '" name="pi_ret_customcharges[]" autocomplete="off"></td>\ */
// purchease-return-invoiceno

// sales-return-invoiceno
$("#sr_customer_id").on('change', function() {
    var url = $('#url').val();
    var si_id = $('#sr_customer_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { si_id: si_id },
        dataType: 'json',
        url: url + '/' + 'get_si_list',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {
                    $('#si_numbers').find('option').not(':first').remove();
                    $('#sectionSINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#si_numbers').append($('<option>', {
                            value: pin.id,
                            text: pin.doc_number
                        }));

                        $("#sectionSINumberDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.doc_number + "</li>");
                    });
                } else {
                    $('#sectionSINumberDiv .current').html('Select Sales Invoive Number *');
                    $('#si_numbers').find('option').not(':first').remove();
                    $('#sectionSINumberDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#getCtrlSiRetNum", function(event) {

    var selected = $("#si_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var si_ids = "";
    selected.each(function() {
        if (si_ids == "") { si_ids = $(this).val(); } else { si_ids += "," + $(this).val(); }
    });

    var url = $('#url').val();
    var i = 1;
    var outstamount = 0;

    $.ajax({
        url: url + '/' + 'get_si_list_for_si_return',
        type: 'GET',
        data: { si_ids: si_ids },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                tr += '<tr>\
                        <td><input value="' + value.si_id + '" class="w-100 sstxtbx" type="text" id="si_id_' + i + '" name="si_id[]" autocomplete="off"></td>\
                        <td><input value="' + value.part_number + '" class="w-100 sstxtbx" type="text" id="part_number_' + i + '" name="part_number[]" autocomplete="off"></td>\
                        <td><input value="' + value.tax + '" class="w-100 sstxtbx" type="text" id="tax_' + i + '" name="tax[]" autocomplete="off"></td>\
                        <td><input value="' + value.qty + '" class="w-100 sstxtbx" type="text" id="qty_' + i + '" name="qty[]" autocomplete="off"></td>\
                        <td><input value="' + value.unitprice + '" class="w-100 sstxtbx" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.value + '" class="w-100 sstxtbx" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.discount + '" class="w-100 sstxtbx" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.customcharges + '" class="w-100 sstxtbx" type="number" id="customcharges_' + i + '" name="customcharges[]" autocomplete="off" min="0"></td>\
                        <td><input value="' + value.vatamount + '" class="w-100 sstxtbx" type="number" id="vatamount_' + i + '" name="vatamount[]" autocomplete="off" min="0">\
                        <input type="hidden" value="' + value.vatamount + '" id="taxableamount_' + i + '">\
                        <input type="hidden" value="' + value.description + '" id="description_' + i + '">\
                        </td>\
                        <td><input class="primary-btn fix-gr-bg" id="add_to_si_return_' + i + '" onclick="fun_add_to_si_return(' + i + ')" type="button" value="Add To Return"></td>\
                    </tr>';
                outstamount += parseFloat(value.discount);
                i++;
            });

            //$("#bi_amount_to_adjust").val(outstamount);

            $('#siListRetInvo tbody').empty();
            $("#siListRetInvo tbody").append(tr);
            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(XMLHttpRequest);
            $('#siListRetInvo tbody').empty();
        }
    }); // get the product data
});

function fun_add_to_si_return(id) {
    var si_id = $('#si_id_' + id).val();
    var part_number = $('#part_number_' + id).val();
    var tax = $('#tax_' + id).val();
    var qty = $('#qty_' + id).val();
    var unitprice = $('#unitprice_' + id).val();
    var value = unitprice * qty;//$('#value_' + id).val();
    var discount = $('#discount_' + id).val();
    var customcharges = $('#customcharges_' + id).val();
    var vatamount = $('#vatamount_' + id).val();
    var taxableamount = $('#taxableamount_' + id).val();
    var description = $('#description_' + id).val();



    var tr = "";
    tr += '<tr>\
    <td>' + id + '</td>\
    <td><input value="' + part_number + '" class="w-100 sstxtbx" type="text" id="si_ret_part_number_' + id + '" name="si_ret_part_number[]" autocomplete="off"></td>\
    <td><input value="' + description + '"class="w-100 sstxtbx" type="text" id="si_ret_descripttion' + id + '" name="si_ret_descripttion[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="si_ret_product_type_' + id + '" name="si_ret_product_type[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="si_ret_tax_cat_' + id + '" name="si_ret_tax_cat[]" autocomplete="off"></td>\
    <td><input value="' + qty + '" class="w-100 sstxtbx" type="text" id="si_ret_qty_' + id + '" name="qty[]" autocomplete="off"></td>\
    <td><input value="' + unitprice + '" class="w-100 sstxtbx" type="text" id="si_ret_unitprice_' + id + '" name="unitprice[]" autocomplete="off"></td>\
    <td><input value="' + value + '" class="w-100 sstxtbx" type="text" id="si_ret_value_' + id + '" name="value[]" autocomplete="off"></td>\
    <td><input value="' + taxableamount + '" class="w-100 sstxtbx" type="text" id="si_ret_taxableamount_' + id + '" name="taxableamount[]" autocomplete="off"></td>\
    <td><input value="' + tax + '" class="w-100 sstxtbx" type="text" id="si_ret_vat_' + id + '" name="vat[]" autocomplete="off"></td>\
    <td><input value="' + vatamount + '" class="w-100 sstxtbx" type="text" id="si_ret_vatamount_' + id + '" name="vatamount[]" autocomplete="off"></td>\
    <td><input  class="w-100 sstxtbx" type="text" id="si_ret_remarks_' + id + '" name="remarks[]" autocomplete="off"></td>\
    <td><input value="' + si_id + '" class="w-100 sstxtbx" type="text" id="si_ret_ref_no_' + id + '" name="si_id_ref[]" autocomplete="off"></td>\
    <td><input class="w-100 sstxtbx" type="text" id="si_ret_serial_no_' + id + '" name="serialno[]" autocomplete="off">\
    <input type="hidden" value="' + part_number + '" name="partno[]" id="si_ret_partno_' + id + '">\
    </td>\
    /tr>';
    $("#SIRetList_table").append(tr);
    $("#add_to_si_return_" + id).hide();

}

/* <td><input value="' + discount + '" class="w-100 sstxtbx" type="text" id="pi_ret_discount_' + id + '" name="pi_ret_discount[]" autocomplete="off"></td>\
    <td><input value="' + customcharges + '" class="w-100 sstxtbx" type="text" id="pi_ret_customcharges_' + id + '" name="pi_ret_customcharges[]" autocomplete="off"></td>\ */
// sales-return-invoiceno


$(document).on("click", "#dn_si_numbers", function(event) {

    var selected = $("#dn_si_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var si_ids = "";
    selected.each(function() {
        if (si_ids == "") { si_ids = $(this).val(); } else { si_ids += "," + $(this).val(); }
    });

    var url = $('#url').val();
    var i = 1;
    var outstamount = 0;

    $.ajax({
        url: url + '/' + 'get_si_list_for_delivery_note',
        type: 'GET',
        data: { si_ids: si_ids },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            var tr = "";
            $.each(response, function(key, value) {
                $('#lbl-dn-sales-invoice').text(value.si_id);
                tr += '<tr>\
                        <td>' + value.num + '<input type="hidden" value="' + value.si_id + '" id="dn_si_id_' + i + '">\
                        <input type="hidden" value="' + value.unitprice + '" id="dn_unit_price_' + i + '">\
                        <input type="hidden" value="' + value.description + '" id="dn_description_' + i + '">\
                        <input type="hidden" value="' + value.part_number_id + '" id="dn_part_number_id_' + i + '">\
                        </td>\
                        <td><input value="' + value.part_number + '" class="w-100 sstxtbx" type="text" id="part_number_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input value="' + value.qty + '" class="w-100 sstxtbx" type="text" id="qty_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input value="0" class="w-100 sstxtbx" type="text" id="qoh_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input value="' + value.exeqty + '" class="w-100 sstxtbx" type="text" id="exeqty_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input value="' + Math.abs(value.qty - value.exeqty) + '" class="w-100 sstxtbx" type="text" id="balqty_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input value="' + value.exeqty + '" class="w-100 sstxtbx" type="text" id="linkval_' + i + '" autocomplete="off" readonly ></td>\
                        <td><input class="primary-btn fix-gr-bg" id="add_to_si_dn_' + i + '" onclick="fun_add_to_si_dn(' + i + ')" type="button" value="Add To DN"></td>\
                    </tr>';
                outstamount += parseFloat(value.discount);
                i++;
            });

            $('#siListDnInvo tbody').empty();
            $("#siListDnInvo tbody").append(tr);
        }, // /success
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#siListDnInvo tbody').empty();
        }
    });
});

function fun_add_to_si_dn(id) {
    var dn_si_id = $('#dn_si_id_' + id).val();
    var part_number = $('#part_number_' + id).val();
    var part_number_id = $('#dn_part_number_id_' + id).val();
    var dn_description = $('#dn_description_' + id).val();
    var pqty = $('#qty_' + id).val();
    var dn_unit_price = $('#dn_unit_price_' + id).val();
    var qoh = $('#qoh_' + id).val();
    var exeqty = $('#exeqty_' + id).val();
    var balqty = $('#balqty_' + id).val();
    var linkval = $('#linkval_' + id).val();

    var tr = "";
    tr += '<tr>\
    <td>' + id + '</td>\
    <td><input value="' + part_number + '" class="w-100 sstxtbx" type="text" id="dn_si_part_number_' + id + '" name="dn_si_part_number[]" autocomplete="off" readonly ></td>\
    <td><input value="' + dn_description + '"class="w-100 sstxtbx" type="text" id="dn_si_descripttion_' + id + '" name="dn_si_descripttion[]" autocomplete="off" readonly ></td>\
    <td><input value="0" class="w-100 sstxtbx" type="number" id="dn_si_qty_' + id + '" name="dn_si_qty[]" autocomplete="off" onchange="fun_sum_si_dn_qty(' + id + ')"></td>\
    <td><input value="' + pqty + '"class="w-100 sstxtbx" type="text" id="dn_si_pqty_' + id + '" name="dn_si_pqty[]" autocomplete="off" readonly ></td>\
    <td><input value="' + balqty + '"class="w-100 sstxtbx" type="text" id="dn_si_balqty_' + id + '" name="dn_si_balqty[]" autocomplete="off" readonly ></td>\
    <td><input value="' + dn_unit_price + '" class="w-100 sstxtbx" type="text" id="dn_si_unit_price_' + id + '" name="dn_si_unit_price[]" autocomplete="off" readonly ></td>\
    <td><input value="' + (dn_unit_price * pqty) + '" class="w-100 sstxtbx" type="text" id="dn_si_value_' + id + '" name="dn_si_value[]" autocomplete="off" readonly >\
    <input type="hidden" value="' + balqty + '" id="dn_si_balqty_main_' + id + '">\
    <input type="hidden" value="' + part_number_id + '" name="dn_si_part_number_id[]" id="dn_part_number_id_' + id + '"></td>\
    </tr>';
    $("#DelNoteList_table").append(tr);
    $("#add_to_si_dn_" + id).hide();

}

function fun_sum_si_dn_qty(id) {
    var qty = $('#dn_si_qty_' + id + '').val();
    var pqty = $('#dn_si_pqty_' + id + '').val();
    var mbalqty = $('#dn_si_balqty_main_' + id + '').val();
    var unitprice = $('#dn_si_unit_price_' + id + '').val();
    if (mbalqty >= qty) {
        $('#dn_si_balqty_' + id + '').val(Math.abs(Number(qty) - Number(mbalqty)));
        $('#dn_si_value_' + id + '').val(Math.abs(Number(qty) * Number(unitprice)));
    } else {
        alert("Qty Error!!");
    }
}
// delivery note

// delivery advice
$("#da_customer_id").on('change', function() {
    var url = $('#url').val();
    var cus_id = $('#da_customer_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { cus_id: cus_id },
        dataType: 'json',
        url: url + '/' + 'get_si_list_delivery_advice',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {
                    $('#da_si_numbers').find('option').not(':first').remove();
                    $('#sectionDaSINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#da_si_numbers').append($('<option>', {
                            value: pin.id,
                            text: pin.doc_number
                        }));

                        $("#sectionDaSINumberDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.doc_number + "</li>");
                    });
                } else {
                    $('#sectionDaSINumberDiv .current').html('Select Sales Invoive Number *');
                    $('#da_si_numbers').find('option').not(':first').remove();
                    $('#sectionDaSINumberDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

// delivery note

// sub group
$("#group_id_sub").on('change', function() {
    var url = $('#url').val();
    var group_id = $('#group_id_sub').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { group_id: group_id },
        dataType: 'json',
        url: url + '/' + 'get_sub_group',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#subgroup').find('option').not(':first').remove();
                    $('#sectionSubGroupDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#subgroup').append($('<option>', {
                            value: pin.id,
                            text: pin.title
                        }));

                        $("#sectionSubGroupDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.title + "</li>");
                    });
                } else {
                    $('#sectionSubGroupDiv .current').html('Select Group Name *');
                    $('#subgroup').find('option').not(':first').remove();
                    $('#sectionSubGroupDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// sub group

// subgroup2
$("#subgroup").on('change', function() {
    var url = $('#url').val();
    var subgroup = $('#subgroup').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { subgroup: subgroup },
        dataType: 'json',
        url: url + '/' + 'get_subgroup2',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#subgroup2').find('option').not(':first').remove();
                    $('#sectionSubGroup2Div ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#subgroup2').append($('<option>', {
                            value: pin.id,
                            text: pin.title
                        }));

                        $("#sectionSubGroup2Div ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.title + "</li>");
                    });
                } else {
                    $('#sectionSubGroup2Div .current').html('Select Group Name 2 *');
                    $('#subgroup2').find('option').not(':first').remove();
                    $('#sectionSubGroup2Div ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// subgroup2

$(document).on("click", "#addRowCL", function(event) {

    var i = $('#cl-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>";
                tr += "<select class='w-100 sstxtbx' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += "<input class='w-100 sstxtbx' type='hidden' id='partno_" + i + "' name='partno[]'>";
                tr += '</td>';

                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";
                tr += '<input class="w-100 sstxtbx" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';

                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_coo[]' id='part_number_coo_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.coo + "</option>";
                });
                tr += "</select>";
                tr += '<input class="w-100 sstxtbx" type="text" id="coo_' + i + '" name="coo[]" autocomplete="off" ></td>';
                
                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_hscode[]' id='part_number_hscode_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.hscode + "</option>";
                });
                tr += "</select>";
                tr += '<input class="w-100 sstxtbx" type="text" id="hscode_' + i + '" name="hscode[]" autocomplete="off" ></td>';
                
                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_weight[]' id='part_number_weight_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.weight + "</option>";
                });
                tr += "</select>";
                tr += '<input type="hidden" id="hweight_' + i + '" name="hweight[]">';
                tr += '<input class="w-100 sstxtbx" type="text" id="weight_' + i + '" name="weight[]" autocomplete="off" ></td>';

                tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="text" id="price_' + i + '" name="price[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="totalprice_' + i + '" name="totalprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')" readonly></td>\
                            ';
                tr += '</tr>';




                $("#po-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});


$(document).on("click", "#addRowQuote", function(event) {

    var i = $('#cl-row-count').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-item-quote',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>";
                tr += "<select class='niceSelect w-100 dynamicstxt_s bb form-control' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += "<input class='w-100 sstxtbx' type='hidden' id='partno_" + i + "' name='partno[]'>";
                tr += '</td>';

                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";
                tr += '<input class="w-100 sstxtbx" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';

                tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';
                
                
                tr += '<td>';
                tr += "<select class='w-100 sstxtbx' name='part_number_price[]' id='part_number_price_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.price + "</option>";
                });
                tr += "</select>";
                tr += '<input class="w-100 sstxtbx" type="number" id="price_' + i + '" name="price[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';


                tr += '<td><input class="w-100 sstxtbx" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';
                tr += '<td><input class="w-100 sstxtbx" type="number" id="totalprice_' + i + '" name="totalprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')" readonly></td>';
                tr += '</tr>';




                $("#quote-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});


$(document).on("click", "#addRowOS", function(event) {

    var i = $('#os-row-count').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";

                tr += "<td>";
                tr += "<select class='form-control' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += '</td>';

                tr += '<td>';
                tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";
                tr += '<input class="form-control" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';


                tr += '<td><input class="form-control" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0" readonly></td>\
                            ';
                tr += "<td><input class='form-control' type='text' id='remarks_"+i+"' name='remarks[]' autocomplete='off'></td>";
                tr += "<td><input class='form-control' type='text' id='refno_"+i+"' name='refno[]' autocomplete='off'></td>";
                tr += '</tr>';




                $("#os-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});

$(document).ready(function () {


// Get State List By Country Id
$("#country").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();

                         $('#city').find('option').not(':first').remove();
                    $('#sectionCityDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                    GLOBAL_STATE_CHANGE_TRIGGER = true; // Set the flag to true to indicate state change
                    if (window.SELECTED_STATE_ID) {
                            $('#state').val(window.SELECTED_STATE_ID).trigger('change');
                        }
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                     $('#city').find('option').not(':first').remove();
                    $('#sectionCityDiv .current').html('');


                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

// Get City List By State Id
// $(document).on('change', '#state', function (event) {
//     $("#loading_bg").css("display", "block");
//     var url = $('#url').val();
//     var state_id = $(this).val();

//     $.ajax({
//         type: "GET",
//         data: { state_id: state_id },
//         dataType: 'json',
//         url: url + '/' + 'get_city',
//         success: function (data) {
//             console.log(data);

//             $.each(data, function (i, item) {
//                 // item expected to be an array of city objects
//                 if (item.length) {
//                     // detect the corresponding city field based on the state element
//                     var target = '#city';
                   

//                     // If the target is a select element, populate options, else set the input value
//                     if ($(target).is('select')) {
//                         $(target).find('option').not(':first').remove();
//                         $.each(item, function (j, pin) {
//                             $(target).append($('<option>', { value: pin.id, text: pin.name }));
//                         });
//                     } else if ($(target).length) {
//                         // populate input with first city (if available) or clear
//                         $(target).val(item.length ? item[0].name : '');
//                     }

//                     // Also update a section list if present (mirror of sectionStateDiv pattern)
//                     var sectionDiv = '#sectionCityDiv';
                  
//                     if ($(sectionDiv).length) {
//                         $(sectionDiv + ' ul').find('li').not(':first').remove();
//                         $.each(item, function (j, pin) {
//                             $(sectionDiv + ' ul').append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
//                         });
//                     }
//                 } else {
//                     console.log('No cities found for state_id:', state_id);
//                     // no cities found: clear corresponding city fields
//                     var target2 = '#city';

//                     $('#city')
//                     .find('option').not(':first').remove()
//                     .val('')
//                     .trigger('change');


            

//                     toastr.info('No cities found for the selected state. Please add a new city.');

//                     $('#modal_country').val($('#country').val());
//                     $('#modal_state').val(state_id);


//                     $('#addCityModal').modal('show');

                   
//                 }
//             });

//             $("#loading_bg").css("display", "none");
//         },
//         error: function (data) {
//             console.log('Error:', data);
//             $("#loading_bg").css("display", "none");
//         }
//     });
// });




$("#country_n").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country_n').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state_n').find('option').not(':first').remove();
                    $('#sectionStateDiv_n ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state_n').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv_n ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv_n .current').html('');
                    $('#state_n').find('option').not(':first').remove();
                    $('#sectionStateDiv_n ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

});





$(document).ready(function() {
$("#country_n_e").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country_n_e').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state_n_e').find('option').not(':first').remove();
                    $('#sectionStateDiv_n_e ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state_n_e').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv_n_e ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv_n_e .current').html('');
                    $('#state_n_e').find('option').not(':first').remove();
                    $('#sectionStateDiv_n_e ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
});


// Get State List By Country Id

// Get State List By Country Id shipping
$(document).ready(function(){
$("#country_ship").on('change', function() {//kunal modified
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country_ship').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state_ship').find('option').not(':first').remove();
                    $('#sectionStateDiv_ship ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state_ship').append($('<option>', {
                            value: pin.id,
                            text: pin.name,
                            selected: pin.name.toLowerCase() === 'dubai'  // select Dubai by default
                        }));

                        $("#sectionStateDiv_ship ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv_ship .current').html('');
                    $('#state_ship').find('option').not(':first').remove();
                    $('#sectionStateDiv_ship ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
});

// Get State List By Country Id shipping

// Get State List By Country Id
$(document).ready(function(){

$("#country_vat").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country_vat').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#vat_state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#vat_state').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#vat_state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

});

// Get VAT Details By Country Id vat
$(document).ready(function(){
$("#country_vat").on('change', function() {
    $("#loading_bg").css("display", "block");

    var url = $('#url').val();
    var vat_id = $('#country_vat').val();
            $.ajax({
                url: url + '/' + 'get-vat-details',
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    vat_id: vat_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#vat_percentage").val();
                        $("#loading_bg").css("display", "none");
                    } else {
                        if(dataResult['data']!= null){
                        $("#vat_percentage").val(dataResult['data'].vat_percentage);
                        }
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
});
});

// Get VAT Details By Country Id vat

// Get State List By Country Id VAT Page
$("#country_vat-exe").on('change', function() {
    var url = $('#url').val();
    var country_id = $('#country').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state_vat').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state_vat').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state_vat').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// Get State List By Country Id VAT Page


// Get VAT Details By State Id
$("#state_vat").on('change', function() {
    var url = $('#url').val();
    var state_vat = $('#state_vat').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { state_vat: state_vat },
        dataType: 'json',
        url: url + '/' + 'get_vat_state',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#vat_type').find('option').not(':first').remove();

                    $.each(item, function(i, pin) {
                        
                    $('#vat_percentage').val(pin.vat_percentage);

                        $('#state').append($('<option>', {
                            value: pin.id,
                            text: pin.vat_percentage
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// Get VAT Details By Country Id

// Get VAT Details By State Id
$("#customer_with_vat").on('change', function() {
    var url = $('#url').val();
    var customer_with_vat = $('#customer_with_vat').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { customer_with_vat: customer_with_vat },
        dataType: 'json',
        url: url + '/' + 'get_customer_vat',
        success: function(data) {
            console.log(data);             
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    //$('#vat_type').find('option').not(':first').remove();

                    $.each(item, function(i, pin) {
                        
                    $("#vat_type").val(pin.type);
                    $("#vat_country").val(pin.cname);
                    $("#vat_state").val(pin.sname);
                    $("#vat_percentage").val(pin.vat_percentage);
                    $("#vat_number").val(pin.vat_number);
                        

                        $('#state').append($('<option>', {
                            value: pin.id,
                            text: pin.vat_percentage
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });            
            console.log(a);
            $('#get_pending_list').click();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// Get VAT Details By Country Id

//PROFORMA INVOICE PAGE START
//addQtPending PROFORMA INVOICE PAGE
$(document).on("click", "#addQtPending", function(event) {
    
    
    var url = $('#url').val();
    var qt_id = $('#hd_pending_qt_id').val();
    console.log(qt_id);
    console.log(url);
    $.ajax({
        type: "GET",
        data: { qt_id: qt_id },
        dataType: 'json',
        url: url + '/' + 'quotation-pending-item-list',
        success: function(data) {
            console.log("data = ",data);
            var a = '';            
            var tr="";
            var pro_qty = "0";
            
            var qty_total = 0;
            var unitprice_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;

            $.each(data, function(i, item) {
             
                if (item.length) {
                    $.each(item, function(i, pin) {

                        // value = number_format(pin.qty * pin.price, 2, '.', '');
                        // taxamount=number_format(value - pin.discount, 2, '.', '');
                        // vatamount = number_format((taxamount)*5/100, 2, '.', '');
                        // totalamount = ((pin.qty * pin.price) - pin.discount)+((pin.qty * pin.price) - pin.discount)*5/100;
                        // pin.price = number_format(pin.price, 2, '.', '');
                        // pin.discount = number_format(pin.discount, 2, '.', '');

                        console.log(pin)

                        value = parseFloat(pin.qty * pin.price).toFixed(2);
                        taxamount = parseFloat(value - pin.discount).toFixed(2);
                        vatamount = parseFloat(taxamount * 0.05).toFixed(2);
                        totalamount = parseFloat((pin.qty * pin.price) - pin.discount + ((pin.qty * pin.price) - pin.discount) * 0.05).toFixed(2);

                        pin.price = parseFloat(pin.price).toFixed(2);
                        pin.discount = parseFloat(pin.discount).toFixed(2);

                        



                        tr +=  "<tr>\
                        <td><input class='form-control text-center' type='number' id='sort_id_" + i + "' name='sort_id[]' value='"+(i+1)+"' ></td>\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+pin.product_id+"'>"+ pin.part_number +"</option></select></td>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+pin.description+"' autocomplete='off' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='cost_" + i + "' name='cost[]' value='"+formatAmount(pin.cost)+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-center' step='any' type='text' id='tax_" + i + "' name='tax[]' value='"+pin.vat+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='"+pin.qty+"' autocomplete='off' min='0'onchange='calc_change_new(this)'>\
                        <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+pin.product_id+"'/></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='unitprice_" + i + "' name='unitprice[]' value='"+formatAmount(pin.price)+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='value_" + i + "' name='value[]' value='"+formatAmount(value)+"' autocomplete='off' min='0'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='discount_" + i + "' name='discount[]' value='"+formatAmount(pin.discount)+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='taxamount_" + i + "' name='taxableamount[]' value='"+formatAmount(taxamount)+"'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='vatamount_" + i + "' name='vatamount[]' value='"+formatAmount(vatamount)+"'></td>\
                        <td readonly class='jshide'><input class='form-control text-end' step='any' type='text' id='totalamount_" + i + "' name='totalamount[]' value='"+formatAmount((Number(totalamount)))+"'></td>\
                        </tr>";

                        // value = number_format(pin.qty * pin.price, 2, '.', '');
                        // taxamount=number_format(value - pin.discount, 2, '.', '');
                        // vatamount = number_format((taxamount)*5/100, 2, '.', '');
                        // totalamount = ((pin.qty * pin.price) - pin.discount)+((pin.qty * pin.price) - pin.discount)*5/100;
                        
                        // tr += "<tr><td class='jshide12'><input type=checkbox checked id=id_"+ (i+1) +" value="+ pin.id +"></td>\
                        // <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='"+pin.part_number+"' readonly>\
                        // <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+pin.product_id+"'</td>\
                        // <td class='jshide1'><input class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='"+pin.description+"' ></td>\
                        // <td><input class='form-control' type='number' id='qty_" + i + "' name='qty[]' autocomplete='off' min='0' value='"+pin.qty+"' onchange='calc_change(" + i + ")'readonly></td>\
                        // <td class='jshide'><input class='form-control' type='text' id='unitprice_" + i + "' value='"+ number_format(pin.price, 2, '.', '') +"' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change(" + i + ")'></td>\
                        // <td class='jshide'><input class='form-control' type='text' id='value_" + i + "' value='"+ value +"' name='value[]' autocomplete='off' min='0' readonly></td>\
                        // <td class='jshide'><input class='form-control' type='text' id='discount_" + i + "' value='"+ number_format(pin.discount, 2, '.', '') +"' name='discount[]' autocomplete='off' min='0' onchange='calc_change(" + i + ")'></td>\
                        // <td class='jshide'><input class='form-control' type='text' id='taxamount_" + i + "' value='"+ taxamount +"' name='taxamount[]' readonly></td>\
                        // <td class='jshide'><input class='form-control' type='text' id='vatamount_" + i + "' value='"+ vatamount +"' name='vatamount[]' readonly></td>\
                        // <td class='jshide1'><input class='form-control' type='text' id='totalamount_" + i + "' value='"+ number_format(totalamount, 2, '.', '') +"' name='totalamount[]' readonly></td>\
                        // </tr>";


                       

                        $('#row-count').val(i+1);
                        $('#payment_terms').val(pin.payment_terms).trigger('change');
                        $('#delivery_terms').val(pin.delivery_time);
                        $("#sales_man").val(pin.user_id).trigger('change');
                        $("#currency").val(pin.currency_id).trigger('change');
                        $("#narration").val(pin.note);
                        $("#shipping_name").val(pin.cust_name);
                        $("#shipping_address").val(pin.address);
                        $("#country").val(pin.vat_country).trigger('change');
                        $("#state").val(pin.vat_state).trigger('change');
                        $('#lpo_number').val(pin.deal_id);
                        // $('#lpo_date').val(pin.date);
                        $('#lpo_date').val(pin.date ? pin.date.split('-').reverse().join('/') : '');
                        $('#deal_id').val(qt_id);

                        qty_total += Number(pin.qty);
                        unitprice_total += Number(pin.unitprice);
                        value_total += Number(pin.value);
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(pin.taxableamount);
                        vatamount_total += Number(pin.vatamount);
                        totalamount_total += (Number(pin.taxableamount) + Number(pin.vatamount));
                        
                        $('#qty_total').html(qty_total);
                        $('#unitprice_total').html(unitprice_total);
                        $('#value_total').html(value_total);
                        $('#discount_total').html(discount_total);
                        $('#taxableamount_total').html(taxableamount_total);
                        $('#vatamount_total').html(vatamount_total);
                        $('#totalamount_total').html(totalamount_total);

                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#myTable tbody').empty();
            $("#myTable tbody").append(tr);
            $(".jshide").show();
            $(".jshide1").hide();
            update_totals();

        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
//addQtPending

//addQtPendingItems
$(document).on("click", "#addQtPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    $('#pfo-table tbody').empty();
    $("#pfo-table tbody").append(getSelectedRows);    
    $(".jshide12").hide();
    $(".jshide1").show();
    $("#btn_close2").click();
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addQtPendingItems
//PROFORMA INVOICE PAGE END

//SALES INVOICE PAGE START
//addProfoPending
$(document).on("click", "#addProfoPending", function(event) {
    var url = $('#url').val();
    var qt_id = $('#hd_pending_profo_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { qt_id: qt_id },
        dataType: 'json',
        url: url + '/' + 'get-proforma-invoice-items-for-si',
        success: function(data) {
            console.log(data);
            var a = '';            
            var tr="";
            var value=0;
            var taxamount=0;
            var vatamount=0;
            var totalamount=0;
            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {
                        
                        value = number_format(pin.qty * pin.unitprice, 2, '.', '');
                        taxamount=number_format(value - pin.discount, 2, '.', '');
                        vatamount = number_format((taxamount)*5/100, 2, '.', '');
                        totalamount = ((pin.qty * pin.unitprice) - pin.discount)+((pin.qty * pin.unitprice) - pin.discount)*5/100;

                        tr += "<tr><td class='jshide12'><input type=checkbox id=id_"+ (i+1) +" value="+ pin.id +"></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='"+pin.partnumber+"' readonly>\
                        <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+pin.part_number+"'</td>\
                        <td class='jshide1'><input class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='"+pin.description+"' ></td>\
                        <td><input class='form-control qty' type='number' id='qty_" + i + "' name='qty[]' autocomplete='off' min='0' value='"+pin.qty+"' onchange='calc_change_new(this)' readonly></td>\
                        <td class='jshide'><input class='form-control unitprice' type='text' id='unitprice_" + i + "' onblur='formatCurrency(this)' value='"+ formatAmount(pin.unitprice, 2, '.', '') +"' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control' type='text' id='value_" + i + "' value='"+ formatAmount(value) +"' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td class='jshide'><input class='form-control' type='text' id='discount_" + i + "' onblur='formatCurrency(this)' value='"+ formatAmount(pin.discount, 2, '.', '') +"' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control' type='text' id='taxableamount_" + i + "' value='"+ formatAmount(taxamount) +"' name='taxableamount[]' readonly></td>\
                        <td class='jshide'><input class='form-control' type='text' id='vatamount_" + i + "' value='"+ formatAmount(vatamount) +"' name='vatamount[]' readonly></td>\
                        <td class='jshide1'><input class='form-control' type='text' id='totalamount_" + i + "' value='"+ formatAmount(totalamount, 2, '.', '') +"' name='totalamount[]' readonly></td>\
                        </tr>";
                        $('#si-row-count').val(i+1);
                        $('#payment_terms').val(pin.payment_terms);
                        $("#sales_man").val(pin.sales_man).trigger('change');
                        $("#currency").val(pin.currency);
                        $('#delivery_terms').val(pin.delivery_terms);
                        $("#shipping_name").val(pin.shipping_name);
                        $("#shipping_address").val(pin.shipping_address);
                        $("#customer_type").val(pin.customer_type);
                        $("#sale_type").val(pin.sale_type);
                        $("#country").val(pin.customer_country).trigger('change');;
                        $("#state").val(pin.customer_state).trigger('change');;
                        $('#end_user_name').val(pin.end_user_name);
                        $('#contact_person_name').val(pin.contact_person_name);
                        $('#contact_person_email').val(pin.contact_person_email);
                        $('#contact_person_no').val(pin.contact_person_no);
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#table_id tbody').empty();
            $("#table_id tbody").append(tr);
            $(".jshide").show();
            $(".jshide1").hide();
            update_totals();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#addProfoPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    $('#si-table tbody').empty();
    $("#si-table tbody").append(getSelectedRows);    
    $('#table_id tbody').empty();
    $(".jshide12").hide();
    $(".jshide1").show();
    $("#btn_close2").click();
});
//addProfoPending
//SALES INVOICE PAGE END

//SALES RETURN PAGE START
//addSRPending
$(document).on("click", "#addSRPending", function(event) {
    var url = $('#url').val();
    var id = $('#hd_pending_dn_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { id: id },
        dataType: 'json',
        url: url + '/' + 'get-si-list-for-si-return',
        success: function(data) {
            console.log("Salers Return",data);
            var a = '';            
            var tr="";
            var value=0;
            var taxamount=0;
            var vatamount=0;
            var totalamount=0;

            var qty_total=0;
            var value_total=0;
            var discount_total=0;
            var taxableamount_total=0;
            var vatamount_total=0;
            var amount_total=0;


            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {
                        
                        value = (pin.qty * pin.unitprice);
                        taxamount=(value - pin.discount);
                        vatamount = ((taxamount)*pin.tax/100);
                        totalamount = ((pin.qty * pin.unitprice) - pin.discount)+((pin.qty * pin.unitprice) - pin.discount)*pin.tax/100;

                        qty_total += pin.qty;
                        value_total += pin.qty * pin.unitprice;
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(taxamount);
                        vatamount_total += Number(vatamount);
                        amount_total += Number(totalamount);

                        tr += "<tr><td><input class='form-control text-center' type='text' name='sort_id[]' value="+ pin.sort_id +"></td>\
                        <td><select class='form-control noborder' name='part_number[]'><option value='"+pin.part_number+"'>"+pin.partnumber+"</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='"+pin.part_number+"' />\
                        <input type='hidden' name='product_type[]' value='"+ (pin.product_type || '') +"' />\
                        <input type='hidden' name='product_type_part_number_text[]' value='"+ (pin.partnumber || '') +"' />\
                        <input class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='"+pin.description+"' ></td>\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' autocomplete='off' min='0' value='"+pin.tax+"' readonly></td>\
                        <td><input class='form-control text-center qty'  data-enter-skip type='number' id='qty_" + i + "' name='qty[]' autocomplete='off' min='0'  value='"+pin.qty+"' onchange='calc_change_new(this)' onkeypress='return set_license_key(this, event)'></td>\
                        <td><input class='form-control text-end unitprice' type='text' id='unitprice_" + i + "' value='"+ formatAmount(pin.unitprice) +"' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='value_" + i + "' value='"+ formatAmount(value) +"' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='discount_" + i + "' value='"+ formatAmount(pin.discount) +"' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='taxableamount_" + i + "' value='"+ formatAmount(taxamount) +"' name='taxableamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='vatamount_" + i + "' value='"+ formatAmount(vatamount) +"' name='vatamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='totalamount_" + i + "' value='"+ formatAmount(totalamount) +"' name='totalamount[]' readonly></td>\
                        <td><input class='form-control text-end srl' type='test' id='srl_"+ i +"' name='serial_no[]' value='"+ (pin.serial_no || '') +"'></td>\
                        </tr>";
                        $('#dn-row-count').val(i+1);
                        $('#dn_doc_number').val(pin.dn_doc_number);
                        // $('#dn_doc_date').val(pin.dn_doc_date);
                        $('#dn_doc_date').val(pin.dn_doc_date ? pin.dn_doc_date.split('-').reverse().join('/') : '');

                        
                        $('#si_doc_number').val(pin.doc_number);
                        // $('#si_doc_date').val(pin.doc_date);
                        $('#si_doc_date').val(pin.doc_date ? pin.doc_date.split('-').reverse().join('/') : '');

                        
                        //$('#si_doc_number').val(pin.invoice_no);
                        //$('#si_doc_date').val(pin.invoice_date);

                        $('#reference_no').val(pin.lpo_no);
                        // $('#reference_date').val(pin.lpo_date);
                        $('#reference_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#printed_invoice_number').val(pin.printed_invoice_number);
                        // $('#supplier_name').val(pin.supplier_name);
                        $('#deal_id').val(pin.deal_code);

                        $('#payment_terms').val(pin.paymentterms).trigger('change');
                        $("#currency").val(pin.currency).trigger('change');
                        $('#delivery_terms').val(pin.delivery_terms).trigger('change');
                        $('#end_user_name').val(pin.end_user_name);
                        $('#contact_person_name').val(pin.contact_person_name);
                        $('#contact_person_email').val(pin.contact_person_email);
                        $('#contact_person_no').val(pin.contact_person_no);
                        
                        $('#adj_dln_no').val(pin.doc_number);
                        $('#adj_siv_no').val(pin.invoice_no);
                        $('#adj_total').val(Number(totalamount)+Number(vatamount));

                        $('#sales_man').val(pin.sales_man).trigger('change');

                      if (pin.ref_supplier_id) {
    var supplierIds = pin.ref_supplier_id.split(','); // convert string to array
    $('#ref_supplier_id').val(supplierIds).trigger('change');
}





                        $('#qty_total').text(qty_total.toFixed(2));
                        $('#value_total').text(value_total.toFixed(2));
                        $('#discount_total').text(discount_total.toFixed(2));
                        $('#taxableamount_total').text(taxableamount_total.toFixed(2));
                        $('#vatamount_total').text(vatamount_total.toFixed(2));
                        $('#amount_total').text(amount_total.toFixed(2));
                        $('#tax').val(pin.tax);

                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#myTable tbody').empty();
            $("#myTable tbody").append(tr);
            if (typeof applyLicenseQtyHighlightForRow === 'function') {
                $('#myTable tbody tr').each(function() {
                    applyLicenseQtyHighlightForRow($(this));
                });
            }
            update_totals();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#addDnPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    $('#si-table tbody').empty();
    $("#si-table tbody").append(getSelectedRows);    
    $('#table_id tbody').empty();
    $(".jshide12").hide();
    $(".jshide1").show();
    $("#btn_close2").click();
});
//addsiPending
//SALES RETURN PAGE END

$(document).ready(function () {
//DELIVERY NOTE PAGE START 
function normalizeDateForDnCfc(dateStr) {
    if (!dateStr) return '';
    var s = String(dateStr).trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
        var p = s.split('-');
        return p[2] + '/' + p[1] + '/' + p[0];
    }
    return s;
}

function applySiChargesToDnFrightTable(charges) {
    var $table = $('#fright_table');
    if (!$table.length) return;

    var $tbody = $table.find('tbody');
    var $baseRow = $tbody.find('tr:first');
    if (!$baseRow.length) return;

    $tbody.find('.js-example-basic-single').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });
    $tbody.find('.date-picker').each(function () {
        if (this._flatpickr) {
            this._flatpickr.destroy();
        }
    });

    var $template = $baseRow.clone();
    $tbody.empty();

    var rows = Array.isArray(charges) && charges.length ? charges : [{}];
    rows.forEach(function (charge, idx) {
        var rowNo = idx + 1;
        var $row = $template.clone();
        $row.attr('id', 'fright_row_' + rowNo);

        $row.find('select, input').each(function () {
            var $el = $(this);
            var oldId = $el.attr('id');
            if (oldId) {
                $el.attr('id', oldId.replace(/_\d+$/, '_' + rowNo));
            }
            $el.val('');
        });

        $row.find('input[name="cfc_date[]"]').val(normalizeDateForDnCfc(charge.date || ''));
        $row.find('input[name="cfc_bill_no[]"]').val(charge.bill_number || '');
        var $nameSelect = $row.find('select[name="cfc_name[]"]');
        if (charge.cfc_name && !$nameSelect.find('option[value="' + charge.cfc_name + '"]').length) {
            $nameSelect.append('<option value="' + charge.cfc_name + '">' + charge.cfc_name + '</option>');
        }
        $nameSelect.val(charge.cfc_name || '');
        var $creditSelect = $row.find('select[name="cfc_credit_account[]"]');
        if (charge.cfc_credit_account && !$creditSelect.find('option[value="' + charge.cfc_credit_account + '"]').length) {
            $creditSelect.append('<option value="' + charge.cfc_credit_account + '">' + charge.cfc_credit_account + '</option>');
        }
        $creditSelect.val(charge.cfc_credit_account || '');
        $row.find('input[name="cfc_amount[]"]').val(charge.cfc_amount != null ? charge.cfc_amount : '');
        $row.find('input[name="cfc_remarks[]"]').val(charge.cfc_remarks || '');

        $tbody.append($row);
    });

    $('#fright_row').val(rows.length);

    $tbody.find('.js-example-basic-single').select2({ width: '100%' });
    $tbody.find('.date-picker').each(function () {
        flatpickr(this, { dateFormat: 'd/m/Y', allowInput: true });
    });

    $tbody.find('input[name="cfc_amount[]"]').trigger('input');
}

function fetchAndApplySiChargesToDn(opts) {
    if (!$('#delivery-note-create-form').length) return;
    opts = opts || {};
    var siId = opts.si_id != null ? String(opts.si_id).trim() : '';
    var invoiceNo = opts.invoice_no != null ? String(opts.invoice_no).trim() : '';
    var dnId = opts.dn_id != null ? String(opts.dn_id).trim() : '';
    if (!siId && !invoiceNo && !dnId) return;

    var url = $('#url').val();
    var token = $('input[name="_token"]').first().val();
    var req = { _token: token };
    if (siId) req.si_id = siId;
    if (invoiceNo) req.invoice_no = invoiceNo;
    if (dnId) req.dn_id = dnId;

    $.ajax({
        type: "POST",
        url: url + '/' + 'delivery-note-get-cfc-by-si',
        dataType: 'json',
        data: req,
        success: function (res) {
            applySiChargesToDnFrightTable((res && res.data) ? res.data : []);
        },
        error: function (err) {
            console.log('Unable to load SI charges for DN', err);
        }
    });
}

$(document).on("click", "#addDNPending", function(event) {
    console.log("hbhreffefd")
    var url = $('#url').val();
    var si_id = $('#hd_pending_dn_id').val() || $('#si_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { si_id: si_id },
        dataType: 'json',
        url: url + '/' + 'sales-invoice-pending-item-list',
        success: function(data) {
            console.log(data);
            var a = '';            
            var tr="";
            var value=0;
            var taxamount=0;
            var vatamount=0;
            var totalamount=0;
            var qty_total=0;
            var unitprice_total=0;
            var value_total=0;
            var discount_total=0;
            var taxableamount_total=0;
            var vatamount_total=0;
            var total_amount=0;
            var type2Items = [];
            var siDeviceSerial = '';
            
            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {
                        
                        value = (pin.qty * pin.unitprice);
                        taxamount=(value - pin.discount);
                        vatamount = ((taxamount)*pin.tax/100);
                        totalamount = ((pin.qty * pin.unitprice) - pin.discount)+((pin.qty * pin.unitprice) - pin.discount)*pin.tax/100;

                        tr += "<tr><td><input class='form-control text-center' type='number' name='sort_id[]' value="+ pin.sort_id +"></td>\
                        <td><select class='form-control' name='part_number[]'><option value='"+pin.part_number+"'>"+pin.partnumber+"</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='"+pin.part_number_txt+"' /><input type='hidden' name='product_type[]' value='"+pin.product_type+"' /><textarea class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='"+pin.description+"' >"+pin.description+"</textarea></td>\
                        <td><input class='form-control text-center qty rc' type='number' id='tax_" + i + "' name='tax[]' autocomplete='off' min='0' value='"+(pin.tax ? Number(pin.tax) : '')+"' readonly></td>\
                        <td><input class='form-control text-center qty rc' type='number' id='qty_" + i + "' onkeypress='return set_license_key_normal(event, this)' data-enter-skip  name='qty[]' autocomplete='off' min='0' value='"+pin.qty+"' onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='unitprice_" + i + "' value='"+ formatAmount(pin.unitprice) +"' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='value_" + i + "' value='"+ formatAmount(value) +"' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='discount_" + i + "' value='"+ formatAmount(pin.discount) +"' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='taxableamount_" + i + "' value='"+ formatAmount(taxamount) +"' name='taxableamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='vatamount_" + i + "' value='"+ formatAmount(vatamount) +"' name='vatamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='totalamount_" + i + "' value='"+ formatAmount(totalamount) +"' name='totalamount[]' readonly></td>\
                        <td><input class='form-control text-end srl' type='test' id='srl_" + i + "' name='serial_no[]' onclick='srlno_add(" + i + ")' ></td>\
                        </tr>";
                        $('#si-row-count').val(i+1);
                        $('#payment_terms').val(pin.payment_terms);
                        $("#lpo_no").val(pin.lpo_number);
                  
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $("#currency").val(pin.currency);
                        $("#invoice_no").val(pin.doc_number);
                       
                        $('#invoice_date').val(pin.doc_date ? pin.doc_date.split('-').reverse().join('/') : '');

                    
                        
                        $("#sales_man").val(pin.sales_man).trigger('change');
                        $("#deal_id").val(pin.deal_code);
                        $("#supplier_name").val(pin.supplier_name);

                        qty_total += pin.qty;
                        unitprice_total += Number(pin.unitprice);
                        value_total += Number(value);
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(taxamount);
                        vatamount_total =+ Number(vatamount);
                        total_amount += Number(totalamount);
                        
                        $("#qty_total").text(qty_total);
                        $("#unitprice_total").text(unitprice_total.toFixed(2));
                        $("#value_total").text(value_total.toFixed(2));
                        $("#discount_total").text(discount_total.toFixed(2));
                        $("#taxableamount_total").text(taxableamount_total.toFixed(2));
                        $("#vatamount_total").text(vatamount_total.toFixed(2));
                        $("#total_amount").text(total_amount.toFixed(2));

                        $('#end_user_name').val(pin.end_user_name);
                        $('#contact_person_name').val(pin.contact_person_name);
                        $('#contact_person_email').val(pin.contact_person_email);
                        $('#contact_person_no').val(pin.contact_person_no);

                        console.log("pin.product_type", pin.device_serial, pin.part_number, pin.part_number_txt);

                        // Collect type-2 (device) items for device serial modal
                        siDeviceSerial = pin.device_serial || siDeviceSerial;
                        if (parseInt(pin.product_type) === 2) {
                            var _partKey = String(pin.part_number);
                            var _existing2 = type2Items.filter(function(x){ return x.part_number === _partKey; });
                            if (_existing2.length) {
                                _existing2[0].qty += parseFloat(pin.qty) || 0;
                            } else {
                                type2Items.push({ part_number: _partKey, partnumber: pin.partnumber || pin.part_number_txt, qty: parseFloat(pin.qty) || 0 });
                            }
                        }
                    }
                    
                    );
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#myTable tbody').empty();
            $("#myTable tbody").append(tr);
            //$(".jshide").show();
            //$(".jshide1").hide();
            update_totals();

            // Build DeviceSerialModal for type-2 (device) items
            if (type2Items.length > 0) {
                $('#device_serial_container').show();
                if (siDeviceSerial) { $('#device_serial').val(siDeviceSerial); }
                // Parse existing device_serial map
                var dsMap = {};
                if (siDeviceSerial) {
                    siDeviceSerial.split('|').forEach(function(seg){
                        seg = seg.trim();
                        if (!seg) return;
                        var kv = seg.split(/: */, 2);
                        if (kv.length !== 2) return;
                        var key = kv[0].trim();
                        var vals = kv[1].split(',').map(function(s){ return s.trim(); }).filter(function(s){ return s; });
                        if (key) dsMap[key] = vals;
                    });
                }
                var modalHtml = '';
                var rowNum = 1;
                type2Items.forEach(function(item) {
                    var qty = Math.round(item.qty) || 1;
                    var existingSerials = dsMap[item.part_number] || [];
                    var inputs = '';
                    for (var j = 1; j <= qty; j++) {
                        var sval = existingSerials[j-1] ? existingSerials[j-1].replace(/"/g, '&quot;') : '';
                        inputs += '<div class="serial-input-row" data-index="' + j + '">' +
                            '<span class="text-muted" style="min-width:20px;">' + j + '.</span>' +
                            '<input type="text" name="serial_no[' + item.part_number + '][]" class="form-control form-control-sm part-serial-input" value="' + sval + '" autocomplete="off">' +
                            '</div>';
                    }
                    modalHtml += '<div class="part-serial-section" data-part-number="' + item.part_number + '" data-qty="' + qty + '" data-row-index="' + (rowNum-1) + '">' +
                        '<div class="part-serial-header d-flex align-items-center justify-content-between mb-2">' +
                            '<div>' +
                                '<div class="part-name">Row ' + rowNum + ': ' + (item.partnumber || item.part_number) + '</div>' +
                                '<small class="text-muted">Qty: ' + qty + '</small>' +
                            '</div>' +
                            '<div class="serial-count-display qty-badge">0 of ' + qty + '</div>' +
                        '</div>' +
                        '<div class="serial-inputs-list" data-qty="' + qty + '">' +
                            '<input type="hidden" value="' + item.part_number + '" name="part_number[]" />' +
                            inputs +
                        '</div>' +
                    '</div>';
                    rowNum++;
                });
                $('#serial-parts-container').html(modalHtml);
                try {
                    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                        window._deviceSerialModal = new bootstrap.Modal(document.getElementById('DeviceSerialModal'));
                    }
                } catch(e) {}
            } else {
                $('#device_serial_container').hide();
                $('#device_serial').val('');
                $('#serial-parts-container').html('');
            }

            $("#addDNPending").prop("disabled", false);
            fetchAndApplySiChargesToDn({
                si_id: si_id,
                invoice_no: $('#invoice_no').val(),
                dn_id: $('#dln_id').val()
            });
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
});
$(document).on("click", "#addDNPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    $('#DelNoteList_table tbody').empty();
    $("#DelNoteList_table tbody").append(getSelectedRows);
    $(".jshide12").hide();
    $(".jshide1").show();
    $("#btn_close2").click();
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});

$(document).on('change blur', '#invoice_no', function () {
    if (!$('#delivery-note-create-form').length) return;
    fetchAndApplySiChargesToDn({
        si_id: $('#si_id').val(),
        invoice_no: $('#invoice_no').val(),
        dn_id: $('#dln_id').val()
    });
});

$(document).on('change', '#si_id', function () {
    if (!$('#delivery-note-create-form').length) return;
    fetchAndApplySiChargesToDn({
        si_id: $('#si_id').val(),
        invoice_no: $('#invoice_no').val(),
        dn_id: $('#dln_id').val()
    });
});

$(document).ready(function () {
    if (!$('#delivery-note-create-form').length) return;
    var siId = String($('#si_id').val() || '').trim();
    var invoiceNo = String($('#invoice_no').val() || '').trim();
    var dnId = String($('#dln_id').val() || '').trim();
    if (siId || invoiceNo || dnId) {
        fetchAndApplySiChargesToDn({
            si_id: siId,
            invoice_no: invoiceNo,
            dn_id: dnId
        });
    }
});
//DELIVERY NOTE PAGE END

//addPoPending

// $(document).on("click", "#addPoPending", function(event) {
//     console.log("clicked-1212121212")
//     var url = $('#url').val();
//     var po_id = $('#hd_pending_po_id').val();
//     //var vat = $('#net_vat').val();
//     console.log(url);
//     $.ajax({
//         type: "GET",
//         data: { po_id: po_id },
//         dataType: 'json',
//         url: url + '/' + 'goods-receipt-note-pending-item-list',
//         success: function(data) {
//             var data_count=1;
//             if(data==""){
//                 data_count=0;
//             }
//             console.log(data);
//             var a = '';            
//             var tr="";
//             var pro_qty = "0";
//             var selected_pos = [];
//             var selected_dealid = [];
//             var qty_total = 0;
//             var value_total = 0;
//             var discount_total = 0;
//             var taxableamount_total = 0;
//             var vatamount_total = 0;
//             var totalamount_total = 0;
//             var fright_total = 0;
//             var customs_total = 0;

//             $.each(data, function(i, item) {
//                 if (item.length) {
//                     $.each(item, function(i, pin) {
                        
//                         if (pin.pro_qty != null){
//                             pro_qty=pin.pro_qty;
//                         }
//                         var taxamount = pin.taxableamount;
//                         var vatamount = pin.vatamount;
//                         var totalamount = Number(pin.taxableamount) + Number(pin.vatamount);
//                         var hscode=0;

//                         qty_total += (pin.po_qty - pin.grn_qty);
//                         value_total += Number(pin.value);
//                         discount_total += Number(pin.discount);
//                         fright_total += Number(pin.fright);
//                         customs_total += Number(pin.customcharges);
//                         taxableamount_total += Number(taxamount);
//                         vatamount_total += Number(vatamount);
//                         totalamount_total += Number(totalamount);
//                         if(pin.hscode=="" || pin.hscode==null){
//                             hscode=0;
//                         } else { hscode=pin.hscode; }
                        
//                         tr +=  "<tr>\
//                         <td><input class='form-control text-center' type='text' id='sort_id_" + i + "' name='sort_id[]' value='" + (i+1) +"' />\
//                         <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+pin.part_id+"'>"+ pin.part_number +"</option></select></td>\
//                         <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+ pin.description +"'>\
//                         <input type='hidden' id='part_number_txt_" + i + "' name='part_number_txt[]' value='"+pin.part_number+"'/><input type='hidden' id='po_itm_id_" + i + "' name='po_itm_id[]' value='"+pin.id+"'/><input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+pin.part_id+"'/><input type='hidden' id='list_po_id_" + i + "' name='list_po_id[]' value='"+pin.po_id+"'/>\
//                         <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='"+(pin.po_qty - pin.grn_qty)+"'/></td>";
                        
//                         if(pin.company_id==2){
//                         tr +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode_txt[]' id='hscode_" + i + "' value='"+hscode+"' readonly></td>";
//                         } else{
//                             tr +=  "<input type='hidden' id='hscode_" + i + "' name='hscode_txt[]' value='0' readonly></td>";
//                         }
//                         tr +=  "<td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='po_qty_" + i + "' min='0' value='"+(pin.po_qty)+"' readonly></td>\
//                         <td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='exe_qty_" + i + "' min='0' value='"+(pin.grn_qty)+"' readonly></td>\
//                         <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' value='"+parseInt(pin.tax)+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
//                         <td><input class='form-control text-center qty_total' type='number' id='qty_" + i + "' name='qty[]' value='"+(pin.po_qty - pin.grn_qty)+"' autocomplete='off' min='0'onchange='calc_change_new(this)' onkeypress='set_license_key_po("+i+","+pin.product_type+")'></td>\
//                         <td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='bal_qty_" + i + "' min='0' value='0' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='unitprice_" + i + "' name='unitprice[]' value='"+formatAmount(pin.unitprice)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='value_" + i + "' name='value[]' value='"+formatAmount(pin.value)+"' autocomplete='off' min='0' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='discount_" + i + "' name='discount[]' value='"+formatAmount(pin.discount)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='fright_" + i + "' name='fright[]' value='"+formatAmount(pin.fright)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='customcharges_" + i + "' name='customcharges[]' value='"+formatAmount(pin.customcharges)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='taxamount_" + i + "' name='taxableamount[]' value='"+formatAmount(taxamount)+"' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='vatamount_" + i + "' name='vatamount[]' value='"+formatAmount(vatamount)+"' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' step='any' id='totalamount_" + i + "' name='totalamount[]' value='"+formatAmount((Number(pin.taxableamount)+Number(pin.vatamount)).toFixed(2))+"' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' name='serial_no[]' value='"+(pin.serial_no ?? '')+"' readonly></td>\
//                         </tr>";
//                         $('#row-count').val(i+1);
//                         // $('#lpo_date').val(pin.po_date);

//                         $('#lpo_date').val(pin.po_date ? pin.po_date.split('-').reverse().join('/') : '');

//                         //$("#createdby").val(pin.created_by);
//                         $("#payment_terms").val(pin.payment_terms);
//                         $("#currency").val(pin.currency);
//                         $("#sales_person").val(pin.user_id).trigger('change');
//                         $("#reference").val(pin.narration);
                        
//                         if(!selected_pos.includes(pin.doc_number)) {
//                             selected_pos.push(pin.doc_number);
//                         }
//                         $('#lpo_number').val(selected_pos);

//                         if(!selected_dealid.includes(pin.code)) {
//                             selected_dealid.push(pin.code);
//                         }
//                         $("#deal_id").val(selected_dealid);

//                         $("#qty_total").text(qty_total);
//                         $("#value_total").text(value_total.toFixed(2));
//                         $("#discount_total").text(discount_total.toFixed(2));
//                         $("#fright_total").text(fright_total.toFixed(2));
//                         $("#customs_total").text(customs_total.toFixed(2));
//                         $("#taxableamount_total").text(taxableamount_total.toFixed(2));
//                         $("#vatamount_total").text(vatamount_total.toFixed(2));
//                         $("#totalamount_total").text(totalamount_total.toFixed(2));


//                     });
//                 } else {
//                     $('#sectionStateDiv .current').html('');
//                     $('#state').find('option').not(':first').remove();
//                     $('#sectionStateDiv ul').find('li').not(':first').remove();
//                 }
//             });
//             console.log(a);
            
//             //$("#table_id thead").show();
//             if (data_count == 0) {
//                 const rowHtml = '<tr>\
//     <td><input type="text" class="form-control" name="sort_id[]" value="1" /></td>\
//     <td class="noborder"><select class="form-control noborder" name="part_number[]"></select></td> \
//     <td><input class="form-control" type="text" name="description[]" autocomplete="off" readonly="true">\
//         <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>\
//         <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>\
//         <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>\
//         <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden></td>\
//     <td><input type="number" class="form-control" name="tax[]" onchange="calc_change_new(this)"></td>\
//     <td><input class="form-control" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>\
//     <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
//     <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly></td>\
//     <td><input class="form-control" type="number" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
//     <td><input class="form-control" type="number" name="fright[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
//     <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
//     <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
//     <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
//     <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>\
//     <td><input class="form-control" type="text" name="serial_no[]"></td>\
// </tr>';

//                 $('#myTable tbody').empty();
//                 $("#myTable tbody").append(rowHtml);
//                 fillTableToFitScreenHeight('myTable', 65);
//             } else {
//                 $('#myTable tbody').empty();
//             }
//             $("#myTable tbody").append(tr);
//             $(".jshide").show();
//             $(".jshide1").hide();
//             update_totals();

//         },
//         error: function(data) {
//             console.log('Error:', data);
//         }
//     });


// });

/**
 * Calculate and update popup GRN totals
 * Production-ready function for live recalculation
 */
function updatePopupGRNTotals() {
    var totalQty = 0;
    var totalPrice = 0;
    var totalDiscount = 0;
    var totalValue = 0;
    var totalVat = 0;
    var grandTotal = 0;

    // Loop through each data row (skip header rows)
    $(".popupGRN tbody tr").not(".doc-header-row").each(function() {
        var $row = $(this);
        var $checkbox = $row.find('.po_check');
        
        // Only count checked rows
        if ($checkbox.length === 0 || $checkbox.is(':checked')) {
            var qty = parseFloat($row.find('.qty-input').val()) || 0;
            var price = parseFloat($row.find('.price-input').val()) || 0;
            var discount = parseFloat($row.find('.discount-input').val()) || 0;
            var tax = parseFloat($row.find('.tax-input').val()) || 0;
            
            // Calculate: Value = (qty * price) - discount
            var rowValue = (qty * price) - discount;
            
            // Calculate VAT amount based on tax percentage
            var vatAmount = rowValue * (tax / 100);
            
            // Total with VAT = Value + VAT
            var rowTotal = rowValue + vatAmount;
            
            // Update value cell in the row (show value including VAT) — formatted with commas
            $row.find('.value-cell').text(formatAmount(rowTotal));
            
            // Update hidden inputs for when submitting (keep plain numeric strings)
            $row.find('.taxamt-input').val(rowValue.toFixed(2));
            $row.find('.vatamt-input').val(vatAmount.toFixed(2));
            $row.find('.ttamt-input').val(rowTotal.toFixed(2));
            
            totalQty += qty;
            totalPrice += price;
            totalDiscount += discount;
            totalValue += rowValue;
            totalVat += vatAmount;
            grandTotal += rowTotal;
        }
    });

    // Update footer totals (formatted with comma thousands separators)
    // Qty: show without trailing decimals when integer, allow up to 2 decimals otherwise
    var formattedQty = Number(totalQty).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    $('#popup_total_qty').text(formattedQty);
    $('#popup_total_price').text(formatAmount(totalPrice));
    $('#popup_total_discount').text(formatAmount(totalDiscount));
    $('#popup_total_value').text(formatAmount(grandTotal));
}

// Live recalculation on input changes in popup GRN (including tax)
$(document).on('input change', '.popupGRN .qty-input, .popupGRN .price-input, .popupGRN .discount-input, .popupGRN .tax-input', function() {
    updatePopupGRNTotals();
});

// Recalculate when checkbox is toggled
$(document).on('change', '.popupGRN .po_check, #po_check_all', function() {
    updatePopupGRNTotals();
});

let isAutoFilling = true;


$(document).on("click", "#addPoPending", function(event) {
    console.log("clicked-1212121212")
    var url = $('#url').val();
    var po_id = $('#hd_pending_po_id').val();
    //var vat = $('#net_vat').val();
    console.log(url);
  $.ajax({ 
    type: "GET",
    data: { po_id: po_id },
    dataType: 'json',
    url: url + '/' + 'goods-receipt-note-pending-item-list',
    success: function(data) {
        var tr = "";
        var qty_total = 0;
        var value_total = 0;
        var discount_total = 0;
        var taxableamount_total = 0;
        var vatamount_total = 0;
        var totalamount_total = 0;
        var fright_total = 0;
        var selected_pos = [];
        var selected_dealid = [];
        var selected_ref_company = [];
        var customs_total = 0;
        var price_total = 0;
        var sales_person = null;
        var newOption = null;


        console.log("data = ",data);

        // Loop through each PO document
        $.each(data[0], function(doc_number, items) {
        let colspan = (window.COMPANY_ID == 2) ? 10 : 9;

            // Add header row for this document
            tr += `
                <tr class="doc-header-row">
                    <th colspan="${colspan}">${doc_number}</th>
                </tr>
            `;

            // Loop through each item under this PO
            $.each(items, function(i, pin) {
                console.log("pin", pin);
                var poBalance = (pin.po_qty - pin.grn_qty);
                var taxPercent = parseInt(pin.tax) || 0;
                var hscode = pin.hscode ?? 0;
                var unitPrice = Number(pin.unitprice);
                var discountAmt = Number(pin.discount);
                console.log("tax",taxPercent)
                var taxamount = pin.taxableamount;
                var vatamount = pin.vatamount;
                var totalamount = Number(taxamount) + Number(vatamount);
                
                // Calculate: Value = (qty * price) - discount
                var rowValue = (poBalance * unitPrice) - discountAmt;
                
                // Calculate VAT based on tax percentage
                var vatAmount = rowValue * (taxPercent / 100);
                
                // Total with VAT
                var rowTotal = rowValue + vatAmount;

                qty_total += poBalance;
                price_total += unitPrice;
                value_total += rowTotal;  // Use total with VAT
                discount_total += discountAmt;
                fright_total += Number(pin.fright);
                customs_total += Number(pin.customcharges);
                taxableamount_total += rowValue;
                vatamount_total += vatAmount;
                totalamount_total += rowTotal;

        console.log("PO_ID", pin.id);


                // Build table row
                tr += "<tr>" +
                    "<input type='hidden' class='freight-input' name='freight1[]' value='" + formatAmount(pin.fright) + "'>" +
                    "<input type='hidden' class='customcharges-input' name='customcharges1[]' value='" + formatAmount(pin.fright) + "'>" +
                    "<input type='hidden' class='taxamt-input' name='taxableamount1[]' value='" + formatAmount(taxamount) + "'>" +
                    "<input type='hidden' class='vatamt-input' name='vatamount1[]' value='" + formatAmount(vatamount) + "'>" +
                    "<input type='hidden' class='ttamt-input' name='totalamount1[]' value='" + formatAmount(totalamount.toFixed(2)) + "'>" +
                    "<input type='hidden' class='serialno-input' name='serialno1[]' value='" + (pin.serial_no ?? '') + "'>" +
                    "<input type='hidden' class='partid-input' name='part_number_id[]' value='" + (pin.part_id ?? '') + "'>" +
                    "<input type='hidden' class='poid-input' name='po_id[]' value='" + pin.po_id + "'>" +
                    "<input type='hidden' class='producttype-input' name='product_type[]' value='" + pin.product_type + "'>" +
                    "<td class='no-toggle'><input type='checkbox' class='po_check' checked name='selected_item_id[]' value='" + pin.id + "'></td>" +
                    "<td class='no-toggle'><input type='text' class='form-control form-control-sm text-center border-0' value='" + pin.sort_id + "'></td>" +
                    "<td>" + pin.part_number + "</td>" +
                    "<td>" + pin.description + "</td>";

                if (pin.company_id == 2) {
                    tr += "<td><input class='form-control border-0 hscode-input' type='text' " +
                          "name='hscode_txt1[]' id='hscode_1" + i + "' value='" + hscode + "' readonly></td>";
                } else {
                    tr += "<input type='hidden' class='hscode-input' id='hscode_1" + i + 
                          "' name='hscode_txt1[]' value='0' readonly>";
                }

                tr += "<td class='no-toggle'><input type='number' class='form-control form-control-sm text-center tax-input border-0' " +
                      "name='tax1[" + pin.id + "]' value='" + parseInt(pin.tax) + "'></td>" +
                      "<td class='no-toggle'><input type='number' class='form-control form-control-sm text-center qty-input border-0' " +
                      "name='qty1[" + pin.id + "]' value='" + poBalance + "'></td>" +
                      "<td class='no-toggle'><input type='number' step='0.01' class='form-control form-control-sm text-end price-input border-0' " +
                      "name='unitprice1[" + pin.id + "]' value='" + unitPrice.toFixed(2) + "'></td>" +
                      "<td class='no-toggle'><input type='number' step='0.01' class='form-control form-control-sm text-end discount-input border-0' " +
                      "name='discount1[" + pin.id + "]' value='" + discountAmt.toFixed(2) + "'></td>" +
                      "<td class='text-end no-toggle value-cell'>" + rowTotal.toFixed(2) + "</td>" +
                      "</tr>";

                // Set top-level info
                $('#lpo_date').val(pin.po_date ? pin.po_date.split('-').reverse().join('/') : '');
                $("#payment_terms").val(pin.payment_terms);
                $("#currency").val(pin.currency).trigger('change');

                if(pin.sales_person){
                    sales_person = pin.sales_person;
// $("#sales_person").val(pin.sales_person).trigger('change');
                }else{
                    //create new option for it
                    newOption = new Option(pin.sales_person_name, pin.sales_person, true, true);
                    // $('#sales_person').append(newOption).trigger('change');

                }

                

                // ref_company_id may be a comma-separated string (e.g., "1,18"). Split into individual ids and ensure uniqueness.
                if (pin.ref_company_id) {
                    var ids = String(pin.ref_company_id).split(',').map(function(x){ return x.trim(); }).filter(Boolean);
                    ids.forEach(function(id) {
                        if (!selected_ref_company.includes(id)) selected_ref_company.push(id);
                    });
                }

                // Update hidden inputs container (used by GRN form) instead of targeting a possibly commented-out select
                try {
                    var $container = $('#ref_company_hidden_inputs');
                    if ($container.length) {
                        $container.empty();
                        selected_ref_company.forEach(function(v) {
                            // Append one hidden input per selected company id
                            $container.append('<input type="hidden" name="ref_company_id[]" value="' + $('<div>').text(v).html() + '" />');
                        });

                        // Update visible text input: prefer company names (look up in modal select); fall back to ids
                        var displayText = selected_ref_company.map(function(v) {
                            var $opt = $('#modal_ref_company_select option[value="' + v + '"]');
                            if ($opt.length) return $opt.text().trim();
                            return v;
                        }).filter(Boolean).join(', ');
                        $('#customer_reference_input').val(displayText);
                    } else {
                        // Fallback for pages that still have a select element
                        $("#ref_company_id").val(selected_ref_company).trigger('change');
                    }
                } catch (e) {
                    console.error('Error updating ref_company inputs', e);
                    $("#ref_company_id").val(selected_ref_company).trigger('change');
                }

                if(!selected_pos.includes(pin.doc_number)) selected_pos.push(pin.doc_number);
                $('#lpo_number').val(selected_pos);

                if(!selected_dealid.includes(pin.code)) selected_dealid.push(pin.code);
                $("#deal_id").val(selected_dealid);

                isAutoFilling = false;
                // Populate shipping details from PO
                if(pin.shipping_supplier) {
                    $("#shipping_supplier").val(pin.shipping_supplier).trigger('change');
                }
                if(pin.shipping_name) {
                    $("#shipping_name").val(pin.shipping_name);
                }
                if(pin.shipping_email) {
                    $("#shipping_email").val(pin.shipping_email);
                }
                if(pin.shipping_contact_no) {
                    $("#shipping_contact_no").val(pin.shipping_contact_no);
                }
                if(pin.shipping_address_1) {
                    $("#shipping_address_1").val(pin.shipping_address_1);
                }
                setTimeout(() => {
    isAutoFilling = true;
}, 100);

                if(pin.supplier_country) {
                    $("#country").val(pin.supplier_country).trigger('change');
                }
                if(pin.supplier_state) {
                    console.log("SETTING STATE TO ", pin.supplier_state);
                    window.SELECTED_STATE_ID = pin.supplier_state;
                    // $("#state").val(pin.supplier_state).trigger('change');
                }
                if(pin.vat_percent) {
                    $("#vat_percent").val(pin.vat_percent);
                }
                if(pin.vat_number) {
                    $("#vat_number").val(pin.vat_number);
                }
                if(pin.supplier_type) {
                    $("#supplier_type").val(pin.supplier_type).trigger('change');
                }
                if(pin.purchase_type) {
                    $("#purchase_type").val(pin.purchase_type).trigger('change');
                }

            });

        });

        if(sales_person){
      
$("#sales_person").val(sales_person).trigger('change');
                }else if(newOption){
     
                    $('#sales_person').append(newOption).trigger('change');

                }
        // Inject table rows
        $(".popupGRN tbody").empty().append(tr);

        // Update footer totals
        $('#popup_total_qty').text(qty_total);
        $('#popup_total_price').text(price_total.toFixed(2));
        $('#popup_total_discount').text(discount_total.toFixed(2));
        $('#popup_total_value').text(value_total.toFixed(2));

        // Show modal
        $("#po_pending_popup_win").modal("show");
    },
});


});




let poPendingFirstLoad = true;
$(document).on("click", "#addPoPendingINMAINTable", function () {


    // GET ALL VALUES FROM MODAL TABLE (popupGRN)
    var rows = $(".popupGRN tbody tr");

// Instead of clearing the whole table, build a list of new rows and replace/append only the first N rows
    let newRows = [];
    let i = 0;

    // Build new row HTMLs into an array (only for checked rows)
    rows.each(function () {
        // only include checked rows
        if (!$(this).find(".po_check").prop("checked")) return;

        // READ ALL EDITABLE FIELDS FROM MODAL
        let pin_id      = $(this).find(".po_check").val();
        let sl          = i + 1;
        let part_no     = $(this).find("td:eq(2)").text().trim();
        let desc        = $(this).find("td:eq(3)").text().trim();
        let tax         = $(this).find(".tax-input").val();
        let qty         = $(this).find(".qty-input").val();
        let price       = $(this).find(".price-input").val();
        let discount    = $(this).find(".discount-input").val();
        let hscode      = $(this).find(".hscode-input").val();
        let part_id     = $(this).find(".partid-input").val();
        let po_id       = $(this).find(".poid-input").val();
        let serialno    = $(this).find(".serialno-input").val();
        let product_type    = $(this).find(".producttype-input").val();

        // Calculations
        let value = (qty * price);
        var fin_taxableamount = value - parseFloat(discount);
        var fin_vatamount = fin_taxableamount * (parseFloat(tax) / 100);
        var total_amount = fin_taxableamount + fin_vatamount;
        if (isNaN(value)) value = 0;

        // Build new row HTML
        let newRow = `
        <tr>

            <td><input class='form-control text-center' type='text' 
                name='sort_id[]' value='${sl}' /></td>

            <td>
                <select class='form-control' name='part_number[]'>
                    <option value='${part_id}'>${part_no}</option>
                </select>
            </td>

            <td>
                <textarea class='form-control' type='text' name='description[]' rows="1" value='${desc}'>${desc}</textarea>
                <input type='hidden' name='part_number_txt[]' value='${part_no}'/>
                <input type='hidden' name='po_itm_id[]' value='${pin_id}'/>
                <input type='hidden' name='part_id[]' value='${part_id}'/>
                <input type='hidden' name='list_po_id[]' value='${po_id}'/>
                <input type='hidden' name='grn_qty[]' value='${qty}'/>
                <input type='hidden' name='product_type[]' value='${product_type}'/>
            </td>`;

            if (Number(window.COMPANY_ID) === 2) {
                        newRow +=  "<td><input class='form-control text-center' type='text' autocomplete='off' name='hscode_txt[]' id='hscode_" + i + "' value='"+hscode+"' readonly></td>";
                        } else{
                            newRow +=  "<input type='hidden' id='hscode_" + i + "' name='hscode_txt[]' value='0' readonly></td>";
            }

            newRow += `
            <td>
                <input class='form-control text-center' type='number' onchange="calc_change_new(this)"
                       name='tax[]' value='${tax}'/>
            </td>

            <td>
                <input class='form-control text-center qty_total' type='number' data-enter-skip onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"
                       name='qty[]' value='${qty}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'  onblur="formatCurrency(this)"
                            autocomplete="off" min="0" onchange="calc_change_new(this)" 
                       name='unitprice[]' value='${formatAmount(price)}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='value[]' value='${formatAmount(value)}' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='discount[]' value='${formatAmount(discount)}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='fright[]' value='0.00'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'  onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='customcharges[]' value='0.00'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='taxableamount[]' value='${formatAmount(fin_taxableamount)}' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='vatamount[]' value='${formatAmount(fin_vatamount)}' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'
                       name='totalamount[]' value='${formatAmount(total_amount)}' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='serial_no[]' value='${serialno}' readonly/>
            </td>

        </tr>`;

        newRows.push(newRow);
        i++;
    });

    // Replace first N existing rows and append any extras, keep remaining rows intact
    let $tbody = $("#myTable tbody");
    let existingRows = $tbody.find("tr");


    // if (existingRows.length === 0) {
        $tbody.empty().append(newRows.join(''));
    // } else {
    //     for (let idx = 0; idx < newRows.length; idx++) {
    //         if (idx < existingRows.length) {
    //             $(existingRows[idx]).replaceWith(newRows[idx]);
    //         } else {
    //             $tbody.append(newRows[idx]);
    //         }
    //     }
    // }

    poPendingFirstLoad = false;     // maintain existing behavior flag
    
    update_totals();



    // CLOSE POPUP
    $("#po_pending_popup_win").modal("hide");

});



//addPoPending

//addPoPendingItems
$(document).on("click", "#addPoPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone().get().reverse();
getSelectedRows.shift(); // remove first selected row

$("#table_id thead").hide();

    $('#pi-table tbody').empty();
    $("#pi-table tbody").append(getSelectedRows);
    $(".jshide3").hide();
    $(".jshide2").show();
    $("#btn_close2").click();
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addPoPendingItems

//addGRNPIPending
// $(document).on("click", "#addGRNPending", function(event) {
//     var url = $('#url').val();
//     var grn_id = $('#hd_pending_grn_id').val();
//     var po_id = $('#hd_pending_po_id').val();
//     console.log(url);
//     $.ajax({
//         type: "GET",
//         data: { grn_id: grn_id, po_id: po_id },
//         dataType: 'json',
//         url: url + '/' + 'goods-receipt-note-for-pi-item-list',
//         success: function(data) {
//             console.log(data);
//             var a = '';            
//             var tr="";
//             var pro_qty = "0";
            
//             var qty_total = 0;
//             var unitprice_total = 0;
//             var value_total = 0;
//             var discount_total = 0;
//             var taxableamount_total = 0;
//             var vatamount_total = 0;
//             var totalamount_total = 0;

//             $.each(data, function(i, item) {
                
//                 if (item.length) {
//                     $.each(item, function(i, pin) {

//                         if (pin.pro_qty != null){
//                             pro_qty=pin.pro_qty;
//                         }
                        
//                         tr +=  "<tr>\
//                         <td><input class='form-control text-center' type='number' id='sort_id_" + i + "' name='sort_id[]' value='"+(i+1)+"' ></td>\
//                         <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+pin.part_id+"'>"+ pin.part_number +"</option></select></td>\
//                         <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='"+pin.part_number+"'/>\
//                         <input type='hidden' id='part_id_" + i + "' name='hscode_txt[]' value='"+pin.part_id+"'/>\
//                         <input type='hidden' id='part_id_" + i + "' name='product_type[]' value='"+pin.part_id+"'/>\
//                         <input type='hidden' id='part_id_" + i + "' name='product_type_part_number_text[]' value='"+pin.part_id+"'/>\
//                         <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='"+pin.grn_qty+"'/>\
//                         <input class='form-control' type='text' name='description[]' autocomplete='off' value='"+pin.description+"' >\
//                         <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' value='"+pin.tax+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
//                         <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='"+pin.grn_qty+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='unitprice_" + i + "' name='unitprice[]' value='"+formatAmount(pin.unitprice)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='value_" + i + "' name='value[]' value='"+formatAmount(pin.value)+"' autocomplete='off' min='0'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='discount_" + i + "' name='discount[]' value='"+formatAmount(pin.discount)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='fright_" + i + "' name='fright[]' value='"+formatAmount(pin.fright)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='customcharges_" + i + "' name='customcharges[]' value='"+formatAmount(pin.customcharges)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='taxamount_" + i + "' name='taxableamount[]' value='"+formatAmount(pin.taxableamount)+"'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='vatamount_" + i + "' name='vatamount[]' value='"+formatAmount(pin.vatamount)+"'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='any' type='text' id='totalamount_" + i + "' name='totalamount[]' value='"+formatAmount((Number(pin.taxableamount) + Number(pin.vatamount)))+"'></td>\
//                         </tr>";
//                         $('#row-count').val(i+1);
//                         $('#lpo_number').val(pin.lpo_number);
//                         $('#po_id').val(pin.po_id);
//                         // $('#lpo_date').val(pin.lpo_date);
//                         $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

//                         $('#payment_terms').val(pin.payment_terms);
//                         $('#currency').val(pin.currency);
//                         $('#bill_number').val(pin.bill_number);
//                         // $('#bill_date').val(pin.bill_date);
//                         $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

//                         $('#awbno').val(pin.awbno);
//                         $('#boeno').val(pin.boeno);
//                         $('#warehouse').val(pin.warehouse);
                        
//                         $('#grn_no').val(pin.doc_number);
//                         // $('#grn_date').val(pin.grn_date);
//                         $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');
                        
//                         $('#sales_person').val(pin.sales_person).trigger('change');
                        
//                         $('#reference').val(pin.reference);
//                         $('#narration').val(pin.narration);
                        
//                         $('#deal_id').val(pin.deal_id);

//                         $('#shipping_name').val(pin.shipping_name);
//                         $('#shipping_address_1').val(pin.shipping_address_1);
//                         $('#shipping_address_2').val(pin.shipping_address_2);
//                         $('#shipping_contact_no').val(pin.shipping_contact_no);
//                         $('#supplier_type').val(pin.supplier_type);
//                         $('#purchase_type').val(pin.purchase_type);
//                         $('#country').val(pin.supplier_country).trigger('change');
//                         $('#state').val(pin.supplier_state);

//                         qty_total += Number(pin.grn_qty);
//                         unitprice_total += Number(pin.unitprice);
//                         value_total += Number(pin.value);
//                         discount_total += Number(pin.discount);
//                         taxableamount_total += Number(pin.taxableamount);
//                         vatamount_total += Number(pin.vatamount);
//                         totalamount_total += (Number(pin.taxableamount) + Number(pin.vatamount));

//                         $('#qty_total').html(qty_total);
//                         $('#unitprice_total').html(unitprice_total);
//                         $('#value_total').html(value_total);
//                         $('#discount_total').html(discount_total);
//                         $('#taxableamount_total').html(taxableamount_total);
//                         $('#vatamount_total').html(vatamount_total);
//                         $('#totalamount_total').html(totalamount_total);
//                     });
//                 } else {
//                     $('#sectionStateDiv .current').html('');
//                     $('#state').find('option').not(':first').remove();
//                     $('#sectionStateDiv ul').find('li').not(':first').remove();
//                 }
//                 get_deal_code();
//             });
//             console.log(a);
//             $('#myTable tbody').empty();
//             $("#myTable tbody").append(tr);
//             $(".jshide").show();
//             $(".jshide1").hide();
//             update_totals();
//         },
//         error: function(data) {
//             console.log('Error:', data);
//         }
//     });

// });




$(document).on("click", "#addGRNPending", function () {

    let url = $('#url').val();
    let grn_id = $('#hd_pending_grn_id').val();

    console.log("URL =", grn_id);

    

    $.ajax({
        type: "GET",
        data: { grn_id: grn_id },
        dataType: "json",
        url: url + '/' + 'goods-receipt-note-for-pi-item-list',

        success: function (data) {
          

            let tr = "";
            let row = 0;

            let qty_total = 0;
            let unitprice_total = 0;
            let value_total = 0;
            let discount_total = 0;
            let taxableamount_total = 0;
            let vatamount_total = 0;
            let totalamount_total = 0;

            // Collect top-level identifiers when multiple rows are selected
            let selected_lpo_numbers = [];
            let selected_po_ids = [];
            let selected_bill_numbers = [];
            let selected_grn_numbers = []; 
            let selected_dealcodes = [];
            let ref_company_ids = [];
            var sales_person = null;
            var newOption = null;

            $.each(data, function(i, item) {



                if (!item.length) return;

                $.each(item, function(idx, pin) {


                    row++;

                    tr += `
                    <tr>
                        <td class='no-toggle'><input type="checkbox" class="po_check" checked value="${pin.id}"></td>

                        <td class="text-center no-toggle">${row}</td>

                        <td>${pin.part_number}
                            <input type="hidden" name="part_id[]" value="${pin.part_id}">
                        </td>

                        <td>${pin.description ?? ""}</td>

                        <td class="text-center no-toggle pe-0">
                            <input type="number" class="form-control grn-tax-inp border-0 text-center" 
                                name="tax[]" id="tax_${row}" 
                                value="${parseInt(pin.tax)}">
                            <input type="hidden" class="grnid-input" name="grn_id[]" value="${pin.grn_id}">
                        </td>

                        <td class="text-center  no-toggle  pe-0">
                            ${pin.grn_qty}
                            <input type="hidden" name="grn_qty[]" value="${pin.grn_qty}">
                        </td>

                        <td class="text-center no-toggle pe-0">
                            <input type="text" class="form-control grn-qty-inp border-0 text-center" 
                                name="qty[]" id="qty_${row}" 
                                value="${pin.grn_qty}">
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-unit-inp border-0 text-end" 
                                name="unitprice[]" id="unitprice_${row}" 
                                value="${Number(pin.unitprice).toFixed(2)}" >
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-value-inp border-0 text-end" 
                                name="value[]" readonly id="value_${row}" 
                                value="${Number(pin.value).toFixed(2)}">
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-discount-inp border-0 text-end" 
                                name="discount[]" id="discount_${row}" 
                                value="${Number(pin.discount).toFixed(2)}" >
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-fright-inp border-0 text-end" 
                                name="fright[]" id="fright_${row}" 
                                value="${Number(pin.fright).toFixed(2)}" >
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-customcharges-inp border-0 text-end" 
                                name="customcharges[]" id="customcharges_${row}" 
                                value="${Number(pin.customcharges).toFixed(2)}" >
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-taxableamount-inp border-0  text-end" readonly
                                name="taxableamount[]" id="taxamount_${row}" 
                                value="${Number(pin.taxableamount).toFixed(2)}">
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-vatamount-inp border-0 text-end"  readonly
                                name="vatamount[]" id="vatamount_${row}" 
                                value="${Number(pin.vatamount).toFixed(2)}">
                        </td>

                        <td class="text-end no-toggle pe-0">
                            <input type="text" step="any" class="form-control grn-totalamount-inp border-0 text-end" readonly
                                name="totalamount[]" id="totalamount_${row}" 
                                value="${(Number(pin.taxableamount) + Number(pin.vatamount)).toFixed(2)}">
                        </td>
                    </tr>`;



                        // Collect top-level identifiers (may be multiple when user selects several checkboxes)
                        if (pin.lpo_number && !selected_lpo_numbers.includes(pin.lpo_number)) selected_lpo_numbers.push(pin.lpo_number);
                        if (pin.po_id && !selected_po_ids.includes(String(pin.po_id))) selected_po_ids.push(String(pin.po_id));

                        // Keep date per-item (last one wins for single-date fields)
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#payment_terms').val(pin.payment_terms).trigger('change');
                        $('#currency').val(pin.currency).trigger('change');;
                        if (pin.bill_number && !selected_bill_numbers.includes(pin.bill_number)) selected_bill_numbers.push(pin.bill_number);
                        // Keep bill date as single field
                        $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

                        $('#awbno').val(pin.awbno);
                        $('#boeno').val(pin.boeno);
                        $('#warehouse').val(pin.warehouse).trigger('change');
                        
                        if (pin.doc_number && !selected_grn_numbers.includes(pin.doc_number)) selected_grn_numbers.push(pin.doc_number);
                        // $('#grn_date').val(pin.grn_date);
                        $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');
                        
                        $('#sales_person').val(pin.sales_person).trigger('change');

                        if(!sales_person && pin.sales_person){
                            sales_person = pin.sales_person;
                        }else if(!sales_person && pin.sales_person_name){
                            //create new option for it
                            newOption = new Option(pin.sales_person_name, pin.sales_person_name, true, true);
                        }


                        
                        $('#reference').val(pin.reference);
                        $('#narration').val(selected_bill_numbers.join(', '));

                        
                        if(pin.deal_code && !selected_dealcodes.includes(pin.deal_code)) selected_dealcodes.push(pin.deal_code);
                        $('#deal_id').val(selected_dealcodes.join(', '));

                        // Collect ref_company_id values (support comma lists like "1,18") and dedupe
                        if (pin.ref_company_id) {
                            String(pin.ref_company_id).split(',').map(function(x){ return x.trim(); }).filter(Boolean).forEach(function(id){
                                if (ref_company_ids.indexOf(id) === -1) ref_company_ids.push(id);
                            });
                        }

                        // Update hidden inputs container and visible customer reference input (mirror pi_add behavior)
                        try {
                            var $refContainer = $('#ref_company_hidden_inputs');
                            if ($refContainer.length) {
                                $refContainer.empty();
                                var refNames = [];
                                ref_company_ids.forEach(function(id){
                                    $refContainer.append('<input type="hidden" name="ref_company_id[]" value="' + $('<div>').text(id).html() + '" />');
                                    var optText = $('#modal_ref_company_select option[value="' + id + '"]').text().trim();
                                    if (optText) refNames.push(optText);
                                });
                                $('#customer_reference_input').val(refNames.join(', '));
                            } else {
                                // fallback: set a legacy field if present
                                $('#ref_company_id').val(ref_company_ids).trigger('change');
                            }
                        } catch (e) {
                            console.error('Error updating ref company inputs', e);
                            $('#ref_company_id').val(ref_company_ids).trigger('change');
                        }

                        
                        

                         if(pin.shipping_supplier) {
                            console.log("SETTING SHIPPING SUPPLIER TO ", pin.shipping_supplier);
                            $("#shipping_supplier").val(pin.shipping_supplier).trigger('change');
                            }

                        $('#shipping_name').val(pin.shipping_name);
                        $('#shipping_address_1').val(pin.shipping_address_1);
                        $('#shipping_address_2').val(pin.shipping_address_2);
                        $('#shipping_contact_no').val(pin.shipping_contact_no);
                        $('#supplier_type').val(pin.supplier_type).trigger('change');
                        $('#purchase_type').val(pin.purchase_type).trigger('change');
                        $('#country').val(pin.supplier_country).trigger('change');
                        $('#state').val(pin.supplier_state).trigger('change');

                    // running totals
                    qty_total += Number(pin.grn_qty);
                    unitprice_total += Number(pin.unitprice);
                    value_total += Number(pin.value);
                    discount_total += Number(pin.discount);
                    taxableamount_total += Number(pin.taxableamount);
                    vatamount_total += Number(pin.vatamount);
                    totalamount_total += Number(pin.taxableamount) + Number(pin.vatamount);

                });

            });
            
                // Set top-level fields (comma-joined if multiple selections)
                if (selected_lpo_numbers.length) $('#lpo_number').val(selected_lpo_numbers.join(', '));
                if (selected_po_ids.length) $('#po_id').val(selected_po_ids.join(','));
                if (selected_bill_numbers.length) $('#bill_number').val(selected_bill_numbers.join(', ')).trigger('change');
                if (selected_grn_numbers.length) $('#grn_no').val(selected_grn_numbers.join(', '));

                // Inject table rows
                $(".popupPI tbody").empty().append(tr);

                 if(sales_person){
                    $("#sales_person").val(sales_person).trigger('change');
                }else if(newOption){
     
                    $('#sales_person').append(newOption).trigger('change');

                }

                // Recalculate & format each inserted row for display
                $(".popupPI tbody tr").each(function() {
                    recalcPopupPIRow($(this));
                });
                updatePopupPITotals();


                // Show modal
                $("#po_pending_popup_win").modal("show");
           

        },

        error: function (err) {
            console.log("ERROR", err);
        }
    });

});

// Recalculation helpers for popup PI (GRN pending)
function recalcPopupPIRow($tr) {
    // Read inputs (strip commas)
    var qty = parseFloat(($tr.find('.grn-qty-inp').val() || '').toString().replace(/,/g,'')) || 0;
    var unit = parseFloat(($tr.find('.grn-unit-inp').val() || '').toString().replace(/,/g,'')) || 0;
    var discount = parseFloat(($tr.find('.grn-discount-inp').val() || '').toString().replace(/,/g,'')) || 0;
    var fright = parseFloat(($tr.find('.grn-fright-inp').val() || '').toString().replace(/,/g,'')) || 0;
    var customcharges = parseFloat(($tr.find('.grn-customcharges-inp').val() || '').toString().replace(/,/g,'')) || 0;
    var taxPercent = parseFloat(($tr.find('.grn-tax-inp').val() || '').toString().replace(/,/g,'')) || 0;

    // Calculate
    var value = qty * unit;
    var taxable = value - discount + fright + customcharges;
    var vat = taxable * (taxPercent/100);
    var total = taxable + vat;

    // Update readonly fields (formatted)
    $tr.find('.grn-value-inp').val(formatAmount(value));
    $tr.find('.grn-taxableamount-inp').val(formatAmount(taxable));
    $tr.find('.grn-vatamount-inp').val(formatAmount(vat));
    $tr.find('.grn-totalamount-inp').val(formatAmount(total));
}

function updatePopupPITotals() {
    var totalQty = 0;
    var totalValue = 0;
    var totalDiscount = 0;
    var totalTaxable = 0;
    var totalVat = 0;
    var grandTotal = 0;

    $(".popupPI tbody tr").each(function() {
        var $tr = $(this);
        if (!$tr.find('.po_check').is(':checked')) return;
        var qty = parseFloat(($tr.find('.grn-qty-inp').val() || '').toString().replace(/,/g,'')) || 0;
        var value = parseFloat(($tr.find('.grn-value-inp').val() || '').toString().replace(/,/g,'')) || 0;
        var discount = parseFloat(($tr.find('.grn-discount-inp').val() || '').toString().replace(/,/g,'')) || 0;
        var taxable = parseFloat(($tr.find('.grn-taxableamount-inp').val() || '').toString().replace(/,/g,'')) || 0;
        var vat = parseFloat(($tr.find('.grn-vatamount-inp').val() || '').toString().replace(/,/g,'')) || 0;

        totalQty += qty;
        totalValue += value;
        totalDiscount += discount;
        totalTaxable += taxable;
        totalVat += vat;
        grandTotal += (taxable + vat);
    });

    // set footer elements if present
    $('#popup_total_qty').text(Number(totalQty).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
    $('#popup_total_price').text(formatAmount(totalValue));
    $('#popup_total_discount').text(formatAmount(totalDiscount));
    $('#popup_total_value').text(formatAmount(grandTotal));
}

// Live handlers
$(document).on('input change', '.popupPI .grn-qty-inp, .popupPI .grn-unit-inp, .popupPI .grn-discount-inp, .popupPI .grn-fright-inp, .popupPI .grn-customcharges-inp, .popupPI .grn-tax-inp', function() {
    var $tr = $(this).closest('tr');
    recalcPopupPIRow($tr);
    updatePopupPITotals();
});

// Checkbox change should recalc totals
$(document).on('change', '.popupPI .po_check, #po_check_all', function() {
    updatePopupPITotals();
});

// Format editable numeric inputs on blur
$(document).on('blur', '.popupPI .grn-qty-inp, .popupPI .grn-unit-inp, .popupPI .grn-discount-inp, .popupPI .grn-fright-inp, .popupPI .grn-customcharges-inp', function() {
    var v = parseFloat(($(this).val() || '').toString().replace(/,/g,''));
    if (isNaN(v)) $(this).val(''); else $(this).val(formatAmount(v));
    var $tr = $(this).closest('tr');
    recalcPopupPIRow($tr);
    updatePopupPITotals();
});

$(document).on("click", "#addGRNPendingINMAINTable", function () {

    // Also load GRN credit/freight accounts into PI bottom table.
    fetchAndApplySelectedGrnCharges();

    let mainTbody = $("#myTable tbody");
    let currentRows = $("#myTable tbody tr").length;
    let newRowCount = 0;

    function numVal($el) { return parseFloat(($el.val() || '').toString().replace(/,/g,'')) || 0; }

    // Build list of new rows from checked popup rows (do not clear existing `#myTable` rows)
    let newRows = [];
    let newRowCountLocal = 0;

    $(".popupPI tbody tr").each(function () {
        let chk = $(this).find(".po_check");
        if (!chk.is(":checked")) return;

        newRowCountLocal++;

        let pin_id      = $(this).find(".po_check").val();
        let part_no        = $(this).find("td:eq(2)").text().trim();
        let description    = $(this).find("td:eq(3)").text().trim();
        let part_id        = $(this).find("input[name='part_id[]']").val();
        let tax            = Math.round(numVal($(this).find("input[name='tax[]']")));
        let qty            = numVal($(this).find("input[name='qty[]']"));
        let price          = numVal($(this).find("input[name='unitprice[]']"));
        let value          = numVal($(this).find("input[name='value[]']"));
        let discount       = numVal($(this).find("input[name='discount[]']"));
        let fright         = numVal($(this).find("input[name='fright[]']"));
        let customcharges  = numVal($(this).find("input[name='customcharges[]']"));
        let taxable        = numVal($(this).find("input[name='taxableamount[]']"));
        let vat            = numVal($(this).find("input[name='vatamount[]']"));
        let grn_id        = $(this).find("input[name='grn_id[]']").val();
        let total          = numVal($(this).find("input[name='totalamount[]']"));

        let newRow = `
            <tr>

                <td><input type="text" class="form-control text-center" 
                    name="sort_id[]" value="${newRowCountLocal}" /></td>

                <td class="noborder">
                    <select class="form-control noborder" name="part_number[]">
                        <option value="${part_id}" selected>${part_no}</option>
                    </select>
                </td>

                <td>
                    <textarea class="form-control" name="description[]" rows="1">${description}</textarea>
                    <input class="form-control" type="text" name="part_number_txt[]" hidden>
                    <input class="form-control" type="text" name="hscode_txt[]" hidden>
                    <input class="form-control" type="text" name="product_type[]" hidden>
                    <input class="form-control" type="text" name="product_type_part_number_text[]" hidden>
                    <input class="form-control" type="text" name="grn_id_main[]" value="${grn_id}" hidden>
                    <input class="form-control" type="text" name="grn_item_id[]" value="${pin_id}" hidden>
                </td>

                <td><input type="number" class="form-control text-center" step="1"
                    name="tax[]" value="${tax}"  onchange="calc_change_new(this)"></td>

                <td><input type="text" class="form-control text-center" step="1"
                    name="qty[]" value="${formatAmount(qty)}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>

                <td><input type="text" class="form-control text-end"
                    name="unitprice[]" value="${formatAmount(price)}" 
                    onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>

                <td><input type="text" class="form-control text-end"
                    name="value[]" value="${formatAmount(value)}" readonly></td>

                <td><input type="text" class="form-control text-end"
                    name="discount[]" value="${formatAmount(discount)}" 
                    onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>

                <td><input type="text" class="form-control text-end"
                    name="fright[]" value="${formatAmount(fright)}" 
                    onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>

                <td><input type="text" class="form-control text-end"
                    name="customcharges[]" value="${formatAmount(customcharges)}" 
                    onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>

                <td><input type="text" class="form-control text-end"
                    name="taxableamount[]" value="${formatAmount(taxable)}" readonly></td>

                <td><input type="text" class="form-control text-end"
                    name="vatamount[]" value="${formatAmount(vat)}" readonly></td>

                <td><input type="text" class="form-control text-end"
                    name="totalamount[]" value="${formatAmount(total)}" readonly></td>

            </tr>
        `;

        newRows.push(newRow);
    });

    // Merge new rows into the existing table without clearing it
    let existingRowsFinal = mainTbody.find('tr');
    let existingCountFinal = existingRowsFinal.length;

    // if (existingCountFinal === 0) {
        mainTbody.empty().append(newRows.join(''));
    // } else {
    //     for (let idx = 0; idx < newRows.length; idx++) {
    //         if (idx < existingCountFinal) {
    //             $(existingRowsFinal[idx]).replaceWith(newRows[idx]);
    //         } else {
    //             mainTbody.append(newRows[idx]);
    //         }
    //     }
    // }

    // Recalculate totals once
    update_totals();

    $("#po_pending_popup_win").modal("hide");
});



//addGRNPIPending
//addGRNPendingItems
function normalizeDateForUi(dateStr) {
    if (!dateStr) return '';
    var s = String(dateStr).trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
        var p = s.split('-');
        return p[2] + '/' + p[1] + '/' + p[0];
    }
    return s;
}

function applyGrnChargesToPiFrightTable(charges) {
    var $table = $('#fright_table');
    if (!$table.length) return;

    var $tbody = $table.find('tbody');
    var $baseRow = $tbody.find('tr:first');
    if (!$baseRow.length) return;

    // Reset plugins before cloning rows.
    $tbody.find('.js-example-basic-single').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });
    $tbody.find('.date-picker').each(function () {
        if (this._flatpickr) {
            this._flatpickr.destroy();
        }
    });

    var $template = $baseRow.clone();
    $tbody.empty();

    var rows = Array.isArray(charges) && charges.length ? charges : [{}];
    rows.forEach(function (charge, idx) {
        var rowNo = idx + 1;
        var $row = $template.clone();
        $row.attr('id', 'fright_row_' + rowNo);

        $row.find('select, input').each(function () {
            var $el = $(this);
            var oldId = $el.attr('id');
            if (oldId) {
                $el.attr('id', oldId.replace(/_\d+$/, '_' + rowNo));
            }
            $el.val('');
        });

        $row.find('input[name="cfc_date[]"]').val(normalizeDateForUi(charge.date || ''));
        $row.find('input[name="cfc_bill_no[]"]').val(charge.bill_number || '');
        $row.find('select[name="cfc_name[]"]').val(charge.cfc_name || '');
        $row.find('select[name="cfc_credit_account[]"]').val(charge.cfc_credit_account || '');
        $row.find('input[name="cfc_amount[]"]').val(charge.cfc_amount != null ? charge.cfc_amount : '');
        $row.find('input[name="cfc_remarks[]"]').val(charge.cfc_remarks || '');

        $tbody.append($row);
    });

    $('#fright_row').val(rows.length);

    $tbody.find('.js-example-basic-single').select2({ width: '100%' });
    $tbody.find('.date-picker').each(function () {
        flatpickr(this, { dateFormat: 'd/m/Y', allowInput: true });
    });

    // Trigger page total recalculation handlers.
    $tbody.find('input[name="cfc_amount[]"]').trigger('input');
}

function fetchAndApplySelectedGrnCharges() {
    var selectedGrnIds = [];
    $(".popupPI tbody tr .po_check:checked").each(function () {
        var grnId = $(this).closest('tr').find('input[name="grn_id[]"]').val();
        if (grnId && selectedGrnIds.indexOf(String(grnId)) === -1) {
            selectedGrnIds.push(String(grnId));
        }
    });

    if (!selectedGrnIds.length) {
        return;
    }

    var url = $('#url').val();
    var token = $('input[name="_token"]').first().val();

    $.ajax({
        type: "POST",
        url: url + '/' + 'purchase-invoice-get-cfc-by-grn',
        dataType: 'json',
        data: {
            _token: token,
            grn_id: selectedGrnIds.join(',')
        },
        success: function (res) {
            applyGrnChargesToPiFrightTable((res && res.data) ? res.data : []);
        },
        error: function (err) {
            console.log('Unable to load GRN charges for PI', err);
        }
    });
}

$(document).on("click", "#addGRNPendingItems", function(event) {
    fetchAndApplySelectedGrnCharges();
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone().get().reverse();
    $('#grn-pi-table tbody').empty();
    $("#grn-pi-table tbody").append(getSelectedRows);    
    //$(".jshide").hide();
    $(".jshide12").hide();
    $("#btn_close2").click();
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addGRNPendingItems


//PURCHASE RETURN
//addPIPending
// $(document).on("click", "#addPIPending", function(event) {
//     var url = $('#url').val();
//     var pi_id = $('#hd_pending_pi_id').val();
//     console.log(url);
//     $.ajax({
//         type: "GET",
//         data: { pi_id: pi_id },
//         dataType: 'json',
//         url: url + '/' + 'get-pi-list-for-pi-return',
//         success: function(data) {
//             console.log(data);
//             var a = '';            
//             var tr="";
//             var pro_qty = "0";
            
//             var qty_total = 0;
//             var unitprice_total = 0;
//             var value_total = 0;
//             var discount_total = 0;
//             var taxableamount_total = 0;
//             var vatamount_total = 0;
//             var totalamount_total = 0;

//             $.each(data, function(i, item) {
//                 if (item.length) {
//                     $.each(item, function(i, pin) {

//                         if (pin.pro_qty != null){
//                             pro_qty=pin.pro_qty;
//                         }
                        
//                         tr +=  "<tr>\
//                         <td><input class='form-control text-center' type='number' autocomplete='off' name='sort_id[]' value='"+(i+1)+"' /></td>\
//                         <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+ pin.part_number +"'>"+ pin.part_number_txt +"</option></select></td>\
//                         <td><textarea class='form-control' name='description[]' rows='1'>"+ pin.description +"</textarea></td>\
//                         <td><input type='hidden' id='partno_" + i + "' name='part_number_txt[]' value='"+pin.part_number_txt+"'/>\
//                         <input type='hidden' id='pi_qty_" + i + "' name='pi_qty[]' value='"+pin.qty+"'/>\
//                         <input class='form-control' type='number' autocomplete='off' min='0' value='"+pin.qty+"' readonly></td>\
//                         <td><input class='form-control text-center' type='number' autocomplete='off' min='0' id='tax_" + i + "' name='tax[]' value='"+parseInt(pin.tax)+"'  onchange='calc_change_new(this)'></td>\
//                         <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='"+pin.qty+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onkeypress='set_license_key_po("+i+","+pin.product_type+")'></td>\
//                         <td class='jshide'><input class='form-control text-end' step='Any' type='text' id='unitprice_" + i + "' name='unitprice[]' value='"+formatAmount(pin.unitprice)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' id='value_" + i + "' name='value[]' value='"+formatAmount(pin.value)+"' autocomplete='off' min='0' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' id='discount_" + i + "' name='discount[]' value='"+formatAmount(pin.discount)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' id='taxamount_" + i + "' name='taxableamount[]' value='"+formatAmount(pin.taxableamount)+"' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' id='vatamount_" + i + "' name='vatamount[]' value='"+formatAmount(pin.vatamount)+"' readonly></td>\
//                         <td class='jshide'><input class='form-control text-end' type='text' id='totalamount_" + i + "' name='totalamount[]' value='"+formatAmount((Number(pin.taxableamount) + Number(pin.vatamount)))+"' readonly></td>\
//                         <td class='jshide' style='display:none;'><input class='form-control srl' type='text' name='serial_no[]'></td>\
//                         </tr>";
//                         $('#row-count').val(i+1);
//                         $('#pi_number').val(pin.doc_number);
//                         // $('#pi_date').val(pin.pi_date);
//                         $('#pi_date').val(pin.pi_date ? pin.pi_date.split('-').reverse().join('/') : '');

//                         $('#lpo_number').val(pin.lpo_number);
//                         $('#po_id').val(pin.ref_po_id);
//                         $('#grn_id').val(pin.ref_grn_id);
//                         // $('#lpo_date').val(pin.lpo_date);
//                         $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

//                         $('#payment_terms').val(pin.payment_terms).trigger('change');
//                         $('#currency').val(pin.currency);
//                         $('#bill_number').val(pin.bill_number);
//                         // $('#bill_date').val(pin.bill_date);
//                         $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

//                         $('#awbno').val(pin.awbno);
//                         $('#warehouse').val(pin.warehouse);
                        
//                         $('#grn_no').val(pin.doc_number);
//                         // $('#grn_date').val(pin.grn_date);
//                         $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');

                        
//                         $('#salesman_name').val(pin.salesman_name);
//                         $('#reference').val(pin.reference);
//                         $('#deal_id').val(pin.code);

//                         $('#shipping_name').val(pin.shipping_name);
//                         $('#shipping_address_1').val(pin.shipping_address_1);
//                         $('#shipping_address_2').val(pin.shipping_address_2);
//                         $('#shipping_contact_no').val(pin.shipping_contact_no);
//                         $('#supplier_type').val(pin.supplier_type);
//                         $('#purchase_type').val(pin.purchase_type);
//                         $('#country').val(pin.supplier_country);
//                         $('#state').val(pin.supplier_state);

//                         qty_total += Number(pin.qty);
//                         unitprice_total += Number(pin.unitprice);
//                         value_total += Number(pin.value);
//                         discount_total += Number(pin.discount);
//                         taxableamount_total += Number(pin.taxableamount);
//                         vatamount_total += Number(pin.vatamount);
//                         totalamount_total += (Number(pin.taxableamount) + Number(pin.vatamount));

                        

//                         $('#adj_pi_no').val(pin.doc_number);
//                         $('#adj_lpo_no').val(pin.lpo_number);
//                         $('#adj_total').val(totalamount_total);
                        
//                         $('#tax').val(pin.tax);

//                     });
//                 } else {
//                     $('#sectionStateDiv .current').html('');
//                     $('#state').find('option').not(':first').remove();
//                     $('#sectionStateDiv ul').find('li').not(':first').remove();
//                 }
//             });
//             console.log(a);
//             $('#myTable tbody').empty();
//             $("#myTable tbody").append(tr);
//             $(".jshide").show();
//             $(".jshide1").hide();
//             update_totals();
//         },
//         error: function(data) {
//             console.log('Error:', data);
//         }
//     });

// });

$(document).on("click", "#addPIPending", function () {

    let url     = $('#url').val();
    let pi_id   = $('#hd_pending_pi_id').val();

    $.ajax({
        type: "GET",
        url: url + '/get-pi-list-for-pi-return',
        data: { pi_id: pi_id },
        dataType: "json",

        success: function (data) {

            let tr = "";
            let pro_qty = 0;

            // totals
            let qty_total = 0,
                unitprice_total = 0,
                value_total = 0,
                discount_total = 0,
                taxableamount_total = 0,
                vatamount_total = 0,
                totalamount_total = 0;
            let ref_company_ids = [];
            let sales_person = null;
            let newOption = null;


            $.each(data, function (index, itemGroup) {

                if (!itemGroup.length) return;

                $.each(itemGroup, function (i, pin) {

                    if (pin.pro_qty != null) pro_qty = pin.pro_qty;

                    tr += `
                    <tr>
                        <td><input type="checkbox" class="po_check" checked value="${pin.id}"></td>
                        <td class="text-center">${i + 1}</td>

                        <td>
                            ${pin.part_number_txt}
                            <input type="hidden" name="part_id[]" value="${pin.part_number}">
                        </td>

                        <td>${pin.description}</td>

                        <td class="text-center">
                            ${pin.qty}
                            <input type="hidden" name="grn_qty[]" value="${pin.qty}">
                        </td>

                        <td class="text-center">
                            <input type="number" class="form-control text-center border-0" 
                                name="tax[]" value="${Number(pin.tax).toFixed(0)}" 
                                step="1" onchange="calc_change_new(this)">
                        </td>

                        <td class="text-center">
                            <input type="number" class="form-control text-center border-0" 
                                name="qty[]" value="${Number(pin.qty).toFixed(0)}" 
                                step="1" onchange="calc_change_new(this)">
                        </td>

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="unitprice[]" value="${Number(pin.unitprice).toFixed(2)}" 
                                onchange="calc_change_new(this)">
                        </td> 

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="value[]" value="${Number(pin.value).toFixed(2)}" readonly>
                        </td>

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="discount[]" value="${Number(pin.discount).toFixed(2)}" 
                                onchange="calc_change_new(this)">
                        </td>

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="taxableamount[]" value="${Number(pin.taxableamount).toFixed(2)}" readonly>
                        </td>

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="vatamount[]" value="${Number(pin.vatamount).toFixed(2)}" readonly>
                        </td>

                        <td class="text-end">
                            <input type="number" step="any" class="form-control text-end border-0" 
                                name="totalamount[]" 
                                value="${(Number(pin.taxableamount) + Number(pin.vatamount)).toFixed(2)}" 
                                readonly>
                        </td>
                    </tr>
                    `;

                     $('#pi_number').val(pin.doc_number);
                        // $('#pi_date').val(pin.pi_date);
                        $('#pi_date').val(pin.pi_date ? pin.pi_date.split('-').reverse().join('/') : '');

                        $('#lpo_number').val(pin.lpo_number);
                        $('#po_id').val(pin.ref_po_id);
                        $('#grn_id').val(pin.ref_grn_id);
                        // $('#lpo_date').val(pin.lpo_date);
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#payment_terms').val(pin.payment_terms).trigger('change');
                        $('#currency').val(pin.currency).trigger('change');
                        $('#bill_number').val(pin.bill_number);
                        // $('#bill_date').val(pin.bill_date);
                        $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

                        $('#awbno').val(pin.awbno);
                        $('#warehouse').val(pin.warehouse);
                        
                        $('#grn_no').val(pin.doc_number);
                        // $('#grn_date').val(pin.grn_date);
                        $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');



                        if(pin.sales_person){
                            sales_person = pin.sales_person;
                        // $('#sales_person').val(pin.sales_person).trigger('change');

                        }else{
                          //create new option and select it
                             newOption = new Option(pin.sales_person_name, pin.sales_person_name, true, true);
                            // $('#sales_person').append(newOption).trigger('change');


                        }
                        
                        // $('#ref_company_id').val(pin.ref_company_id).trigger('change');

                         // Collect ref_company_id values (support comma lists like "1,18") and dedupe
                        if (pin.ref_company_id) {
                            String(pin.ref_company_id).split(',').map(function(x){ return x.trim(); }).filter(Boolean).forEach(function(id){
                                if (ref_company_ids.indexOf(id) === -1) ref_company_ids.push(id);
                            });
                        }

                        // Update hidden inputs container and visible customer reference input (mirror pi_add behavior)
                        try {
                            var $refContainer = $('#ref_company_hidden_inputs');
                            if ($refContainer.length) {
                                $refContainer.empty();
                                var refNames = [];
                                ref_company_ids.forEach(function(id){
                                    $refContainer.append('<input type="hidden" name="ref_company_id[]" value="' + $('<div>').text(id).html() + '" />');
                                    var optText = $('#modal_ref_company_select option[value="' + id + '"]').text().trim();
                                    if (optText) refNames.push(optText);
                                });
                                $('#customer_reference_input').val(refNames.join(', '));
                            } else {
                                // fallback: set a legacy field if present
                                $('#ref_company_id').val(ref_company_ids).trigger('change');
                            }
                        } catch (e) {
                            console.error('Error updating ref company inputs', e);
                            $('#ref_company_id').val(ref_company_ids).trigger('change');
                        }
                        
                        $('#reference').val(pin.reference);
                        $('#deal_id').val(pin.code);

                          if(pin.shipping_supplier) {
                            console.log("SETTING SHIPPING SUPPLIER TO ", pin.shipping_supplier);
                            $("#shipping_supplier").val(pin.shipping_supplier).trigger('change');
                            }
                        $('#shipping_name').val(pin.shipping_name);
                        $('#shipping_address_1').val(pin.shipping_address_1);
                        $('#shipping_address_2').val(pin.shipping_address_2);
                        $('#shipping_contact_no').val(pin.shipping_contact_no);
                        // $('#supplier_type').val(pin.supplier_type).trigger('change');
                        // $('#purchase_type').val(pin.purchase_type).trigger('change');
                        // $('#country').val(pin.supplier_country).trigger('change');
                        // $('#state').val(pin.supplier_state).trigger('change');

                        qty_total += Number(pin.qty);
                        unitprice_total += Number(pin.unitprice);
                        value_total += Number(pin.value);
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(pin.taxableamount);
                        vatamount_total += Number(pin.vatamount);
                        totalamount_total += (Number(pin.taxableamount) + Number(pin.vatamount));

                        

                        $('#adj_pi_no').val(pin.doc_number);
                        $('#adj_lpo_no').val(pin.lpo_number);
                        $('#adj_total').val(totalamount_total);
                        
                        $('#tax').val(pin.tax);

                    // totals
                    qty_total              += Number(pin.qty);
                    unitprice_total        += Number(pin.unitprice);
                    value_total            += Number(pin.value);
                    discount_total         += Number(pin.discount);
                    taxableamount_total    += Number(pin.taxableamount);
                    vatamount_total        += Number(pin.vatamount);
                    totalamount_total      += Number(pin.taxableamount) + Number(pin.vatamount);
                });
            });

            // Inject rows into modal table
            $(".popupPR tbody").html(tr);

            
                        if(sales_person){
                   
                        $('#sales_person').val(sales_person).trigger('change');

                        }else if(newOption){
                 
                            $('#sales_person').append(newOption).trigger('change');


                        }
                        

            // Show the modal
            $("#po_pending_popup_win").modal("show");
        },

        error: function (err) {
            console.error("Error:", err);
        }
    });

});

$(document).on("click", "#btnAddPIRows", function () {

    let $popupRows = $(".popupPR tbody tr");
    let $mainTable = $("#myTable tbody");
    $mainTable.empty();

    let newRowsHTML = "";
let i = 1;   // start sort index for NEW rows only
    $popupRows.each(function (index, row) {

        let $row = $(row);

        // ONLY add checked rows
        if (!$row.find(".po_check").is(":checked")) return;

              let part_number  = $row.find("td").eq(2).contents().filter(function() {
                                return this.nodeType === 3; // text node
                            }).text().trim(); // <-- this extracts `${pin.part_number}`
        let part_id        = $row.find('input[name="part_id[]"]').val();
        let description    = $row.find('td').eq(3).text().trim();
        let pi_qty         = $row.find('input[name="grn_qty[]"]').val();
        let tax            = $row.find('input[name="tax[]"]').val();
        let qty            = $row.find('input[name="qty[]"]').val();
        let unitprice      = $row.find('input[name="unitprice[]"]').val();
        let value          = $row.find('input[name="value[]"]').val();
        let discount       = $row.find('input[name="discount[]"]').val();
        let taxableamount  = $row.find('input[name="taxableamount[]"]').val();
        let vatamount      = $row.find('input[name="vatamount[]"]').val();
        let totalamount    = $row.find('input[name="totalamount[]"]').val();

        // BUILD NEW MAIN TABLE ROW
        newRowsHTML += `
        <tr>
            <td><input type="text" class="form-control text-center" name="sort_id[]" value="${i++}"></td>

            <td class="noborder">
                <select class="form-control noborder" name="part_number[]">
                    <option value="${part_id}" selected>${part_number}</option>
                </select>
            </td>

            <td>
                <textarea class="form-control" name="description[]" rows="1">${description}</textarea>
            </td>

            <td>
                <input class="form-control text-center" type="number" name="pi_qty[]" value="${pi_qty}" readonly>
            </td>

            <td>
                <input type="number" class="form-control text-center" name="tax[]" value="${tax}" onchange="calc_change_new(this)">
            </td>

            <td>
                <input class="form-control text-center" type="number" name="qty[]" value="${qty}" min="0" onchange="calc_change_new(this)">
            </td>

            <td>
                <input class="form-control text-end" type="text" name="unitprice[]" value="${unitprice}" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
            </td>

            <td>
                <input class="form-control text-end" type="text" name="value[]" value="${value}" readonly>
            </td>

            <td>
                <input class="form-control text-end" type="text" name="discount[]" value="${discount}" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
            </td>

            <td>
                <input class="form-control text-end" type="text" name="taxableamount[]" value="${taxableamount}" readonly>
            </td>

            <td>
                <input class="form-control text-end" type="text" name="vatamount[]" value="${vatamount}" readonly>
            </td>

            <td>
                <input class="form-control text-end" type="text" name="totalamount[]" value="${totalamount}" readonly>
            </td>

            <td>
                <input class="form-control" type="text" name="serial_no[]">
            </td>
        </tr>
        `;
    });

    // append to main table
    $mainTable.append(newRowsHTML);
    update_totals()

    

    // close popup
    $("#po_pending_popup_win").modal("hide");
});


//addPIPending
//addPIPendingItems
$(document).on("click", "#addPIPendingItems", function(event) {
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone().get().reverse();
    $('#pi-ret-table tbody').empty();
    $("#pi-ret-table tbody").append(getSelectedRows);    
    //$(".jshide").hide();
    $(".jshide12").hide();
    $(".jshide2").show();
    $("#btn_close2").click();
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addPIPendingItems

//addPIPendingSTL
$(document).on("click", "#addPIPendingSTL", function(event) {
    var url = $('#url').val();
    var pi_id = $('#hd_pending_pi_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { pi_id: pi_id },
        dataType: 'json',
        url: url + '/' + 'get-pi-list-for-stl',
        success: function(data) {
            console.log(data);
            var a = '';
            var tr="";
            var tr1="";
            var sr = 1;
            var description="";
            var sum_class="";
            var total=0;

            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {

                        if(i==0){
                            $('#table_id_stl_docno_'+pi_id).text(pin.doc_number);
                            $('#table_id_stl_billno_'+pi_id).text(pin.bill_number);
                            $('#table_id_stl_awbno_'+pi_id).text("AWB No. "+pin.awbno);
                            $('#table_id_stl_boeno_'+pi_id).text("BOE No. "+pin.boeno);
                        }

                        if (pin.description.toLowerCase().includes('license'.toLowerCase())) {
                            description = "Networking License";
                            sum_class="license";
                        }
                        else if (pin.description.toLowerCase().includes('licence'.toLowerCase())) {
                            description = "Networking License";
                            sum_class="license";
                        } else {
                            description = "Networking " + pin.cat_name;
                            sum_class="networking";
                        }
                        tr +=  "<tr><td class='jshide3 text-center'>"+ sr +"</td><td class='jshide12'><input type=checkbox style='margin-left:4px' checked id=id_"+ (i+1) +" value="+ pin.id +"></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='"+ pin.part_number_txt +"' readonly>\
                        <input type='hidden' id='partno_" + i + "' name='partno[]' value='"+pin.part_number+"'/>\
                        <input type='hidden' id='purchase_inv_" + i + "' name='purchase_inv[]' value='"+pi_id+"'/><input type='hidden' id='pi_inv_no_" + i + "' name='pi_inv_no[]' value='"+pin.bill_number+"'/><input type='hidden' id='awbno_" + i + "' name='awbno[]' value='"+pin.awbno+"'/><input type='hidden' id='boeno_" + i + "' name='boeno[]' value='"+pin.boeno+"'/>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+ description +"' readonly>\
                        <td class='jshide'><input class='form-control text-end "+sum_class+"' type='text' id='amount_" + i + "' name='amount[]' value='"+formatAmount(Number(pin.taxableamount) + Number(pin.vatamount))+"' onchange='set_total()' readonly></td>\
                        <td class='jshide3 text-center'>&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn-sm btn-light edit-btn'><i class='ico icon-outline-pen-2 text-success' style='font-size: 16px;'></i></a> <a class='btn-sm btn-light delete-btn' onclick='deleteRow(this)'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a></td>\
                        </tr>";
                        sr++;
                        total += (Number(pin.taxableamount) + Number(pin.vatamount));

                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#table_id tbody').empty();
            $("#table_id tbody").append(tr);
            $(".jshide").show();
            $(".jshide3").hide();
            $(".jshide1").hide();
            $('#table_id_total_'+pi_id).text(total.toFixed(2));
            set_total();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });

});
//addPIPendingSTL
//addPIPendingItemsSTL
$(document).on("click", "#addPIPendingSTLItems", function(event) {
    var pi_id = $('#hd_pending_pi_id').val();
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    getSelectedRows = getSelectedRows.get().reverse();
    $('#table_id_stl_'+pi_id+' tbody').empty();
    $("#table_id_stl_"+pi_id+" tbody").append(getSelectedRows);
    $("#table_id_stl_"+pi_id).css('display','');
    //$(".jshide").hide();
    $(".jshide12").hide();
    $(".jshide2").show();
    $(".jshide3").show();
    $('#pi_pending_popup_win').modal('hide');
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addPIPendingItemsSTL

//addPIPendingSTL
$(document).on("click", "#addPOPendingSTL", function(event) {
    var url = $('#url').val();
    var po_id = $('#hd_pending_po_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { po_id: po_id },
        dataType: 'json',
        url: url + '/' + 'get-po-list-for-stl',
        success: function(data) {
            console.log(data);
            var a = '';
            var tr="";
            var tr1="";
            var sr = 1;
            var description="";
            var sum_class="";
            var total=0;

            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {

                        if(i==0){
                            $('#table_id_stl_docno_'+po_id).text(pin.doc_number);
                            $('#table_id_stl_billno_'+po_id).text(pin.doc_number);
                            $('#table_id_stl_awbno_'+po_id).text("AWB No. "+pin.awbno);
                            $('#table_id_stl_boeno_'+po_id).text("BOE No. "+pin.boeno);
                        }

                        if (pin.description.toLowerCase().includes('license'.toLowerCase())) {
                            description = "Networking License";
                            sum_class="license";
                        }
                        else if (pin.description.toLowerCase().includes('licence'.toLowerCase())) {
                            description = "Networking License";
                            sum_class="license";
                        } else {
                            description = "Networking " + pin.cat_name;
                            sum_class="networking";
                        }
                        tr +=  "<tr><td class='jshide3 text-center'>"+ sr +"</td><td class='jshide12'><input style='margin-left:4px' type=checkbox checked id=id_"+ (i+1) +" value="+ pin.id +"></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='"+ pin.part_number_txt +"' readonly>\
                        <input type='hidden' id='partno_" + i + "' name='partno[]' value='"+pin.part_number+"'/>\
                        <input type='hidden' id='purchase_inv_" + i + "' name='purchase_inv[]' value='"+po_id+"'/><input type='hidden' id='pi_inv_no_" + i + "' name='pi_inv_no[]' value='"+pin.doc_number+"'/><input type='hidden' id='awbno_" + i + "' name='awbno[]' value='"+pin.awbno+"'/><input type='hidden' id='boeno_" + i + "' name='boeno[]' value='"+pin.boeno+"'/>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+ description +"' readonly>\
                        <td class='jshide'><input class='form-control text-end "+sum_class+"' type='text' id='amount_" + i + "' name='amount[]' value='"+formatAmount(Number(pin.taxableamount) + Number(pin.vatamount))+"' onchange='set_total()' readonly></td>\
                        <td class='jshide3 text-center'>&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn-sm btn-light edit-btn'><i class='ico icon-outline-pen-2 text-success' style='font-size: 16px;'></i></a> <a class='btn-sm btn-light delete-btn'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a></td>\
                        </tr>";
                        sr++;
                        total += (Number(pin.taxableamount) + Number(pin.vatamount));

                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $('#po_table_id tbody').empty();
            $("#po_table_id tbody").append(tr);
            $(".jshide").show();
            $(".jshide3").hide();
            $(".jshide1").hide();
           
            $('#table_id_total_'+po_id).text(formatAmount(total));
            set_total();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });

});
//addPIPendingSTL
//addPIPendingItemsSTL
$(document).on("click", "#addPOPendingSTLItems", function(event) {
    var pi_id = $('#hd_pending_po_id').val();
    var getSelectedRows = $("#po_table_id input:checked").parents("tr").clone();
    getSelectedRows = getSelectedRows.get().reverse();
    $('#po_table_id_stl_'+pi_id+' tbody').empty();
    $("#po_table_id_stl_"+pi_id+" tbody").append(getSelectedRows);
    $("#po_table_id_stl_"+pi_id).css('display','');
    //$(".jshide").hide();
    $(".jshide12").hide();
    $(".jshide2").show();
    $(".jshide3").show();
    $('#po_pending_popup_win').modal('hide');
        /*var tr = $("#table_id").find("TR:has(td)").clone();        
        $('#po-table tbody').empty();
        $("#po-table").append(tr);
        $(".jshide").hide();
        $(".jshide1").show();
        $("#btn_close2").click();*/
});
//addPIPendingItemsSTL



//PURCHASE RETURN

//addQtPendingItems

$(document).on("click", "#addRowSI", function(event) {

    var i = $('#si-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>";
                // tr += "<div class='input-effect'>";
                // tr += "<select class='niceSelect w-100 bb form-control' name='part_number[]' id='part_number' style='display:none'>";
                // tr += "<option data-display='Part No *' value='none'>Part No *</option>";

                // $.each(response, function(index, value) {
                //     tr += '<option value="' + value.id + '">' + value.part_number + ' / ' + value.description + '</option>';
                // });

                // tr += '</select>';
                tr += "<select class='form-control js-example-basic-single' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td>';

                tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";


                tr += '<input class="form-control" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';
                
                tr += '<td><input class="form-control" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="customcharges_' + i + '" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="taxableamount_' + i + '" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" id="vatamount_' + i + '" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
                            ';
                tr += '</tr>';

                $("#si-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});

$(document).on("click", "#addRowPO", function(event) {

    var i = $('#po-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>";
                // tr += "<div class='input-effect'>";
                // tr += "<select class='niceSelect w-100 bb form-control' name='part_number[]' id='part_number' style='display:none'>";
                // tr += "<option data-display='Part No *' value='none'>Part No *</option>";

                // $.each(response, function(index, value) {
                //     tr += '<option value="' + value.id + '">' + value.part_number + ' / ' + value.description + '</option>';
                // });

                // tr += '</select>';
                tr += "<select class='form-control js-example-basic-single' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td>';

                tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";


                tr += '<input class="form-control" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';
                tr += '<td><select class="form-control" name="tax[]" id="tax_' + i + '" readonly="true" onchange="calc_change(' + i + ')">\
                                                            <option value="' + net_vat + '">VAT ' + net_vat + '%</option>\
                                                            <option value="0">None</option>\
                                                        </select></td>';

                tr += '<td><input class="form-control" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="customcharges_' + i + '" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="taxableamount_' + i + '" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" id="vatamount_' + i + '" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
                            ';
                tr += '</tr>';

                $("#po-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});

// subgroup2


$(document).on("click", "#addRowAdditionalLocation", function(event) {
    alert("122");

                var tr = "<tr id='rowone'>";
                tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="price_' + i + '" name="price[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="totalprice_' + i + '" name="totalprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')" readonly></td>\
                            ';
                tr += '</tr>';




                $("#AdditionalLocation tbody tr:last").after(tr);

});

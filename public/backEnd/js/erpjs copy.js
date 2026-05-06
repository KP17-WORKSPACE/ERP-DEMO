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
    if (time == null) {
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
    if (date == null) {
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
    if (date == null) {
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
    if (datetime == null) {
        return "--";
    }
    var parts = datetime.split(' ');
    var date = get_format_date(parts[0]);
    var time = get_format_time(parts[1]);
    return date + ' ' + time;
}
function formatAmount(input) {
    let inputStr = input.toString();

    let number = parseFloat(inputStr.replace(/,/g, ''));

    if (!isNaN(number)) {
        return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        return '';
    }
}
function formatCurrency(input) {
    let value = input.value.replace(/,/g, '');
    if (value === '' || isNaN(value)) {
        input.value = '';
        return;
    }
    let floatValue = formatAmount(value);
    input.value = floatValue;
}

// cashreceipt-add
$(document).on("click", "#addRowCR", function (event) {
    var i = $('#cr-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-cr-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addCtrlCashBookAdjest", function (event) {
    var url = $('#url').val();
    var cr_account_id = $('#cr_account_id').val();
    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-cr-balancelist',
        type: 'GET',
        data: { account_id: cr_account_id },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            //alert(XMLHttpRequest);
            $('#crListCashBookAdjest tbody').empty();
        }
    }); // get the product data
});
// cashreceipt-add

// receipt-add
$(document).on("click", "#addRowRE", function (event) {
    var i = $('#br-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-re-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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
$(document).on("click", "#addCtrlBankBookAdjest", function (event) {
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
                tr += '<tr>\
                        <td><input value="' + value.deal_id + '" class="form-control" type="text" id="bi_deal_id_' + i + '" name="bi_deal_id[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + parseFloat(value.total) + '" class="form-control" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + parseFloat(value.paid) + '" class="form-control" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + parseFloat(value.balance) + '" class="form-control" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="" class="form-control tot_amt" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
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
            $("#addCtrlBankBookAdjest").prop("disabled", false);

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// receipt-add

// receipt-edit
$(document).on("click", "#addCtrlBankBookAdjestEdit", function (event) {
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
        data: { account_id: br_account_id, doc_number: doc_number },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + parseFloat(value.total) + '" class="form-control" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + parseFloat(value.paid) + '" class="form-control" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + parseFloat(value.balance) + '" class="form-control" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="'+ parseFloat(value.bi_amount) + '" class="form-control tot_amt" step="any" type="number" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
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

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// receipt-edit

// journal-voucher-add
$(document).on("click", "#addCtrlJournalVoucherAdjest", function (event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var br_account_type = $('#account_type').val();
    var get_url = '';
    if (br_account_type == "CUS") {
        get_url = 'journalvoucher-get-adjestment-list-cus';
    } else if (br_account_type == "SUP") {
        get_url = 'journalvoucher-get-adjestment-list-sup';
    } else {
        get_url = 'journalvoucher-get-adjestment-list';
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {


                if (value.balance > 0) {
                    tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.total + '" class="form-control" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + value.paid + '" class="form-control" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + value.balance + '" class="form-control" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="" class="form-control tot_amt" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                    outstamount += parseFloat(value.total);
                    footer_total += parseFloat(value.total);
                    footer_paid += parseFloat(value.paid);
                    footer_balance += parseFloat(value.balance);
                    footer_adjustment += parseFloat(value.total);
                    i++;
                }
            });
            $("#bi_balance_to_adjust").val(outstamount);
            $("#footer_total").text(footer_total);
            $("#footer_paid").text(footer_paid);
            $("#footer_balance").text(footer_balance);


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjest").prop("disabled", false);

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// journal-voucher-add

// journal-voucher-edit
$(document).on("click", "#addCtrlJournalVoucherAdjestEdit", function (event) {
    var url = $('#url').val();
    var br_account_id = $('#br_account_id').val();
    var doc_number = $('#doc_number').val();

    var br_account_type = $('#account_type').val();
    var get_url = '';
    if (br_account_type == "CUS") {
        get_url = 'journalvoucher-get-adjestment-list-edit-cus';
    } else if (br_account_type == "SUP") {
        get_url = 'journalvoucher-get-adjestment-list-edit-sup';
    } else {
        get_url = 'journalvoucher-get-adjestment-list-edit';
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
        data: { account_id: br_account_id, doc_number: doc_number },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
                //if(value.balance > 0){
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.total + '" class="form-control" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + value.paid + '" class="form-control" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + value.balance + '" class="form-control" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="'+ value.bi_amount + '" class="form-control tot_amt" step="any" type="number" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
                        <td><input value="" class="form-control" type="text" id="bi_narration_' + i + '" name="bi_narration[]" autocomplete="off"></td>\
                    </tr>';
                outstamount += parseFloat(value.total);
                footer_total += parseFloat(value.total);
                footer_paid += parseFloat(value.paid);
                footer_balance += parseFloat(value.balance);
                footer_adjustment += parseFloat(value.total);
                i++;
                //}

            });
            $("#bi_balance_to_adjust").val(outstamount);
            $("#footer_total").text(footer_total);
            $("#footer_paid").text(footer_paid);
            $("#footer_balance").text(footer_balance);


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlBankBookAdjestEdit").prop("disabled", false);




            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
    $("#loading_bg").css("display", "none");
});
// journal-voucher-edit

// payment-add
$(document).on("click", "#addRowPY", function (event) {
    var i = $('#br-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-py-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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
$(document).on("click", "#addCtrlPaymentAdjest", function (event) {
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.total + '" class="form-control text-end" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + value.paid + '" class="form-control text-end" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + value.balance + '" class="form-control text-end" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="" class="form-control tot_amt text-end" type="number" step="any" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
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
            $("#footer_total").text(footer_total);
            $("#footer_paid").text(footer_paid);
            $("#footer_balance").text(footer_balance);


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlPaymentAdjest").prop("disabled", false);

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// payment-add

// payment-edit
$(document).on("click", "#addCtrlPaymentAdjestEdit", function (event) {
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
        data: { account_id: br_account_id, doc_number: doc_number },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
                tr += '<tr>\
                        <td><input value="' + value.doc_number + '" class="form-control row_ctrl" type="text" id="bi_doc_no_' + i + '" name="bi_doc_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.doc_date + '" class="form-control" type="text" id="bi_doc_date_' + i + '" name="bi_doc_date[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.lpo_number + '" class="form-control" type="text" id="bi_lpo_no_' + i + '" name="bi_lpo_no[]" autocomplete="off" readonly></td>\
                        <td><input value="' + value.total + '" class="form-control text-end" type="number" id="bi_total_' + i + '" name="bi_total[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="' + value.paid + '" class="form-control text-end" type="number" id="bi_paid_' + i + '" name="bi_paid[]" autocomplete="off" min="0" onchange="BankBookAdjestBalance(' + i + ')" readonly></td>\
                        <td><input value="' + value.balance + '" class="form-control text-end" type="number" id="bi_balance_' + i + '" name="bi_balance[]" autocomplete="off" min="0" readonly></td>\
                        <td><input value="'+ value.bi_amount + '" class="form-control tot_amt text-end" step="any" type="number" id="bi_amount_' + i + '" name="bi_amount[]" autocomplete="off" min="0" onclick="get_set_amount(' + i + ')"></td>\
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
            $("#footer_total").text(footer_total);
            $("#footer_paid").text(footer_paid);
            $("#footer_balance").text(footer_balance);


            $('#crListBankBookAdjest tbody').empty();
            $("#crListBankBookAdjest tbody").append(tr);
            $("#addCtrlPaymentAdjest").prop("disabled", false);

            //$("#crListCashBookAdjest tbody tr:last").after(tr);
        }, // /success
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#crListBankBookAdjest tbody').empty();
        }
    }); // get the product data
});
// payment-edit


// cashpayment-add
$(document).on("click", "#addRowCP", function (event) {
    var i = $('#cp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-cp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addCtrlCashPaymentAdjest", function (event) {
    var url = $('#url').val();
    var cp_account_id = $('#cp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-cp-balancelist',
        type: 'GET',
        data: { account_id: cp_account_id },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#cpListCashPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// cashpayment-add

// bankpayment-add
$(document).on("click", "#addRowBP", function (event) {
    var i = $('#bp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-bp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addCtrlBankPaymentAdjest", function (event) {
    var url = $('#url').val();
    var bp_account_id = $('#bp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-bp-balancelist',
        type: 'GET',
        data: { account_id: bp_account_id },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#cpListBankPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// bankpayment-add

// postdatedreceipt-add
$(document).on("click", "#addRowPDR", function (event) {
    var i = $('#pdr-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-pdr-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addCtrlPostdatedReceiptAdjest", function (event) {
    var url = $('#url').val();
    var pdr_account_id = $('#pdr_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-pdr-balancelist',
        type: 'GET',
        data: { account_id: pdr_account_id },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#pdrListPostdatedReceiptAdjest tbody').empty();
        }
    }); // get the product data
});
// postdatedreceipt-add

// postdatedpayment-add
$(document).on("click", "#addRowPDP", function (event) {
    var i = $('#pdp-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-pdp-custlist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addRowJV", function (event) {
    var i = $('#jv-row-count').val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-jv-accolist',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>" + i + "</td>";
            tr += "<td>";
            tr += "<select class='form-control js-example-basic-single' name='account_id[]' id='account_id_" + i + "'>";
            tr += "<option value=''></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addCtrlPostdatedPaymentAdjest", function (event) {
    var url = $('#url').val();
    var pdp_account_id = $('#pdp_account_id').val();

    var i = 1;
    var outstamount = 0;
    $.ajax({
        url: url + '/' + 'get-pdp-balancelist',
        type: 'GET',
        data: { account_id: pdp_account_id },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $('#pdpListPostdatedPaymentAdjest tbody').empty();
        }
    }); // get the product data
});
// postdatedpayment-add

// purchease-return-invoiceno
$("#pr_supplier_id").on('change', function () {
    var url = $('#url').val();
    var pi_id = $('#pr_supplier_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { pi_id: pi_id },
        dataType: 'json',
        url: url + '/' + 'get_pi_list',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {
                    $('#pi_numbers').find('option').not(':first').remove();
                    $('#sectionPINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#getCtrlPiRetNum", function (event) {

    var selected = $("#pi_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var pi_ids = "";
    selected.each(function () {
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
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
$("#sr_customer_id").on('change', function () {
    var url = $('#url').val();
    var si_id = $('#sr_customer_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { si_id: si_id },
        dataType: 'json',
        url: url + '/' + 'get_si_list',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {
                    $('#si_numbers').find('option').not(':first').remove();
                    $('#sectionSINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#getCtrlSiRetNum", function (event) {

    var selected = $("#si_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var si_ids = "";
    selected.each(function () {
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
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


$(document).on("click", "#dn_si_numbers", function (event) {

    var selected = $("#dn_si_numbers option:selected");

    if (selected.length == 0) { alert("Please choose Invoice"); return false; }

    var si_ids = "";
    selected.each(function () {
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
        success: function (response) {
            console.log(response);
            var tr = "";
            $.each(response, function (key, value) {
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
        error: function (XMLHttpRequest, textStatus, errorThrown) {
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
$("#da_customer_id").on('change', function () {
    var url = $('#url').val();
    var cus_id = $('#da_customer_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { cus_id: cus_id },
        dataType: 'json',
        url: url + '/' + 'get_si_list_delivery_advice',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {
                    $('#da_si_numbers').find('option').not(':first').remove();
                    $('#sectionDaSINumberDiv ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

// delivery note

// sub group
$("#group_id_sub").on('change', function () {
    var url = $('#url').val();
    var group_id = $('#group_id_sub').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { group_id: group_id },
        dataType: 'json',
        url: url + '/' + 'get_sub_group',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {

                    $('#subgroup').find('option').not(':first').remove();
                    $('#sectionSubGroupDiv ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// sub group

// subgroup2
$("#subgroup").on('change', function () {
    var url = $('#url').val();
    var subgroup = $('#subgroup').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { subgroup: subgroup },
        dataType: 'json',
        url: url + '/' + 'get_subgroup2',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {

                    $('#subgroup2').find('option').not(':first').remove();
                    $('#sectionSubGroup2Div ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// subgroup2

$(document).on("click", "#addRowCL", function (event) {

    var i = $('#cl-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            console.log(response);

            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>";
            tr += "<select class='w-100 sstxtbx' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
            });
            tr += "</select>";
            tr += "<input class='w-100 sstxtbx' type='hidden' id='partno_" + i + "' name='partno[]'>";
            tr += '</td>';

            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.description + "</option>";
            });
            tr += "</select>";
            tr += '<input class="w-100 sstxtbx" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';

            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_coo[]' id='part_number_coo_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.coo + "</option>";
            });
            tr += "</select>";
            tr += '<input class="w-100 sstxtbx" type="text" id="coo_' + i + '" name="coo[]" autocomplete="off" ></td>';

            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_hscode[]' id='part_number_hscode_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.hscode + "</option>";
            });
            tr += "</select>";
            tr += '<input class="w-100 sstxtbx" type="text" id="hscode_' + i + '" name="hscode[]" autocomplete="off" ></td>';

            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_weight[]' id='part_number_weight_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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


$(document).on("click", "#addRowQuote", function (event) {

    var i = $('#cl-row-count').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-item-quote',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            console.log(response);

            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
            tr += "<td>";
            tr += "<select class='niceSelect w-100 dynamicstxt_s bb form-control' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
            });
            tr += "</select>";
            tr += "<input class='w-100 sstxtbx' type='hidden' id='partno_" + i + "' name='partno[]'>";
            tr += '</td>';

            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.description + "</option>";
            });
            tr += "</select>";
            tr += '<input class="w-100 sstxtbx" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';

            tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>';


            tr += '<td>';
            tr += "<select class='w-100 sstxtbx' name='part_number_price[]' id='part_number_price_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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


$(document).on("click", "#addRowOS", function (event) {

    var i = $('#os-row-count').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            console.log(response);

            var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";

            tr += "<td>";
            tr += "<select class='form-control' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
            });
            tr += "</select>";
            tr += '</td>';

            tr += '<td>';
            tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.description + "</option>";
            });
            tr += "</select>";
            tr += '<input class="form-control" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';


            tr += '<td><input class="form-control" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="form-control" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0" readonly></td>\
                            ';
            tr += "<td><input class='form-control' type='text' id='remarks_" + i + "' name='remarks[]' autocomplete='off'></td>";
            tr += "<td><input class='form-control' type='text' id='refno_" + i + "' name='refno[]' autocomplete='off'></td>";
            tr += '</tr>';




            $("#os-table tbody tr:last").after(tr);
        } // /success
    }); // get the product data

});

$(document).ready(function () {


    // Get State List By Country Id
    $("#country").on('change', function () {
        $("#loading_bg").css("display", "block");
        var url = $('#url').val();
        var country_id = $('#country').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { country_id: country_id },
            dataType: 'json',
            url: url + '/' + 'get_state',
            success: function (data) {
                console.log(data);
                var a = '';
                $.each(data, function (i, item) {
                    if (item.length) {

                        $('#state').find('option').not(':first').remove();
                        $('#sectionStateDiv ul').find('li').not(':first').remove();

                        $.each(item, function (i, pin) {
                            $('#state').append($('<option>', {
                                value: pin.id,
                                text: pin.name
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
                $("#loading_bg").css("display", "none");
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $("#country_n").on('change', function () {
        $("#loading_bg").css("display", "block");
        var url = $('#url').val();
        var country_id = $('#country_n').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { country_id: country_id },
            dataType: 'json',
            url: url + '/' + 'get_state',
            success: function (data) {
                console.log(data);
                var a = '';
                $.each(data, function (i, item) {
                    if (item.length) {

                        $('#state_n').find('option').not(':first').remove();
                        $('#sectionStateDiv_n ul').find('li').not(':first').remove();

                        $.each(item, function (i, pin) {
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
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

});

$(document).ready(function () {
    $("#country_n_e").on('change', function () {
        $("#loading_bg").css("display", "block");
        var url = $('#url').val();
        var country_id = $('#country_n_e').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { country_id: country_id },
            dataType: 'json',
            url: url + '/' + 'get_state',
            success: function (data) {
                console.log(data);
                var a = '';
                $.each(data, function (i, item) {
                    if (item.length) {

                        $('#state_n_e').find('option').not(':first').remove();
                        $('#sectionStateDiv_n_e ul').find('li').not(':first').remove();

                        $.each(item, function (i, pin) {
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
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});


// Get State List By Country Id

// Get State List By Country Id shipping
$(document).ready(function () {
    $("#country_ship").on('change', function () {//kunal modified
        $("#loading_bg").css("display", "block");
        var url = $('#url').val();
        var country_id = $('#country_ship').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { country_id: country_id },
            dataType: 'json',
            url: url + '/' + 'get_state',
            success: function (data) {
                console.log(data);
                var a = '';
                $.each(data, function (i, item) {
                    if (item.length) {

                        $('#state_ship').find('option').not(':first').remove();
                        $('#sectionStateDiv_ship ul').find('li').not(':first').remove();

                        $.each(item, function (i, pin) {
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
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});

// Get State List By Country Id shipping

// Get State List By Country Id
$(document).ready(function () {

    $("#country_vat").on('change', function () {
        $("#loading_bg").css("display", "block");
        var url = $('#url').val();
        var country_id = $('#country_vat').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { country_id: country_id },
            dataType: 'json',
            url: url + '/' + 'get_state',
            success: function (data) {
                console.log(data);
                var a = '';
                $.each(data, function (i, item) {
                    if (item.length) {

                        $('#vat_state').find('option').not(':first').remove();
                        $('#sectionStateDiv ul').find('li').not(':first').remove();

                        $.each(item, function (i, pin) {
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
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

});

// Get VAT Details By Country Id vat
$(document).ready(function () {
    $("#country_vat").on('change', function () {
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
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                    $("#vat_percentage").val();
                    $("#loading_bg").css("display", "none");
                } else {
                    $("#vat_percentage").val(dataResult['data'].vat_percentage);
                    $("#loading_bg").css("display", "none");
                }
            }
        });
    });
});

// Get VAT Details By Country Id vat

// Get State List By Country Id VAT Page
$("#country_vat-exe").on('change', function () {
    var url = $('#url').val();
    var country_id = $('#country').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {

                    $('#state_vat').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();

                    $.each(item, function (i, pin) {
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// Get State List By Country Id VAT Page


// Get VAT Details By State Id
$("#state_vat").on('change', function () {
    var url = $('#url').val();
    var state_vat = $('#state_vat').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { state_vat: state_vat },
        dataType: 'json',
        url: url + '/' + 'get_vat_state',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {

                    $('#vat_type').find('option').not(':first').remove();

                    $.each(item, function (i, pin) {

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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// Get VAT Details By Country Id

// Get VAT Details By State Id
$("#customer_with_vat").on('change', function () {
    var url = $('#url').val();
    var customer_with_vat = $('#customer_with_vat').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { customer_with_vat: customer_with_vat },
        dataType: 'json',
        url: url + '/' + 'get_customer_vat',
        success: function (data) {
            console.log(data);
            var a = '';
            $.each(data, function (i, item) {
                if (item.length) {

                    //$('#vat_type').find('option').not(':first').remove();

                    $.each(item, function (i, pin) {

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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
// Get VAT Details By Country Id

//PROFORMA INVOICE PAGE START
//addQtPending PROFORMA INVOICE PAGE
$(document).on("click", "#addQtPending", function (event) {

    var url = $('#url').val();
    var qt_id = $('#hd_pending_qt_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { qt_id: qt_id },
        dataType: 'json',
        url: url + '/' + 'quotation-pending-item-list',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var pro_qty = "0";

            var qty_total = 0;
            var unitprice_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;

            $.each(data, function (i, item) {

                if (item.length) {
                    $.each(item, function (i, pin) {

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



                        tr += "<tr>\
                        <td><input class='form-control text-center' type='number' id='sort_id_" + i + "' name='sort_id[]' value='" + (i + 1) + "' ></td>\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='" + pin.product_id + "'>" + pin.part_number + "</option></select></td>\
                        <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='" + pin.qty + "' autocomplete='off' min='0'onchange='calc_change_new(this)'>\
                        <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='" + pin.product_id + "'/></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='unitprice_" + i + "' name='unitprice[]' value='" + pin.price + "' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='value_" + i + "' name='value[]' value='" + value + "' autocomplete='off' min='0'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='discount_" + i + "' name='discount[]' value='" + pin.discount + "' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='taxamount_" + i + "' name='taxableamount[]' value='" + taxamount + "'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='vatamount_" + i + "' name='vatamount[]' value='" + vatamount + "'></td>\
                        <td readonly class='jshide'><input class='form-control text-end' step='any' type='number' id='totalamount_" + i + "' name='totalamount[]' value='" + (Number(totalamount)) + "'></td>\
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




                        $('#row-count').val(i + 1);
                        $('#payment_terms').val(pin.payment_terms);
                        $('#delivery_terms').val(pin.delivery_time);
                        $("#sales_man").val(pin.user_id).trigger('change');
                        $("#currency").val(pin.currency_id);
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});
//addQtPending

//addQtPendingItems
$(document).on("click", "#addQtPendingItems", function (event) {
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
$(document).on("click", "#addProfoPending", function (event) {
    var url = $('#url').val();
    var qt_id = $('#hd_pending_profo_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { qt_id: qt_id },
        dataType: 'json',
        url: url + '/' + 'get-proforma-invoice-items-for-si',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var value = 0;
            var taxamount = 0;
            var vatamount = 0;
            var totalamount = 0;
            $.each(data, function (i, item) {
                if (item.length) {
                    $.each(item, function (i, pin) {

                        value = number_format(pin.qty * pin.unitprice, 2, '.', '');
                        taxamount = number_format(value - pin.discount, 2, '.', '');
                        vatamount = number_format((taxamount) * 5 / 100, 2, '.', '');
                        totalamount = ((pin.qty * pin.unitprice) - pin.discount) + ((pin.qty * pin.unitprice) - pin.discount) * 5 / 100;

                        tr += "<tr><td class='jshide12'><input type=checkbox id=id_" + (i + 1) + " value=" + pin.id + "></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='" + pin.partnumber + "' readonly>\
                        <input type='hidden' id='part_id_" + i + "' name='part_id[]' value='" + pin.part_number + "'</td>\
                        <td class='jshide1'><input class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='" + pin.description + "' ></td>\
                        <td><input class='form-control qty' type='number' id='qty_" + i + "' name='qty[]' autocomplete='off' min='0' value='" + pin.qty + "' onchange='calc_change_new(this)' readonly></td>\
                        <td class='jshide'><input class='form-control unitprice' type='text' id='unitprice_" + i + "' onblur='formatCurrency(this)' value='" + formatAmount(pin.unitprice, 2, '.', '') + "' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control' type='text' id='value_" + i + "' value='" + formatAmount(value) + "' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td class='jshide'><input class='form-control' type='text' id='discount_" + i + "' onblur='formatCurrency(this)' value='" + formatAmount(pin.discount, 2, '.', '') + "' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control' type='text' id='taxableamount_" + i + "' value='" + formatAmount(taxamount) + "' name='taxableamount[]' readonly></td>\
                        <td class='jshide'><input class='form-control' type='text' id='vatamount_" + i + "' value='" + formatAmount(vatamount) + "' name='vatamount[]' readonly></td>\
                        <td class='jshide1'><input class='form-control' type='text' id='totalamount_" + i + "' value='" + formatAmount(totalamount, 2, '.', '') + "' name='totalamount[]' readonly></td>\
                        </tr>";
                        $('#si-row-count').val(i + 1);
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
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#addProfoPendingItems", function (event) {
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
$(document).on("click", "#addSRPending", function (event) {
    var url = $('#url').val();
    var id = $('#hd_pending_dn_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { id: id },
        dataType: 'json',
        url: url + '/' + 'get-si-list-for-si-return',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var value = 0;
            var taxamount = 0;
            var vatamount = 0;
            var totalamount = 0;

            var qty_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var amount_total = 0;


            $.each(data, function (i, item) {
                if (item.length) {
                    $.each(item, function (i, pin) {

                        value = (pin.qty * pin.unitprice);
                        taxamount = (value - pin.discount);
                        vatamount = ((taxamount) * pin.tax / 100);
                        totalamount = ((pin.qty * pin.unitprice) - pin.discount) + ((pin.qty * pin.unitprice) - pin.discount) * pin.tax / 100;

                        qty_total += pin.qty;
                        value_total += pin.qty * pin.unitprice;
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(taxamount);
                        vatamount_total += Number(vatamount);
                        amount_total += Number(totalamount);

                        tr += "<tr><td><input class='form-control text-center' type='text' name='sort_id[]' value=" + pin.sort_id + "></td>\
                        <td><select class='form-control noborder' name='part_number[]'><option value='"+ pin.part_number + "'>" + pin.partnumber + "</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='" + pin.part_number + "' /><input class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='" + pin.description + "' ></td>\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' autocomplete='off' min='0' value='" + pin.tax + "' readonly></td>\
                        <td><input class='form-control text-center qty' type='number' id='qty_" + i + "' name='qty[]' autocomplete='off' min='0'  value='" + pin.qty + "' onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-end unitprice' type='text' id='unitprice_" + i + "' value='" + formatAmount(pin.unitprice) + "' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='value_" + i + "' value='" + formatAmount(value) + "' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='discount_" + i + "' value='" + formatAmount(pin.discount) + "' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='taxableamount_" + i + "' value='" + formatAmount(taxamount) + "' name='taxableamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='vatamount_" + i + "' value='" + formatAmount(vatamount) + "' name='vatamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='totalamount_" + i + "' value='" + formatAmount(totalamount) + "' name='totalamount[]' readonly></td>\
                        <td><input class='form-control text-end srl' type='test' id='srl_"+ i + "' name='serial_no[]'></td>\
                        </tr>";
                        $('#dn-row-count').val(i + 1);
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
                        $('#supplier_name').val(pin.supplier_name);
                        $('#deal_id').val(pin.deal_id);

                        $('#payment_terms').val(pin.paymentterms);
                        $("#currency").val(pin.currency);
                        $('#delivery_terms').val(pin.delivery_terms);
                        $('#end_user_name').val(pin.end_user_name);
                        $('#contact_person_name').val(pin.contact_person_name);
                        $('#contact_person_email').val(pin.contact_person_email);
                        $('#contact_person_no').val(pin.contact_person_no);

                        $('#adj_dln_no').val(pin.doc_number);
                        $('#adj_siv_no').val(pin.invoice_no);
                        $('#adj_total').val(Number(totalamount) + Number(vatamount));


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
            update_totals();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#addDnPendingItems", function (event) {
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
    $(document).on("click", "#addDNPending", function (event) {
        console.log("hbhreffefd")
        var url = $('#url').val();
        var si_id = $('#hd_pending_dn_id').val();
        console.log(url);
        $.ajax({
            type: "GET",
            data: { si_id: si_id },
            dataType: 'json',
            url: url + '/' + 'sales-invoice-pending-item-list',
            success: function (data) {
                console.log(data);
                var a = '';
                var tr = "";
                var value = 0;
                var taxamount = 0;
                var vatamount = 0;
                var totalamount = 0;
                var qty_total = 0;
                var unitprice_total = 0;
                var value_total = 0;
                var discount_total = 0;
                var taxableamount_total = 0;
                var vatamount_total = 0;
                var total_amount = 0;

                $.each(data, function (i, item) {
                    if (item.length) {
                        $.each(item, function (i, pin) {

                            value = (pin.qty * pin.unitprice);
                            taxamount = (value - pin.discount);
                            vatamount = ((taxamount) * pin.tax / 100);
                            totalamount = ((pin.qty * pin.unitprice) - pin.discount) + ((pin.qty * pin.unitprice) - pin.discount) * pin.tax / 100;

                            tr += "<tr><td><input class='form-control text-center' type='number' name='sort_id[]' value=" + pin.sort_id + "></td>\
                        <td><select class='form-control' name='part_number[]'><option value='"+ pin.part_number + "'>" + pin.partnumber + "</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='" + pin.part_number_txt + "' /><textarea class='form-control' type='text' id='description_" + i + "' name='description[]' autocomplete='off' min='0' value='" + pin.description + "' >" + pin.description + "</textarea></td>\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' autocomplete='off' min='0' value='" + (pin.tax ? Number(pin.tax) : '') + "' readonly></td>\
                        <td><input class='form-control text-center qty rc' type='number' id='qty_" + i + "' onkeypress='set_license_key_po(" + i + "," + pin.product_type + ")'  name='qty[]' autocomplete='off' min='0' value='" + pin.qty + "' onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='unitprice_" + i + "' value='" + formatAmount(pin.unitprice) + "' name='unitprice[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='value_" + i + "' value='" + formatAmount(value) + "' name='value[]' autocomplete='off' min='0' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='discount_" + i + "' value='" + formatAmount(pin.discount) + "' name='discount[]' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td><input class='form-control text-end' type='text' id='taxableamount_" + i + "' value='" + formatAmount(taxamount) + "' name='taxableamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='vatamount_" + i + "' value='" + formatAmount(vatamount) + "' name='vatamount[]' readonly></td>\
                        <td><input class='form-control text-end' type='text' id='totalamount_" + i + "' value='" + formatAmount(totalamount) + "' name='totalamount[]' readonly></td>\
                        <td><input class='form-control text-end srl' type='test' id='srl_" + i + "' name='serial_no[]' onclick='srlno_add(" + i + ")' ></td>\
                        </tr>";
                            $('#si-row-count').val(i + 1);
                            $('#payment_terms').val(pin.payment_terms);
                            $("#lpo_no").val(pin.lpo_number);

                            $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                            $("#currency").val(pin.currency);
                            $("#invoice_no").val(pin.doc_number);

                            $('#invoice_date').val(pin.doc_date ? pin.doc_date.split('-').reverse().join('/') : '');

                            $("#sales_man").val(pin.sales_man).trigger('change');
                            $("#deal_id").val(pin.deal_id);
                            $("#supplier_name").val(pin.supplier_name);

                            qty_total += pin.qty;
                            unitprice_total += Number(pin.unitprice);
                            value_total += Number(value);
                            discount_total += Number(pin.discount);
                            taxableamount_total += Number(taxamount);
                            vatamount_total = + Number(vatamount);
                            total_amount += Number(totalamount);

                            $("#qty_total").text(qty_total);
                            $("#unitprice_total").text(unitprice_total.toFixed(2));
                            $("#value_total").text(value_total.toFixed(2));
                            $("#discount_total").text(discount_total.toFixed(2));
                            $("#taxableamount_total").text(taxableamount_total.toFixed(2));
                            $("#vatamount_total").text(vatamount_total.toFixed(2));
                            $("#total_amount").text(total_amount.toFixed(2));
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
                $("#addDNPending").prop("disabled", false);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});
$(document).on("click", "#addDNPendingItems", function (event) {
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
//DELIVERY NOTE PAGE END

//addPoPending

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
            var data_count=1;
            if(data==""){
                data_count=0;
            }
            console.log(data);
            var a = '';            
            var tr="";
            var pro_qty = "0";
            var selected_pos = [];
            var selected_dealid = [];
            var qty_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;
            var fright_total = 0;
            var customs_total = 0;

            $.each(data, function(i, item) {
                if (item.length) {
                    $.each(item, function(i, pin) {

                        if (pin.pro_qty != null){
                            pro_qty=pin.pro_qty;
                        }
                        var taxamount = pin.taxableamount;
                        var vatamount = pin.vatamount;
                        var totalamount = Number(pin.taxableamount) + Number(pin.vatamount);
                        var hscode=0;

                        qty_total += (pin.po_qty - pin.grn_qty);
                        value_total += Number(pin.value);
                        discount_total += Number(pin.discount);
                        fright_total += Number(pin.fright);
                        customs_total += Number(pin.customcharges);
                        taxableamount_total += Number(taxamount);
                        vatamount_total += Number(vatamount);
                        totalamount_total += Number(totalamount);
                        if(pin.hscode=="" || pin.hscode==null){
                            hscode=0;
                        } else { hscode=pin.hscode; }

                        tr +=  "<tr>\
                        <td><input class='form-control text-center' type='text' id='sort_id_" + i + "' name='sort_id[]' value='" + (i+1) +"' />\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+pin.part_id+"'>"+ pin.part_number +"</option></select></td>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='"+ pin.description +"'>\
                        <input type='hidden' id='part_number_txt_" + i + "' name='part_number_txt[]' value='"+pin.part_number+"'/><input type='hidden' id='po_itm_id_" + i + "' name='po_itm_id[]' value='"+pin.id+"'/><input type='hidden' id='part_id_" + i + "' name='part_id[]' value='"+pin.part_id+"'/><input type='hidden' id='list_po_id_" + i + "' name='list_po_id[]' value='"+pin.po_id+"'/>\
                        <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='"+(pin.po_qty - pin.grn_qty)+"'/></td>";

                        if(pin.company_id==2){
                        tr +=  "<td><input class='form-control' type='text' autocomplete='off' name='hscode_txt[]' id='hscode_" + i + "' value='"+hscode+"' readonly></td>";
                        } else{
                            tr +=  "<input type='hidden' id='hscode_" + i + "' name='hscode_txt[]' value='0' readonly></td>";
                        }
                        tr +=  "<td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='po_qty_" + i + "' min='0' value='"+(pin.po_qty)+"' readonly></td>\
                        <td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='exe_qty_" + i + "' min='0' value='"+(pin.grn_qty)+"' readonly></td>\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' value='"+parseInt(pin.tax)+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-center qty_total' type='number' id='qty_" + i + "' name='qty[]' value='"+(pin.po_qty - pin.grn_qty)+"' autocomplete='off' min='0'onchange='calc_change_new(this)' onkeypress='set_license_key_po("+i+","+pin.product_type+")'></td>\
                        <td style='display:none;'><input class='form-control' type='number' autocomplete='off' id='bal_qty_" + i + "' min='0' value='0' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='unitprice_" + i + "' name='unitprice[]' value='"+formatAmount(pin.unitprice)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='value_" + i + "' name='value[]' value='"+formatAmount(pin.value)+"' autocomplete='off' min='0' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='discount_" + i + "' name='discount[]' value='"+formatAmount(pin.discount)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='fright_" + i + "' name='fright[]' value='"+formatAmount(pin.fright)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='customcharges_" + i + "' name='customcharges[]' value='"+formatAmount(pin.customcharges)+"' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='taxamount_" + i + "' name='taxableamount[]' value='"+formatAmount(taxamount)+"' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='vatamount_" + i + "' name='vatamount[]' value='"+formatAmount(vatamount)+"' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' step='any' id='totalamount_" + i + "' name='totalamount[]' value='"+formatAmount((Number(pin.taxableamount)+Number(pin.vatamount)).toFixed(2))+"' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' name='serial_no[]' value='"+(pin.serial_no ?? '')+"' readonly></td>\
                        </tr>";
                        $('#row-count').val(i+1);
                        // $('#lpo_date').val(pin.po_date);

                        $('#lpo_date').val(pin.po_date ? pin.po_date.split('-').reverse().join('/') : '');

                        //$("#createdby").val(pin.created_by);
                        $("#payment_terms").val(pin.payment_terms);
                        $("#currency").val(pin.currency);
                        $("#sales_person").val(pin.user_id).trigger('change');
                        $("#reference").val(pin.narration);

                        if(!selected_pos.includes(pin.doc_number)) {
                            selected_pos.push(pin.doc_number);
                        }
                        $('#lpo_number').val(selected_pos);

                        if(!selected_dealid.includes(pin.code)) {
                            selected_dealid.push(pin.code);
                        }
                        $("#deal_id").val(selected_dealid);

                        $("#qty_total").text(qty_total);
                        $("#value_total").text(value_total.toFixed(2));
                        $("#discount_total").text(discount_total.toFixed(2));
                        $("#fright_total").text(fright_total.toFixed(2));
                        $("#customs_total").text(customs_total.toFixed(2));
                        $("#taxableamount_total").text(taxableamount_total.toFixed(2));
                        $("#vatamount_total").text(vatamount_total.toFixed(2));
                        $("#totalamount_total").text(totalamount_total.toFixed(2));


                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);

            //$("#table_id thead").show();
            if (data_count == 0) {
                const rowHtml = '<tr>\
    <td><input type="text" class="form-control" name="sort_id[]" value="1" /></td>\
    <td class="noborder"><select class="form-control noborder" name="part_number[]"></select></td> \
    <td><input class="form-control" type="text" name="description[]" autocomplete="off" readonly="true">\
        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>\
        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>\
        <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>\
        <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden></td>\
    <td><input type="number" class="form-control" name="tax[]" onchange="calc_change_new(this)"></td>\
    <td><input class="form-control" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>\
    <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
    <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly></td>\
    <td><input class="form-control" type="number" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
    <td><input class="form-control" type="number" name="fright[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
    <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
    <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
    <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
    <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>\
    <td><input class="form-control" type="text" name="serial_no[]"></td>\
</tr>';

                $('#myTable tbody').empty();
                $("#myTable tbody").append(rowHtml);
                fillTableToFitScreenHeight('myTable', 65);
            } else {
                $('#myTable tbody').empty();
            }
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

$(document).on("click", "#addPoPending", function (event) {
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
        success: function (data) {
            var tr = "";
            var qty_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;
            var fright_total = 0;
            var customs_total = 0;

            $.each(data, function (i, item) {
                if (item.length) {

                    $.each(item, function (i, pin) {

                        var poBalance = (pin.po_qty - pin.grn_qty);
                        var taxamount = pin.taxableamount;
                        var vatamount = pin.vatamount;
                        var totalamount = Number(taxamount) + Number(vatamount);
                        var hscode = pin.hscode ?? 0;

                        qty_total += poBalance;
                        value_total += Number(pin.value);
                        discount_total += Number(pin.discount);
                        fright_total += Number(pin.fright);
                        customs_total += Number(pin.customcharges);
                        taxableamount_total += Number(taxamount);
                        vatamount_total += Number(vatamount);
                        totalamount_total += Number(totalamount);
                        tr +=
                            "<tr>" +

                            // checkbox
                            "<td class='no-toggle'><input type='checkbox' class='po_check' checked name='selected_item_id[]' value='" + pin.id + "'></td>" +

                            // SERIAL NUMBER (readonly input)
                            "<td class='no-toggle'><input type='text' class='form-control form-control-sm text-center border-0' value='" + (i + 1) + "' readonly></td>" +

                            // PART NUMBER (non editable)
                            "<td>" + pin.part_number + "</td>" +

                            // DESCRIPTION (non editable)
                            "<td>" + pin.description + "</td>" +

                            // TAX (editable)
                            "<td class='no-toggle'><input type='number' class='form-control form-control-sm text-center tax-input border-0' name='tax[" + pin.id + "]' value='" + parseInt(pin.tax) + "'></td>" +

                            // QTY (editable)
                            "<td class='no-toggle'><input type='number' class='form-control form-control-sm text-center qty-input border-0' name='qty[" + pin.id + "]' value='" + poBalance + "'></td>" +

                            // UNIT PRICE (editable)
                            "<td class='no-toggle'><input type='number' step='0.01' class='form-control form-control-sm text-end price-input border-0' name='unitprice[" + pin.id + "]' value='" + pin.unitprice + "'></td>" +

                            // DISCOUNT (editable)
                            "<td class='no-toggle'><input type='number' step='0.01' class='form-control form-control-sm text-end discount-input border-0' name='discount[" + pin.id + "]' value='" + pin.discount + "'></td>" +

                            // VALUE (readonly – auto calculated)
                            "<td class='text-end no-toggle'>" + formatAmount(pin.value) + "</td>" +


                            "</tr>";
                    });

                }
            });

            // INJECT INTO MODAL TABLE
            $(".popupGRN tbody").empty().append(tr);

            // SHOW THE MODAL
            $("#po_pending_popup_win").modal("show");
        },

        error: function (data) {
            console.log('Error:', data);
        }
    });

});

function fillLpoHeaderData() {

    let firstRow = $(".popupGRN tbody tr:first");

    if (firstRow.length === 0) return;

    $("#lpo_date").val(firstRow.data("po_date") || "");
    $("#payment_terms").val(firstRow.data("payment_terms") || "");
    $("#currency").val(firstRow.data("currency") || "");
    $("#sales_person").val(firstRow.data("sales_person")).trigger("change");
    $("#reference").val(firstRow.data("narration") || "");

    $("#lpo_number").val(firstRow.data("doc_number"));
    $("#deal_id").val(firstRow.data("deal_code"));
}


let poPendingFirstLoad = true;
$(document).on("click", "#addPoPendingINMAINTable", function () {


    // GET ALL VALUES FROM MODAL TABLE (popupGRN)
    var rows = $(".popupGRN tbody tr");

    // CLEAR MAIN TABLE FIRST
    // CLEAR MAIN TABLE ONLY ON FIRST CLICK
    // if (poPendingFirstLoad) {
    $("#myTable tbody").empty();
    poPendingFirstLoad = false;     // <--- do not empty again
    // }

    let i = 0;

    rows.each(function () {

        // only insert checked rows
        if (!$(this).find(".po_check").prop("checked")) return;

        // READ ALL EDITABLE FIELDS FROM MODAL
        let pin_id = $(this).find(".po_check").val();
        let sl = i + 1;
        let part_no = $(this).find("td:eq(2)").text().trim();
        let desc = $(this).find("td:eq(3)").text().trim();
        let tax = $(this).find(".tax-input").val();
        let qty = $(this).find(".qty-input").val();
        let price = $(this).find(".price-input").val();
        let discount = $(this).find(".discount-input").val();

        // we must keep value readonly (calculated)
        let value = (qty * price) - discount;
        if (isNaN(value)) value = 0;

        // ================================
        // REBUILD ROW EXACT LIKE SCRIPT #2
        // ================================
        let newRow = `
        <tr>

            <td><input class='form-control text-center' type='text' 
                name='sort_id[]' value='${sl}' /></td>

            <td>
                <select class='form-control' name='part_number[]'>
                    <option value='${pin_id}'>${part_no}</option>
                </select>
            </td>

            <td>
                <textarea class='form-control' type='text' name='description[]' rows="1" value='${desc}'>${desc}</textarea>
                <input type='hidden' name='part_number_txt[]' value='${part_no}'/>
                <input type='hidden' name='po_itm_id[]' value='${pin_id}'/>
                <input type='hidden' name='part_id[]' value='${pin_id}'/>
                <input type='hidden' name='list_po_id[]' value='${$("#hd_pending_po_id").val()}'/>
                <input type='hidden' name='grn_qty[]' value='${qty}'/>
            </td>

            <td>
                <input class='form-control text-center' type='number' onchange="calc_change_new(this)"
                       name='tax[]' value='${tax}'/>
            </td>

            <td>
                <input class='form-control text-center qty_total' type='number'  onchange="calc_change_new(this)" onkeypress="set_license_key(this)"
                       name='qty[]' value='${qty}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'  onblur="formatCurrency(this)"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"
                       name='unitprice[]' value='${price}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='value[]' value='${value.toFixed(2)}' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='discount[]' value='${discount}'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='fright[]' value='0'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'  onblur="formatCurrency(this)" onchange="calc_change_new(this)"
                       name='customcharges[]' value='0'/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='taxableamount[]' value='0' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='vatamount[]' value='0' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text'
                       name='totalamount[]' value='0' readonly/>
            </td>

            <td class='jshide'>
                <input class='form-control text-end' type='text' 
                       name='serial_no[]' value='' readonly/>
            </td>

        </tr>`;

        $("#myTable tbody").append(newRow);
        i++;
    });

    // UPDATE TOTALS
    if (typeof update_totals === "function") update_totals();

    // FILL ALL HEADER / HIDDEN FIELDS
    fillLpoHeaderData();

    // CLOSE POPUP
    $("#po_pending_popup_win").modal("hide");

});



//addPoPending

//addPoPendingItems
$(document).on("click", "#addPoPendingItems", function (event) {
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
$(document).on("click", "#addGRNPending", function (event) {
    var url = $('#url').val();
    var grn_id = $('#hd_pending_grn_id').val();
    var po_id = $('#hd_pending_po_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { grn_id: grn_id, po_id: po_id },
        dataType: 'json',
        url: url + '/' + 'goods-receipt-note-for-pi-item-list',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var pro_qty = "0";

            var qty_total = 0;
            var unitprice_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;

            $.each(data, function (i, item) {

                if (item.length) {
                    $.each(item, function (i, pin) {

                        if (pin.pro_qty != null) {
                            pro_qty = pin.pro_qty;
                        }

                        tr += "<tr>\
                        <td><input class='form-control text-center' type='number' id='sort_id_" + i + "' name='sort_id[]' value='" + (i + 1) + "' ></td>\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='" + pin.part_id + "'>" + pin.part_number + "</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='" + pin.part_number + "'/>\
                        <input type='hidden' id='part_id_" + i + "' name='hscode_txt[]' value='" + pin.part_id + "'/>\
                        <input type='hidden' id='part_id_" + i + "' name='product_type[]' value='" + pin.part_id + "'/>\
                        <input type='hidden' id='part_id_" + i + "' name='product_type_part_number_text[]' value='" + pin.part_id + "'/>\
                        <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='" + pin.grn_qty + "'/>\
                        <input class='form-control' type='text' name='description[]' autocomplete='off' value='"+ pin.description + "' >\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' value='" + pin.tax + "' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='" + pin.grn_qty + "' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='unitprice_" + i + "' name='unitprice[]' value='" + formatAmount(pin.unitprice) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='value_" + i + "' name='value[]' value='" + formatAmount(pin.value) + "' autocomplete='off' min='0'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='discount_" + i + "' name='discount[]' value='" + formatAmount(pin.discount) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='fright_" + i + "' name='fright[]' value='" + formatAmount(pin.fright) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='customcharges_" + i + "' name='customcharges[]' value='" + formatAmount(pin.customcharges) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='taxamount_" + i + "' name='taxableamount[]' value='" + formatAmount(pin.taxableamount) + "'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='vatamount_" + i + "' name='vatamount[]' value='" + formatAmount(pin.vatamount) + "'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='text' id='totalamount_" + i + "' name='totalamount[]' value='" + formatAmount((Number(pin.taxableamount) + Number(pin.vatamount))) + "'></td>\
                        </tr>";
                        $('#row-count').val(i + 1);
                        $('#lpo_number').val(pin.lpo_number);
                        $('#po_id').val(pin.po_id);
                        // $('#lpo_date').val(pin.lpo_date);
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#payment_terms').val(pin.payment_terms);
                        $('#currency').val(pin.currency);
                        $('#bill_number').val(pin.bill_number);
                        // $('#bill_date').val(pin.bill_date);
                        $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

                        $('#awbno').val(pin.awbno);
                        $('#boeno').val(pin.boeno);
                        $('#warehouse').val(pin.warehouse);

                        $('#grn_no').val(pin.doc_number);
                        // $('#grn_date').val(pin.grn_date);
                        $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');

                        $('#sales_person').val(pin.sales_person).trigger('change');

                        $('#reference').val(pin.reference);
                        $('#narration').val(pin.narration);

                        $('#deal_id').val(pin.deal_id);

                        $('#shipping_name').val(pin.shipping_name);
                        $('#shipping_address_1').val(pin.shipping_address_1);
                        $('#shipping_address_2').val(pin.shipping_address_2);
                        $('#shipping_contact_no').val(pin.shipping_contact_no);
                        $('#supplier_type').val(pin.supplier_type);
                        $('#purchase_type').val(pin.purchase_type);
                        $('#country').val(pin.supplier_country).trigger('change');
                        $('#state').val(pin.supplier_state);

                        qty_total += Number(pin.grn_qty);
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
                get_deal_code();
            });
            console.log(a);
            $('#myTable tbody').empty();
            $("#myTable tbody").append(tr);
            $(".jshide").show();
            $(".jshide1").hide();
            update_totals();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });

});
//addGRNPIPending
//addGRNPendingItems
$(document).on("click", "#addGRNPendingItems", function (event) {
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
$(document).on("click", "#addPIPending", function (event) {
    var url = $('#url').val();
    var pi_id = $('#hd_pending_pi_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { pi_id: pi_id },
        dataType: 'json',
        url: url + '/' + 'get-pi-list-for-pi-return',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var pro_qty = "0";

            var qty_total = 0;
            var unitprice_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;

            $.each(data, function (i, item) {
                if (item.length) {
                    $.each(item, function (i, pin) {

                        if (pin.pro_qty != null) {
                            pro_qty = pin.pro_qty;
                        }

                        tr += "<tr>\
                        <td><input class='form-control text-center' type='number' autocomplete='off' name='sort_id[]' value='"+ (i + 1) + "' /></td>\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='" + pin.part_number + "'>" + pin.part_number_txt + "</option></select></td>\
                        <td><textarea class='form-control' name='description[]' rows='1'>"+ pin.description + "</textarea></td>\
                        <td><input type='hidden' id='partno_" + i + "' name='part_number_txt[]' value='" + pin.part_number_txt + "'/>\
                        <input type='hidden' id='pi_qty_" + i + "' name='pi_qty[]' value='" + pin.qty + "'/>\
                        <input class='form-control' type='number' autocomplete='off' min='0' value='"+ pin.qty + "' readonly></td>\
                        <td><input class='form-control text-center' type='number' autocomplete='off' min='0' id='tax_" + i + "' name='tax[]' value='" + parseInt(pin.tax) + "'  onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='" + pin.qty + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onkeypress='set_license_key_po(" + i + "," + pin.product_type + ")'></td>\
                        <td class='jshide'><input class='form-control text-end' step='Any' type='text' id='unitprice_" + i + "' name='unitprice[]' value='" + formatAmount(pin.unitprice) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' id='value_" + i + "' name='value[]' value='" + formatAmount(pin.value) + "' autocomplete='off' min='0' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' id='discount_" + i + "' name='discount[]' value='" + formatAmount(pin.discount) + "' autocomplete='off' min='0' onchange='calc_change_new(this)' onblur='formatCurrency(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' id='taxamount_" + i + "' name='taxableamount[]' value='" + formatAmount(pin.taxableamount) + "' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' id='vatamount_" + i + "' name='vatamount[]' value='" + formatAmount(pin.vatamount) + "' readonly></td>\
                        <td class='jshide'><input class='form-control text-end' type='text' id='totalamount_" + i + "' name='totalamount[]' value='" + formatAmount((Number(pin.taxableamount) + Number(pin.vatamount))) + "' readonly></td>\
                        <td class='jshide' style='display:none;'><input class='form-control srl' type='text' name='serial_no[]'></td>\
                        </tr>";
                        $('#row-count').val(i + 1);
                        $('#pi_number').val(pin.doc_number);
                        // $('#pi_date').val(pin.pi_date);
                        $('#pi_date').val(pin.pi_date ? pin.pi_date.split('-').reverse().join('/') : '');

                        $('#lpo_number').val(pin.lpo_number);
                        $('#po_id').val(pin.ref_po_id);
                        $('#grn_id').val(pin.ref_grn_id);
                        // $('#lpo_date').val(pin.lpo_date);
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#payment_terms').val(pin.payment_terms).trigger('change');
                        $('#currency').val(pin.currency);
                        $('#bill_number').val(pin.bill_number);
                        // $('#bill_date').val(pin.bill_date);
                        $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

                        $('#awbno').val(pin.awbno);
                        $('#warehouse').val(pin.warehouse);

                        $('#grn_no').val(pin.doc_number);
                        // $('#grn_date').val(pin.grn_date);
                        $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');


                        $('#salesman_name').val(pin.salesman_name);
                        $('#reference').val(pin.reference);
                        $('#deal_id').val(pin.code);

                        $('#shipping_name').val(pin.shipping_name);
                        $('#shipping_address_1').val(pin.shipping_address_1);
                        $('#shipping_address_2').val(pin.shipping_address_2);
                        $('#shipping_contact_no').val(pin.shipping_contact_no);
                        $('#supplier_type').val(pin.supplier_type);
                        $('#purchase_type').val(pin.purchase_type);
                        $('#country').val(pin.supplier_country);
                        $('#state').val(pin.supplier_state);

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
        error: function (data) {
            console.log('Error:', data);
        }
    });

});
//addPIPending
//addPIPendingItems
$(document).on("click", "#addPIPendingItems", function (event) {
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
$(document).on("click", "#addPIPendingSTL", function (event) {
    var url = $('#url').val();
    var pi_id = $('#hd_pending_pi_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { pi_id: pi_id },
        dataType: 'json',
        url: url + '/' + 'get-pi-list-for-stl',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var tr1 = "";
            var sr = 1;
            var description = "";
            var sum_class = "";
            var total = 0;

            $.each(data, function (i, item) {
                if (item.length) {
                    $.each(item, function (i, pin) {

                        if (i == 0) {
                            $('#table_id_stl_docno_' + pi_id).text(pin.doc_number);
                            $('#table_id_stl_billno_' + pi_id).text(pin.bill_number);
                            $('#table_id_stl_awbno_' + pi_id).text("AWB No. " + pin.awbno);
                            $('#table_id_stl_boeno_' + pi_id).text("BOE No. " + pin.boeno);
                        }

                        if (pin.description.toLowerCase().includes('license'.toLowerCase())) {
                            description = "Networking License";
                            sum_class = "license";
                        }
                        else if (pin.description.toLowerCase().includes('licence'.toLowerCase())) {
                            description = "Networking License";
                            sum_class = "license";
                        } else {
                            description = "Networking " + pin.cat_name;
                            sum_class = "networking";
                        }
                        tr += "<tr><td class='jshide3'>" + sr + "</td><td class='jshide12'><input type=checkbox checked id=id_" + (i + 1) + " value=" + pin.id + "></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='" + pin.part_number_txt + "' readonly>\
                        <input type='hidden' id='partno_" + i + "' name='partno[]' value='" + pin.part_number + "'/>\
                        <input type='hidden' id='purchase_inv_" + i + "' name='purchase_inv[]' value='" + pi_id + "'/><input type='hidden' id='pi_inv_no_" + i + "' name='pi_inv_no[]' value='" + pin.bill_number + "'/><input type='hidden' id='awbno_" + i + "' name='awbno[]' value='" + pin.awbno + "'/><input type='hidden' id='boeno_" + i + "' name='boeno[]' value='" + pin.boeno + "'/>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='" + description + "' readonly>\
                        <td class='jshide'><input class='form-control text-end "+ sum_class + "' type='text' id='amount_" + i + "' name='amount[]' value='" + formatAmount(Number(pin.taxableamount) + Number(pin.vatamount)) + "' onchange='set_total()' readonly></td>\
                        <td class='jshide3'><a class='btn-sm btn-info edit-btn'>Edit</a> <a class='btn-sm btn-danger delete-btn'>Delete</a></td>\
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
            $('#table_id_total_' + pi_id).text(total.toFixed(2));
            set_total();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });

});
//addPIPendingSTL
//addPIPendingItemsSTL
$(document).on("click", "#addPIPendingSTLItems", function (event) {
    var pi_id = $('#hd_pending_pi_id').val();
    var getSelectedRows = $("#table_id input:checked").parents("tr").clone();
    getSelectedRows = getSelectedRows.get().reverse();
    $('#table_id_stl_' + pi_id + ' tbody').empty();
    $("#table_id_stl_" + pi_id + " tbody").append(getSelectedRows);
    $("#table_id_stl_" + pi_id).css('display', '');
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
$(document).on("click", "#addPOPendingSTL", function (event) {
    var url = $('#url').val();
    var po_id = $('#hd_pending_po_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { po_id: po_id },
        dataType: 'json',
        url: url + '/' + 'get-po-list-for-stl',
        success: function (data) {
            console.log(data);
            var a = '';
            var tr = "";
            var tr1 = "";
            var sr = 1;
            var description = "";
            var sum_class = "";
            var total = 0;

            $.each(data, function (i, item) {
                if (item.length) {
                    $.each(item, function (i, pin) {

                        if (i == 0) {
                            $('#table_id_stl_docno_' + po_id).text(pin.doc_number);
                            $('#table_id_stl_billno_' + po_id).text(pin.doc_number);
                            $('#table_id_stl_awbno_' + po_id).text("AWB No. " + pin.awbno);
                            $('#table_id_stl_boeno_' + po_id).text("BOE No. " + pin.boeno);
                        }

                        if (pin.description.toLowerCase().includes('license'.toLowerCase())) {
                            description = "Networking License";
                            sum_class = "license";
                        }
                        else if (pin.description.toLowerCase().includes('licence'.toLowerCase())) {
                            description = "Networking License";
                            sum_class = "license";
                        } else {
                            description = "Networking " + pin.cat_name;
                            sum_class = "networking";
                        }
                        tr += "<tr><td class='jshide3'>" + sr + "</td><td class='jshide12'><input type=checkbox checked id=id_" + (i + 1) + " value=" + pin.id + "></td>\
                        <td><input class='form-control' type='text' id='part_number_" + i + "' name='part_number[]' value='" + pin.part_number_txt + "' readonly>\
                        <input type='hidden' id='partno_" + i + "' name='partno[]' value='" + pin.part_number + "'/>\
                        <input type='hidden' id='purchase_inv_" + i + "' name='purchase_inv[]' value='" + po_id + "'/><input type='hidden' id='pi_inv_no_" + i + "' name='pi_inv_no[]' value='" + pin.doc_number + "'/><input type='hidden' id='awbno_" + i + "' name='awbno[]' value='" + pin.awbno + "'/><input type='hidden' id='boeno_" + i + "' name='boeno[]' value='" + pin.boeno + "'/>\
                        <td><input class='form-control' type='text' id='description_" + i + "' name='description[]' value='" + description + "' readonly>\
                        <td class='jshide'><input class='form-control text-end "+ sum_class + "' type='text' id='amount_" + i + "' name='amount[]' value='" + formatAmount(Number(pin.taxableamount) + Number(pin.vatamount)) + "' onchange='set_total()' readonly></td>\
                        <td class='jshide3'><a class='btn-sm btn-info edit-btn'>Edit</a> <a class='btn-sm btn-danger delete-btn'>Delete</a></td>\
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
            $('#po_table_id_total_' + pi_id).text(total.toFixed(2));
            set_total();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });

});
//addPIPendingSTL
//addPIPendingItemsSTL
$(document).on("click", "#addPOPendingSTLItems", function (event) {
    var pi_id = $('#hd_pending_po_id').val();
    var getSelectedRows = $("#po_table_id input:checked").parents("tr").clone();
    getSelectedRows = getSelectedRows.get().reverse();
    $('#po_table_id_stl_' + pi_id + ' tbody').empty();
    $("#po_table_id_stl_" + pi_id + " tbody").append(getSelectedRows);
    $("#po_table_id_stl_" + pi_id).css('display', '');
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

$(document).on("click", "#addRowSI", function (event) {

    var i = $('#si-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

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
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
            });
            tr += "</select>";
            tr += '</td>';
            tr += '<td>';

            tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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

$(document).on("click", "#addRowPO", function (event) {

    var i = $('#po-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

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
            $.each(response, function (key, value) {
                tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
            });
            tr += "</select>";
            tr += '</td>';
            tr += '<td>';

            tr += "<select class='form-control' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
            tr += "<option value='none'></option>";
            $.each(response, function (key, value) {
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


$(document).on("click", "#addRowAdditionalLocation", function (event) {
    alert("122");

    var tr = "<tr id='rowone'>";
    tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="price_' + i + '" name="price[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="totalprice_' + i + '" name="totalprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')" readonly></td>\
                            ';
    tr += '</tr>';




    $("#AdditionalLocation tbody tr:last").after(tr);

});
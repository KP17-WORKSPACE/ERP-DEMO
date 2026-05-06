$("#sales-invoice-create-form").submit(function(event) {
    if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#si_date").val() === "") {
        $('#si_date').css('border-bottom', 'solid 1px #ff0000');
        $('#si_date').focus();
        return false;
    } else if ($("#customer").val() === "") {
        $('#customer').css('border-bottom', 'solid 1px #ff0000');
        $('#customer').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#createdby").val() === "") {
        $('#createdby').css('border-bottom', 'solid 1px #ff0000');
        $('#createdby').focus();
        return false;
    } else if ($("#delivery").val() === "") {
        $('#delivery').css('border-bottom', 'solid 1px #ff0000');
        $('#delivery').focus();
        return false;
    } else if ($("#printed_invoice_number").val() === "") {
        $('#printed_invoice_number').css('border-bottom', 'solid 1px #ff0000');
        $('#printed_invoice_number').focus();
        return false;
    } else if ($("#salesman").val() === "") {
        $('#salesman').css('border-bottom', 'solid 1px #ff0000');
        $('#salesman').focus();
        return false;
    } else if ($("#lpo_date").val() === "") {
        $('#lpo_date').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_date').focus();
        return false;
    } else if ($("#lpo_number").val() === "") {
        $('#lpo_number').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_number').focus();
        return false;
    } else if ($("#payment_terms").val() === "") {
        $('#payment_terms').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_terms').focus();
        return false;
    } else if ($("#bill_number").val() === "") {
        $('#bill_number').css('border-bottom', 'solid 1px #ff0000');
        $('#bill_number').focus();
        return false;
    } else if ($("#bill_date").val() === "") {
        $('#bill_date').css('border-bottom', 'solid 1px #ff0000');
        $('#bill_date').focus();
        return false;
    } else if ($("#customer_type").val() === "") {
        $('#customer_type').css('border-bottom', 'solid 1px #ff0000');
        $('#customer_type').focus();
        return false;
    } else if ($("#purchase_type").val() === "") {
        $('#purchase_type').css('border-bottom', 'solid 1px #ff0000');
        $('#purchase_type').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#purchase-invoice-create-form").submit(function(event) {
    if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#pi_date").val() === "") {
        $('#pi_date').css('border-bottom', 'solid 1px #ff0000');
        $('#pi_date').focus();
        return false;
    } else if ($("#vendors").val() === "") {
        $('#vendors').css('border-bottom', 'solid 1px #ff0000');
        $('#vendors').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#createdby").val() === "") {
        $('#createdby').css('border-bottom', 'solid 1px #ff0000');
        $('#createdby').focus();
        return false;
    } else if ($("#lpo_date").val() === "") {
        $('#lpo_date').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_date').focus();
        return false;
    } else if ($("#lpo_number").val() === "") {
        $('#lpo_number').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_number').focus();
        return false;
    } else if ($("#payment_terms").val() === "") {
        $('#payment_terms').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_terms').focus();
        return false;
    } else if ($("#bill_number").val() === "") {
        $('#bill_number').css('border-bottom', 'solid 1px #ff0000');
        $('#bill_number').focus();
        return false;
    } else if ($("#bill_date").val() === "") {
        $('#bill_date').css('border-bottom', 'solid 1px #ff0000');
        $('#bill_date').focus();
        return false;
    } else if ($("#supplier_type").val() === "") {
        $('#supplier_type').css('border-bottom', 'solid 1px #ff0000');
        $('#supplier_type').focus();
        return false;
    } else if ($("#purchase_type").val() === "") {
        $('#purchase_type').css('border-bottom', 'solid 1px #ff0000');
        $('#purchase_type').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#journalvoucher-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_dr_1").val() === "" && $("#amount_cr_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_dr_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_cr_1').css('border-bottom', 'solid 1px #ff0000');
        $('#account_id_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#cashreceipt-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#receipt_mode").val() === "") {
        $('#receipt_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#receipt_mode').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#bankreceipt-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#receipt_mode").val() === "") {
        $('#receipt_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#receipt_mode').focus();
        return false;
    } else if ($("#cheque_date").val() === "") {
        $('#cheque_date').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_date').focus();
        return false;
    } else if ($("#cheque_number").val() === "") {
        $('#cheque_number').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_number').focus();
        return false;
    } else if ($("#cheque_bank_name").val() === "") {
        $('#cheque_bank_name').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_bank_name').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#postdatedreceipt-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#receipt_mode").val() === "") {
        $('#receipt_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#receipt_mode').focus();
        return false;
    } else if ($("#cheque_date").val() === "") {
        $('#cheque_date').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_date').focus();
        return false;
    } else if ($("#cheque_number").val() === "") {
        $('#cheque_number').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_number').focus();
        return false;
    } else if ($("#cheque_bank_name").val() === "") {
        $('#cheque_bank_name').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_bank_name').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#maturity_date").val() === "") {
        $('#maturity_date').css('border-bottom', 'solid 1px #ff0000');
        $('#maturity_date').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#cashpayment-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#payment_mode").val() === "") {
        $('#payment_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_mode').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#bankpayment-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#payment_mode").val() === "") {
        $('#payment_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_mode').focus();
        return false;
    } else if ($("#cheque_date").val() === "") {
        $('#cheque_date').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_date').focus();
        return false;
    } else if ($("#cheque_number").val() === "") {
        $('#cheque_number').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_number').focus();
        return false;
    } else if ($("#cheque_bank_name").val() === "") {
        $('#cheque_bank_name').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_bank_name').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#postdatedpayment-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#payment_mode").val() === "") {
        $('#payment_mode').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_mode').focus();
        return false;
    } else if ($("#cheque_date").val() === "") {
        $('#cheque_date').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_date').focus();
        return false;
    } else if ($("#cheque_number").val() === "") {
        $('#cheque_number').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_number').focus();
        return false;
    } else if ($("#cheque_bank_name").val() === "") {
        $('#cheque_bank_name').css('border-bottom', 'solid 1px #ff0000');
        $('#cheque_bank_name').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#maturity_date").val() === "") {
        $('#maturity_date').css('border-bottom', 'solid 1px #ff0000');
        $('#maturity_date').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#delivery-note-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#currency").val() === "") {
        $('#currency').css('border-bottom', 'solid 1px #ff0000');
        $('#currency').focus();
        return false;
    } else if ($("#created_by").val() === "") {
        $('#created_by').css('border-bottom', 'solid 1px #ff0000');
        $('#created_by').focus();
        return false;
    } else if ($("#dn_customer_id").val() === "") {
        $('#dn_customer_id').css('border-bottom', 'solid 1px #ff0000');
        $('#dn_customer_id').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#paymentterms").val() === "") {
        $('#paymentterms').css('border-bottom', 'solid 1px #ff0000');
        $('#paymentterms').focus();
        return false;
    } else if ($("#lpo_no").val() === "") {
        $('#lpo_no').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_no').focus();
        return false;
    } else if ($("#lpo_date").val() === "") {
        $('#lpo_date').css('border-bottom', 'solid 1px #ff0000');
        $('#lpo_date').focus();
        return false;
    } else if ($("#issued_by").val() === "") {
        $('#issued_by').css('border-bottom', 'solid 1px #ff0000');
        $('#issued_by').focus();
        return false;
    } else if ($("#received_by").val() === "") {
        $('#received_by').css('border-bottom', 'solid 1px #ff0000');
        $('#received_by').focus();
        return false;
    } else if ($("#driver").val() === "") {
        $('#driver').css('border-bottom', 'solid 1px #ff0000');
        $('#driver').focus();
        return false;
    } else if ($("#vehicleno").val() === "") {
        $('#vehicleno').css('border-bottom', 'solid 1px #ff0000');
        $('#vehicleno').focus();
        return false;
    } else if ($("#invoice_no").val() === "") {
        $('#invoice_no').css('border-bottom', 'solid 1px #ff0000');
        $('#invoice_no').focus();
        return false;
    } else if ($("#invoice_date").val() === "") {
        $('#invoice_date').css('border-bottom', 'solid 1px #ff0000');
        $('#invoice_date').focus();
        return false;
    } else if ($("#account_id_1").val() === "" && $("#amount_1").val() === "") {
        $('#account_id_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').css('border-bottom', 'solid 1px #ff0000');
        $('#amount_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});

$("#delivery-advice-create-form").submit(function(event) {
    if ($("#doc_date").val() === "") {
        $('#doc_date').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_date').focus();
        return false;
    } else if ($("#doc_number").val() === "") {
        $('#doc_number').css('border-bottom', 'solid 1px #ff0000');
        $('#doc_number').focus();
        return false;
    } else if ($("#da_customer_id").val() === "") {
        $('#da_customer_id').css('border-bottom', 'solid 1px #ff0000');
        $('#da_customer_id').focus();
        return false;
    } else if ($("#narration").val() === "") {
        $('#narration').css('border-bottom', 'solid 1px #ff0000');
        $('#narration').focus();
        return false;
    } else if ($("#salesman").val() === "") {
        $('#salesman').css('border-bottom', 'solid 1px #ff0000');
        $('#salesman').focus();
        return false;
    } else if ($("#contact_person").val() === "") {
        $('#contact_person').css('border-bottom', 'solid 1px #ff0000');
        $('#contact_person').focus();
        return false;
    } else if ($("#mobile_no").val() === "") {
        $('#mobile_no').css('border-bottom', 'solid 1px #ff0000');
        $('#mobile_no').focus();
        return false;
    } else if ($("#landline_no").val() === "") {
        $('#landline_no').css('border-bottom', 'solid 1px #ff0000');
        $('#landline_no').focus();
        return false;
    } else if ($("#da_si_numbers").val() === "") {
        $('#da_si_numbers').css('border-bottom', 'solid 1px #ff0000');
        $('#da_si_numbers').focus();
        return false;
    } else if ($("#invoice_date").val() === "") {
        $('#invoice_date').css('border-bottom', 'solid 1px #ff0000');
        $('#invoice_date').focus();
        return false;
    } else if ($("#vehicle_no").val() === "") {
        $('#vehicle_no').css('border-bottom', 'solid 1px #ff0000');
        $('#vehicle_no').focus();
        return false;
    } else if ($("#driver").val() === "") {
        $('#driver').css('border-bottom', 'solid 1px #ff0000');
        $('#driver').focus();
        return false;
    } else if ($("#do_no").val() === "") {
        $('#do_no').css('border-bottom', 'solid 1px #ff0000');
        $('#do_no').focus();
        return false;
    } else if ($("#do_date").val() === "") {
        $('#do_date').css('border-bottom', 'solid 1px #ff0000');
        $('#do_date').focus();
        return false;
    } else if ($("#payment_terms").val() === "") {
        $('#payment_terms').css('border-bottom', 'solid 1px #ff0000');
        $('#payment_terms').focus();
        return false;
    } else if ($("#delivery_date").val() === "") {
        $('#delivery_date').css('border-bottom', 'solid 1px #ff0000');
        $('#delivery_date').focus();
        return false;
    } else if ($("#delivery_time").val() === "") {
        $('#delivery_time').css('border-bottom', 'solid 1px #ff0000');
        $('#delivery_time').focus();
        return false;
    } else if ($("#delivery_address").val() === "") {
        $('#delivery_address').css('border-bottom', 'solid 1px #ff0000');
        $('#delivery_address').focus();
        return false;
    } else if ($("#invoice_amount").val() === "") {
        $('#invoice_amount').css('border-bottom', 'solid 1px #ff0000');
        $('#invoice_amount').focus();
        return false;
    } else if ($("#remarks").val() === "") {
        $('#remarks').css('border-bottom', 'solid 1px #ff0000');
        $('#remarks').focus();
        return false;
    } else if ($("#da_part_no_1").val() === "" && $("#da_qty_1").val() === "") {
        $('#da_part_no_1').css('border-bottom', 'solid 1px #ff0000');
        $('#da_qty_1').css('border-bottom', 'solid 1px #ff0000');
        $('#da_part_no_1').focus();
        return false;
    } else {
        $("#btnSubmit").prop('disabled', true);
        return true;
    }
    $("#btnSubmit").prop('disabled', true);
    return true;
});